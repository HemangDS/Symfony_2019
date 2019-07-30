<?php

namespace Iflair\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	return $this->render('IflairApiBundle:Default:index.html.twig');
    }
}