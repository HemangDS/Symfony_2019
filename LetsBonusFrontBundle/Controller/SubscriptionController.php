<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use iFlair\LetsBonusFrontBundle\Entity\Subscription;
use iFlair\LetsBonusAdminBundle\Entity\Newsletter;
use iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpLists;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpSubscription;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpUserListStatus;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpSegmentListNewsletter;
use ZfrMailChimp\Client\MailChimpClient;
use iFlair\LetsBonusAdminBundle\Controller\NewsletterAdmin;

/* SELLIGENT */
use PhpAmqpLib\Message\AMQPMessage;
use Letsbonus\SelligentSyncBundle\Service\Consumer\InsertDataConsumer;
use Letsbonus\SelligentSyncBundle\Service\Selligent\SoapApiHandler;
use Symfony\Bridge\Monolog\Logger;
/* SELLIGENT */
/* JWT Conversion */
use Firebase\JWT\JWT;

/* JWT Conversion */

class SubscriptionController extends Controller
{
    public function mailChimpStatus($mailChimpApi,$listId,$email)
    {
         /* to get user subscribtion status*/
             try {
                    $info = $mailChimpApi->getListMembersInfo(array(
                        'id' => $listId,
                        'emails' => array(
                                array(
                                       'email' => $email,
                                    ),
                               )
                        ));
                    if(!empty($info['data']))
                        {
                            $count = count($info['data'])-1;
                            $status = $info['data'][$count]["status"];
                        }
                    else
                        {
                            $status = 'pending';
                        }
                    
                }   
                catch (\Guzzle\Service\Exception\ValidationException $e) 
                {
                        $message = $e->getMessage();
                        echo $message;
                        exit();
                }

            return $status;
      /* End user subscribtion status*/
    }

    public function getLists($em,$mailchimpObj, $apikey)
    {
        $listArr = $mailchimpObj->getLists(array(
                    'apikey' => $apikey,
            ));
        $i = 0;
        foreach ($listArr['data'] as $key => $valueArr) {
          
                $listId[$i]['id']  = $valueArr['id'];
                $listId[$i]['name']  = $valueArr['name'];
                $i++;  

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
    
        return $listId;
    }


    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request_data = $request->request->all();
        $connection = $em->getConnection();
        $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);
        $lists = $this->getLists($em, $client, $apikey);
        $previous_url = $this->getRequest()->headers->get('referer');
        $userEmailReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSubscription')->findOneBy(array('sEmail' =>  $request_data['subscriptionemail']));
        $isRegistered = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('email' =>  $request_data['subscriptionemail']));
        if($isRegistered){$regStatus = "yes";} else{$regStatus = "no";}
        
        foreach ($lists as $list_key => $list_value) 
        {
            $listId = $list_value['id'];
            $listReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpLists')->findOneBy(array('list_id'=>$listId));
            $newsletterReferance=$em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findBy(array('list'=>$listReferance->getId()));
                    
            foreach ($newsletterReferance as $newsletterReferancekey => $newsletterReferance_value) 
            {
            $segmentExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=> $newsletterReferance_value , 'list' => $newsletterReferance_value->getList())); 

                $segmentStatus = "";
                if(is_null($segmentExist)== false){ $segmentStatus = $segmentExist->getSegmentId();}
                if(empty($segmentStatus))
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

                $userStatus = "";
                if(is_null($userEmailReferance)== false){ $userStatus = $userEmailReferance->getId();}

                if(empty($userStatus))
                {
                    $Subscription = new MailchimpSubscription();
                    $Subscription->setSEmail(trim($request_data['subscriptionemail']));
                    $Subscription->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($Subscription);
                    $em->flush();
                }

                $segmentExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=> $newsletterReferance_value , 'list' => $newsletterReferance_value->getList()));
                $mailChimpStatus = $this->mailChimpStatus($client,$newsletterReferance_value->getList()->getListId(),$request_data['subscriptionemail']);
                $userEmailReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSubscription')->findOneBy(array('sEmail' =>  $request_data['subscriptionemail']));
                
                if($mailChimpStatus != 'subscribed')
                    {
                       /* Add subscriber */
                        $subscribe = $client->subscribe(array(
                            'apikey' => $apikey,
                            'id' => $newsletterReferance_value->getList()->getListId(),
                            'email' => array(
                                'email' => $request_data['subscriptionemail'],
                              ),
                            'double_optin' => false,
                        ));  
                       /* End Add subscriber */
                    }

                $mailChimpStatus = $this->mailChimpStatus($client,$newsletterReferance_value->getList()->getListId(),$request_data['subscriptionemail']);

                $mailchimpUserListStatus = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('user_id' => $userEmailReferance, 'list_id' => $newsletterReferance_value->getList(), 'segment_id' => $segmentExist));

                $mailchimpUserListStatusVariable = "";
                if(is_null($mailchimpUserListStatus)== false){ $mailchimpUserListStatusVariable = $mailchimpUserListStatus->getId();}

                if(empty($mailchimpUserListStatusVariable))
                {
                    $userInfo = new MailchimpUserListStatus();
                    $userInfo->setUserId($userEmailReferance);
                    $userInfo->setListId($newsletterReferance_value->getList());
                    $userInfo->setUserMailchimpStatus($mailChimpStatus);
                    $userInfo->setSegmentId($segmentExist);
                    $userInfo->setUserRegistered($regStatus);
                    $userInfo->setUserSegmentStatus("Yes");
                    $em->persist($userInfo);
                    $em->flush();  
                }

                $addSubscriber = $client->addStaticSegmentMembers(array(
                'apikey' => $apikey,
                'id' => $newsletterReferance_value->getList()->getListId(),
                'seg_id'=>$segmentExist->getSegmentId(),
                'batch'=>array(array('email'=>$request_data['subscriptionemail']))
                ));

            
                if($addSubscriber["success_count"] == 1)
                {
                    $userExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('list_id'=> $newsletterReferance_value->getList() , 'user_id' => $userEmailReferance, 'segment_id' => $segmentExist));

                    $userExistStatus = "";
                    if(is_null($userExist)== false){ $userExistStatus = $userExist->getId();}

                    if($userExistStatus != "")
                    {
                        $userExist->setUserMailchimpStatus($mailChimpStatus);
                        $userExist->setUserSegmentStatus("Yes");
                        $em->persist($userExist);
                        $em->flush();  
                    }
                    else if($userExistStatus == "")
                    {
                        $userInfo = new MailchimpUserListStatus();
                        $userInfo->setUserId($userEmailReferance);
                        $userInfo->setListId($newsletterReferance_value->getList());
                        $userInfo->setUserMailchimpStatus($mailChimpStatus);
                        $userInfo->setSegmentId($segmentExist);
                        $userInfo->setUserRegistered($regStatus);
                        $userInfo->setUserSegmentStatus("Yes");
                        $em->persist($userInfo);
                        $em->flush();  
                    }

                   /* $subscribeSegments = $client->getListStaticSegments(array(
                        'apikey' => $apikey,
                        'id' => $newsletterReferance_value->getList()->getListId(),
                    )); 
                    foreach ($subscribeSegments as $subscribeSegments_key => $subscribeSegments_value) 
                        {   
                           // $NewsletterAdminController = $this->get('campaigncreate_newsletter');
                            //$newHTML = $NewsletterAdminController->newsletterHTMLAction($em,$newsletterReferance_value->getId());
                            //$campaign = $NewsletterAdminController->zfrCreateCampaignAction($em,$connection,$newsletterReferance_value->getId());
                            $campaign = $client->createCampaign(array(
                                    'apikey' => $apikey,
                                    "type" => "regular",
                                    'options' => array(
                                        'list_id' => $newsletterReferance_value->getList()->getListId(),
                                        "subject" =>  $newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId(),
                                        "from_email"=> "mar.tomas@shoppiday.com",
                                        "from_name"=> $newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId(),
                                        "to_name"=> $newsletterReferance_value->getNname()."-".$newsletterReferance_value->getId(),
                                    ),
                                    "content"=> array(
                                        'html'=> "<h1>segment static campaign</h1>",
                                     ),
                                    'segment_opts' => array(
                                        'saved_segment_id' => $subscribeSegments_value["id"],
                                        ),
                                ));
                            if(empty($sendCampaign))
                            {
                                $campaignSelect=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpCampaignNewsletterStatus')->findOneBy(array('newsletter_id'=> $newsletterReferance_value->getId()));

                                $campaignExistStatus = "";
                                if(is_null($campaignSelect)== false){ $campaignExistStatus = $campaignSelect->getId();}

                                if(!empty($campaignExistStatus))
                                {
                                      $sendCampaign = $client->sendCampaign(array(
                                        'apikey' => $apikey,
                                        'cid' => $campaignSelect->getCampaignId()->getCampaignId(),
                                    ));
                                }
                            }
                            else
                            {
                                $sendCampaign = $client->sendCampaign(array(
                                    'apikey' => $apikey,
                                    'cid' => $campaign["id"],
                                ));
                            }
                

                            echo "<pre>";
                            print_r($campaign);
                            exit();
                        }*/
                    }
            }
        }
        
        return $this->redirect($previous_url);

    }

    public function unsubscriptionAction(Request $request)
    {
        $request = $this->getRequest();
        $data = $request->query->get('m_u'); // get a $_GET parameter
        $result = JWT::decode(trim($data), 'my_key', array('HS256'));
        $subscribed_email = $result->MAIL;

        $em = $this->getDoctrine()->getEntityManager();
        $request_data = $request->request->all();
  
       $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);
        $lists = $this->getLists($em, $client, $apikey);

        try{
            
            foreach ($lists as $list_key => $list_value) 
                {
                    $listId = $list_value['id'];
                    $mailChimpStatus = $this->mailChimpStatus($client,$listId,$request_data['unsubscriptionemail']);

                    if($mailChimpStatus == 'subscribed')
                    {
                        $unsubscribe = $client->unsubscribe(array(
                            'apikey'=>$apikey,
                            'id' => $listId,
                            'email' => array(
                                'email' => $request_data['unsubscriptionemail'],
                              ),
                            'delete_member'=> false,
                            'send_goodbye'=>  true,
                            'send_notify'=>  true
                        ));
                    }
                    
                    $userEmailReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSubscription')->findOneBy(array('sEmail' =>  $request_data['unsubscriptionemail']));
                    $listReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpLists')->findOneBy(array('list_id'=>$listId));

                    if (!empty($userEmailReferance) && !empty($listReferance))   
                    {
                        $userStatusReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('list_id'=>$listReferance->getId(),'user_id'=>$userEmailReferance->getId() ));

                        if($userStatusReferance)
                        {
                            $userStatusReferance->setUserMailchimpStatus("unsubscribed");
                            $em->persist($userStatusReferance);
                            $em->flush();
                        }   
                    }
                }
        }
        catch (ZfrMailChimp\Exception\Email\NotExistsException $e) 
        {
            $message = $e->getMessage();
            echo $message;
        }
        catch (ZfrMailChimp\Exception\Email\NotSubscribedException $e)
        {
           $message = $e->getMessage();
            foreach ($lists as $list_key => $list_value) 
            {
                $listId = $list_value['id'];
                $userEmailReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSubscription')->findOneBy(array('sEmail' =>  $request_data['unsubscriptionemail']));
                $listReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpLists')->findOneBy(array('list_id'=>$listId));
                $userStatusReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('list_id'=>$listReferance->getId(),'user_id'=>$userEmailReferance->getId() ));

                if($userStatusReferance)
                {
                    $userStatusReferance->setUserMailchimpStatus("unsubscribed");
                    $em->persist($userStatusReferance);
                    $em->flush();
                }   
            }
        }
        catch (ZfrMailChimp\Exception\Ls\DoesNotExistException $e)
        {
            $message = $e->getMessage();
            echo $message;
        }
         catch (ZfrMailChimp\Exception\Ls\NotSubscribedException $e)
        {
            $message = $e->getMessage();
            echo $message;
        }
        exit();
    
    }
    public function unsubscriptionProcessAction(Request $request)
    {
        $subscriber_email = $request->request->get('subscriber');
        $subscribed_id = $request->request->get('subscriberid');
        $em = $this->getDoctrine()->getManager();

        $subscription_data = $request->request->all();
        $selected_newsletter_array = array();
        foreach ($subscription_data as $selectin) {
            if (is_array($selectin) || is_object($selectin)):
                foreach ($selectin as $key => $sub) {
                    $selected_newsletter_array[$key] = $sub;
                }
            endif;
        }

        unset($selected_newsletter_array['_token']);
        $subscription_section = array();
        foreach ($selected_newsletter_array as $selection) {
            foreach ($selection as $key => $value) {
                $subscription_section[$key] = $value;
            }
        }
        /*print_r($subscription_section);*/
        /*$subscribed = $this->getDoctrine()->getRepository('iFlairLetsBonusFrontBundle:Subscription')->findBySEmail(trim($subscriber_email));
        $subscribed_id = '';
        foreach($subscribed as $s_id)
        {
            $subscribed_id = $s_id->getId();
        }*/
        foreach ($subscription_section as $subscritpion_status) {
            if (strpos($subscritpion_status, 'Yes') !== false):
                /* Update Entry To Enable */
                $sub_id = explode('Yes', $subscritpion_status);
            $subscrition_p_id = trim($sub_id[0]);
            $subscrition_p_id = str_replace(' ', '', $subscrition_p_id);
            $subscription_enable = $this->getDoctrine()->getRepository('iFlairLetsBonusFrontBundle:NewsletterSubscriptionUsers')->findOneBy(array('id' => $subscrition_p_id));
            $subscription_enable->setStatus(1);
            $em->persist($subscription_enable);
            $em->flush(); elseif (strpos($subscritpion_status, 'No') !== false):
                /* Update Entry To Disable */
                $sub_id1 = explode('No', $subscritpion_status);
            $subscrition_n_id = trim($sub_id1[0]);
            $subscrition_n_id = str_replace(' ', '', $subscrition_n_id);
            $subscription_disable = $this->getDoctrine()->getRepository('iFlairLetsBonusFrontBundle:NewsletterSubscriptionUsers')->findOneBy(array('id' => $subscrition_n_id));
            $subscription_disable->setStatus(0);
            $em->persist($subscription_disable);
            $em->flush(); elseif (strpos($subscritpion_status, 'New') !== false):
                /* New Entry */
                $sub_id2 = explode('New', $subscritpion_status);
            $new_id = $sub_id2[0];
            $news_e_sub = new NewsletterSubscriptionUsers();
            $subscription_obj = $em->getRepository('iFlairLetsBonusFrontBundle:Subscription')->findOneBy(array('id' => trim($subscribed_id)));
            $news_e_sub->setSubscriptionId($subscription_obj);
            $newsletter_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findOneBy(array('id' => trim($new_id)));
            $news_e_sub->setNewsletterId($newsletter_obj);
            $news_e_sub->setStatus(1);
            $em->persist($news_e_sub);
            $em->flush();
            endif;
        }

        $this->get('request')->getSession()->getFlashBag()->add('success', 'Subscription Successfully Updated.!');
        $url = $this->generateUrl('i_flair_lets_bonus_front_ubscriptionsuccesss');

        return new RedirectResponse($url);
    }
    public function unsubscribedAction(Request $request)
    {
        return $this->render('iFlairLetsBonusFrontBundle:Subscription:unsubscriptionsuccess.html.twig');
    }
}
