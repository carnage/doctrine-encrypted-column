<?php
require "./entities/Entity.php";
require "./entities/CreditCardDetails.php";
require "./bootstrap.php";

$entity = $entityManager->find(\Carnage\DceTest\Entity::class, 1);

//var_dump($entity);

echo $entity->getCreditCardDetails()->getNumber();
