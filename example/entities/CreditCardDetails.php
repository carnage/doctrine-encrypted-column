<?php

namespace Carnage\DceTest;

class CreditCardDetails
{
    private $number;
    private $expiry;

    /**
     * CreditCardDetails constructor.
     * @param $number
     * @param $expiry
     */
    public function __construct($number, $expiry)
    {
        $this->number = $number;
        $this->expiry = $expiry;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getExpiry()
    {
        return $this->expiry;
    }
}