<?php

namespace Carnage\EncryptedColumn\TestFixtures;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Carnage\EncryptedColumn\TestFixtures
 * @ORM\Entity()
 */
class Entity 
{
    /**
     * @var integer
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var CreditCardDetails
     * @ORM\Column(type="encrypted")
     */
    protected $creditCardDetails;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return CreditCardDetails
     */
    public function getCreditCardDetails()
    {
        return $this->creditCardDetails;
    }

    /**
     * @param CreditCardDetails $creditCardDetails
     */
    public function setCreditCardDetails($creditCardDetails)
    {
        $this->creditCardDetails = $creditCardDetails;
    }
}