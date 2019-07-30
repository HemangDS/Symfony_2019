<?php
namespace Galop\AdminBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AdminBundle\Entity\Event;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class EventUserUpdate
{
	protected $tokenStorage;

	public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

	public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Events) {
        	$user =  $this->tokenStorage->getToken()->getUser();
        	$entity->setcreatedByUser($user);
            $entity->setUpdatedByUser($user);
            
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
    	$entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Events) {
        	$user =  $this->tokenStorage->getToken()->getUser();
            $entity->setUpdatedByUser($user);
        }
    }
}
?>