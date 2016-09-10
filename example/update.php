<?php
require "./entities/Entity.php";
require "./entities/CreditCardDetails.php";
require "./bootstrap.php";

$entity = $entityManager->find(\Carnage\DceTest\Entity::class, 1);
$entity->setCreditCardDetails(new \Carnage\DceTest\CreditCardDetails('000012340001234', '04/19'));

$entityManager->flush();