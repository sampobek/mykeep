<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Note;
use AppBundle\Entity\NoteHistory;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NoteListener
{
    private $container;

    private $noteHistories = [];

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Note) {
            return;
        }

        $noteHistory = new NoteHistory();
        $noteHistory->setTitle($entity->getTitle());
        $noteHistory->setContent($entity->getContent());
        $noteHistory->setNote($entity);
        $noteHistory->setCreatedAt($entity->getCreatedAt());
        $this->noteHistories[] = $noteHistory;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Note) {
            return;
        }

        if ($args->hasChangedField('title') || $args->hasChangedField('content')) {
            $entity->setUpdatedAt(new \DateTime());

            $noteHistory = new NoteHistory();
            $noteHistory->setTitle($entity->getTitle());
            $noteHistory->setContent($entity->getContent());
            $noteHistory->setNote($entity);
            $noteHistory->setCreatedAt($entity->getUpdatedAt());
            $this->noteHistories[] = $noteHistory;
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $em = $args->getEntityManager();

        if (!empty($this->noteHistories)) {
            foreach ($this->noteHistories as $noteHistory) {
                if ($noteHistory instanceof NoteHistory) {
                    $em->persist($noteHistory);
                }
            }
            $this->noteHistories = [];
            $em->flush();
        }

    }
}