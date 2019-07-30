<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusFrontBundle\Form\LoginType;
use iFlair\LetsBonusFrontBundle\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GotostoreController extends Controller
{
    public function indexAction()
    {
        $user = new FrontUser();
        $form = $this->createForm(new RegistrationType(), $user);
        $login_form = $this->createForm(new LoginType(), $user);

        return $this->render(
            'iFlairLetsBonusFrontBundle:Homepage:gotostore_login.html.twig',
            ['form' => $form->createView(), 'login_form' => $login_form->createView()]
        );
    }
}
