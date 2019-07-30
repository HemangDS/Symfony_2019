<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// ENTITY
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindertiming;

class PartyviewService
{
	protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * View NightClub or Bar when called
     * @param string $name 
     * @return mixed
     */
    public function partyview($party_id, $requseted_date, $user_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $club_title = ''; $club_exclusivity = ''; $dresscode = ''; $dresscode_desc = '';
        $latitude = ''; $longitude = ''; $address = ''; $banner_path = '';
        $club_type = ''; $starttime = ''; $endtime = '';$music_name = array();  $average_rating = 0; $total = 0;
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($party_id);
        /* Code for title exclusivity */
        foreach($partyfinder as  $club)
        {
            $club_title = $club->getClubTitle();
            $club_exclusivity = $club->getClubExclusivity();
            $club_location_id = $club->getClubLocationId()->getId();
            $club_type_id = $club->getClubTypeId()->getId();
            $dresscode_id = $club->getDresscodeId()->getId();
        }
        /* Code for addresss, latitude and longitude */
        if($club_location_id)
        {
            $partyfinderlocation = $em->getRepository('IFlairSoapBundle:Partyfinderlocation')->findById($club_location_id);
            foreach($partyfinderlocation as $location)
            {
                $address = $location->getAddress();
                $latitude = $location->getLatitude();
                $longitude = $location->getLongitude();
            }
        }
        if($club_type_id)
        {
            $partyfindertype = $em->getRepository('IFlairSoapBundle:Partyfindertype')->findById($club_type_id);
            foreach($partyfindertype as $type)
            {
                $club_type = $type->getName();
            }
        }
        /* Code for start time and end time */
        $partytimings = $em->getRepository('IFlairSoapBundle:Partyfindertiming')->findBy(array('partyFinderId' => $partyfinder, 'timingDay' => 'OTHER', 'timingDate' => new \DateTime($requseted_date)));
        if($partytimings)
        {
            foreach($partytimings as $timing)
            {
                $starttime = $timing->getStartTime();
                $endtime = $timing->getEndTime();
            }
        }else{
            $nameOfDay = strtolower(date('l', strtotime($requseted_date)));
            $partytimings = $em->getRepository('IFlairSoapBundle:Partyfindertiming')->findBy(array('partyFinderId' => $partyfinder,'timingDay' => $nameOfDay));
            foreach($partytimings as $timing)
            {
                $starttime = $timing->getStartTime();
                $endtime = $timing->getEndTime();
            }
        }

        /* Code for music generous */
        $partymusic = $em->getRepository('IFlairSoapBundle:Partyfindermusicgenre')->findBy(array('partyFinderId' => $partyfinder));
        foreach($partymusic as $music)
        {
            $partymusic = $em->getRepository('IFlairSoapBundle:Musicgenre')->findById($music->getMusicGenreId()->getId());
            foreach($partymusic as $musicname)
            {
                $music_name[] = $musicname->getName();
            }
        }

        /* Code for ratings */
        $partyfinderatings = $em->getRepository('IFlairSoapBundle:Partyfinderratings')->findBy(array('partyFinderId' => $partyfinder));
        foreach($partyfinderatings as $rating)
        {
            $total += $rating->getUserRatings();
            $average_rating++;
        }
        $average_rating = $total/$average_rating;

        /* Code for banner */
        $partyfindeimage = $em->getRepository('IFlairSoapBundle:Partyfinderimage')->findBy(array('partyFinderId' => $partyfinder,  'imageType' => 'banner'));
        $banner_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
        foreach($partyfindeimage as $image)
        {
            $banner_path = $banner_path.$image->getImageName();
        }

        /* Code for Dresscode */
        $partydress = $em->getRepository('IFlairSoapBundle:Partydresscode')->findById($dresscode_id);
        foreach($partydress as $dress)
        {
            $dresscode = $dress->getName();
            $$dresscode_desc = $dress->getDescription();
        }

        /* Code for favourite */
        $favourite = $em->getRepository('IFlairSoapBundle:Partyfinderfavorite')->findBy(array('userId' => $user_id, 'partyFinderId' => $party_id));
        if($favourite)
        {
            foreach($favourite as $fvurite)
            {
                $favourite_id = $fvurite->getId();
            }
            $favourite = 'true';
        }else{
            $favourite = 'false';
        }
        /*END*/
        $music_name = implode(',', $music_name);
        
        $myresponse = array(
            'message' => 'view successfully..',
            'success' => true,
            'status' => Response::HTTP_OK,
            'content' => array(
                'banner'            =>      $banner_path,
                'club_title'        =>      $club_title,
                'address'           =>      $address,
                'latitude'          =>      $latitude,
                'longitude'         =>      $longitude,
                'ratings'           =>      $average_rating,
                'start_time'        =>      $starttime,
                'end_time'          =>      $endtime,
                'exclusivity'       =>      $club_exclusivity,
                'music'             =>      $music_name,
                'club_type'         =>      $club_type,
                'dresscode'         =>      $dresscode,
                'dresscode_desc'    =>      $$dresscode_desc,
                'favourite'         =>      $favourite
            )
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}