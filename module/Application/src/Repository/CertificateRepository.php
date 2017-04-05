<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Certificate;

/**
 * This is the custom repository class for Certificate entity.
 */
class CertificateRepository extends EntityRepository
{

    /**
     * Retrieves all published certificates in descending date order.
     * @return Query
     */
    public function findAllCertificates()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('crt')
                ->from(Certificate::class, 'crt');
        return $queryBuilder->getQuery();
    }

    /**
     * Finds all published certificates having the given tag.
     * @param string $tagName Name of the tag.
     * @return Query
     */
    public function findCertificatesByMarket($marketName)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('crt')
                ->from(Certificate::class, 'crt')
                ->join('crt.markets', 'm')
                ->where('m.name = ?2')
                ->setParameter('2', $marketName);
        return $queryBuilder->getQuery();
    }
    
    
    /**
     * Retrieves certificate by it's ISIN.
     * @return Query
     */
    public function findCertificateByIsin($isin)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('crt')
                ->from(Certificate::class, 'crt')
                ->where('crt.isin = ?1')
                ->setParameter('1', $isin);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
    
    
    

}
