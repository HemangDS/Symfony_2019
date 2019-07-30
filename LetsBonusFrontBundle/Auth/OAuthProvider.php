<?php

namespace iFlair\LetsBonusFrontBundle\Auth;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;

class OAuthProvider extends OAuthUserProvider
{
    protected $session, $doctrine, $admins;

    public function __construct($session, $doctrine, $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    public function loadUserByUsername($username)
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('iFlairLetsBonusAdminBundle:FrontUser', 'u')
            ->where('u.googleId = :gid')
            ->setParameter('gid', $username)
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

        $this->session->set('user_email', $response->getEmail());
        $this->session->set('user_name', $response->getRealName());
        $this->session->set('user_id', $response->getUsername());

        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('iFlairLetsBonusAdminBundle:FrontUser', 'u')
            ->where('u.googleId = :gid')
            ->setParameter('gid', $response->getUsername())
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

       /* print_r($result);
        exit();*/
        if (!count($result)) {
            $user = new FrontUser();
            $user->setName($response->getRealName());
            $user->setSurname($response->getRealName());
            $user->setEmail($response->getEmail());
            $user->setPassword(md5(uniqid()));
            $user->setEnabled(1);
            $user->setUserGender(1);
          /*  $user->setUserBirthDate(new \DateTime());
            $user->setCreated(new \DateTime());
            $user->setModified(new \DateTime());*/
            $user->setCity(0000);
            $user->setLoginType(2);
            $user->setFacebookId($response->getUsername());
            $user->setGoogleId(0);
            $em->persist($user);
            $em->flush();
        } else {
            $user = $result[0];
        }

        $this->session->set('user_id', $user->getId());

        return $this->loadUserByUsername($response->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'iFlair\\LetsBonusAdminBundle\\Entity\\FrontUser';
    }
}
