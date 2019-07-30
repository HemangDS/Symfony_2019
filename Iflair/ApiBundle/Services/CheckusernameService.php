<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;

class CheckusernameService
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
	public function checkusername($username,$id,$image_path)
	{
		$em = $this->doctrine->getManager();
		$user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $id));
		$found_username = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
		if($found_username)
		{
			$suggested_username = array();
			$count = 0;
			for($i = 0;$i < 100;$i++)
	        {
	            $check_username = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('username' => $username.$i));
	            if(empty($check_username))
	            {
	                $con = $this->doctrine->getManager()->getConnection();
	                $user_stmt = $con->executeQuery('SELECT * FROM fos_user where `username_canonical` = "'.($username.$i).'"');
	                $check_username_canonical = $user_stmt->fetchAll();                 
	                if(empty($check_username_canonical))
	                {
	                    if($count < 3)
	                    {
	                        $suggested_username[$count] = $username.$i;
	                        $count++;
	                    }
	                }
	            }
	        }
			$myresponse = array(
				'success' => true,
				'status' => Response::HTTP_OK,
				'content' => array(
				 'secondary_content' => 'suggest use suggested username.',
				 'suggested_username' => $suggested_username,
				 'flag' => '2'
				)
			);
			$finalResponse = json_encode($myresponse);
			return $finalResponse;
		}else{
			
			$myresponse = array(
				'success' => true,
				'status' => Response::HTTP_OK,
				'content' => array(
				'secondary_content' => 'username confirmed.',
				'flag' => '1'
				)
			);
			$finalResponse = json_encode($myresponse);
			return $finalResponse;
		}
	}
	
	public function finalusername($username,$id,$image_path)
	{
		$request = $this->request->getCurrentRequest();
		$em = $this->doctrine->getManager();
		$user = $em->getRepository('AppBundle:User')->findOneById($id);
		$user->setUsername($username);        
		$em->persist($user);
		$em->flush();
		$last_inserted_id = $user->getId();
		$user_id = $user->getId();
		$festivals = $em->getRepository('IFlairFestivalBundle:festival_view')->findBy(array('userId' => $user_id));
		$festival_ids = array();
		foreach($festivals as $festival)
		{
		$festival_ids[] = $festival->getFestivalId()->getId();
		}
		$partyfinders = $em->getRepository('IFlairSoapBundle:Partyfinderviews')->findBy(array('viewedUserId' => $user_id));
		$viewed_party_locations = array();
		$bar = array(); $nightclub = array();
		foreach($partyfinders as $party)
		{
		$type = $party->getPartyFinderId()->getclubtypeid()->getName();
		if($type == 'Bar'){
		    $bar[] = $party->getPartyFinderId()->getId();
		}else{
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
        $fullname = $user->getFirstname().' '.$user->getLastname();
        $pictureurl = $user->getPictureurl();



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
		
		// Total approved uploaded images.
		$partyuploaded_images_entity = $em->getRepository('IFlairFestivalBundle:FestivalInprogressMultipleImageUpload')->findBy(array('userId' => $user_id,'isApproved' => '1'));
        $uploaded_ids = array();
        foreach($partyuploaded_images_entity as $uploaded_image)
        {
            $uploaded_ids[] = $uploaded_image->getId();
        }
        $festivaluploaded_images_entity = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressMultipleImageUpload')->findBy(array('userId' => $user_id,'isApproved' => '1'));        
        foreach($festivaluploaded_images_entity as $uploaded_image)
        {
            $uploaded_ids[] = $uploaded_image->getId();
        }
        
		$myresponse = array(
			'success' => true,
			'status' => Response::HTTP_OK,
			'id' => $last_inserted_id,
			'country' => $country_list,
			'checkin_count' => $total_checkin_count,
            'pictureurl' => $pictureurl,
            'contribution_count' => $total_contribution,
            'fullname' =>$fullname,
            'uploaded_images_count' => count($uploaded_ids),
			'content' => array(
			 'secondary_content' => 'Username added Successfully.'
			)
		);
		$finalResponse = json_encode($myresponse);
		return $finalResponse;
	}
}
