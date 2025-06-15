<?php

namespace App\EventListener;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Notification;
use App\Message\NotificationMessage;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class EntityChangeListener
{
    private ?MessageBusInterface $bus = null;

    public function setBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    public function postPersist($entity, PostPersistEventArgs $event): void
    {
        if ($entity instanceof Produit && $this->bus) {
            $this->bus->dispatch(new NotificationMessage(
                'CREATION',
                'Produit: ' . $entity->getNom(),
                $entity->getId()
            ));
        }
    }

    public function postUpdate($entity, PostUpdateEventArgs $event): void
    {
        if ($entity instanceof Produit && $this->bus) {
            $this->bus->dispatch(new NotificationMessage(
                'MODIFICATION',
                'Produit: ' . $entity->getNom(),
                $entity->getId()
            ));
        }
        elseif ($entity instanceof User) {
            $this->postUpdateUser($entity, $event);
        }
    }

    public function postRemove($entity, PostRemoveEventArgs $event): void
    {
        if ($entity instanceof Produit && $this->bus) {
            $this->bus->dispatch(new NotificationMessage(
                'SUPPRESSION',
                'Produit: ' . $entity->getNom(),
                $entity->getId()
            ));
        }
    }

    private function postUpdateUser(User $user, PostUpdateEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $uow = $entityManager->getUnitOfWork();
        $changeset = $uow->getEntityChangeSet($user);

        if (isset($changeset['actif'])) {
            [$oldValue, $newValue] = $changeset['actif'];
            
            if ($oldValue === true && $newValue === false) {
                $notification = new Notification();
                $notification->setLabel('Votre compte a été désactivé - ' . (new \DateTime())->format('d/m/Y H:i:s'));
                $notification->setUser($user);
                
                $entityManager->persist($notification);
                $entityManager->flush();
            }
        }
    }
}