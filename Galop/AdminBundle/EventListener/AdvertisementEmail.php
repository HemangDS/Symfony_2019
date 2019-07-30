<?php
namespace Galop\AdminBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Galop\AdminBundle\Entity\Advertisement;
use Galop\AdminBundle\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Swift_Attachment;
use Symfony\Component\HttpFoundation\RequestStack;

class AdvertisementEmail
{
    protected $twig;
    protected $mailer;
    protected $tokenStorage;
    protected $container;

    public function __construct(TokenStorage $tokenStorage, \Swift_Mailer $mailer, \Twig_Environment $twig, RequestStack $request, ContainerInterface $container)
    {
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->request = $request;
        $this->container = $container;
    }

    public function prePersist(Advertisement $advertisement, LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        

        $attachmentArray = array();
       
        if ($entity instanceof Advertisement) {
            $title = $entity->getTitle();
            $startdate = $entity->getStartdate()->format('Y-m-d');
            $enddate = $entity->getEnddate()->format('Y-m-d');
            $zone = $entity->getZone();
            $user =  $this->tokenStorage->getToken()->getUser();
            $updatedByUser = $user;
            $Useremail = $updatedByUser->getEmail();
            $firstname = $updatedByUser->getFirstname();
            $lastname = $updatedByUser->getLastname();
            
            /*images*/
            $getEngDesktopAdd = $entity->getEngDesktopAdd();
            $attachmentArray[] = $this->getAttachmentPath($getEngDesktopAdd);
            $getEngMobileAdd = $entity->getEngMobileAdd();
            $attachmentArray[] = $this->getAttachmentPath($getEngMobileAdd);
            $getEngTabletAdd = $entity->getEngTabletAdd();
            $attachmentArray[] = $this->getAttachmentPath($getEngTabletAdd);

            $getDutchDesktopAdd = $entity->getDutchDesktopAdd();
            $attachmentArray[] = $this->getAttachmentPath($getDutchDesktopAdd);
            $getDutchMobileAdd = $entity->getDutchMobileAdd();
            $attachmentArray[] = $this->getAttachmentPath($getDutchMobileAdd);
            $getDutchTabletAdd = $entity->getDutchTabletAdd();
            $attachmentArray[] = $this->getAttachmentPath($getDutchTabletAdd);

            $getFrenchDesktopAdd = $entity->getFrenchDesktopAdd();
            $attachmentArray[] = $this->getAttachmentPath($getFrenchDesktopAdd);
            $getFrenchMobileAdd = $entity->getFrenchMobileAdd();
            $attachmentArray[] = $this->getAttachmentPath($getFrenchMobileAdd);
            $getFrenchTabletAdd = $entity->getFrenchTabletAdd();
            $attachmentArray[] = $this->getAttachmentPath($getFrenchTabletAdd);

            $attachmentArray = array_filter($attachmentArray);
            
            $message = (new \Swift_Message('Hello Email'))
            ->setSubject('Add new advertisement from : '. $firstname .' '. $lastname)
            ->setFrom($Useremail)
            ->setTo('masum.patel@iflair.com')
            ->setBody(
                $this->twig->render(
                   'advertisement.html.twig',
                    ['name' => $title ,'startdate' => $startdate,'enddate' => $enddate,'zone' => $zone]
                ),
                'text/html'
            );
            
            if(!empty($attachmentArray)) {
                foreach ($attachmentArray as $attachment) {
                    $message->attach(Swift_Attachment::fromPath($attachment));
                }
            }
            $this->mailer->send($message);
        }
    }

    public function preUpdate(Advertisement $advertisement, PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $attachmentArray = array();
       
        if ($entity instanceof Advertisement) {
            $title = $entity->getTitle();
            $startdate = $entity->getStartdate()->format('Y-m-d');
            $enddate = $entity->getEnddate()->format('Y-m-d');
            $zone = $entity->getZone();
            $updatedByUser = $entity->getUpdatedByUser();
            $Useremail = $updatedByUser->getEmail();
            $firstname = $updatedByUser->getFirstname();
            $lastname = $updatedByUser->getLastname();
            
            /*images*/
            $getEngDesktopAdd = $entity->getEngDesktopAdd();
            $attachmentArray[] = $this->getAttachmentPath($getEngDesktopAdd);
            $getEngMobileAdd = $entity->getEngMobileAdd();
            $attachmentArray[] = $this->getAttachmentPath($getEngMobileAdd);
            $getEngTabletAdd = $entity->getEngTabletAdd();
            $attachmentArray[] = $this->getAttachmentPath($getEngTabletAdd);

            $getDutchDesktopAdd = $entity->getDutchDesktopAdd();
            $attachmentArray[] = $this->getAttachmentPath($getDutchDesktopAdd);
            $getDutchMobileAdd = $entity->getDutchMobileAdd();
            $attachmentArray[] = $this->getAttachmentPath($getDutchMobileAdd);
            $getDutchTabletAdd = $entity->getDutchTabletAdd();
            $attachmentArray[] = $this->getAttachmentPath($getDutchTabletAdd);

            $getFrenchDesktopAdd = $entity->getFrenchDesktopAdd();
            $attachmentArray[] = $this->getAttachmentPath($getFrenchDesktopAdd);
            $getFrenchMobileAdd = $entity->getFrenchMobileAdd();
            $attachmentArray[] = $this->getAttachmentPath($getFrenchMobileAdd);
            $getFrenchTabletAdd = $entity->getFrenchTabletAdd();
            $attachmentArray[] = $this->getAttachmentPath($getFrenchTabletAdd);

            $attachmentArray = array_filter($attachmentArray);

            $message = (new \Swift_Message('Hello Email'))
            ->setSubject('Edit advertisement from : '. $firstname .' '. $lastname)
            ->setFrom($Useremail)
            ->setTo('masum.patel@iflair.com')
            ->setBody(
                $this->twig->render(
                   'advertisement.html.twig',
                    ['name' => $title ,'startdate' => $startdate,'enddate' => $enddate,'zone' => $zone]
                ),
                'text/html'
            );
            
            if(!empty($attachmentArray)) {
                foreach ($attachmentArray as $attachment) {
                    $message->attach(Swift_Attachment::fromPath($attachment));
                }
            }
            $this->mailer->send($message);
        }
        // fetch the mailer service this way
        // $this->get('mailer')->send($message);
        //return $this->render('GalopAdminBundle:Default:index.html.twig');
    }

    protected function getAttachmentPath($media)
    {
        if(isset($media) && !empty($media)) {
            $appPath = $this->container->getParameter('kernel.root_dir');
            $webPath = realpath($appPath . '/../web/uploads/media');
            $provider = $this->container->get($media->getProviderName());

            return $webPath."/".$provider->getReferenceImage($media);
        }

        return "";
    }
}
?>