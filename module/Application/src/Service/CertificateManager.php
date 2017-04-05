<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Application\Entity\Certificate;
use Application\Entity\Document;
use Application\Entity\Market;
use Zend\Filter\StaticFilter;
//namespace ZendXml;
//use DOMDocument;
use SimpleXMLElement;

/**
 * The CertificateManager service is responsible for adding new certificates, 
 * updating existing certificates, adding markets to certificate, etc.
 */
class CertificateManager
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager;
     */

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This method exports certificate to array for the displaying sake
     */
    public function toArray(Certificate $certificate)
    {

        $markets = $certificate->getMarkets();
        $markets_array = array();
        if (!empty($markets))
        {
            foreach ($markets as $market)
            {
                $markets_array[] = array(
                    'id' => $market->getId(),
                    'name' => $market->getName()
                );
            }
        }


        $documents = $certificate->getDocuments();
        $documents_array = array();
        if (!empty($documents))
        {
            foreach ($documents as $document)
            {
                $documents_array[] = array(
                    'id' => $document->getId(),
                    'filename' => $document->getFilename(),
                    'type' => $document->getType(),
                    'url' => $document->getUrl(),
                    'dateCreated' => $document->getDateCreated()
                );
            }
        }

        $prices = $certificate->getPriceHistory();
        $prices_array = array();
        if (!empty($prices))
        {
            foreach ($prices as $price)
            {
                $prices_array[] = array('price' => $price->getValue(), 'timestamp' => $price->getCreated());
            }
        }


        return array(
            'Id' => $certificate->getId(),
            'Title' => $certificate->getTitle(),
            'Issuer' => $this->getCertificateIssuerAsString($certificate),
            'ISIN' => $certificate->getIsin(),
            'Type' => $this->getCertificateTypeAsString($certificate),
            'Issuer Price' => $certificate->getPriceIssuing(),
            'Current Price' => $certificate->getPrice(),
            'Trading Market' => $this->convertMarketsToString($certificate),
            'Trading Markets' => $markets_array,
            'Documents Summary' => $this->getDocumentCountStr($certificate),
            'Documents' => $documents_array,
            'Prices' => $prices_array,
        );
    }

    /*
     * export certificate to XML
     * @return SimpleXMLElement
     */
    public function buildXml(Certificate $certificate)
    {
        $certificateArray = $this->toArray($certificate);
        $xml = $this->arrayToXml($certificateArray, '<certificate/>');
        return $xml;
    }

    
    
    /**
     * @param array $array the array to be converted
     * @param string? $rootElement if specified will be taken as root element, 
     * otherwise defaults to  <root>
     * @param SimpleXMLElement? if specified content will be appended, used for recursion
     * @return string XML version of $array
     */
    protected function arrayToXml($array, $rootElement = null, $xml = null)
    {
        $_xml = $xml;

        if ($_xml === null)
        {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        foreach ($array as $k => $v)
        {
            if (is_array($v))
            { //nested array
                $this->arrayToXml($v, $k, $_xml->addChild($k));
            } elseif (is_string($v))
            {
                $_xml->addChild(str_replace(" ", "_", $k), $v);
            } else
            {
                var_dump($k, $v);
            }
        }

        return $_xml->asXML();
    }

    /**
     * This method adds a new certificate.
     */
    public function addNewCertificate($data)
    {
        // Create new Certificate entity.
        $certificate = new Certificate();
        $certificate->setTitle($data['title']);
        $certificate->setIsin($data['isin']);
        $certificate->setIssuer($data['id_issuer']);
        $certificate->setPriceIssuing($data['price_issuing'], $data['id_currency']);
        // Add the entity to entity manager.
        $this->entityManager->persist($certificate);
        // Add markets to certificate
        $this->addMarketsToCertificate($data['markets'], $certificate);
        // Apply changes to database.
        $this->entityManager->flush();
    }

    /**
     * This method allows to update data of a single certificate.
     */
    public function updateCertificate($certificate, $data)
    {
        $certificate->setTitle($data['title']);
        $certificate->setIsin($data['isin']);
        $certificate->setIssuer($data['id_issuer']);
        $certificate->setPriceIssuing($data['price_issuing'], $data['id_currency']);
        // Add markets to certificate
        $this->addMarketsToCertificate($data['markets'], $certificate);
        // Apply changes to database.
        $this->entityManager->flush();
    }

    /**
     * Adds/updates markets in the given certificate.
     */
    private function addMarketsToCertificate($marketsStr, $certificate)
    {
        // Remove market associations (if any)
        $markets = $certificate->getMarkets();
        foreach ($markets as $market)
        {
            $certificate->removeMarketAssociation($market);
        }

        // Add markets to certificate
        $markets = explode(',', $marketsStr);
        foreach ($markets as $marketName)
        {

            $marketName = StaticFilter::execute($marketName, 'StringTrim');
            if (empty($marketName))
            {
                continue;
            }

            $market = $this->entityManager->getRepository(Market::class)
                    ->findOneByName($marketName);
            if ($market == null)
                $market = new Market();

            $market->setName($marketName);
            $market->addCertificate($certificate);

            $this->entityManager->persist($market);

            $certificate->addMarket($market);
        }
    }

    /**
     * Returns type as a string.
     */
    public function getCertificateTypeAsString($certificate)
    {
        switch ($certificate->getType())
        {
            case Certificate::GUARANTEE: return 'Guarantee';
            case Certificate::BONUS: return 'Bonus';
        }
        return 'Standard';
    }

    public function getCertificateIssuerAsString($certificate)
    {
        return $certificate->getIssuer()->getName();
    }

    /**
     * Converts markets of the given certificate to comma separated list (string).
     */
    public function convertMarketsToString($certificate)
    {
        $markets = $certificate->getMarkets();
        $marketCount = count($markets);
        $marketsStr = '';
        $i = 0;
        if (!is_null($markets))
        {
            foreach ($markets as $market)
            {
                $i ++;
                $marketsStr .= $market->getName();
                if ($i < $marketCount)
                    $marketsStr .= ', ';
            }
        }
        return $marketsStr;
    }

    /**
     * Returns count of documents for given certificate as properly formatted string.
     */
    public function getDocumentCountStr($certificate)
    {
        $documentCount = count($certificate->getDocuments());
        if ($documentCount == 0)
            return 'No documents';
        else if ($documentCount == 1)
            return '1 document';
        else
            return $documentCount . ' documents';
    }

    /**
     * This method adds a new document to certificate.
     */
    public function addDocumentToCertificate($certificate, $data)
    {
        // Create new Document entity.
        $document = new Document();
        $document->setCertificate($certificate);
        $document->setAuthor($data['author']);
        $document->setContent($data['document']);
        $currentDate = date('Y-m-d H:i:s');
        $document->setDateCreated($currentDate);

        // Add the entity to entity manager.
        $this->entityManager->persist($document);

        // Apply changes.
        $this->entityManager->flush();
    }

    /**
     * Removes certificate and all associated documents.
     */
    public function removeCertificate($certificate)
    {
        // Remove associated documents
        $documents = $certificate->getDocuments();
        foreach ($documents as $document)
        {
            $this->entityManager->remove($document);
        }

        // Remove market associations (if any)
        $markets = $certificate->getMarkets();
        foreach ($markets as $market)
        {

            $certificate->removeMarketAssociation($market);
        }

        $this->entityManager->remove($certificate);

        $this->entityManager->flush();
    }

    /**
     * Calculates frequencies of market usage.
     */
    public function getMarketCloud()
    {
        $marketCloud = [];
        $certificates = $this->entityManager->getRepository(Certificate::class)
                ->findAllCertificates();
        $totalCertificateCount = count($certificates);

        $markets = $this->entityManager->getRepository(Market::class)->findAll();
        foreach ($markets as $market)
        {
            $certificatesByMarket = $this->entityManager->getRepository(Certificate::class)
                            ->findCertificatesByMarket($market->getName())->getResult();
            $certificateCount = count($certificatesByMarket);
            if ($certificateCount > 0)
            {
                $marketCloud[$market->getName()] = $certificateCount;
            }
        }

        $normalizedMarketCloud = [];

        // Normalize
        foreach ($marketCloud as $name => $certificateCount)
        {
            $normalizedMarketCloud[$name] = $certificateCount / $totalCertificateCount;
        }

        return $normalizedMarketCloud;
    }

}
