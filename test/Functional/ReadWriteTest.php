<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 03/11/16
 * Time: 16:12
 */

namespace Carnage\EncryptedColumn\Tests;

use Carnage\EncryptedColumn\Configuration;
use Carnage\EncryptedColumn\Setup as ECSetup;
use Carnage\EncryptedColumn\Tests\Functional\Fixtures\CreditCardDetails;
use Carnage\EncryptedColumn\Tests\Functional\Fixtures\Entity;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Class ReadWriteTest
 * @package Carnage\EncryptedColumn\Tests
 * @runTestsInSeparateProcesses
 */
class ReadWriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private static $_em;

    /**
     * @var EntityManager
     */
    private $em;

    public function setUp()
    {
        if (self::$_em === null) {
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Fixtures"), $isDevMode, null, null, false);

            $conn = array(
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
            );

            self::$_em = EntityManager::create($conn, $config);

            (new ECSetup())
                ->withKeyPath( __DIR__ . '/Fixtures/enc.key')
                ->register(self::$_em);

            $schemaTool = new SchemaTool(self::$_em);

            $classes = [
                self::$_em->getClassMetadata(Entity::class)
            ];

            $schemaTool->createSchema($classes);
        }

        $this->em = self::$_em;
    }

    public function testInsert()
    {
        $entity = new Entity();
        $entity->setCreditCardDetails(new CreditCardDetails('1234567812345678', '04/19'));

        $this->em->persist($entity);
        $this->em->flush();

        $data = $this->em->getConnection()->fetchAll('SELECT * FROM Entity');
        $savedData = json_decode($data[0]['creditCardDetails']);

        $this->assertObjectHasAttribute('classname', $savedData);
        $this->assertObjectHasAttribute('data', $savedData);
    }

    public function testRead()
    {
        $entity = new Entity();
        $creditCardDetails = new CreditCardDetails('1234567812345678', '04/19');
        $entity->setCreditCardDetails($creditCardDetails);

        $this->em->persist($entity);
        $this->em->flush();

        $this->em->clear();

        $entity = $this->em->find(Entity::class, 1);

        $this->assertEquals($creditCardDetails->getNumber(), $entity->getCreditCardDetails()->getNumber());
        $this->assertEquals($creditCardDetails->getExpiry(), $entity->getCreditCardDetails()->getExpiry());
    }

    public function testUpdateUnrelated()
    {
        $entity = new Entity();
        $entity->setCreditCardDetails(new CreditCardDetails('1234567812345678', '04/19'));

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $data = $this->em->getConnection()->fetchAll('SELECT * FROM Entity');
        $savedData = json_decode($data[0]['creditCardDetails']);

        $entity = $this->em->find(Entity::class, 1);
        $entity->setUnrelated('unrelated');
        $this->em->flush($entity);

        $data = $this->em->getConnection()->fetchAll('SELECT * FROM Entity');

        $this->assertEquals($savedData, json_decode($data[0]['creditCardDetails']));
    }

    public function testUpdate()
    {
        $entity = new Entity();
        $entity->setCreditCardDetails(new CreditCardDetails('1234567812345678', '04/19'));

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $data = $this->em->getConnection()->fetchAll('SELECT * FROM Entity');
        $savedData = json_decode($data[0]['creditCardDetails']);

        $entity = $this->em->find(Entity::class, 1);
        $entity->setCreditCardDetails(new CreditCardDetails('1234567812345678', '04/19'));
        $this->em->flush($entity);

        $data = $this->em->getConnection()->fetchAll('SELECT * FROM Entity');

        $this->assertNotEquals($savedData, json_decode($data[0]['creditCardDetails']));
    }
}