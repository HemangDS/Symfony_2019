<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// ENTITY
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfinderviews;
use IFlairSoapBundle\Entity\Partyfinder;

class PartyvisitedcontinentcountryService
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
    public function partyvisitedcontinentcountry($user_id, $continent_id, $type)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $con = $this->doctrine->getManager()->getConnection();
        
        if($type == "1") // type = 1 , continent wise country flow
        {
            $combineResult = array();
            $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id.' AND `continent_id` = '.$continent_id);
            $fest_result = $fest_stmt->fetchAll();            
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
            foreach($fest_result as $fest) {
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pfc.countryName as countryname')
                    ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfc')
                    ->where("pfc.id =".(int)$fest['country_id']);
                $country_data = $queryBuilder->getQuery()->getResult();

                if(isset($combineResult[$fest['country_id']])) {
                    $combineResult[$fest['country_id']]['country_id'] = $fest['country_id'];
                    $combineResult[$fest['country_id']]['country_name'] = $country_data['0']['countryname'];


                    $combineResult[$fest['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';


                    $festivalExistsCount = count($combineResult[$fest['country_id']]['festival_id']);
                    if($festivalExistsCount > 0) {
                        $combineResult[$fest['country_id']]['festival_id'][$festivalExistsCount] = $fest['festival_id'];
                    } else {
                        $combineResult[$fest['country_id']]['festival_id'] = array($fest['festival_id']);
                    }
                    $cityExistsCount = count($combineResult[$fest['country_id']]['city_id']);
                    if($cityExistsCount > 0) {
                        $combineResult[$fest['country_id']]['city_id'][$cityExistsCount] = $fest['city_id'];
                    } else {
                        $combineResult[$fest['country_id']]['city_id'] = array($fest['city_id']);
                    }                
                } else {
                    $combineResult[$fest['country_id']]['country_id'] = $fest['country_id'];                
                    $combineResult[$fest['country_id']]['country_name'] = $country_data['0']['countryname'];
                    $combineResult[$fest['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';
                    $combineResult[$fest['country_id']]['festival_id'] = array($fest['festival_id']);
                    $combineResult[$fest['country_id']]['city_id'] = array($fest['city_id']);
                }
            }

            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id.' AND `continent_id` = '.$continent_id);
            $party_result = $party_stmt->fetchAll();
            foreach($party_result as $party) {
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pfc.countryName as countryname')
                    ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfc')
                    ->where("pfc.id =".(int)$party['country_id']);
                $country_data = $queryBuilder->getQuery()->getResult();

                if(isset($combineResult[$party['country_id']])) {
                    $combineResult[$party['country_id']]['country_id'] = $party['country_id'];
                    $combineResult[$party['country_id']]['country_name'] = $country_data['0']['countryname'];
                    $combineResult[$party['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';
                    $clubTypeCount = 0;
                    $clubTypeCount = (isset($combineResult[$party['country_id']][$party['club_type']]))?count($combineResult[$party['country_id']][$party['club_type']]):0;
                    if($clubTypeCount > 0) {
                        $combineResult[$party['country_id']][$party['club_type']][$clubTypeCount] = $party['club_id'];
                    } else {
                        $combineResult[$party['country_id']][$party['club_type']] = array($party['club_id']);
                    }
                    $cityExistsCount = count($combineResult[$party['country_id']]['city_id']);
                    if($cityExistsCount > 0) {
                        $combineResult[$party['country_id']]['city_id'][$cityExistsCount] = $party['city_id'];
                    } else {
                        $combineResult[$party['country_id']]['city_id'] = array($party['city_id']);
                    }                
                } else {                
                    $combineResult[$party['country_id']]['country_id'] = $party['country_id'];
                    $combineResult[$party['country_id']]['country_name'] = $country_data['0']['countryname'];
                    $combineResult[$party['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';
                    $combineResult[$party['country_id']][$party['club_type']] = array($party['club_id']);
                    $combineResult[$party['country_id']]['city_id'] = array($party['city_id']);
                }
            }
            $final_data = array(); $count = 0;
            foreach($combineResult as $result)
            {
                if(!empty($result['country_id']) && !empty($result['country_name']))
                {
                    $final_data[$count]['country_id'] = $result['country_id']; 
                    $final_data[$count]['country_name'] = $result['country_name'];
                    $final_data[$count]['country_image'] = $result['country_image']; 
                    $final_data[$count]['festival_id'] = (isset($result['festival_id']))?count($result['festival_id']):0;
                    $final_data[$count]['city_id'] = (isset($result['city_id']))?count($result['city_id']):0;
                    $final_data[$count]['nightclub'] = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                    $final_data[$count]['bars'] = (isset($result['Bar']))?count($result['Bar']):0;
                    $club = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                    $bars = (isset($result['Bar']))?count($result['Bar']):0;
                    $fest = (isset($result['festival_id']))?count($result['festival_id']):0;
                    $final_data[$count]['total_count'] = ($club + $bars + $fest);
                    $count++;
                }
            }
        }else if($type == "2") // type = 2, country wise listing
        {
            $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id);
            $fest_result = $fest_stmt->fetchAll();
            $combineResult = array();
            $countries = array();
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
            foreach($fest_result as $fest)
            {
                if(!empty($fest['country_id']))
                {
                    $countries[] = $fest['country_id'];
                    foreach($fest_result as $fest) {
                        $queryBuilder = $em->createQueryBuilder();
                        $queryBuilder->select('pfc.countryName as countryname')
                            ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfc')
                            ->where("pfc.id =".(int)$fest['country_id']);
                        $country_data = $queryBuilder->getQuery()->getResult();

                        if(isset($combineResult[$fest['country_id']])) {
                            $combineResult[$fest['country_id']]['country_id'] = $fest['country_id'];
                            $combineResult[$fest['country_id']]['country_name'] = $country_data['0']['countryname'];
                            $combineResult[$fest['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';

                            $festivalExistsCount = count($combineResult[$fest['country_id']]['festival_id']);
                            if($festivalExistsCount > 0) {
                                $combineResult[$fest['country_id']]['festival_id'][$festivalExistsCount] = $fest['festival_id'];
                                $combineResult[$fest['country_id']]['festival_id'] = array_unique($combineResult[$fest['country_id']]['festival_id']);
                            } else {
                                $combineResult[$fest['country_id']]['festival_id'] = array($fest['festival_id']);
                            }
                            $cityExistsCount = count($combineResult[$fest['country_id']]['city_id']);
                            if($cityExistsCount > 0) {                      
                                $combineResult[$fest['country_id']]['city_id'][$cityExistsCount] = $fest['city_id'];
                                $combineResult[$fest['country_id']]['city_id'] = array_unique($combineResult[$fest['country_id']]['city_id']);
                            } else {
                                $combineResult[$fest['country_id']]['city_id'] = array($fest['city_id']);
                            }
                        } else {
                            $combineResult[$fest['country_id']]['country_id'] = $fest['country_id'];
                            $combineResult[$fest['country_id']]['country_name'] = $country_data['0']['countryname'];
                            $combineResult[$fest['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';
                            $combineResult[$fest['country_id']]['festival_id'] = array($fest['festival_id']);
                            $combineResult[$fest['country_id']]['city_id'] = array($fest['city_id']);
                        }
                    }
                }
            }

            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id);
                $party_result = $party_stmt->fetchAll();
                foreach($party_result as $party) {
                    if(!empty($party['country_id']))
                    {
                        $queryBuilder = $em->createQueryBuilder();
                        $queryBuilder->select('pfc.countryName as countryname')
                            ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfc')
                            ->where("pfc.id =".(int)$party['country_id']);
                        $country_data = $queryBuilder->getQuery()->getResult();

                        if(isset($combineResult[$party['country_id']])) {
                            $combineResult[$party['country_id']]['country_id'] = $party['country_id'];
                            $combineResult[$party['country_id']]['country_name'] = $country_data['0']['countryname'];
                            $combineResult[$party['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';
                            $clubTypeCount = 0;
                            $clubTypeCount = (isset($combineResult[$party['country_id']][$party['club_type']]))?count($combineResult[$party['country_id']][$party['club_type']]):0;
                            if($clubTypeCount > 0) {
                                $combineResult[$party['country_id']][$party['club_type']][$clubTypeCount] = $party['club_id'];
                            } else {
                                $combineResult[$party['country_id']][$party['club_type']] = array($party['club_id']);
                            }
                            $cityExistsCount = count($combineResult[$party['country_id']]['city_id']);
                            if($cityExistsCount > 0) {
                                $combineResult[$party['country_id']]['city_id'][$cityExistsCount] = $party['city_id'];
                            } else {
                                $combineResult[$party['country_id']]['city_id'] = array($party['city_id']);
                            }                
                        } else {                
                            $combineResult[$party['country_id']]['country_id'] = $party['country_id'];
                            $combineResult[$party['country_id']]['country_name'] = $country_data['0']['countryname'];
                            $combineResult[$party['country_id']]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country_data['0']['countryname'])).'.png';
                            $combineResult[$party['country_id']][$party['club_type']] = array($party['club_id']);
                            $combineResult[$party['country_id']]['city_id'] = array($party['city_id']);
                        }
                    }
                }

            $final_data = array(); $count = 0;
            foreach($combineResult as $result)
            {
                if(!empty($result['country_id']) && !empty($result['country_name']))
                {
                    $final_data[$count]['country_id'] = $result['country_id']; 
                    $final_data[$count]['country_name'] = $result['country_name'];
                    $final_data[$count]['country_image'] = $result['country_image']; 
                    $final_data[$count]['festival_id'] = (isset($result['festival_id']))?count($result['festival_id']):0;
                    $final_data[$count]['city_id'] = (isset($result['city_id']))?count($result['city_id']):0;
                    $final_data[$count]['nightclub'] = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                    $final_data[$count]['bars'] = (isset($result['Bar']))?count($result['Bar']):0;
                    $club = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                    $bars = (isset($result['Bar']))?count($result['Bar']):0;
                    $fest = (isset($result['festival_id']))?count($result['festival_id']):0;
                    $final_data[$count]['total_count'] = ($club + $bars + $fest);
                    $count++;
                }
            }
        }

        $myresponse = array(
            'message' => 'visited area',
            'continent_data' => $final_data,
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
