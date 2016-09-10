<?php
require "./entities/Entity.php";
require "./entities/CreditCardDetails.php";
require "./bootstrap.php";

$entity = new \Carnage\DceTest\Entity();
$entity->setCreditCardDetails(new \Carnage\DceTest\CreditCardDetails('1234567812345678', '04/19'));

$entityManager->persist($entity);
$entityManager->flush();