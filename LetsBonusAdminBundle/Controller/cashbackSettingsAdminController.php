<?php

namespace iFlair\LetsBonusAdminBundle\Controller;



use Sonata\AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\Response;


class cashbackSettingsAdminController extends Controller
{
    public function loadParentCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();  
        $connection = $em->getConnection();
       $statement = $connection->prepare('SELECT s.id AS shop_id, s.title AS shop_title, pc.id AS parent_id, vp.program_name AS vprogram_name, pc.name AS parent_name
                                           FROM lb_shop_parent_category AS spc
                                           JOIN lb_parent_category AS pc ON spc.parent_category_id = pc.id
                                           JOIN lb_shop AS s on spc.shop_id = s.id
                                           JOIN lb_voucher_programs AS vp on s.vprogram_id = vp.id
                                           WHERE s.shopStatus = 1 AND pc.status = 1
                                         ');
        $statement->execute();

        $parent_data = $statement->fetchAll();

        $statement = $connection->prepare('SELECT s.id AS shop_id,s.title AS shop_title, pc.id AS parent_id,vp.program_name AS vprogram_name, pc.name AS parent_name
                                           FROM lb_shop_category AS spc
                                           JOIN lb_category AS pc ON spc.category_id = pc.id
                                           JOIN lb_shop AS s on spc.shop_id = s.id
                                           JOIN lb_voucher_programs AS vp on s.vprogram_id = vp.id
                                           WHERE s.shopStatus = 1 AND pc.status = 1
                                         ');
        $statement->execute();

        $middle_cat_data = $statement->fetchAll();

        $statement = $connection->prepare('SELECT s.id AS shop_id,s.title AS shop_title, pc.id AS parent_id, vp.program_name AS vprogram_name,pc.name AS parent_name
                                           FROM lb_shop_child_category AS spc
                                           JOIN lb_child_category AS pc ON spc.child_category_id = pc.id
                                           JOIN lb_shop AS s on spc.shop_id = s.id
                                           JOIN lb_voucher_programs AS vp on s.vprogram_id = vp.id
                                           WHERE s.shopStatus = 1 AND pc.status = 1
                                         ');
        $statement->execute();

        $child_cat_data = $statement->fetchAll();

        $data = array();

        foreach ($parent_data as $key => $value) 
        {
            $data[$value['parent_name']][$key] = $value;  
        }
        foreach ($middle_cat_data as $key => $value) 
        {
            $data[$value['parent_name']][$key] = $value;  
        }
        foreach ($child_cat_data as $key => $value) 
        {
            $data[$value['parent_name']][$key] = $value;  
        }

        return new Response(json_encode($data));
    }

    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::createAction()
     */
    public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';
        $shopId = array();
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
      
        if ($this->getRestMethod() == 'POST') {
            
            $form->submit($request);

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if (false === $this->admin->isGranted('CREATE', $object)) {
                    throw new AccessDeniedException();
                }

                try {
                    $object = $this->admin->create($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result'   => 'ok',
                            'objectId' => $this->admin->getNormalizedIdentifier($object),
                        ), 200, array());
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->admin->trans(
                            'flash_create_success',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->logModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
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

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form'   => $view,
            'object' => $object,
            'shopId' => $shopId,
        ), null);
    }



     /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::editAction()
     */
    public function editAction($id = null)
    {
      $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';
        $shopId = array();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

      
        if ($request->isMethod('POST')) {
          
            $isFormValid = $form->isValid();
            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                try {
                    $object = $this->admin->update($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result'     => 'ok',
                            'objectId'   => $this->admin->getNormalizedIdentifier($object),
                        ), 200, array());
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->admin->trans(
                            'flash_edit_success',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->logModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->admin->trans(
                            'flash_edit_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();
       
        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());
     
        foreach ($object->getShop()->getSnapshot() as $key => $value) {
            $shopId[] = $value->getId();
        }

        //return $this->render($this->admin->getTemplate($templateKey), array(
        return $this->render('iFlairLetsBonusAdminBundle:CashbackSettings:parent.html.twig', array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
            'shopId' => json_encode($shopId),
        ), null);
    }

      /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::showAction()
     */
   
    public function showAction($id = null)
    {
        $request = $this->getRequest();
        $id      = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);


        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);
        $shop =  array();
      
        foreach ($object->getShop()->getKeys() as $key => $value) {

            $media = $object->getShop()->getValues()[$value]->getImage();
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'brand_on_shop');
            $productpublicUrl = $provider->generatePublicUrl($media, $format);
            $image = $productpublicUrl;
            
            $title= str_replace("'S","&S",$object->getShop()->getValues()[$value]->getTitle());
            $title= str_replace("'s","&s",$title);

            $shop[$object->getShop()->getValues()[$value]->getId()] = array('title'=>$title,
                                                                            'image'=>$image);
        }

        //return $this->render($this->admin->getTemplate('show'), array(
        return $this->render('iFlairLetsBonusAdminBundle:CashbackSettings:base_show.html.twig', array(
            'action'   => 'show',
            'object'   => $object,
            'elements' => $this->admin->getShow(),
            'shop' => json_encode($shop),
        ), null);
    }
}
