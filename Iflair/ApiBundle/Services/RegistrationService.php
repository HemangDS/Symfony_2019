<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;

class RegistrationService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * User Registartion method details, user will be register when called.
     * @param string $name 
     * @return mixed
     */
    public function registration($firstname,$username,$email,$lastname,$pictureurl,$uniqueid,$logintype,$flag)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        if($firstname == '' || $firstname == ' ')
        {
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_BAD_REQUEST,
                'content' => array(
                 'secondary_content' => 'Please Enter Firstname.',
                )
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }
        if($email == '')
        {
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_BAD_REQUEST,
                'content' => array(
                 'secondary_content' => 'Please Enter Email address.',
                )
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$",$email)) {
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_BAD_REQUEST,
                'content' => array(
                 'secondary_content' => 'Invalid Email Address.',
                )
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }

        if($user){

            /* Profile Count  */
            $user_id = $user->getId();
            $festivals = $em->getRepository('IFlairFestivalBundle:festival_view')->findBy(array('userId' => $user_id));
            $festival_ids = array();
            foreach($festivals as $festival)
            {
                if($festival->getFestivalId())
                    $festival_ids[] = $festival->getFestivalId()->getId();
            }
            $partyfinders = $em->getRepository('IFlairSoapBundle:Partyfinderviews')->findBy(array('viewedUserId' => $user_id));
            $viewed_party_locations = array();
            $bar = array(); $nightclub = array();
            foreach($partyfinders as $party)
            {
                $type = $party->getPartyFinderId()->getclubtypeid()->getName();
                if($type == 'Bar'){
                    if($party->getPartyFinderId())
                        $bar[] = $party->getPartyFinderId()->getId();
                }else{
                    if($party->getPartyFinderId())
                    $nightclub[] = $party->getPartyFinderId()->getId();
                }
            }
            $total_checkin_count = 0; 
            $total_checkin_count = count(array_unique($festival_ids));
            $total_checkin_count += count(array_unique($bar));
            $total_checkin_count += count(array_unique($nightclub));
            

            // Contribution Data
            $total_contribution = 0;
            $festival_contribution = $em->getRepository('IFlairFestivalBundle:ContributionAdddFestival')->findBy(array('userId' => $user_id));
            $festival_id = array();
            foreach($festival_contribution as $contribution)
            {
                $festival_id[] = $contribution->getId();
            }
            $party_contribution = $em->getRepository('IFlairSoapBundle:ContributionAddParty')->findBy(array('userId' => $user_id));
            $party_ids = array();
            foreach($party_contribution as $party)
            {
                $party_ids[] = $party->getId();
            }
            $total_contribution = count($festival_id) + count($party_ids);

            $user->setUsername($user->getUsername());
            $user->setPassword(md5(uniqid()));
            $user->setEmail($email);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setPictureurl($pictureurl);
            $user->setUniqueid($uniqueid);
            $user->setLogintype($logintype);
            $user->setFlag('1');
            $em->persist($user);
            $em->flush();
            $last_inserted_id = $user->getId();

            // Total approved uploaded images.
            $partyuploaded_images_entity = $em->getRepository('IFlairFestivalBundle:FestivalInprogressMultipleImageUpload')->findBy(array('userId' => $user->getId(),'isApproved' => '1'));
            $uploaded_ids = array();
            foreach($partyuploaded_images_entity as $uploaded_image)
            {
                $uploaded_ids[] = $uploaded_image->getId();
            }
            $festivaluploaded_images_entity = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressMultipleImageUpload')->findBy(array('userId' => $user->getId(),'isApproved' => '1'));        
            foreach($festivaluploaded_images_entity as $uploaded_image)
            {
                $uploaded_ids[] = $uploaded_image->getId();
            }

            $setting_loaded1 = $this->doctrine->getRepository('IFlairSoapBundle:Settings')->findOneBy(array('userId' => $last_inserted_id));

            if($user->getUsername() != $user->getEmail() && $user->getUsername() != '')
            {   
                $flag = '2';
                if($user->getCountryId()){
                    $countryName = $user->getCountryId()->getCountryName();
                    $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
                    $country_image = $image_path.strtolower($countryName).'.png';
                }else{
                    $country_image = '';
                    $flag = '3';

                    /* Get country name, image, id */
                    $queryBuilder = $em->createQueryBuilder();
                    $queryBuilder->select('pfc.id','pfc.countryName')
                    ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfc');
                    $result = $queryBuilder->getQuery()->getResult();
                    $country_list = array();
                    $count = 0;
                    $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
                    foreach($result as $res)
                    {
                        $country_list[$count]['id'] = $res['id'];
                        $country_list[$count]['logo'] = $image_path.strtolower(str_replace(' ', '_', $res['countryName'])).'.png';
                        $country_list[$count]['name'] = $res['countryName'];
                        $count++;
                    }
                }

                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'id' => $last_inserted_id,
                    'pictureurl' => $pictureurl,
                    'username' => $user->getUsername(),
                    'fullname' =>$firstname.' '.$lastname,
                    'checkin_count' => $total_checkin_count,
                    'standard_range' => $setting_loaded1->getStandardRange(),
                    'country_image' => $country_image,
                    'country_list' => $country_list,
                    'contribution_count' => $total_contribution,
                    'uploaded_images_count' => count($uploaded_ids),
                    'flag' => $flag,
                    'content' => array(
                     'secondary_content' => 'User added Successfully.'
                    )
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else{

                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'id' => $last_inserted_id,
                    'username' => $username,
                    'pictureurl' => $pictureurl,
                    'username' => $username,
                    'standard_range' => $setting_loaded1->getStandardRange(),
                    'country_list' => $country_list,
                    'fullname' =>$firstname.' '.$lastname,
                    'checkin_count' => $total_checkin_count,
                    'uploaded_images_count' => count($uploaded_ids),
                    'contribution_count' => $total_contribution,
                    'flag' => '1',
                    'content' => array(
                     'secondary_content' => 'User added Successfully.'
                    )
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }
        }else{
        $em = $this->doctrine->getManager();
        $user1 = new User();
        $user1->setUsername($email);
        $user1->setUsernameCanonical($email);
        $user1->setPassword(md5(uniqid()));
        $user1->setEmail($email);
        $user1->setFirstname($firstname);
        $user1->setLastname($lastname);
        $user1->setPictureurl($pictureurl);
        $user1->setUniqueid($uniqueid);
        $user1->setLogintype($logintype);
        $user1->setFlag('1');
        $user1->setCountryId(NULL);
        $em->persist($user1);
        $em->flush();
        $last_inserted_id = $user1->getId();
        $setting_loaded1 = $this->doctrine->getRepository('IFlairSoapBundle:Settings')->findOneBy(array('userId' => $last_inserted_id));
        if($setting_loaded1){
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,
                'flag' => '1',
                'standard_range' => $setting_loaded1->getStandardRange(),
                'id' => $last_inserted_id,
                'content' => array(
                 'secondary_content' => 'User Registered Sucessfully.'
                )
            );
        }else{
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,
                'flag' => '1',
                'standard_range' => '0',
                'id' => $last_inserted_id,
                'content' => array(
                 'secondary_content' => 'User Registered Sucessfully.'
                )
            );
        }
            $finalResponse = json_encode($myresponse);
            /* insert default setting data into database :: START */
            $last_user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $last_inserted_id));
            $settings = new Settings();
            $settings->setDistance('Km');
            $settings->setCurrency('USD');
            $settings->setStandardRange('16');
            $settings->setShowClosedLocation('1');
            $settings->setShowAfterhourClubs('1');
            $settings->setPreferedMusicGenres('Black Music');
            $settings->setAllowGps('0');
            $settings->setAllowPushMessages('1');
            $settings->setReceiveNewsletter('1');
            $settings->setDateFormat('DD/MM/YYYY');
            $settings->setDateHours('1');
            $settings->setModifiedTime(new \DateTime());
            $settings->setUserId($last_user);
            $em->persist($settings);
            $em->flush();
            /* END */
            return $finalResponse;
        }
    }
}
