<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\CmsAboutus;
use iFlair\LetsBonusAdminBundle\Entity\CmsCareers;
use iFlair\LetsBonusAdminBundle\Entity\CmsContact;
use iFlair\LetsBonusAdminBundle\Entity\CmsCookiespolicy;
use iFlair\LetsBonusAdminBundle\Entity\CmsHowitworks;
use iFlair\LetsBonusAdminBundle\Entity\CmsLegalWarning;
use iFlair\LetsBonusAdminBundle\Entity\CmsPolicies;
use iFlair\LetsBonusAdminBundle\Entity\CmsPress;
use iFlair\LetsBonusAdminBundle\Entity\CmsPrivacypolicy;
use iFlair\LetsBonusAdminBundle\Entity\CmsPromoteyourstore;
use iFlair\LetsBonusAdminBundle\Entity\CmsTermsandconditions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CmspageController extends Controller
{
    /**
     * @Route("/aboutus", name="aboutus")
     * @throws \LogicException
     */
    public function cmsaboutusAction()
    {
        $aboutus_content = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsAboutus')
            ->findOneBy(['status' => 1]);

        if ($aboutus_content) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/aboutus:aboutus.html.twig',
                [
                    'aboutuscontent' => $aboutus_content,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/careers", name="Careers")
     * @throws \LogicException
     */
    public function cmscareersAction()
    {
        $careers = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsCareers')
            ->findOneBy(['status' => 1]);

        if ($careers) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/careers:careers.html.twig',
                [
                    'careers' => $careers,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/contact", name="Contact")
     * @throws \LogicException
     */
    public function cmscontactAction()
    {
        $contact = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsContact')
            ->findOneBy(['status' => 1]);

        if ($contact) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/contact:contact.html.twig',
                [
                    'contact' => $contact,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/cookiespolicy", name="Cookies Policy")
     * @throws \LogicException
     */
    public function cmscookiespolicyAction()
    {
        $cookiespolicy = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsCookiespolicy')
            ->findOneBy(['status' => 1]);

        if ($cookiespolicy) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/cookiespolicy:cookiespolicy.html.twig',
                [
                    'cookiespolicy' => $cookiespolicy,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/como-funciona", name="Como funciona")
     * @throws \LogicException
     */
    public function cmshowitworksAction()
    {
        $howitworks = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsHowitworks')
            ->findOneBy(['status' => 1]);

        if ($howitworks) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/howitworks:howitworks.html.twig',
                [
                    'howitworks' => $howitworks,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/policies", name="Policies")
     * @throws \LogicException
     */
    public function cmspoliciesAction()
    {
        $policies = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsPolicies')
            ->findOneBy(['status' => 1]);

        if ($policies) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/policies:policies.html.twig',
                [
                    'policies' => $policies,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/prensa", name="Prensa")
     * @throws \LogicException
     */
    public function cmspressAction()
    {
        $press = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsPress')
            ->findOneBy(['status' => 1]);

        if ($press) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/press:press.html.twig',
                [
                    'press' => $press,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/privacypolicy", name="Privacy Policy")
     * @throws \LogicException
     */
    public function cmsprivacypolicyAction()
    {
        $privacypolicy = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsPrivacypolicy')
            ->findOneBy(['status' => 1]);

        if ($privacypolicy) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/privacypolicy:privacypolicy.html.twig',
                [
                    'privacypolicy' => $privacypolicy,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/promoteyourstore", name="Promote Your Store")
     * @throws \LogicException
     */
    public function cmspromoteyourstoreAction()
    {
        $promoteyourstore = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsPromoteyourstore')
            ->findOneBy(['status' => 1]);

        if ($promoteyourstore) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/promoteyourstore:promoteyourstore.html.twig',
                [
                    'promoteyourstore' => $promoteyourstore,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/termsandconditions", name="Terms And Conditions")
     * @throws \LogicException
     */
    public function cmstermsandconditionsAction()
    {
        $termsandconditions = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsTermsandconditions')
            ->findOneBy(['status' => 1]);

        if ($termsandconditions) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/termsandconditions:termsandconditions.html.twig',
                [
                    'termsandconditions' => $termsandconditions,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/sitemaps", name="sitemaps")
     * @throws \LogicException
     */
    public function cmssitemapsAction()
    {
        $sitemap = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsSitemap')
            ->findOneBy(['status' => 1]);

        if ($sitemap) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/sitemap:sitemap.html.twig',
                [
                    'sitemap' => $sitemap,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/accessibility", name="accessibility")
     * @throws \LogicException
     */
    public function cmsaccessibilityAction()
    {
        $accessibility = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsAccessibility')
            ->findOneBy(['status' => 1]);

        if ($accessibility) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/accessibility:accessibility.html.twig',
                [
                    'accessibility' => $accessibility,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/allhelp", name="allhelp")
     * @throws \LogicException
     */
    public function cmsallhelpAction()
    {
        $cmsallhelp = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsAllhelp')
            ->findOneBy(['status' => 1]);

        if ($cmsallhelp) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/allhelp:allhelp.html.twig',
                [
                    'cmsallhelp' => $cmsallhelp,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/createclaim", name="createclaim")
     * @throws \LogicException
     */
    public function cmscreateclaimAction()
    {
        $createclaim = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsCreateclaim')
            ->findOneBy(['status' => 1]);

        if ($createclaim) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/createclaim:createclaim.html.twig',
                [
                    'createclaim' => $createclaim,
                ]
            );
        }

        return new Response();
    }

    /**
     * @Route("/aviso-legal", name="aviso-legal")
     * @throws \LogicException
     */
    public function cmslegalwarningAction()
    {
        $legalwarning = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusAdminBundle:CmsLegalWarning')
            ->findOneBy(['status' => 1]);

        if ($legalwarning) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Cms/legalwarning:legalwarning.html.twig',
                [
                    'legalwarning' => $legalwarning,
                ]
            );
        }

        return new Response();
    }
}
