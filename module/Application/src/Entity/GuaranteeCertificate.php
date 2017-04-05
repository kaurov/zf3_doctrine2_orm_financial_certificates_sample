<?php

namespace Application\Entity;

/**
 * @property $participation_rate
 */
class GuaranteeCertificate extends Certificate
{

    public function buildXml(\SimpleXMLElement $node)
    {
        throw new \RuntimeException('XML Export is forbiden for Guarantee Certificate');
    }

    public function toArray()
    {
        $toArray = parent::toArray();
        $toArray['Participation Rate'] = $this->participation_rate;
        return $toArray;
    }

}
