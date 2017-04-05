<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
//use Application\Data\CertificateRepository;
use Application\Entity\Certificate;

/**
 * This is the Certificate controller class of the Blog application. 
 * This controller is used for managing certificates (adding/editing/viewing/deleting).
 */
class CertificateController extends AbstractActionController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;

    /**
     * Certificate manager.
     * @var Application\Service\CertificateManager 
     */
    private $certificateManager;

    /**
     * @var CertificateRepository
     */
    private $certificateRepository;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $certificateManager)
    {
        $this->entityManager = $entityManager;
        $this->certificateManager = $certificateManager;
        $this->certificateRepository = $this->entityManager->getRepository(Certificate::class);
    }

    /**
     * This action displays the "View Certificate" page allowing to see the certificate title
     * and content. The page also contains a form allowing
     * to add a comment to certificate. 
     */
    public function indexAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        $marketFilter = $this->params()->fromQuery('market', null);

        if ($marketFilter)
        {
            // Filter certificates by market
            $query = $this->certificateRepository->findCertificatesByMarket($marketFilter);
        } else
        {
            // Get recent certificates
            $query = $this->certificateRepository->findAllCertificates();
        }
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        // Get popular markets.
        $marketCloud = null;
        $marketCloud = $this->certificateManager->getMarketCloud();
        // Render the view template.
        return new ViewModel([
            'certificates' => $paginator,
            'certificateManager' => $this->certificateManager,
            'marketCloud' => $marketCloud
        ]);
    }

    /**
     * This action displays the "View Certificate" page allowing to see the certificate title
     * and content. The page also contains a form allowing
     * to add a comment to certificate. 
     */
    public function viewAction()
    {
        $isin = $this->params()->fromRoute('id', -1);
        // Validate input parameter
        if (!$isin)
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        // Find the certificate by ID
        $certificate = $this->certificateRepository->findCertificateByIsin($isin);
        if ($certificate == null)
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Render the view template.
        return new ViewModel([
            'certificateArray' => $this->certificateManager->toArray($certificate),
            'certificate' => $certificate,
            'certificateManager' => $this->certificateManager
        ]);
    }

    public function xmlviewAction()
    {
        $isin = $this->params()->fromRoute('id', -1);
        // Validate input parameter
        if (!$isin)
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        // Find the certificate by ISIN
        $certificate = $this->certificateRepository->findCertificateByIsin($isin);
        if ($certificate == null)
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        try
        {
            $xml = new ViewModel([
                'certificateArray' => $this->certificateManager->toArray($certificate),
                'certificate' => $certificate,
                'certificateManager' => $this->certificateManager
            ]);
            // Disable layouts; `MvcEvent` will use this View Model instead
            $xml->setTerminal(true);
        } catch (\RuntimeException $e)
        {
            return $this->response->setStatusCode(405)->setContent($e->getMessage());
        }

        $headers = $this->getResponse()->getHeaders();
        $headers->addHeaderLine('Content-type', 'application/xml');
        //$this->getResponse()->setContent('Some content');
        //$this->getResponse()->setContent($xml);
        return $xml;
    }


}
