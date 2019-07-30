<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use iFlair\LetsBonusAdminBundle\Entity\Clicks;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTrackings;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('iFlairLetsBonusFrontBundle:Default:index.html.twig');
    }

    public function clickAction()
    {
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $shopId = $request->request->get('shopId');
        $shopRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $shop = $shopRepository->findOneBy(array('id' => $shopId));
        $companyId = $shop->getCompanies();
        $shopHistoryId = $request->request->get('shopHistoryId');
        $shopOffers = $request->request->get('shopOffers');
        $tabType = $request->request->get('tabType');
        $tabId = $request->request->get('tabId');
        $tabPosition = $request->request->get('tabPosition');
        $affiliateUrlOrigin = $request->request->get('affiliateUrlOrigin');
        $affiliateUrl = $request->request->get('affiliateUrl');
        $programId = $request->request->get('programId');

        $session = $this->get('session');
        if (!empty($session->get('user_id'))) {
            $cashbackTrackings = new cashbackTrackings();
            $cashbackTrackings->setShopId($shopId);
            $cashbackTrackings->setUserId($session->get('user_id'));
            $cashbackTrackings->setProgramId($programId);
            $cashbackTrackings->setUrlAffiliation($affiliateUrlOrigin);
            $cashbackTrackings->setIp($request->getClientIp());
            $cashbackTrackings->setUserAgent($request->headers->get('User-Agent'));
            $cashbackTrackings->setRedirectUrl($affiliateUrl);
            $em->persist($cashbackTrackings);
            $em->flush();
        }

        $clicks = new Clicks();
        $clicks->setShopId($shopId);
        $clicks->setShopshistoryId($shopHistoryId);
        if (!empty($session->get('user_id'))) {
            $clicks->setUserId($session->get('user_id'));
        } else {
            $clicks->setUserId(null);
        }
        if ($shopOffers) {
            $clicks->setType($shopOffers);
        } else {
            $clicks->setType(null);
        }
        if ($tabType) {
            $clicks->setTabType($tabType);
        } else {
            $clicks->setTabType(null);
        }
        if ($tabId) {
            $clicks->setTabId($tabId);
        } else {
            $clicks->setTabId(null);
        }
        if ($tabPosition) {
            $clicks->setTabPosition($tabPosition);
        } else {
            $clicks->setTabPosition(null);
        }
        $clicks->setIp(ip2long($_SERVER['REMOTE_ADDR']));
        $clicks->setUserAgent($_SERVER ['HTTP_USER_AGENT']);
        if ($companyId) {
            $clicks->setCompanyId($companyId);
        } else {
            $clicks->setCompanyId(null);
        }
        $em->persist($clicks);
        $em->flush();

        return new Response();
    }

    public function getAffiliation($shop, $shopHistory, &$em)
    {
        $arg = '';
        $session = $this->getRequest()->getSession();
        $shopId = $shop->getId();
        //$shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId));
        if(isset($shopHistory) && !empty($shopHistory))
        {
            $shopHistoryId = $shopHistory->getId();
            $redirectUrl = $shopHistory->getUrlAffiliate();
            $network = $shop->getNetwork()->getName();
            if (!empty($session->get('user_id'))) {
                $userId = $session->get('user_id');

                $parsedUrl = parse_url($redirectUrl);
                $prefix = (empty($parsedUrl['query'])) ? '?' : '&';

                if ($network == 'Zanox'):
                    $arg .= $prefix.'zpar0='.$userId;
                $arg .= '&zpar1='.$shopId;
                $arg .= '&zpar3='.$shopHistoryId; elseif ($network == 'TradeDoubler'):
                    $arg .= $prefix.'epi='.$userId;
                $arg .= '&epi2='.$shopId;
                //$arg.='&epi3='.$shop['Shopshistory']['id'];

                elseif ($network == 'Webgains'):
                    $webgainsclickRef = urlencode($userId.':'.$shopId.':'.$shopHistoryId);
                $arg .= $prefix.'clickref='.$webgainsclickRef; elseif ($network == 'CJ'):
                    $cjclickRef = $userId.'x'.$shopId.'x'.$shopHistoryId;
                $arg .= $prefix.'SID='.$cjclickRef; elseif ($network == 'TDI'):
                    $arg .= $prefix.'epi='.$userId;
                $arg .= '&epi2='.$shopId; elseif ($network == 'Ebay'):
                    $ebayCustomId = $userId.'-'.$shopId.'-'.$shopHistoryId;
                $arg .= $prefix.'customid='.$ebayCustomId; elseif ($network == 'Amazon'):
                    $amazonCustomId = $userId.'-'.$shopId.'-'.$shopHistoryId;
                $arg .= $prefix.'ascsubtag='.$amazonCustomId;
                endif;

                return $arg;
            }
        }
    }
}
