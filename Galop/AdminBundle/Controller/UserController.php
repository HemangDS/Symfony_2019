<?php

namespace Galop\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Galop\AdminBundle\Entity\User;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/usercd/{id}")
     */
    public function getAction()
    {
      $restresult = $this->getDoctrine()->getRepository('GalopAdminBundle:User')->findAll();
        if ($restresult === null) {
          return new View("there are no users exist", Response::HTTP_NOT_FOUND);
     }
        return $restresult;
    }
}
?>