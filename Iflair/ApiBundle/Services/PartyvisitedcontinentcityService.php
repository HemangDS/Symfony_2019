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

class PartyvisitedcontinentcityService
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
    public function partyvisitedcontinentcity($user_id, $country_id, $type)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $con = $this->doctrine->getManager()->getConnection();

        if($type == "1")
        {
            $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id.' AND `country_id` = '.$country_id);
            $fest_result = $fest_stmt->fetchAll();
            $combineResult = array();
            foreach($fest_result as $fest) {
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pfc.cityName as cityname')
                    ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc')
                    ->where("pfc.id =".$fest['city_id']);
                $city_data = $queryBuilder->getQuery()->getResult();


                if(isset($combineResult[$fest['city_id']])) {
                    $combineResult[$fest['city_id']]['city_id'] = $fest['city_id'];
                    $festivalExistsCount = count($combineResult[$fest['city_id']]['festival_id']);
                    if($festivalExistsCount > 0) {
                        $combineResult[$fest['city_id']]['festival_id'][$festivalExistsCount] = $fest['festival_id'];
                    } else {
                        $combineResult[$fest['city_id']]['festival_id'] = array($fest['festival_id']);
                    }
                    $cityExistsCount = count($combineResult[$fest['city_id']]['city_id']);
                    if($cityExistsCount > 0) {
                        $combineResult[$fest['city_id']]['city_id'][$cityExistsCount] = $fest['city_id'];
                    } else {
                        $combineResult[$fest['city_id']]['city_id'] = array($fest['city_id']);
                    }
                    $combineResult[$fest['city_id']]['cityname'] = $city_data[0]['cityname'];
                } else {
                    $combineResult[$fest['city_id']]['festival_id'] = array($fest['festival_id']);
                    $combineResult[$fest['city_id']]['cityname'] = $city_data[0]['cityname'];
                    $combineResult[$fest['city_id']]['city_id'] = array($fest['city_id']);
                }
            }

            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id.' AND `country_id` = '.$country_id);
            $party_result = $party_stmt->fetchAll();

            foreach($party_result as $party) {
                //fetch country name
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pfc.cityName as cityname')
                    ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc')
                    ->where("pfc.id =".$party['city_id']);
                $city_data = $queryBuilder->getQuery()->getResult();

                if(isset($combineResult[$party['city_id']])) {
                    $clubTypeCount = 0;
                    $clubTypeCount = (isset($combineResult[$party['city_id']][$party['club_type']]))?count($combineResult[$party['city_id']][$party['club_type']]):0;
                    
                    if($clubTypeCount > 0) {
                        $combineResult[$party['city_id']][$party['club_type']][$clubTypeCount] = $party['club_id'];
                    } else {
                        $combineResult[$party['city_id']][$party['club_type']] = array($party['club_id']);
                    }
                    $cityExistsCount = count($combineResult[$party['city_id']]['city_id']);
                    if($cityExistsCount > 0) {
                        $combineResult[$party['city_id']]['city_id'][$cityExistsCount] = $party['city_id'];
                    } else {
                        $combineResult[$party['city_id']]['city_id'] = array($party['city_id']);
                    }
                    $combineResult[$party['city_id']]['cityname'] = $city_data[0]['cityname'];

                } else {                
                    $combineResult[$party['city_id']][$party['club_type']] = array($party['club_id']);
                    $combineResult[$party['city_id']]['cityname'] = $city_data[0]['cityname'];
                    $combineResult[$party['city_id']]['city_id'] = array($party['city_id']);
                }
            }

            $final_data = array(); $count = 0;
            foreach($combineResult as $result)
            {
                $final_data[$count]['festival_id'] = (isset($result['festival_id']))?count($result['festival_id']):0;
                $final_data[$count]['city_count'] = (isset($result['city_id']))?count($result['city_id']):0;
                $final_data[$count]['city_id'] = (isset($result['city_id']))?array_shift($result['city_id']):0;
                $final_data[$count]['nightclub'] = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                $final_data[$count]['bars'] = (isset($result['Bar']))?count($result['Bar']):0;
                $final_data[$count]['cityname'] = (isset($result['cityname']))?$result['cityname']:'';
                $club = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                $bar = (isset($result['Bar']))?count($result['Bar']):0;
                $fest = (isset($result['festival_id']))?count($result['festival_id']):0;
                $total_count = ($club + $bar + $fest);
                $final_data[$count]['total_count'] = (isset($total_count))?$total_count:0;
                $count++;
            }
        }else if($type == "2"){

            $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id);
            $fest_result = $fest_stmt->fetchAll();
            $combineResult = array();
            foreach($fest_result as $fest) {
                //fetch country name
                if(!empty($fest['city_id']))
                {
                    $queryBuilder = $em->createQueryBuilder();
                    $queryBuilder->select('pfc.cityName as cityname')
                        ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc')
                        ->where("pfc.id =".$fest['city_id']);
                    $city_data = $queryBuilder->getQuery()->getResult();


                    if(isset($combineResult[$fest['city_id']])) {
                        $country = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($fest['city_id']);
                        $combineResult[$fest['city_id']]['country_name'] = $country->getCountryId()->getCountryName();
                        $combineResult[$fest['city_id']]['city_id'] = $fest['city_id'];
                        $festivalExistsCount = count($combineResult[$fest['city_id']]['festival_id']);
                        if($festivalExistsCount > 0) {
                            $combineResult[$fest['city_id']]['festival_id'][$festivalExistsCount] = $fest['festival_id'];
                        } else {
                            $combineResult[$fest['city_id']]['festival_id'] = array($fest['festival_id']);
                        }
                        $cityExistsCount = count($combineResult[$fest['city_id']]['city_id']);
                        if($cityExistsCount > 0) {
                            $combineResult[$fest['city_id']]['city_id'][$cityExistsCount] = $fest['city_id'];
                        } else {
                            $combineResult[$fest['city_id']]['city_id'] = array($fest['city_id']);
                        }
                        $combineResult[$fest['city_id']]['cityname'] = $city_data[0]['cityname'];
                    } else {
                        $country = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($fest['city_id']);
                        $combineResult[$fest['city_id']]['country_name'] = array($country->getCountryId()->getCountryName());
                        $combineResult[$fest['city_id']]['festival_id'] = array($fest['festival_id']);
                        $combineResult[$fest['city_id']]['cityname'] = $city_data[0]['cityname'];
                        $combineResult[$fest['city_id']]['city_id'] = array($fest['city_id']);
                    }
                }
            }

            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id);
            $party_result = $party_stmt->fetchAll();

            foreach($party_result as $party) {
                //fetch country name
                if(!empty($party['city_id']))
                {
                    $queryBuilder = $em->createQueryBuilder();
                    $queryBuilder->select('pfc.cityName as cityname')
                        ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc')
                        ->where("pfc.id =".$party['city_id']);
                    $city_data = $queryBuilder->getQuery()->getResult();

                    if(isset($combineResult[$party['city_id']])) {
                        $country = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($party['city_id']);
                        $combineResult[$party['city_id']]['country_name'] = $country->getCountryId()->getCountryName();

                        $clubTypeCount = 0;
                        $clubTypeCount = (isset($combineResult[$party['city_id']][$party['club_type']]))?count($combineResult[$party['city_id']][$party['club_type']]):0;
                        
                        if($clubTypeCount > 0) {
                            $combineResult[$party['city_id']][$party['club_type']][$clubTypeCount] = $party['club_id'];
                        } else {
                            $combineResult[$party['city_id']][$party['club_type']] = array($party['club_id']);
                        }
                        $cityExistsCount = count($combineResult[$party['city_id']]['city_id']);
                        if($cityExistsCount > 0) {
                            $combineResult[$party['city_id']]['city_id'][$cityExistsCount] = $party['city_id'];
                        } else {
                            $combineResult[$party['city_id']]['city_id'] = array($party['city_id']);
                        }
                        $combineResult[$party['city_id']]['cityname'] = $city_data[0]['cityname'];

                    } else {
                        $country = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($party['city_id']);
                        $combineResult[$party['city_id']]['country_name'] = array($country->getCountryId()->getCountryName());
                        $combineResult[$party['city_id']][$party['club_type']] = array($party['club_id']);
                        $combineResult[$party['city_id']]['cityname'] = $city_data[0]['cityname'];
                        $combineResult[$party['city_id']]['city_id'] = array($party['city_id']);
                    }
                }
            }

            $final_data = array(); $count = 0;
            foreach($combineResult as $result)
            {
                $final_data[$count]['festival_id'] = (isset($result['festival_id']))?count($result['festival_id']):0;
                $final_data[$count]['city_count'] = (isset($result['city_id']))?count($result['city_id']):0;
                
                if(is_array($result['city_id']))
                    $final_data[$count]['city_id'] = (isset($result['city_id']))?array_shift($result['city_id']):0;
                else
                    $final_data[$count]['city_id'] = (isset($result['city_id']))?$result['city_id']:0;

                $final_data[$count]['nightclub'] = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                $final_data[$count]['bars'] = (isset($result['Bar']))?count($result['Bar']):0;
                $final_data[$count]['cityname'] = (isset($result['cityname']))?$result['cityname']:'';
                $final_data[$count]['country_name'] = (isset($result['country_name']))?array_shift($result['country_name']):'';
                array_shift($final_data[$count]['country_name']);
                $club = (isset($result['Nightclub']))?count($result['Nightclub']):0;
                $bar = (isset($result['Bar']))?count($result['Bar']):0;
                $fest = (isset($result['festival_id']))?count($result['festival_id']):0;
                $total_count = ($club + $bar + $fest);
                $final_data[$count]['total_count'] = (isset($total_count))?$total_count:0;
                $count++;
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
