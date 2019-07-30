<?php

namespace iFlair\LetsBonusAdminBundle\Auth;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
//use iFlair\LetsBonusAdminBundle\Entity\User;
use Application\Sonata\UserBundle\Entity\User;

class OAuthProvider extends OAuthUserProvider
{
    protected $session, $doctrine, $admins;

    public function __construct($session, $doctrine, $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    public function loadUserByUsername($email)
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('ApplicationSonataUserBundle:User', 'u')
            ->where('u.email = :getEmail')
            ->setParameter('getEmail', $email)
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

        if (count($result)) {
            return $result[0];
        } else {
            return new User();
        }
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $em = $this->doctrine->getManager();

        $this->session->set('email', $response->getEmail());

        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('ApplicationSonataUserBundle:User', 'u')
            ->where('u.email = :gEmail')
            ->setParameter('gEmail', $response->getEmail())
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

        if (!count($result)) {
            $user = new User();
            $user->setUsername($response->getRealName());
            $user->setUsernameCanonical($response->getNickname());
            $user->setEmail($response->getEmail());
            $user->setEmailCanonical($response->getEmail());
            $user->setEnabled(1);
            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword(md5(uniqid()), $user->getSalt());
            $user->setPassword($password);
            $user->setExpiresAt(new \DateTime(date('Y-m-d h:i:s')));
            $user->setConfirmationToken($response->getAccessToken());
            //$user->setGoogleId($response->getUsername());
            $em->persist($user);
            $em->flush();
        } else {
            $user = $result[0];
        }

        $this->session->set('id', $user->getId());

        return $this->loadUserByUsername($response->getEmail());
    }

    public function supportsClass($class)
    {
        return $class === 'Application\\Sonata\\UserBundle\\Entity\\User';
    }
}
