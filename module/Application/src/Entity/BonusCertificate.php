<?php

namespace Application\Entity;

class BonusCertificate extends Certificate
{

    public function isBarrierHit()
    {
        return $this->current_price >= $this->barrier_level;
    }

    public function toArray()
    {
        $toArray = parent::toArray();
        $toArray['Barrier Level'] = $this->barrier_level;
        $toArray['Barrier Hit?'] = $this->isBarrierHit() ? 'YES' : 'NO';
        return $toArray;
    }

}
