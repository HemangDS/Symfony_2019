<?php

namespace Galop\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use AdminBundle\Entity\ApiArticle;

class ApiArticleAdminController extends CRUDController
{
	
	public function createAction()
    {
    	$request = $this->getRequest();
        $templateKey = 'edit';
		if ($request->getMethod() == 'POST') {
            $object = $this->admin->getNewInstance();

            $this->admin->setSubject($object);

	        /** @var $form \Symfony\Component\Form\Form */
	        $form = $this->admin->getForm();
	        $form->setData($object);
			$form->submit($request->request->get($form->getName()));
	        $isFormValid = $form->isValid();
	       	
	       	/*clientid validation*/
			$params = $request->request->all();
			foreach ($params as $value) {
				$key1 .= $value['clientkey'];
			}
			$em = $this->getDoctrine()->getEntityManager();
			$clienttoken = $em->getRepository('GalopAdminBundle:ApiUser')->findBy(array('UserToken' => $key1));
			
	        if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
	        	try{	
	        		if(!empty($clienttoken))
	        		{
	        			/*apiuseid*/
	        			$repository = $em->getRepository('GalopAdminBundle:ApiUser');
		                $Userid = $repository->findById($clienttoken);

		                foreach ($Userid as $key => $value) {
		                   $getUserid = $value->getId();
		                }
		             	$object->setApiuserId($getUserid);
		             	/*Uniquie artical id*/
		             	  $Gla = $Gop = '';
			              for($i = 0; $i < 3; $i++){
			                $Gla .= chr(mt_rand(65, 90));  
			                $Gop .= mt_rand(0, 9);
			              }
			              $uniqueid =  $Gla . $Gop;
			              $object->setArticleID($uniqueid);
			        	  $object = $this->admin->create($object);
						
			            $this->addFlash(
	                        'sonata_flash_success',
	                        $this->admin->trans(
	                            'flash_create_success',
	                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
	                            'SonataAdminBundle' , Response::HTTP_OK
	                        )
	                    );
	                    return $this->redirectTo($object);
	        		}else
	        		{
	        			$this->addFlash(
	                        'sonata_flash_error','Incorrect Client Key.',
	                        $this->admin->trans(
	                            'flash_create_error',
	                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
	                            'SonataAdminBundle', Response::HTTP_NOT_ACCEPTABLE 
	                        )
	                    );
	        		}
	        	} catch (ModelManagerException $e) {
                    $this->logModelManagerException($e);

                    $isFormValid = false;
                }

                if (!$isFormValid) {
	                if (!$this->isXmlHttpRequest()) {
	                    $this->addFlash(
	                        'sonata_flash_error',
	                        $this->admin->trans(
	                            'flash_create_error',
	                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
	                            'SonataAdminBundle'
	                        )
	                    );
	                }
	            } elseif ($this->isPreviewRequested()) {
	                // pick the preview template if the form was valid and preview was requested
	                $templateKey = 'preview';
	                $this->admin->getShow();
	            }
	        }
        }
        $form = $this->admin->getForm();
        $view = $form->createView();
        

        // set the theme for the current Admin Form

        $this->get('twig')->getExtension(FormExtension::class)->renderer->setTheme($view, $this->admin->getFormTheme());
        
        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form' => $view,
            'object' => $object,
        ), null);
    }
}