<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Variations;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ShopAdminController extends Controller
{
    public function loadVouchersAction()
    {
        $request = $this->getRequest();
        $voucherProgramId = $request->get('voucherProgramId');
        $shopId = $request->get('shopId');
        $em = $this->getDoctrine()->getManager();
        if ($shopId) {
            $shopRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
            $shop = $shopRepository->findOneBy(array('id' => $shopId, 'vprogram' => $voucherProgramId));
            if ($shop) {
                $connection = $em->getConnection();
                $voucherRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Voucher');
                $selectedVouchers = $voucherRepository->getShopSelectedVouchers($shopId, $voucherProgramId, $em);

                $finalVouchers = $voucherRepository->getShopMergeVouchers($shopId, $voucherProgramId, $selectedVouchers, $em);

                return new Response(json_encode($finalVouchers));
            } else {
                $voucherRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Voucher');
                $vouchers = $voucherRepository->getShopVouchersByVoucherProgram($voucherProgramId, $em);

                return new Response(json_encode($vouchers));
            }
        } else {
            $voucherRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Voucher');
            $vouchers = $voucherRepository->getShopVouchersByVoucherProgram($voucherProgramId, $em);

            return new Response(json_encode($vouchers));
        }
    }

    public function loadShopHistoryAction(Request $request){
        $shopId = $request->get('shopId');
        $em = $this->getDoctrine()->getManager();
        if ($shopId) {
            $shopHistoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
            $shopHistories = $shopHistoryRepository->findBy(array('shop' => $shopId));
            if($shopHistories){
                $i=0;
                foreach ($shopHistories as $shopHistory) {
                    $history['history_id'] = $shopHistory->getId();
                    $history['history_title'] = $shopHistory->getTitle();
                    $history['history_url'] = $shopHistory->getUrl();
                    if(strlen($shopHistory->getIntroduction()>30)) {
                        $history['history_introduction'] = substr($shopHistory->getIntroduction(), 0, 30)."...";
                    }else{
                        $history['history_introduction'] = $shopHistory->getIntroduction();
                    }
                    if(strlen($shopHistory->getDescription())>30) {
                        $history['history_description'] = substr(strip_tags($shopHistory->getDescription()), 0, 30)."...";
                    }else{
                        $history['history_description'] = strip_tags($shopHistory->getDescription());
                    }
                    if(strlen($shopHistory->getTearms())>30) {
                        $history['history_terms'] = substr(strip_tags($shopHistory->getTearms()), 0, 30)."...";
                    }else{
                        $history['history_terms'] = strip_tags($shopHistory->getTearms());
                    }
                    $history['history_cashbackprice'] = $shopHistory->getCashbackPrice();
                    $history['history_cashbackpercentage'] = $shopHistory->getCashbackPercentage();
                    $history['history_letsbonuspercentage'] = $shopHistory->getLetsBonusPercentage();
                    $history['history_affiliate'] = $shopHistory->getUrlAffiliate();
                    $history['history_startdate'] = $shopHistory->getStartDate()->format('Y-m-d H:i:s');
                   // $history['history_enddate'] = $shopHistory->getEndDate()->format('Y-m-d H:i:s');
                    $history['history_tag'] = $shopHistory->getTag();
                    $history['history_labelcrossout'] = $shopHistory->getPrevLabelCrossedOut();
                    $history['history_shippinginfo'] = $shopHistory->getShippingInfo();
                    $history['history_created'] = $shopHistory->getCreated()->format('Y-m-d H:i:s');
                    $history['history_modified'] = $shopHistory->getModified()->format('Y-m-d H:i:s');
                    $history['administrator_id'] = $shopHistory->getAdministrator();
                     
                    $connection = $em->getConnection();
                    $query = $connection->prepare('SELECT a.username FROM lb_fos_user_user as a WHERE a.id = :id');
                    $query->bindValue('id', $shopHistory->getAdministrator());
                    $query->execute();
                    $administrator = $query->fetchAll();
                    foreach ($administrator as $key => $value) {
                      
                         $history['administrator'] = $value['username'];
                    }
                   
                    $sm = $this->getDoctrine()->getEntityManager();
                    $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistory->getId()));
                    
                    if (!empty($slug)) {
                           $history['shopHistory_slug'] = $slug->getSlugName();
                    }

                    $histories[$i] = $history;
                    $i++;
                }
            }
            return new Response(json_encode($histories));
        }
    }

    public function cloneShopHistoryAction(Request $request){

        $shopHistoryId = $request->request->get('shopHistoryId');
        $em = $this->getDoctrine()->getManager();
        if ($shopHistoryId) 
        {
            $shopHistoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
            $shopHistories = $shopHistoryRepository->findBy(array('id' => $shopHistoryId));
            if($shopHistories)
            {
                $i=0;
                foreach ($shopHistories as $shopHistory) {
                    $history['history_id'] = $shopHistory->getId();
                    $history['history_title'] = $shopHistory->getTitle();
                    $history['history_url'] = $shopHistory->getUrl();
                    $history['history_introduction'] = $shopHistory->getIntroduction();
                    $history['history_description'] = $shopHistory->getDescription();
                    $history['history_terms'] = $shopHistory->getTearms();
                    $history['history_cashbackprice'] = $shopHistory->getCashbackPrice();
                    $history['history_cashbackpercentage'] = $shopHistory->getCashbackPercentage();
                    $history['history_letsbonuspercentage'] = $shopHistory->getLetsBonusPercentage();
                    $history['history_affiliate'] = $shopHistory->getUrlAffiliate();
                    $history['history_startdate'] = $shopHistory->getStartDate()->format('Y-m-d H:i:s');
                   // $history['history_enddate'] = $shopHistory->getEndDate()->format('Y-m-d H:i:s');
                    $history['history_tag'] = $shopHistory->getTag();
                    $history['history_labelcrossout'] = $shopHistory->getPrevLabelCrossedOut();
                    $history['history_shippinginfo'] = $shopHistory->getShippingInfo();
                    $history['history_created'] = $shopHistory->getCreated()->format('Y-m-d H:i:s');
                    $history['history_modified'] = $shopHistory->getModified()->format('Y-m-d H:i:s');
                    $histories[$i] = $history;
                    $i++;
                }
            }
            
            return new Response(json_encode($histories));
        }

    }
    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::createAction()
     */
    /*public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

        $this->admin->setSubject($object);*/

    /** @var $form \Symfony\Component\Form\Form */
        /*$form = $this->admin->getForm();
        $form->setData($object);

        if ($this->getRestMethod() == 'POST') {
            $form->submit($request);

            $isFormValid = $form->isValid();
            $uniqid = $request->query->get('uniqid');
            $post_parameter_name = $request->request->all()[$uniqid]['title'];

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if (false === $this->admin->isGranted('CREATE', $object)) {
                    throw new AccessDeniedException();
                }

                try {
                    $id = '';
                    $db_parameter_name = '';
                    $slug_response = $this->get('app.category_url_slugger')->urlVerification($id, $object->getTitle(), 'Slug', 'slugName', Constants::SHOP_IDENTIFIER, $db_parameter_name, $post_parameter_name);

                    if ($slug_response == false) {
                        $this->get('session')->getFlashBag()->add('sonata_flash_error', 'Slug Name already exists');
                        $view = $form->createView();

                                // set the theme for the current Admin Form
                                $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

                        return $this->render($this->admin->getTemplate($templateKey), array(
                                'action' => 'create',
                                'form' => $view,
                                'object' => $object,
                            ));
                    } else {
                        $object = $this->admin->create($object);
                        $slug = new Slug();
                        $slug->setCategoryType(Constants::SHOP_IDENTIFIER);
                        $slug->setSlugName($slug_response);
                        $slug->setCategoryId($object->getId());

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($slug);
                        $em->flush();
                    }

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result' => 'ok',
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
            'form' => $view,
            'object' => $object,
        ), null);
    }*/

    /*public function logModelManagerException($e)
    {
        $context = array('exception' => $e);
        if ($e->getPrevious()) {
            $context['previous_exception_message'] = $e->getPrevious()->getMessage();
        }
        $this->getLogger()->error($e->getMessage(), $context);
    }*/

    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::editAction()
     */
    /*public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);*/

    /** @var $form \Symfony\Component\Form\Form */
        /*$form = $this->admin->getForm();
        $db_parameter_name = $object->getTitle();
        $form->setData($object);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $isFormValid = $form->isValid();
            $uniqid = $request->query->get('uniqid');
            $post_parameter_name = $request->request->all()[$uniqid]['title'];
            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                try {

                //$slug_response = $this->get('app.category_url_slugger')->urlVerification($id,$object->getName(), 'parentCategory','parentUrl' );
                  $slug_response = $this->get('app.category_url_slugger')->urlVerification($id, $object->getTitle(), 'Slug', 'slugName', Constants::SHOP_IDENTIFIER, $db_parameter_name, $post_parameter_name);

                    if ($slug_response == false) {
                        $this->get('session')->getFlashBag()->add('sonata_flash_error', 'Slug Name already exists');
                        $view = $form->createView();

                            // set the theme for the current Admin Form
                            $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

                        return $this->render($this->admin->getTemplate($templateKey), array(
                                'action' => 'edit',
                                'form' => $view,
                                'object' => $object,
                            ), null);
                    } else {
                        $object = $this->admin->update($object);
                        $em = $this->getDoctrine()->getManager();
                        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $object->getId()));

                        if ($slug) {
                            // Edit slug name

                          //  $categories[$value['parent_category_id']][$key]['categoryurlSlug'] = $slug->getSlugName();
                            $slug->setSlugName($slug_response);

                            $em->persist($slug);
                            $em->flush();
                        } else {
                            $slug = new Slug();
                            $slug->setCategoryType(Constants::SHOP_IDENTIFIER);
                            $slug->setSlugName($slug_response);
                            $slug->setCategoryId($object->getId());

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($slug);
                            $em->flush();
                        }
                    }

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result' => 'ok',
                            'objectId' => $this->admin->getNormalizedIdentifier($object),
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

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form' => $view,
            'object' => $object,
        ), null);
    }*/

    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::deleteAction()
     */
   /* public function deleteAction($id)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if ($this->getRestMethod() == 'DELETE') {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            try {
                $this->get('app.category_url_slugger')->removeSlug(Constants::SHOP_IDENTIFIER, $object->getId(), 'Slug', 'categoryType', 'categoryId');
                $this->admin->delete($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'ok'), 200, array());
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->admin->trans(
                        'flash_delete_success',
                        array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                        'SonataAdminBundle'
                    )
                );
            } catch (ModelManagerException $e) {
                $this->logModelManagerException($e);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'error'), 200, array());
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans(
                        'flash_delete_error',
                        array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                        'SonataAdminBundle'
                    )
                );
            }

            return $this->redirectTo($object);
        }

        return $this->render($this->admin->getTemplate('delete'), array(
            'object' => $object,
            'action' => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
        ), null);
    }*/
    /*public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->submit($request);

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->create($object);

                /*echo '<pre>';
                $administrator = $object->getAdministrator();
                $title = $object->getTitle();
                $url = $object->getUrl();
                $intro = $object->getIntroduction();
                $des = $object->getDescription();
                $terms = $object->getTearms();
                $cshbk = $object->getCashbackPrice();
                $cahp = $object->getCashbackPercentage();
                $ltsp = $object->getLetsBonusPercentage();
                $tag = $object->getTag();
                $crs = $object->getPrevLabelCrossedOut();
                $shp = $object->getShippingInfo();
                $var = $object->getVariation();
                print_r($amdinis);
                exit;*/

                /*if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->get('session')->setFlash('sonata_flash_success','flash_create_success');
                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                $this->get('session')->setFlash('sonata_flash_error', 'flash_create_error');
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form'   => $view,
            'object' => $object,
        ));
    }*/

    public function create1Action()
    {
        echo '1';
        exit();
        /*echo '<pre>';
        is_array($this->get('request')->request->all())
        print_r($this->get('request')->request->all());exit;*/
        //$request = $this->get('request');
        //$session->getRequest()->getSession();
        /*if ($request->getMethod() == 'POST') {
            $request = $this->get('request')->request->all();
            $em = $this->getDoctrine()->getManager();
            $shop = new Shop();
            foreach($request as $key=>$value){
                /*$shop->setTitle($value['title']);
                $shop->setUrl($value['url']);
                $shop->setIntroduction($value['introduction']);
                $shop->setDescription($value['description']);
                $shop->setTearms($value['tearms']);
                $shop->setKeywords($value['keywords']);                
                $shop->setCashbackPrice($value['cashbackPrice']);
                $shop->setCashbackPercentage($value['cashbackPercentage']);
                //$shop->setNetwork($value['network']);
                $shop->setProgramId($value['programId']);
                $shop->setUrlAffiliate($value['urlAffiliate']);
                $shop->setDaysValidateConfirmation($value['daysValidateConfirmation']);
                $shop->setHighlightedHome($value['highlightedHome']);
                $shop->setShopStatus($value['shopStatus']);
                $shop->setStartDate(new \DateTime());
                $shop->setEndDate(new \DateTime());
                $shop->setLetsBonusPercentage($value['letsBonusPercentage']);
                $shop->setPrevLabelCrossedOut($value['prevLabelCrossedOut']);
                //$shop->setLabelTag($value['labelTag']);
                $shop->setShippingInfo($value['shippingInfo']);
                $shop->setInternalNotes($value['internalNotes']);
                $em->persist($shop);
                $em->flush();*/
                /*$variations = new Variations();
                foreach($value['variations'] as $nValue) {
                    $variations->setNumber($nValue['number']);
                    $variations->setTitle($nValue['title']);
                    $variations->setDate($nValue['date']);
                    $variations->setShop($shop);
                    $em->merge($variations);
                    $em->flush();
                }
            }
        }*/
        return parent::createAction(); // TODO: Change the autogenerated stub*/
    }
}
