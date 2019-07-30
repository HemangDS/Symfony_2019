<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class VoucherProgramsAdminController extends CRUDController
{
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

            $uniqid = $request->query->get('uniqid');
            $post_parameter_name = $request->request->all()[$uniqid]['programName'];

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if (false === $this->admin->isGranted('CREATE', $object)) {
                    throw new AccessDeniedException();
                }

                try {
                    $id = '';
                    $db_parameter_name = '';

                    $slug_response = $this->get('app.category_url_slugger')->urlVerification($id, $object->getProgramName(), 'Slug', 'slugName', Constants::MARCAS_IDENTIFIER, $db_parameter_name, $post_parameter_name);

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
                        $slug->setCategoryType(Constants::MARCAS_IDENTIFIER);
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
    }

    public function logModelManagerException($e)
    {
        $context = array('exception' => $e);
        if ($e->getPrevious()) {
            $context['previous_exception_message'] = $e->getPrevious()->getMessage();
        }
        $this->getLogger()->error($e->getMessage(), $context);
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

        $db_parameter_name = $object->getProgramName();

        $form->setData($object);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $uniqid = $request->query->get('uniqid');
            $post_parameter_name = $request->request->all()[$uniqid]['programName'];

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                try {
                    $slug_response = $this->get('app.category_url_slugger')->urlVerification($id, $object->getProgramName(), 'Slug', 'slugName', Constants::MARCAS_IDENTIFIER, $db_parameter_name, $post_parameter_name);

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
                        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                    array('categoryType' => Constants::MARCAS_IDENTIFIER, 'categoryId' => $object->getId()));
                        if ($slug) {
                            //  $categories[$value['parent_category_id']][$key]['categoryurlSlug'] = $slug->getSlugName();
                            $slug->setSlugName($slug_response);

                            $em->persist($slug);
                            $em->flush();
                        } else {
                            $slug = new Slug();
                            $slug->setCategoryType(Constants::MARCAS_IDENTIFIER);
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
    }

    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::deleteAction()
     */
    public function deleteAction($id)
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
                $this->get('app.category_url_slugger')->removeSlug(Constants::MARCAS_IDENTIFIER, $object->getId(), 'Slug', 'categoryType', 'categoryId');
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
    }
}
