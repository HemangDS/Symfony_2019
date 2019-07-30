<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use iFlair\LetsBonusAdminBundle\Entity\Newsletter;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusFrontBundle\Controller\BrandController;
/* SELLIGENT */
use Letsbonus\SelligentSyncBundle\Service\Selligent\SoapApiHandler;
use Prolix\MailchimpBundle\Exception\MailchimpAPIException;
use ZfrMailChimp\Client\MailChimpClient;
use Guzzle\Plugin\Async\AsyncPlugin;

use iFlair\LetsBonusAdminBundle\Provider\inc;
/* SELLIGENT */

use iFlair\LetsBonusAdminBundle\Entity\MailchimpLists;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpSubscription;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpUserListStatus;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpCampaign;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpCampaignNewsletterStatus;
use iFlair\LetsBonusFrontBundle\Controller\SubscriptionController;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpSegmentListNewsletter;
// use Symfony\Component\Validator\Constraints\Date;

class NewsletterAdminController extends CRUDController
{
    public function createAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);

        $listArr = $client->getLists(array(
                    'apikey' => $apikey,
            ));
       
        foreach ($listArr['data'] as $key => $valueArr) {
           
                $listChecking = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpLists')->findOneBy(array('list_id' => $valueArr['id']));
                if(!$listChecking)
                {
                    $mailchimpLists = new MailchimpLists();
                    $mailchimpLists->setListName($valueArr['name']);
                    $mailchimpLists->setListId($valueArr['id']);
                    $em->persist($mailchimpLists);
                    $em->flush();
                }
                else
                {
                    if($listChecking->getListName() != $valueArr['name'])
                    {
                        $listChecking->setListName($valueArr['name']);
                        $em->persist($listChecking);
                        $em->flush();
                    }
                }
            
        }
        $session = $this->get('session');
        if ($session->get('SELECTED_DATE') != null) {
            $session->remove('SELECTED_DATE');

            return parent::createAction();
        } else {
            return parent::createAction();
        }
    }

    public function NewsletterdateselectionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $Data = $this->getRequest()->request;

        $datas = (array) $Data;
        $selected_date = '';
        foreach ($datas as $dta) {
            foreach ($dta as $key => $val) {
                $selected_date = $key;
            }
        }

        $date_selected = str_replace('_', ' ', $selected_date);
        /*$date_selected = str_replace(',', ' ', $date_selected);
        $date_selected = str_replace('  ', ' ', $date_selected);  //May 26 2016 5:33:38 pm*/
        $session = $this->get('session');
        /* FOR GET DATA FROM SESSION */
        /*echo "selected date:".$date_selected."\n";
        echo "get data: ".$session->get('SELECTED_DATE');*/
        $session->set('SELECTED_DATE', $date_selected);

        return new Response();
    }

    public function getNewsletterData($em, $newsletter_id)
    {
        if (isset($newsletter_id) && !empty($newsletter_id)) {
            $connection = $em->getConnection();

            $newsletter_entity = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter');
            $newsletter_transactions = $newsletter_entity->findOneById($newsletter_id);

            if ($newsletter_transactions) {
                $banner_query = 'SELECT newsletter_banner_id FROM `newsletter_newsletter_banner`';
                $banner_condition = 'WHERE newsletter_id = :newsletter_id';
                $banner_statement = $connection->prepare($banner_query.$banner_condition);
                $banner_statement->bindValue('newsletter_id', $newsletter_id);
                $banner_statement->execute();
                $newsletter_banner_ids = $banner_statement->fetchAll();
                $newsletter_bannerids = array();
                foreach ($newsletter_banner_ids as $nbid) {
                    if (!in_array($nbid['newsletter_banner_id'], $newsletter_bannerids)) {
                        $newsletter_bannerids[] = $nbid['newsletter_banner_id'];
                    }
                }

                $shophistory_query = 'SELECT shop_history_id FROM `newsletter_shop_history`';
                $shophistory_condition = 'WHERE newsletter_id = :newsletter_id';
                $shophistory_statement = $connection->prepare($shophistory_query.$shophistory_condition);
                $shophistory_statement->bindValue('newsletter_id', $newsletter_id);
                $shophistory_statement->execute();
                $newsletter_shophistiry_ids = $shophistory_statement->fetchAll();

                $newsletter_shophistoryids = array();
                foreach ($newsletter_shophistiry_ids as $nsid) {
                    if (!in_array($nsid['shop_history_id'], $newsletter_shophistoryids)) {
                        $newsletter_shophistoryids[] = $nsid['shop_history_id'];
                    }
                }

                $voucherprogram_query = 'SELECT voucher_programs_id FROM `newsletter_voucher_programs`';
                $voucherprogram_condition = 'WHERE newsletter_id = :newsletter_id';
                $voucherprogram_statement = $connection->prepare($voucherprogram_query.$voucherprogram_condition);
                $voucherprogram_statement->bindValue('newsletter_id', $newsletter_id);
                $voucherprogram_statement->execute();
                $newsletter_voucherprogram_ids = $voucherprogram_statement->fetchAll();
                $newsletter_voucherprogramids = array();
                foreach ($newsletter_voucherprogram_ids as $vpid) {
                    if (!in_array($vpid['voucher_programs_id'], $newsletter_voucherprogramids)) {
                        $newsletter_voucherprogramids[] = $vpid['voucher_programs_id'];
                    }
                }

                $newsletter_templet_name = $newsletter_transactions->getTemplatename();

                $provider = $this->container->get('sonata.media.provider.image');

                $baners_urls = array();
                $banner_created_date_cmp = 0;
                $bnr_count = 0;
                $newslbanner_entity = $em->getRepository('iFlairLetsBonusAdminBundle:NewsletterBanner');
                foreach ($newsletter_bannerids as $nwbanner_id) {
                    $newslbanner_transaction_data = $newslbanner_entity->findBy(array('id' => $nwbanner_id));
                    foreach ($newslbanner_transaction_data as $data) {
                        $banner_created_date = $data->getModified()->format('Y-m-d');
                        $media = $data->getImage();
                        if ($data->getFirstbanner()) {
                            if ($banner_created_date_cmp < strtotime($banner_created_date)) {
                                $banner_created_date_cmp = strtotime($banner_created_date);
                                $format = $provider->getFormatName($media, 'newsletter_main_banner');
                                $sliderRecord = $provider->generatePublicUrl($media, $format);
                                $baners_urls['mainimage'] = $sliderRecord;
                            } else {
                                $format = $provider->getFormatName($media, 'newsletter_sub_banner');
                                $sliderRecord = $provider->generatePublicUrl($media, $format);
                                $baners_urls[$bnr_count] = $sliderRecord;
                                ++$bnr_count;
                            }
                        } else {
                            $format = $provider->getFormatName($media, 'newsletter_sub_banner');
                            $sliderRecord = $provider->generatePublicUrl($media, $format);
                            $baners_urls[$bnr_count] = $sliderRecord;
                            ++$bnr_count;
                        }
                    }
                }

                /* SHOP HISTORY DATA */
                $shop_email_data = array();
                $count = 0;
                $newslshophistory_entity = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
                $shop_entity = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
                $voucherprogram_entity = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
                $cashbacksetting_entity = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings');
                $brandController = new BrandController();
                $brandController->setContainer($this->container);
                foreach ($newsletter_shophistoryids as $newslettershophid) {
                    $shoph_data = $newslshophistory_entity->findOneBy(array('id' => $newslettershophid));

                    $shop_email_data[$count]['id'] = $shoph_data->getId(); // Id            
                    $shop_email_data[$count]['title'] = ''; // Title            
                    $shop_email_data[$count]['description'] = ''; // Description
                    $cashbackPrice = $shoph_data->getCashbackPrice();
                    $cashbackPercentage = $shoph_data->getCashbackPercentage();
                    $shop_email_data[$count]['cashbackpercentage'] = '';
                    $shop_email_data[$count]['shopType'] = '';

                    $shop_id = $shoph_data->getShop();
                    $shop_data = $shop_entity->findOneBy(array('id' => $shoph_data->getShop()));

                    if (!empty($shop_data->getNewsletterImage())):
                        $shop_media = $shop_data->getNewsletterImage();

                        /*$tetste = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneBy(array('name' => trim($shop_media)));
                        foreach($tetste as $d) {
                            echo "media id : ".$d->getId();
                            echo "media name : ".$d->getName();
                        }*/

                        $shop_format = $provider->getFormatName($shop_media, 'default_hoy_te_recomendamos');
                    $sliderRecord1 = $provider->generatePublicUrl($shop_media, $shop_format);
                    $shop_email_data[$count]['productname'] = $sliderRecord1; // product image
                        // echo $newslettershophid.' --- '.$sliderRecord1.'<br/>';
                    else:
                        $shop_email_data[$count]['productname'] = ''; // product image
                    endif;

                    $shop_email_data[$count]['enddate'] = '';
                    $voucherprogram_data = $voucherprogram_entity->findOneBy(array('id' => $shop_data->getVprogram()));
                    $voucherProgramName = $voucherprogram_data->getProgramName();

                    //Offers = cashback,voucher(i.e. voucher/offer),offer(i.e. product)
                    $shopType = $shop_data->getOffers();
                    $titleCashbackConst = 'Cashback';
                    $titleVoucherConst = 'Cupón descuento';
                    $titleOfferConst = 'Oferta';
                    switch ($shopType) {
                        case 'offer':
                            //TO-DO :: Load product record
                        break;
                        case 'voucher':
                            $voucherFinal = $brandController->getVoucherByShopId($shop_data->getId(), $shop_data->getVprogram(), $connection);
                            if (count($voucherFinal) > 0) {
                                if (isset($voucherFinal[0]['voucher_code']) && !empty($voucherFinal[0]['voucher_code'])) {
                                    //voucher
                                    $shop_email_data[$count]['title'] = $titleVoucherConst.' '.$voucherProgramName;
                                    $shop_email_data[$count]['shopType'] = 'voucher';
                                } else {
                                    //offer
                                    $shop_email_data[$count]['title'] = $titleOfferConst.' '.$voucherProgramName;
                                    $shop_email_data[$count]['shopType'] = 'oferta';
                                }
                                $shop_email_data[$count]['description'] = $voucherFinal[0]['short_description'];
                                $enddate = $voucherFinal[0]['voucher_expire_date'];
                                if (isset($enddate) && !empty($enddate)) {
                                    $shop_email_data[$count]['enddate'] = date('d/m/Y', strtotime($enddate)); // EndDate
                                }
                            }
                        break;
                        case 'cashback':
                        default:
                            $shop_email_data[$count]['title'] = $titleCashbackConst.' '.$voucherProgramName;
                            $shop_email_data[$count]['description'] = $shoph_data->getTitle();
                            $shop_email_data[$count]['shopType'] = 'cashback';
                            if ($cashbackPrice > 0) {
                                $shop_email_data[$count]['cashbackpercentage'] = $cashbackPrice.'€';
                            } elseif ($cashbackPercentage > 0) {
                                $shop_email_data[$count]['cashbackpercentage'] = $cashbackPercentage.'%';
                            }
                            //$enddate = $shoph_data->getEndDate();
                            //$shop_email_data[$count]['enddate'] = $enddate->format('d/m/Y'); // EndDate
                            $shop_email_data[$count]['enddate'] = '';
                        break;
                    }

                    // Brand Logo
                    $shop_email_data[$count]['brandlogopath'] = '';
                    $shop_email_data[$count]['brandid'] = $voucherprogram_data->getId();
                    $uploadedLogo = $voucherprogram_data->getImage();
                    $networkProvidedLogo = $voucherprogram_data->getLogoPath();
                    if (!empty($uploadedLogo)) {
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                        $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                        $shop_email_data[$count]['brandlogopath'] = $provider->generatePublicUrl($uploadedLogo, $format);
                    } elseif (!empty($networkProvidedLogo)) {
                        $shop_email_data[$count]['brandlogopath'] = $networkProvidedLogo;
                    }

                    // Cashback Settings for Getting Cashback Type Doubel Or Triple etc.. 

                    $cashbacksettingshop_query = 'SELECT cashback_settings_id FROM `lb_cachback_settings_shop`';
                    $cashbacksettingshop_condition = 'WHERE shop_id = :shop_id';

                    $cashbacksettingshop_statement = $connection->prepare($cashbacksettingshop_query.$cashbacksettingshop_condition);
                    $cashbacksettingshop_statement->bindValue('shop_id', $shop_data->getId());
                    $cashbacksettingshop_statement->execute();
                    $newsletter_shophistiry_ids = $cashbacksettingshop_statement->fetchAll();

                    $cashback_setting_id = '';
                    foreach ($newsletter_shophistiry_ids as $cbshpid) {
                        $cashback_setting_id = $cbshpid['cashback_settings_id'];
                    }

                    $cashbacksetting_data = $cashbacksetting_entity->findOneBy(array('id' => (int) $cashback_setting_id));

                    if (!empty($cashbacksetting_data)):
                        if ($cashbacksetting_data->getType()):
                            $shop_email_data[$count]['cashbacktype'] = $cashbacksetting_data->getType(); // Cashback Type Like :: Double, Triple ETC...
                        else:
                            $shop_email_data[$count]['cashbacktype'] = '';
                    endif;
                    endif;

                    //  Product URL Remaining
                    ++$count;
                }

                /* VOUCHER PROGRAMS DATA */
                /*print_r($newsletter_voucherprogramids);*/
                $voucher_programs_array = array();
                $countt = 0;
                foreach ($newsletter_voucherprogramids as $newslettervp) {
                    // BRAND URL ID
                    $voucher_programs_array[$countt]['brandurlid'] = $newslettervp;
                    $voucherprogram_data = $voucherprogram_entity->findOneBy(array('id' => (int) $newslettervp));

                    // Brand Logo
                    $voucher_programs_array[$countt]['brandlogo'] = '';
                    $uploadedLogo = $voucherprogram_data->getImage();
                    $networkProvidedLogo = $voucherprogram_data->getLogoPath();
                    if (!empty($uploadedLogo)) {
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                        $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                        $voucher_programs_array[$countt]['brandlogo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                    } elseif (!empty($networkProvidedLogo)) {
                        $voucher_programs_array[$countt]['brandlogo'] = $networkProvidedLogo;
                    }
                    // Brand Name
                    $voucher_programs_array[$countt]['brandname'] = $voucherprogram_data->getProgramName();

                    $vprogram_id = $voucherprogram_data->getId();

                    $voucher_program_query = 'SELECT id FROM `lb_shop`';
                    $voucher_program_condition = "WHERE vprogram_id = :vprogram_id AND offers='cashback'";
                    $voucher_program_statement = $connection->prepare($voucher_program_query.$voucher_program_condition);
                    $voucher_program_statement->bindValue('vprogram_id', $vprogram_id);
                    $voucher_program_statement->execute();
                    $shops_ids = $voucher_program_statement->fetchAll(); // shops

                    $shop_hostory_cashback = array();
                    $shcount = 0;
                    foreach ($shops_ids as $shp_id) {
                        $shophistoy_data = $newslshophistory_entity->findBy(array('shop' => (int) $shp_id['id']));

                        foreach ($shophistoy_data as $shop_hostory) {
                            $shop_hostory_cashback[$shcount]['shopid'] = $shop_hostory->getShop();
                            $shop_hostory_cashback[$shcount]['cashbackpercentage'] = $shop_hostory->getCashbackPercentage();
                            ++$shcount;
                        }
                    }

                    $max_cb_shopid = 0;
                    $max_cb_percentage = 0;
                    foreach ($shop_hostory_cashback as $shop) {
                        if ($max_cb_percentage < $shop['cashbackpercentage']):
                            $max_cb_percentage = $shop['cashbackpercentage'];
                        $max_cb_shopid = $shop['shopid'];
                        endif;
                    }

                    // MAXIMUM CASHBACK PERCENTAGE
                    $voucher_programs_array[$countt]['maximumcashbackpercentage'] = $max_cb_percentage;

                    // SHOP ID WITH MAXIMUM CASHBACK PERCENTAGE

                    $voucher_program_shop_query = 'SELECT `cashback_settings_id` FROM `lb_cachback_settings_shop`';
                    $voucher_program_shop_condition = 'WHERE shop_id = :shop_id';
                    $voucher_program_shop_statement = $connection->prepare($voucher_program_shop_query.$voucher_program_shop_condition);
                    $voucher_program_shop_statement->bindValue('shop_id', $max_cb_shopid);
                    $voucher_program_shop_statement->execute();
                    $cashbacksettings_id = $voucher_program_shop_statement->fetchAll();

                    $cashback_id = 0;
                    foreach ($cashbacksettings_id as $cb_id) {
                        $cashback_id = $cb_id['cashback_settings_id'];
                    }
                    $cashbacksettings_data = $cashbacksetting_entity->findBy(array('id' => (int) $cashback_id));
                    $cashback_type = '';
                    foreach ($cashbacksettings_data as $cash_data) {
                        $cashback_type = $cash_data->getType();
                    }

                // MAXIMUM CASHBACK TYPE :: DOUBLE OR TRIPPLE
                    $voucher_programs_array[$countt]['cashbacktype'] = $cashback_type;

                    $offer_count_query = 'SELECT id FROM `lb_voucher`';
                    $offer_count_condition = 'WHERE program_id = :program_id';
                    $offer_count_statement = $connection->prepare($offer_count_query.$offer_count_condition);
                    $offer_count_statement->bindValue('program_id', $newslettervp);
                    $offer_count_statement->execute();
                    $available_offers = $offer_count_statement->fetchAll(); // shops

                // TOTAL COUPENS AND OFFERS COUNT :: DONE
                    $voucher_programs_array[$countt]['availabletotaloffercount'] = count($available_offers);

                    ++$countt;
                }

                $banner_keys = array('mainbanner', 'first', 'second', 'third', 'forth', 'fifth', 'six', 'seven', 'eight', 'nine', 'ten');
                $banners_count = count($baners_urls);
                $banner_keys = array_slice($banner_keys, 0, $banners_count);

                $combined_bannners = array_combine($banner_keys, $baners_urls);

                $shop_block_title = $newsletter_transactions->getShopblocktitle();
                $voucher_block_title = $newsletter_transactions->getVoucherblocktitle();

                return array(
                    'combined_bannners' => $combined_bannners,
                    'shop_email_data' => $shop_email_data,
                    'voucher_programs_array' => $voucher_programs_array,
                    'shopblocktitle' => $shop_block_title,
                    'voucherblocktitle' => $voucher_block_title,
                    'newsletter_templet_name' => $newsletter_templet_name,
                    'newsletter_transactions' => $newsletter_transactions,
                );
            }
        }

        return false;
    }

/* CREATE ACTIVE STATE CAMPAIGN :: START */
    public function testCampaignAction()
    {
         $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);
        $response = $client->getListMembers(array(
            'id' => '4ed0308689'
        ));
        $user = $client->createCampaign(array(
            'id' => '4ed0308689',
            'email' => array(
                'email' => 'testing.testuser34@gmail.com',
            )
        ));

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $object = $this->admin->getSubject();
        $newsletter_id = $object->getId();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $newsletter_id));
        }
        $newsletter_entity = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter');
        $newsletter_campaign = $newsletter_entity->findOneById($newsletter_id);

        if (!empty($newsletter_campaign->getCampaignId())):

            $shop_bradcast_obj = new SoapApiHandler($this->getParameter('selligent_broadcast_soap'), $this->getParameter('selligent_user'), $this->getParameter('selligent_pasw'));

        $Camp_id = $newsletter_campaign->getCampaignId();
        $input = array();
        $input['CampaignID'] = $Camp_id;
        $input['NewState'] = 'ACTIVE';

        $selligent_response_campaign = $shop_bradcast_obj->call('SetCampaignState', $input);

            // if(!isset($selligent_response_campaign->ErrorStr)):
            $this->get('request')->getSession()->getFlashBag()->add('success', 'Campaign Id '.$Camp_id.' State Changed To ACTIVE');
        $url = $this->get('request')->headers->get('referer');

        return new RedirectResponse($url);
            // else:
            //  $this->get('request')->getSession()->getFlashBag()->add('error', 'Campaign Id '.$Camp_id.' State Can not be Change, Please Try Again Later');
            //  $url = $this->get('request')->headers->get('referer');
            //  return new RedirectResponse($url);
            // endif;
        else:

            $this->get('request')->getSession()->getFlashBag()->add('error', 'Campaign Not Generated.Please First Test Campaign!');
        $url = $this->get('request')->headers->get('referer');

        return new RedirectResponse($url);
        endif;
    }
/* CREATE ACTIVE STATE CAMPAIGN :: END */

    /* CREATE TEST STATE CAMPAIGN :: START */
    public function newslettertestAction()
    {
        $newsletter_id = $this->get('request')->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $newsletter_id));
        }

        $newsletterData = $this->getNewsletterData($em, $newsletter_id);
        $combined_bannners = $newsletterData['combined_bannners'];
        $shop_email_data = $newsletterData['shop_email_data'];
        $voucher_programs_array = $newsletterData['voucher_programs_array'];
        $shop_block_title = $newsletterData['shopblocktitle'];
        $voucher_block_title = $newsletterData['voucherblocktitle'];
        $newsletter_templet_name = $newsletterData['newsletter_templet_name'];
        $newsletter_transactions = $newsletterData['newsletter_transactions'];

        $newsletter_name = $newsletter_transactions->getNname();
        $newsletter_subject = $newsletter_transactions->getAsunto();
        //$newsletter_segment_id = $newsletter_transactions->getselligentId();
        /*echo "<pre>";
        print_r($combined_bannners);
        print_r($shop_email_data);
        print_r($voucher_programs_array);
        print_r($newsletter_id);
        print_r($shop_block_title);
        print_r($voucher_block_title);
        die();*/
        $startdate = $newsletter_transactions->getNdate()->format('YmdHis');

        $description = $newsletter_transactions->getNname().''.$startdate.''.$newsletter_transactions->getAsunto();

        $xml_data = $this->renderView(
                        "iFlairLetsBonusFrontBundle:NewsletterTemplate:$newsletter_templet_name",
                        array(
                            'combined_bannners' => $combined_bannners,
                            'shop_email_data' => $shop_email_data,
                            'voucher_programs_array' => $voucher_programs_array,
                            'shopblocktitle' => $shop_block_title,
                            'voucherblocktitle' => $voucher_block_title,
                        )
                    );
        /*$Constraints = array("MAIL not like 'kamal.joshi@iflair.com'","MAIL not like 'alkesh.sanghadiya@iflair.com'");
        $appy_Constraints = implode(",",$Constraints);*/

        $Xml = '<API>
                <CAMPAIGN NAME="'.$newsletter_name.'-'.$startdate.'" STATE="TEST" FOLDERID="'.$this->getParameter('selligent_folderid').'" START_DT="'.$startdate.'" TAG="" DESCRIPTION="'.$description.'" MACATEGORY="'.$this->getParameter('selligent_macategory').'" PRODUCTID="" CLASHPLANID="'.$this->getParameter('selligent_clashplanid').'" />
                <EMAILS>
                    <EMAIL NAME="'.$newsletter_name.'-'.$startdate.'" FOLDERID="'.$this->getParameter('selligent_folderid').'" MAILDOMAINID="'.$this->getParameter('selligent_maildomainid').'" LIST_UNSUBSCRIBE="'.$this->getParameter('selligent_list_unsubscribe').'" QUEUEID="'.$this->getParameter('selligent_queueid').'" TAG="" MACATEGORY="'.$this->getParameter('selligent_macategory').'" >
                        <TARGET LISTID="'.$this->getParameter('selligent_listid').'" PRIORITY_FIELD="'.$this->getParameter('selligent_priorityfield').'" PRIORITY_SORTING="'.$this->getParameter('selligent_prioritysorting').'" SEGMENTID="'.$newsletter_segment_id.'" CONSTRAINT="MAIL not like \'alkesh.sanghadiya@iflair.com\'" SCOPES="'.$this->getParameter('selligent_scopes').'" />
                        <CONTENT>
                            <HTML><![CDATA['.$xml_data.'
                                    ]]>
                            </HTML>
                            <TEXT><![CDATA[This is new Test Email]]></TEXT>
                            <FROM_ADDR><![CDATA['.$this->getParameter('selligent_from_addr').']]></FROM_ADDR>
                            <FROM_NAME><![CDATA['.$this->getParameter('selligent_from_name').']]></FROM_NAME>
                            <TO_ADDR><![CDATA[~MAIL~]]></TO_ADDR>
                            <TO_NAME><![CDATA[~NAME~]]></TO_NAME>
                            <REPLY_ADDR><![CDATA['.$this->getParameter('selligent_reply_addr').']]></REPLY_ADDR>
                            <REPLY_NAME><![CDATA['.$this->getParameter('selligent_reply_name').']]></REPLY_NAME>
                            <SUBJECT><![CDATA['.$newsletter_subject.']]></SUBJECT>
                        </CONTENT>
                    </EMAIL>
                </EMAILS>
            </API>';

        $xml = <<<XML
$Xml
XML;
        $shop_bradcast_obj = new SoapApiHandler($this->getParameter('selligent_broadcast_soap'), $this->getParameter('selligent_user'), $this->getParameter('selligent_pasw'));

        $input = array();
        $input['Xml'] = $xml;

        $selligent_response = $shop_bradcast_obj->call('CreateCampaign', $input);

        /*print_r($selligent_response);*/

        $campaign_data = $selligent_response->Xml;
        $campaign_xml = simplexml_load_string($campaign_data);
        $campaing_id = '';
        foreach ($campaign_xml->CAMPAIGN->attributes() as $param => $value) {
            if ($param == 'CAMPAIGNID') {
                $campaing_id = $value[0];
            }
        }
        if (!empty($campaing_id)) {
            $newsletter_campaign_entity = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter');
            $newsletter_transactions = $newsletter_campaign_entity->findOneById($newsletter_id);
            $newsletter_transactions->setCampaignId(trim($campaing_id));
            $em->persist($newsletter_transactions);
            $em->flush();

            $this->get('request')->getSession()->getFlashBag()->add('success', 'Campaign Created with id :: '.$campaing_id);
            $url = $this->get('request')->headers->get('referer');

            return new RedirectResponse($url);
        } else {
            $this->get('request')->getSession()->getFlashBag()->add('error', 'Campaign Not Created.. Please Fill Correct Data.!');
            $url = $this->get('request')->headers->get('referer');

            return new RedirectResponse($url);
        }
    }
    
    public function previewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $object = $this->admin->getSubject();
        $newsletter_id = $object->getId();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $newsletter_id));
        }

        $newsletterData = $this->getNewsletterData($em, $newsletter_id);
        $combined_bannners = $newsletterData['combined_bannners'];
        $shop_email_data = $newsletterData['shop_email_data'];
        $voucher_programs_array = $newsletterData['voucher_programs_array'];
        $shop_block_title = $newsletterData['shopblocktitle'];
        $voucher_block_title = $newsletterData['voucherblocktitle'];
       // $newsletter_templet_name = $newsletterData['newsletter_templet_name'].".html.twig";
        // print_r($combined_bannners);
    
        return $this->render("iFlairLetsBonusAdminBundle:NewsletterTemplate:template1.html.twig", array(
                'combined_bannners' => $combined_bannners,
                'shop_email_data' => $shop_email_data,
                'voucher_programs_array' => $voucher_programs_array,
                'newsletterid' => $newsletter_id,
                'shopblocktitle' => $shop_block_title,
                'voucherblocktitle' => $voucher_block_title,
            )
        );
    }


    public function newsletterHTMLAction($em , $newsletterid)
    {
         $newsletterData = $this->getNewsletterData($em, $newsletterid);
                $combined_bannners = $newsletterData['combined_bannners'];
                $shop_email_data = $newsletterData['shop_email_data'];
                $voucher_programs_array = $newsletterData['voucher_programs_array'];
                $shop_block_title = $newsletterData['shopblocktitle'];
                $voucher_block_title = $newsletterData['voucherblocktitle'];
                $newsletter_templet_name = $newsletterData['newsletter_templet_name'].".html.twig";
                
                $newsletterHTML = $this->render("iFlairLetsBonusAdminBundle:NewsletterTemplate:$newsletter_templet_name", array(
                        'combined_bannners' => $combined_bannners,
                        'shop_email_data' => $shop_email_data,
                        'voucher_programs_array' => $voucher_programs_array,
                        'newsletterid' => $newsletterid,
                        'shopblocktitle' => $shop_block_title,
                        'voucherblocktitle' => $voucher_block_title,
                    )
                );
                return $newsletterHTML;
    }

    public function addMailchimpCampaignDataAction($em,$newsltr_id,$title,$campaign_id,$status)
    {
        $newsletterReferance = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findOneBy(array('id'=>$newsltr_id));

        $Campaign = new MailchimpCampaign();
        $Campaign->setCampaignName($title);
        $Campaign->setCampaignId($campaign_id);
        $Campaign->setCampaignStatus($status);
        $em->persist($Campaign);
        $em->flush();

        $campaignReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaign')->findOneBy(array('campaign_id'=>$campaign_id));

        $CampaignNewsletter = new MailchimpCampaignNewsletterStatus();
        $CampaignNewsletter->setCampaignId( $campaignReferance );
        $CampaignNewsletter->setNewsletterId($newsletterReferance);
        $CampaignNewsletter->setCampaignNewsletterStatus(0);
        $em->persist($CampaignNewsletter);
        $em->flush();

    }

    public function zfrCreateCampaignAction($listId,$newsletter_name,$apikey,$newsletterHTML,$client,$segmentReferance)
    {
        $campaign = $client->createCampaign(array(
            'apikey' => $apikey,
            "type" => "regular",
            'options' => array(
                'list_id' => $listId,
                "subject" => $newsletter_name,
                "from_email"=> "no-reply@shoppiday.com",
                "from_name"=> "newsletter",
                "to_name"=> "newsletter",
            ),
            "content"=> array(
                'html'=> $newsletterHTML,
             ),
             'segment_opts' => array(
                'saved_segment_id' => $segmentReferance,
                ),
        ));
        return $campaign;
    }

    public function zfrUpdateCampaignAction($client,$apikey,$getCampaignId,$newsletterHTML,$content,$options,$getNname)
    {
       $campaign = $client->updateCampaign(array(
                                'apikey' => $apikey,
                                'cid' => $getCampaignId,
                                'name' => $content,
                                'value' => array(
                                    'html'=> $newsletterHTML,
                                ),
                            ));
                       
        $campaign1 = $client->updateCampaign(array(
                'apikey' => $apikey,
                'cid' => $getCampaignId,
                'name' => $options,
                'value' => array(
                    "subject" => $getNname,
                    'title' => $getNname,
                ),
            ));
      
    }
     
    public function zfrSegmentCreateAction($em,$client,$apikey,$newsletterReferance_value)
    {
        try
            {
                $addSegment = $client->addStaticListSegment(array(
                    'apikey' => $apikey,
                    'id' => $newsletterReferance_value->getList()->getListId(),
                    'name'=>$newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId(),
                    ));

                $segmentInfo = new MailchimpSegmentListNewsletter();
                $segmentInfo->setSegmentId($addSegment["id"]);
                $segmentInfo->setSegmentName($newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId());
                $segmentInfo->setList($newsletterReferance_value->getList());
                $segmentInfo->setNewsletter($newsletterReferance_value);
                $em->persist($segmentInfo);
                $em->flush();
            }
            catch(\ZfrMailChimp\Exception\Ls\InvalidOptionException $e)
            {
                 $getSegment = $client->getListStaticSegments(array(
                     'apikey' => $apikey,
                            'id' => $newsletterReferance_value->getList()->getListId(),  
                    ));

                foreach ($getSegment as $getSegmentkey => $getSegmentvalue) 
                {
                if($getSegmentvalue["name"] == $newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId())
                    {
                        $segmentId = $getSegmentvalue["id"];
                    }
                }
              
                $segmentInfo = new MailchimpSegmentListNewsletter();
                $segmentInfo->setSegmentId($segmentId);
                $segmentInfo->setSegmentName($newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId());
                $segmentInfo->setList($newsletterReferance_value->getList());
                $segmentInfo->setNewsletter($newsletterReferance_value);
                $em->persist($segmentInfo);
                $em->flush();
            }
    }

    public function campaigncreateAction($id = null)
    {   
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
         $previous_url = $this->getRequest()->headers->get('referer');
        $query = $connection->prepare('SELECT l.list_id, n.nname AS newsletter_name, n.id AS newsletter_id
                                        FROM lb_mailchimp_lists AS l
                                        LEFT JOIN lb_newsletter AS n ON l.id = n.list_id 
                                        WHERE n.id = :newsletterid ');
        $query->bindValue('newsletterid', $id);
        $query->execute();
        $listId = $query->fetchAll();

         $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);

        $newsletterHTML = $this->newsletterHTMLAction($em,$id);

        $newsletterReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaignNewsletterStatus')->findOneBy(array('newsletter_id'=>$id));

        $nwsReferance = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findOneBy(array('id'=>$id));

        $segmentReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=>$nwsReferance));

        $segmentStatus = "";
        if(is_null($segmentReferance)== false){ $segmentStatus = $segmentReferance->getId();}


        if(!$newsletterReferance)
        {   
            foreach ($listId as $list_key => $list_value) 
            {
                if(empty($segmentStatus))
                {
                    $this->zfrSegmentCreateAction($em,$client,$apikey,$nwsReferance);
                    $segmentReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=>$nwsReferance));
                }


                $campaign = $this->zfrCreateCampaignAction($list_value['list_id'],$list_value['newsletter_name'],$apikey,$newsletterHTML->getContent(),$client,$segmentReferance->getSegmentId());
                $this->addMailchimpCampaignDataAction($em,$id, $campaign["title"],$campaign["id"],$campaign["status"]);
            }
        }
        else
        {
            $query = $connection->prepare('SELECT campaign_newsletter_status,campaign_id FROM lb_mailchimp_campaign_newsletter_status WHERE newsletter_id = :newsletterid ORDER BY id DESC LIMIT 1');
            $query->bindValue('newsletterid', $id);
            $query->execute();
            $lastnewletterstatus = $query->fetchAll();

            $newsletterReferance = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findOneBy(array('id'=>$id));
                   
            $d1 = $newsletterReferance->getCreated()->format('Y-m-d H:i:s');
            $d2 = $newsletterReferance->getModified()->format('Y-m-d H:i:s');

            foreach ($lastnewletterstatus as $key => $lastnewletterstatus_value) 
            {
                if($lastnewletterstatus_value['campaign_newsletter_status'] == 0)
                {
                    if($d1 != $d2)
                    {
                        // update campain as its mail sending status is 0
                        $campaignReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaign')->findOneBy ( array('id' =>$lastnewletterstatus_value['campaign_id']));

                        $this->zfrUpdateCampaignAction($client,$apikey,$campaignReferance->getCampaignId(),$newsletterHTML->getContent(),'content','options', $newsletterReferance->getNname());

                        $campaignReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaign')->findOneBy(array('campaign_id'=>$campaignReferance->getCampaignId()));
                        $campaignReferance->setCampaignName($newsletterReferance->getNname());
                        $em->persist($campaignReferance);
                        $em->flush();
                    }
                }
                elseif($lastnewletterstatus_value['campaign_newsletter_status'] == 1)
                {
                    if($d1 != $d2)
                    {
                        // add new campain as its status is 1 and that campain is modified
                        foreach ($listId as $list_key => $list_value) 
                        { 
                            if(empty($segmentStatus))
                            {
                                $this->zfrSegmentCreateAction($em,$client,$apikey,$nwsReferance);
                                $segmentReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=>$nwsReferance));
                            }

                            $campaign = $this->zfrCreateCampaignAction($list_value['list_id'],$list_value['newsletter_name'],$apikey,$newsletterHTML->getContent(),$client,$segmentReferance->getSegmentId());

                            $this->addMailchimpCampaignDataAction($em,$id, $campaign["title"],$campaign["id"],$campaign["status"]);
                        }
                    }
                    else if($d1 == $d2)
                    {
                        // replicate campain as its status is 1 and that campain is not modified
                        
                        $campaignReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaign')->findOneBy ( array('id' =>$lastnewletterstatus_value['campaign_id']));

                        $replicateCampaign = $client->replicateCampaign(array(
                            'apikey' => $apikey,
                            'cid' => $campaignReferance->getCampaignId(),
                        ));
                       
                        $this->addMailchimpCampaignDataAction($em,$id, $replicateCampaign["title"],$replicateCampaign["id"],$replicateCampaign["status"]);
                    }
                }
            }


        }
        return $this->redirect($previous_url);

    }

    public function campaignsendAction($id = null)
    {   
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $previous_url = $this->getRequest()->headers->get('referer');
        $query = $connection->prepare('SELECT c.campaign_id, cns.campaign_newsletter_status AS status, cns.campaign_id AS campId
                                        FROM lb_mailchimp_campaign AS c
                                        LEFT JOIN lb_mailchimp_campaign_newsletter_status AS cns ON cns.campaign_id = c.id
                                        WHERE cns.newsletter_id = :newsletter_id ORDER BY cns.id DESC LIMIT 1');
     
        $query->bindValue('newsletter_id', $id);
        $query->execute();
        $campaignId = $query->fetchAll();


         $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);

        if(!empty($campaignId))
        {
           foreach ($campaignId as $campaign_key => $campaign_value) 
            {
                if($campaign_value['status'] == 0)
                {
                    $sendCampaign = $client->sendCampaign(array(
                        'apikey' => $apikey,
                        'cid' => $campaign_value['campaign_id'],
                    ));
                
                    $campaignReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaignNewsletterStatus')->findOneBy(array('newsletter_id'=>$id, 'campaign_id' => $campaign_value['campId']));
                
                    $campaignReferance->setCampaignNewsletterStatus(1);
                    $em->persist($campaignReferance);
                    $em->flush();
                }
                else
                {
                    echo "please do replication";
                }
            }
        }
        else{
            echo "no campaign found for sending";
        }
        return $this->redirect($previous_url);
    }

    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::deleteAction()
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
         $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);
        $request = $this->getRequest();
        $id      = $request->get($this->admin->getIdParameter());
        $object  = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if ($this->getRestMethod() == 'DELETE') {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            try{
                $campaignArr = array();
                $newsletterReferance=$em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findOneBy(array('id'=>$id));
                $segmentReferance=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=> $newsletterReferance )); 
                $mailchimpUserListStatus = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findBy(array('segment_id' => $segmentReferance));
                if( $mailchimpUserListStatus)
                {
                    foreach ($mailchimpUserListStatus as $mailchimpUserListStatus_key => $mailchimpUserListStatus_value) {
                         $deleteSegmentUser = $client->deleteStaticSegmentMembers(array(
                            'apikey' => $apikey,
                            'id' => $newsletterReferance->getList()->getListId(),
                            'seg_id' => $segmentReferance->getSegmentId(),
                            'batch'=>array(array('email'=>$mailchimpUserListStatus_value->getUserId()->getSEmail()))
                        ));

                        $em->remove($mailchimpUserListStatus_value);
                        $em->flush();
                    }
                }
                $mailchimpUserListStatus = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findBy(array('segment_id' => $segmentReferance));
                if(!$mailchimpUserListStatus)
                {
                    $deleteSegment = $client->deleteListSegment(array(
                            'apikey' => $apikey,
                            'id' => $newsletterReferance->getList()->getListId(),
                            'seg_id' => $segmentReferance->getSegmentId(),
                            ));

                    $em->remove($segmentReferance);
                    $em->flush();
                }

                $MailchimpCampaignNewsletterStatus = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaignNewsletterStatus')->findBy(array('newsletter_id' => $newsletterReferance));
                 if( $MailchimpCampaignNewsletterStatus)
                {
                    foreach ($MailchimpCampaignNewsletterStatus as $MailchimpCampaignNewsletterStatus_key => $MailchimpCampaignNewsletterStatus_value) {
                        
                        $campaignArr[] = $MailchimpCampaignNewsletterStatus_value->getCampaignId()->getId();
                        $em->remove($MailchimpCampaignNewsletterStatus_value);
                        $em->flush();
                    }
                }

                if(!empty($campaignArr))
                {
                    foreach ($campaignArr as $campaignArr_key => $campaignArr_value) {
                        $campaign = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaign')->findOneBy(array('id' => $campaignArr_value));
                        if($campaign)
                        {
                            $deleteCampaign = $client->deleteCampaign(array(
                                'apikey' => $apikey,
                                'cid' => $campaign->getCampaignId(),
                            ));

                            $em->remove($campaign);
                            $em->flush();
                        }
                    }
                }
              
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
            'object'     => $object,
            'action'     => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
        ), null);
    }
}
