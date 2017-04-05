<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/*


  use Nfx\Display\XmlExportableInterface;
  use Nfx\Display\ExportableInterface;
  use SimpleXMLElement;

  class Certificate extends Row implements XmlExportableInterface, ExportableInterface
 */

/**
 * This class represents a single certificate in a blog.
 * @ORM\Entity(repositoryClass="\Application\Repository\CertificateRepository")
 * @ORM\Table(name="certificate")
 */
class Certificate
{

    protected $priceHistory;

    const STANDARD = 1;
    const GUARANTEE = 2;
    const BONUS = 3;

    static public $typeEnum = array
        (
        self::STANDARD => 'Standard certificate',
        self::GUARANTEE => 'Guarantee certificate',
        self::BONUS => 'Bonus certificate',
    );
    static public $currencyEnum = array
        (
        36 => 'AUD',
        764 => 'THB',
        933 => 'BYN',
        975 => 'BGN',
        410 => 'KRW',
        344 => 'HKD',
        208 => 'DKK',
        840 => 'USD',
        978 => 'EUR',
        818 => 'EGP',
        392 => 'JPY',
        985 => 'PLN',
        356 => 'INR',
        364 => 'IRR',
        124 => 'CAD',
        191 => 'HRK',
        484 => 'MXN',
        498 => 'MDL',
        376 => 'ILS',
        554 => 'NZD',
        578 => 'NOK',
        643 => 'RUB',
        946 => 'RON',
        360 => 'IDR',
        702 => 'SGD',
        960 => 'XDR',
        398 => 'KZT',
        949 => 'TRY',
        348 => 'HUF',
        826 => 'GBP',
        203 => 'CZK',
        752 => 'SEK',
        756 => 'CHF',
        156 => 'CNY'
    );

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="isin")
     * @ORM\Column(type="string", nullable=false)
     */
    protected $isin;

    /**
     * @ORM\Column(name="title")  
     */
    protected $title;

    /**
     * @ORM\Column(name="type")  
     */
    protected $type;

    /**
     * @ORM\Column(name="id_currency")  
     */
    private $id_currency;

    /**
     * @ORM\Column(name="price_issuing")  
     */
    private $price_issuing;

    /**
     * @ORM\Column(name="price_current")  
     */
    private $price_current;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="\Application\Entity\Issuer",
     *     inversedBy="Certificate"
     * )
     * @ORM\JoinColumn(
     *     name="id_issuer",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    protected $issuer;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Document", mappedBy="certificate")
     * @ORM\JoinColumn(name="id", referencedColumnName="id_certificate")
     */
    protected $documents;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Market", inversedBy="certificate")
     * @ORM\JoinTable(name="certificate_market",
     *      joinColumns={@ORM\JoinColumn(name="id_certificate", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_market", referencedColumnName="id")}
     *      )
     */
    protected $markets;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->priceHistory = new ArrayCollection();
        $this->markets = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * Returns ISIN of this certificate.
     * @return String
     */
    public function getIsin()
    {
        return $this->isin;
    }

    /**
     * Sets ISIN of this certificate.
     * @param String $isin
     */
    public function setIsin($isin)
    {
        $this->isin = $isin;
    }

    /**
     * Returns ID of this certificate.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID of this certificate in the database.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns title.
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets title.
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns markets for this certificate.
     * @return array
     */
    public function getMarkets()
    {
        return $this->markets;
    }

    /**
     * Adds a new market to this certificate.
     * @param $market
     */
    public function addMarket($market)
    {
        $this->markets[] = $market;
    }

    /**
     * Removes association between this certificate and the given market.
     * @param type $market
     */
    public function removeMarketAssociation($market)
    {
        $this->markets->removeElement($market);
    }

    public function getPrice()
    {
        return $this->price_current . ' ' . self::$currencyEnum[$this->id_currency];
    }

    public function getPriceIssuing()
    {
        return $this->price_issuing . ' ' . self::$currencyEnum[$this->id_currency];
    }

    public function setPriceIssuing($price_issuing, $id_currency)
    {
        $this->price_issuing = $price_issuing;
        $this->price_current = $price_issuing;
        $this->id_currency = $id_currency;
    }

    

    public function toArray()
    {
        return array(
            'Issuer' => $this->issuer,
            'ISIN' => $this->isin,
            'Title' => $this->title,
            'Type' => self::$typeEnum[$this->type],
            'Issuer Price' => $this->getPriceIssuing(),
            'Current Price' => $this->getPrice(),
            'Trading Market' => $this->trading_market,
        );
    }
    
    public function getPriceHistory()
    {
        return $this->priceHistory;
    }

    public function setPriceHistory($priceHistory)
    {
        $this->priceHistory = $priceHistory;
    }

    public function getDocuments()
    {
        return $this->documents;
    }

    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

}
