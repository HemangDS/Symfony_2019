<?php
namespace Galop\AdminBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class GlobalUserUpdater
{
	protected $tokenStorage;
    private $kernel;

	public function __construct(TokenStorage $tokenStorage, KernelInterface $kernel)
    {
        $this->tokenStorage = $tokenStorage;
        $this->kernel = $kernel;
    }

	public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\News) {
            $user =  $this->tokenStorage->getToken()->getUser();
            $entity->setCreatedByUser($user);
            $entity->setUpdatedByUser($user);
            $name = '';
            if(!empty($user->getFirstname()))
                $name.= $user->getFirstname();
            if(!empty($user->getLastname()))
                if(!empty($name))
                    $name.= " " .$user->getLastname();
                else
                    $name.= $user->getLastname();

            $entity->setAuthor($name);
        }
        
        if ($entity instanceof \Galop\AdminBundle\Entity\Pages) {
            $user =  $this->tokenStorage->getToken()->getUser();
            $entity->setCreatedByUser($user);
            $entity->setUpdatedByUser($user);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
    	$entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\News) {
        	$user =  $this->tokenStorage->getToken()->getUser();
            $entity->setUpdatedByUser($user);
        }

        if ($entity instanceof \Galop\AdminBundle\Entity\Pages) {
            $user =  $this->tokenStorage->getToken()->getUser();
            $entity->setUpdatedByUser($user);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Pages) {
            $this->clearCache();
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Pages) {
            $this->clearCache();
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Galop\AdminBundle\Entity\Pages) {
            $this->clearCache();
        }
    }

    public function clearCache()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();
        $input = new ArrayInput(array(
            'command' => 'cache:clear',
            '--env' => "prod",
            '--no-warmup' => true
        ));

        $application->run($input, $output);
        $content = $output->fetch();
    }
}
?>