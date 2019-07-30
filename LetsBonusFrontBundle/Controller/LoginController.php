<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Facebook;
use FOS\UserBundle\Model\UserInterface;
use Google_Client;
use Google_Service_Oauth2;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusFrontBundle\Form\LoginType;
use iFlair\LetsBonusFrontBundle\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class LoginController extends Controller
{

    /**
     * Show login page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLoginPageAction()
    {
        $form = $this->createForm(new RegistrationType());
        $login_form = $this->createForm(new LoginType());

        return $this->render(
            'iFlairLetsBonusFrontBundle:Homepage:login-page.html.twig',
            ['form' => $form->createView(), 'login_form' => $login_form->createView()]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function loginAction(Request $request)
    {
        $sm = $this->get('doctrine.orm.default_entity_manager');
        $data = $request->request->all();
        if ($request->get('email') !== null && $request->get('password') !== null) {
            /** @var FrontUser $user */
            $user = $sm->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(
                ['email' => $request->get('email'), 'loginType' => 1]
            );
            if (null !== $user) {
                if ($user->isToImport()) {
                    $this->sendEmailRecoverPassword($user, $request);

                    return $this->redirect($this->generateUrl('i_flair_lets_bonus_front_homepage'));
                } elseif ($user->getIsShoppiday() === 1 || ($user->getIsShoppiday() === 0 && $user->getApiFlag() === 1)
                ) {
                    $encoder = new MessageDigestPasswordEncoder();
                    if ($encoder->isPasswordValid($user->getPassword(), $data['password'], $user->getSalt())) {
                        $this->initSession($request, $user);
                        $url = $request->headers->get('referer');
                        if ($url === $this->generateUrl('i_flair_lets_bonus_front_login',[], UrlGeneratorInterface::ABSOLUTE_URL)
                            || preg_match('!resetear-contrase!i',$url)
                            || preg_match('!recuperar-contrase!i',$url)
                        ) {
                            $url = $this->generateUrl('i_flair_lets_bonus_front_homepage');
                        }

                        return $this->redirect($url);
                    }
                } else {
                    return $this->logoutAction($request);
                }
            }
        }
        $request->getSession()
            ->getFlashBag()
            ->add('notice', 'El email y/o la contraseña no son correctos');

        return $this->redirect($this->generateUrl('i_flair_lets_bonus_front_homepage'));
    }

    /**
     * @param UserInterface|FrontUser $user
     *
     * @param Request                 $request
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function sendEmailRecoverPassword(UserInterface $user, Request $request)
    {
        $user->setConfirmationToken($this->get('fos_user.util.token_generator')->generateToken());
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($user);
        $em->flush();
        $message = \Swift_Message::newInstance()
            ->setSubject('Shoppiday - Cambia tu contraseña')
            ->setFrom($this->container->getParameter('from_send_email_id'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'iFlairLetsBonusFrontBundle:Email:forgot_password_mail.html.twig',
                    [
                        'name' => $user->getUserFullName(),
                        'email' => $user->getEmail(),
                        'token' => $user->getConfirmationToken(),
                    ]
                ),
                'text/html'
            );


        $this->get('mailer')->send($message);
    }

    /**
     * Method that register a new user from the register form on the website.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function registerAction(Request $request)
    {
        $encoder = new MessageDigestPasswordEncoder();
        $em = $this->get('doctrine.orm.default_entity_manager');
        $previous_url = $request->headers->get('referer');
        $data = $request->request->all();
        if ($request->getMethod() === 'POST') {
            $user = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(
                ['email' => $data['email'], 'loginType' => 1]
            );
            if (null === $user) {
                $user = new FrontUser();
                $userbirthdate = $data['userBirthDate']['year'].'-'.$data['userBirthDate']['month'].'-'.$data['userBirthDate']['day'];
                $password = $encoder->encodePassword($data['password']['first'], $user->getSalt());
                $user->setName($data['name']);
                $user->setSurname($data['surname']);
                $user->setEmail($data['email']);
                $user->setUsername($data['email']);
                $user->setPassword($password);
                $user->setUserBirthDate(new \DateTime($userbirthdate));
                $user->setUserGender($data['userGender']);
                $user->setCity($data['city']);
                $user->setIsShoppiday(1);
                $user->setApiFlag(0);
                $user->setLoginType(1);
                $user->setFacebookId(0);
                $user->setGoogleId(0);
                $user->addRole(FrontUser::ROLE_DEFAULT);
                $user->setCreated(new \Datetime());
                $user->setModified(new \Datetime());
                $user->setUserCreateDate(new \Datetime());
                $user->setConfirmationToken($this->get('fos_user.util.token_generator')->generateToken());
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Confirma tu cuenta y empieza a ahorrar')
                    ->setFrom($this->container->getParameter('from_send_email_id'))
                    ->setTo($data['email'])
                    ->setBody(
                        $this->renderView(
                            'iFlairLetsBonusFrontBundle:Email:registere_success.html.twig',
                            [
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'gender' => $data['userGender'],
                                'token' => $user->getConfirmationToken(),
                            ]
                        ),
                        'text/html'
                    );


                $this->get('mailer')->send($message);
                $request->getSession()
                    ->getFlashBag()
                    ->add(
                        'success',
                        '¡Bienvenido a Shoppiday! Confirma tu registro en el e-mail que te hemos enviado'
                    );
            } elseif ($user->isToImport()) {
                $this->sendEmailRecoverPassword($user, $request);
            } else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('notice', 'Este email ya ha sido registrado');
            }

            return $this->redirect($previous_url);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction(Request $request)
    {
        $previous_url = $request->headers->get('referer');
        $request->getSession()->clear();
        session_destroy();
        return $this->redirect($previous_url);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function googleLoginAction(Request $request)
    {
        $clientId = $this->container->getParameter('google_client_id');
        $clientSecret = $this->container->getParameter('google_client_secret');
        $redirectUri = $this->generateUrl(
            'i_flair_lets_bonus_front_login_google',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $session = $request->getSession();
        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->addScope('email');
        $client->addScope('profile');
        $service = new Google_Service_Oauth2($client);
        $authUrl = $client->createAuthUrl();
        if (null !== $session->get('access_token')) {
            $client->setAccessToken($session->get('access_token'));
        } else {
            $authUrl = $client->createAuthUrl();
        }

        if (null !== $request->get('code')) {
            $client->authenticate($_GET['code']);
            $session->set('access_token', $client->getAccessToken());
            $client->setAccessToken($session->get('access_token'));
            $userProfile = $service->userinfo->get();
            /** @var FrontUser $user */
            $user = $this->getDoctrine()
                ->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                ->findOneBy(['email' => $userProfile->getEmail()]);

            if (null === $user) {
                $user = $this->getDoctrine()
                    ->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                    ->findOneBy(['googleId' => $userProfile->getId()]);
            }

            if (null === $user) {
                $user = new FrontUser();
                $user->setEmail($userProfile->email);
                $user->setUsername($userProfile->email);
                $user->setName($userProfile->name);
                $user->setSurname($userProfile->familyName);
                $encoder = new MessageDigestPasswordEncoder();
                $password = $encoder->encodePassword(md5(uniqid('', true)), $user->getSalt());
                $user->setPassword($password);
                $user->setUserGender(1);
                $user->setCity('');
                $user->setLoginType(3);
                $user->setFacebookId(0);
                $user->setApiFlag(1);
                $user->setIsShoppiday(1);
                $user->setCreated(new \Datetime());
                $user->setUserCreateDate(new \Datetime());
                $gender = $userProfile->gender[0] === 'm' ? 0 : 1;
                $user->setUserGender($gender);
//                $user->setUserBirthDate(new \DateTime($userProfile->getBirthday()));
            }
            $user->setEnabled(1);
            $user->setGoogleId($userProfile->id);
            $user->setLastLogin(new \DateTime(date('Y-m-d H:i:s')));
            $user->setModified(new \Datetime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->initSession($request, $user);
            $session->set('login_type', '3');
            $authUrl = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return $this->redirect($authUrl);
    }

    public function tokenGenerateAction(Request $request)
    {
        $session = $request->getSession();
        $accessToken = $request->request->get('accessToken');
        $session->set('accessToken', $accessToken );
        return new Response('1');

    }

    public function facebookLoginAction(Request $request)
    {

        $appId = $this->container->getParameter('facebook_client_id');
        $appSecret = $this->container->getParameter('facebook_client_secret');

        $fb = new Facebook\Facebook(
            [
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'default_graph_version' => 'v2.2',
            ]
        );
        $session = $request->getSession();
        $accessToken = $session->get('accessToken');

        try {
            $response = $fb->get('/me?fields=id,first_name,last_name,email,gender,locale,picture', $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: '.$e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: '.$e->getMessage();
            exit;
        }
        $userProfile = $response->getGraphUser();
        if (null === $session->get('user_id')) {
            $em = $this->getDoctrine()->getManager();
            /** @var FrontUser $user */
            $user = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                ->findOneBy(['email' => $userProfile['email']]);
            if (null === $user) {
                $user = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                    ->findOneBy(['facebookId' => $userProfile['id']]);
            }

            if (null === $user) {
                $gender = $userProfile['gender'] === 'male' ? 0 : 1;
                $user = new FrontUser();
                $user->setEmail($userProfile['email']);
                $user->setUsername($userProfile['email']);
                $user->setName($userProfile['first_name']);
                $user->setSurname($userProfile['last_name']);
                if ($userProfile['birthday']) {
                    $user->setUserBirthDate(new \DateTime($userProfile['birthday']));
                }
                $encoder = new MessageDigestPasswordEncoder();
                $password = $encoder->encodePassword(md5(uniqid('', true)), $user->getSalt());
                $user->setPassword($password);
                $user->setEnabled(1);
                $user->setUserGender($gender);
                $user->setUserCreateDate(new \DateTime());
                $user->setCreated(new \Datetime());
                $user->setUserCreateDate(new \Datetime());
                $user->setCity('');
                $user->setLoginType(2);
                $user->setIsShoppiday(1);
                $user->setApiFlag(1);
            }
            $user->setFacebookId($userProfile['id']);
            $user->setLastLogin(new \DateTime(date('Y-m-d H:i:s')));
            $user->setModified(new \Datetime());
            $em->persist($user);
            $em->flush();
            $logout = $fb->getRedirectLoginHelper()->getLogoutUrl($fb->getRedirectLoginHelper()->getAccessToken(), $this->generateUrl('i_flair_lets_bonus_front_logout'));
            $this->initSession($request, $user, '2', $logout);
        }

        return $this->redirect($this->generateUrl('i_flair_lets_bonus_front_homepage'));
    }

    /**
     * Show form to send email for rememeber password.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPasswordAction()
    {
        $form = $this->createForm(new LoginType());

        return $this->render(
            'iFlairLetsBonusFrontBundle:Homepage:forgot_password.html.twig',
            [
                'login_form' => $form->createView(),
            ]
        );
    }

    /**
     * Send an email with the token to do a reset password
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function forgotPasswordSentAction(Request $request)
    {
        $email = $request->get('email');
        if (null !== $email) {
            $entityManager = $this->get('doctrine.orm.default_entity_manager');
            /** @var FrontUser $user */
            $user = $entityManager->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(
                ['email' => $email, 'loginType' => 1]
            );
            if (null !== $user) {
                $this->sendEmailRecoverPassword($user, $request);
            }
        }

        return $this->render('iFlairLetsBonusFrontBundle:Homepage:forgot_password_send.html.twig');
    }

    /**
     * @param Request $request
     * @param         $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function accountConfirmAction(Request $request, $token)
    {
        $entityManager = $this->get('doctrine.orm.default_entity_manager');
        $user = $entityManager->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(
            ['confirmationToken' => $token, 'loginType' => 1]
        );
        if (null !== $user) {
            if (!$user->isEnabled()) {
                $user->setEnabled(1);
                $user->setConfirmationToken('');
                $entityManager->persist($user);
                $entityManager->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Tu cuenta ha sido activada');
            } else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Tu cuenta ya estaba activada');
            }
            $this->initSession($request, $user, '1');
        }

        return $this->redirect($this->generateUrl('i_flair_lets_bonus_front_homepage'));
    }


    /**
     * @param Request $request
     * @param string  $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var FrontUser $user */
        $user = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(
            ['confirmationToken' => $token, 'loginType' => 1]
        );
        $data = $request->request->all();
        if (null !== $user) {
            $form = $this->createForm(new RegistrationType(), $user);
            if (array_key_exists('password', $data) && $data['password']['first'] !== ''
                && $data['password']['first'] === $data['password']['second']
            ) {
                $encoder = new MessageDigestPasswordEncoder();
                $password = $encoder->encodePassword($data['password']['first'], $user->getSalt());

                if ($user->isToImport()) {
                    $user->setApiFlag(1);
                }
                $user->setPassword($password);
                $user->setConfirmationToken('');
                $em->persist($user);
                $em->flush();

                return $this->render('iFlairLetsBonusFrontBundle:Homepage:reset_password_ok.html.twig');
            }

            return $this->render(
                'iFlairLetsBonusFrontBundle:Homepage:reset_password.html.twig',
                [
                    'form' => $form->createView(),
                    'user_id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'token' => $token,
                ]
            );
        }
        throw $this->createNotFoundException();
    }

    /**
     * @param Request   $request
     * @param FrontUser $user
     * @param string    $logType
     * @param null      $logout
     */
    protected function initSession(Request $request, FrontUser $user, $logType = '1', $logout = null)
    {
        $session = $request->getSession();
        $session->set('user_id', $user->getId());
        $session->set('user_email', $user->getEmail());
        $session->set('user_name', $user->getName());
        $session->set('user', $user);
        if ($user->getImage()) {
            $session->set('userimage', $user->getImage()->getId());
        } else {
            $session->set('userimage', null);
        }
        $session->set('login_type', $logType);
        $session->set('logout', $logout);
    }
}