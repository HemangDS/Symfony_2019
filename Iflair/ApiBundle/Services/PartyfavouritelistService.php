<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use IFlairSoapBundle\Entity\Settings;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfinderfavorite;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PartyfavouritelistService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * Set user wise Applications settings.
     * @return mixed
     */
    public function displayfavouritelist($user_id)
    {
        $em = $this->doctrine->getManager();
        $request = $this->request->getCurrentRequest();
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinderfavorite')->findBy(array('userId' => $user_id));
        $club_special_list = array(); $list_cnt = 0; 
        foreach($partyfinder as $party)
        {
            $partyid = $party->getPartyFinderId()->getId();
            $partytype = $party->getPartyFinderId()->getClubTypeId()->getName();
            
            $club_special_list[$list_cnt]['partyid'] = $partyid;
            $club_special_list[$list_cnt]['partytype'] = $partytype;
            $club_location_id = 0;
            $partyfinderlocation = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($partyid);
            foreach($partyfinderlocation as  $club)
            {
                $club_location_id = $club->getClubLocationId()->getId();
            }
            $city = '';
            if($club_location_id)
            {
                $partyfinderlocation = $em->getRepository('IFlairSoapBundle:Partyfinderlocation')->findById($club_location_id);
                foreach($partyfinderlocation as $location)
                {
                    $city = $location->getCityId()->getCityName();
                    $country = $location->getCityId()->getCountryId()->getCountryName();
                }
            }
            $club_special_list[$list_cnt]['city'] = $city;
            $club_special_list[$list_cnt]['country'] = $country;
            /* Code for ratings */
            $partyfinderatings = $em->getRepository('IFlairSoapBundle:Partyfinderratings')->findBy(array('partyFinderId' => $partyid));
            $total = 0; $average_rating = 0;
            foreach($partyfinderatings as $rating)
            {
                $total += $rating->getUserRatings();
                $average_rating++;
            }
            if($average_rating == 0)
            {
                $average_rating = 1;
            }
            $average_rating = $total/$average_rating;
            $club_special_list[$list_cnt]['average_rating'] = $average_rating;

            /* Code for image */
            $partyfindeimage = $em->getRepository('IFlairSoapBundle:Partyfinderimage')->findBy(array('partyFinderId' => $partyid, 'imageType' => 'logo'));
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
            foreach($partyfindeimage as $image)
            {
                $image_path = $image_path.$image->getImageName();
            }
            $club_special_list[$list_cnt]['club_name'] = $party->getPartyFinderId()->getClubTitle();
            $club_special_list[$list_cnt]['club_logo'] = $image_path;
            $list_cnt++;
        }
        $nightclub = array(); $bar = array(); $cnt = 0; $cntt = 0;
        foreach($club_special_list as $club)
        {
            if($club['partytype'] == 'Nightclub')
            {
                $nightclub[$cnt] = $club;
                $cnt++;
            }else{
                $bar[$cntt] = $club;
                $cntt++;
            }
        }


        /* Festival Start */
        $festival = $em->getRepository('IFlairFestivalBundle:festivalFavourite')->findBy(array('userId' => $user_id));
        $festival_favourite = array();$list_cnt = 0; 
        foreach($festival as $fest)
        {
            if(!in_array($fest->getFestivalId()->getId(), $festival_favourite))
            {
                $festival_list[$list_cnt]['partyid'] = $fest->getFestivalId()->getId();
                $festival_list[$list_cnt]['club_name'] = $fest->getFestivalId()->getTitle();
                $festival_list[$list_cnt]['partytype'] = 'festival';
                $festival_organiser = $em->getRepository('IFlairFestivalBundle:festival_organizer')->findOneByFestivalId($fest->getFestivalId()->getId());
                $festival_list[$list_cnt]['city'] = $festival_organiser->getCity()->getCityName();
                $festival_list[$list_cnt]['country'] = $festival_organiser->getCountry()->getCountryName();
                $festival_ratings = $em->getRepository('IFlairFestivalBundle:festival_type_ratings')->findByFestivalId($fest->getFestivalId()->getId());
                $count = 0; $festival_rating = 0;
                foreach($festival_ratings as $rating)
                {
                    $festival_rating += $rating->getUserRatings();
                    $count++;
                }
                if($festival_rating > 0)
                    $avg_rating = $festival_rating / $count;
                else
                    $avg_rating = 0;
                $festival_list[$list_cnt]['average_rating'] = $avg_rating;

                $path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
                $festival_logos = $em->getRepository('IFlairFestivalBundle:festival_image')->findOneBy(array("festivalId" => $fest->getFestivalId()->getId(), "imageType" => 'logo'));                
                if(isset($festival_logos)){
                    $festival_logo = $path.$festival_logos->getImageName();                
                    $festival_list[$list_cnt]['club_logo'] = $festival_logo;
                }else{
                    $festival_list[$list_cnt]['club_logo'] = $path;
                }
                $festival_favourite[] = $fest->getFestivalId()->getId();
                $list_cnt++;
            }
        }
        /* Festival End */


        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'list favourite successfully.',
            'no_of_nightclub' => count($nightclub),
            'nightclub' => $nightclub,
            'no_of_bar' => count($bar),
            'bar' => $bar,
            'no_of_festival' => count($festival_list),
            'festival' => $festival_list
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
