<?php

namespace Carnage\EncryptedColumn\Listener;

use Carnage\EncryptedColumn\Dbal\EncryptedColumn;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class MetadataListener implements EventSubscriber
{
    private $listener;
    private $registered = false;

    /**
     * MetadataListener constructor.
     * @param $listener
     */
    public function __construct(EncryptListener $listener)
    {
        $this->listener = $listener;
    }

    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadataInfo $classMetadata */
        $classMetadata = $eventArgs->getClassMetadata();
        foreach ($classMetadata->getFieldNames() as $field) {
            if ($classMetadata->getTypeOfField($field) === EncryptedColumn::ENCRYPTED) { //might be a type instance ?
                $classMetadata->addEntityListener(Events::postLoad, EncryptListener::class, 'postLoad');
                $classMetadata->addEntityListener(Events::prePersist, EncryptListener::class, 'prePersist');
                $classMetadata->addEntityListener(Events::preUpdate, EncryptListener::class, 'preUpdate');
                
                break;
            }
        }

        if (!$this->registered) {
            $em = $eventArgs->getEntityManager();
            $em->getConfiguration()->getEntityListenerResolver()->register($this->listener);
            $this->registered = true;
        }
    }
}