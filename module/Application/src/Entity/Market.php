<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class represents a market.
 * @ORM\Entity
 * @ORM\Table(name="market")
 */
class Market 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="name") 
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Certificate", mappedBy="market")
     */
    protected $certificates;
    
    /**
     * Constructor.
     */
    public function __construct() 
    {        
        $this->certificates = new ArrayCollection();        
    }

    /**
     * Returns ID of this market.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets ID of this market.
     * @param int $id
     */
    public function setId($id) 
    {
        $this->id = $id;
    }

    /**
     * Returns name.
     * @return string
     */
    public function getName() 
    {
        return $this->name;
    }

    /**
     * Sets name.
     * @param string $name
     */
    public function setName($name) 
    {
        $this->name = $name;
    }
    
    /**
     * Returns certificates which have this market.
     * @return type
     */
    public function getCertificates() 
    {
        return $this->certificates;
    }
    
    /**
     * Adds a certificate which has this market.
     * @param type $certificate
     */
    public function addCertificate($certificate) 
    {
        $this->certificates[] = $certificate;        
    }
    
    
}

