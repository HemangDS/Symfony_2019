<?php
namespace Galop\AdminBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AdminBundle\Entity\Advertisement;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AdvertisementUserUpdater
{
	protected $tokenStorage;

	public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

	public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Advertisement) {
        	$user =  $this->tokenStorage->getToken()->getUser();
        	$entity->setUserid($user);
            if(!is_string($user)) {
                $entity->setUpdatedByUser($user);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
    	$entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Advertisement) {
        	$user =  $this->tokenStorage->getToken()->getUser();
            
        	if(!is_string($user)) {
                $entity->setUpdatedByUser($user);
            }
        }
    }
}
?>