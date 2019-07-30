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

class PartyvisitedareasService
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
    public function partyvisitedareas($user_id)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $con = $this->doctrine->getManager()->getConnection();
        $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id);        
        $fest_result = $fest_stmt->fetchAll();
        $continent = array(); $city = array(); $country = array(); $fesitval = array();
        foreach($fest_result as $fest)
        {
            $continent[] = $fest['continent_id'];
            $city[] = $fest['city_id'];
            $country[] = $fest['country_id'];
            $fesitval[] = $fest['festival_id'];
        }

        $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id);        
        $party_result = $party_stmt->fetchAll();
        $bars = array(); $nightclub = array();
        foreach($party_result as $party)
        {
            $continent[] = $party['continent_id'];
            $city[] = $party['city_id'];
            $country[] = $party['country_id'];
            if($party['club_type'] == 'Bar')
                $bars[] = $party['club_id'];
            if($party['club_type'] == 'Nightclub')
                $nightclub[] = $party['club_id'];
        }
        $continent = array_unique(array_filter($continent));
        $city = array_unique(array_filter($city));
        $country = array_unique(array_filter($country));
        $fesitval = array_unique(array_filter($fesitval));
        $bars = array_unique(array_filter($bars));
        $nightclub = array_unique(array_filter($nightclub));

        $myresponse = array(
            'message' => 'visited area',
            'cities' => count($city),
            'countries' => count($country),
            'continents' => count($continent),
            'festival_ids' => count(array_unique($fesitval)),
            'nightclub' => count(array_unique($nightclub)),
            'bar' => count(array_unique($bars)),
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
