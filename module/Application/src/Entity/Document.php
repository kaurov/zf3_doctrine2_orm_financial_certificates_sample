<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a document related to a certificate.
 * @ORM\Entity
 * @ORM\Table(name="document")
 */
class Document
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Certificate", inversedBy="id")
     * @ORM\JoinColumn(name="id_certificate", referencedColumnName="id")
     */
    protected $certificate;

    /**
     * @ORM\Column(name="type")  
     */
    protected $type;

    /**
     * Title of the document to be displayed to the customer
     * @ORM\Column(name="filename")  
     */
    protected $filename;

    /**
     * @ORM\Column(name="url")  
     */
    protected $url;

    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;

    /**
     * Returns ID of this document.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID of this document.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns document text.
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets document text.
     * @param string $document
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Returns document's type.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets document's type.
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns document's URL.
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets document's URL.
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns the date when this certificate was created.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets the date when this certificate was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = (string) $dateCreated;
    }

    /*
     * Returns associated certificate.
     * @return \Application\Entity\Certificate
     */

    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * Sets associated certificate.
     * @param \Application\Entity\Certificate $certificate
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
        $certificate->addDocument($this);
    }
    
    

}
