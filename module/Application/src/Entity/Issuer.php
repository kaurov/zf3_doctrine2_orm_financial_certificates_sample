<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a issuer.
 * @ORM\Entity
 * @ORM\Table(name="issuer")
 */
class Issuer
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="title") 
     */
    protected $title;

    /**
     * @ORM\OneToMany(
     *     targetEntity="\Application\Entity\Certificate",
     *     mappedBy="id_certificate"
     * )
     */
    private $certificate;

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
        return $this->title;
    }

    /**
     * Sets name.
     * @param string $name
     */
    public function setName($name)
    {
        $this->title = $name;
    }

}
