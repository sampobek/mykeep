<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Note;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NoteListener
{
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Note) {
            return;
        }

        if ($args->hasChangedField('title') || $args->hasChangedField('content')) {
            $entity->setUpdatedAt(new \DateTime());
        }

    }
}