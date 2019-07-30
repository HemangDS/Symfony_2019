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

class PartyvisitedcontinentService
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
    public function partyvisitedcontinent($user_id)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $con = $this->doctrine->getManager()->getConnection();
        $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id);
        $fest_result = $fest_stmt->fetchAll();
        $continents = array();
        foreach($fest_result as $fest)
        {
            $continents[] = $fest['continent_id'];
        }
        $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id);
        $party_result = $party_stmt->fetchAll();
        foreach($party_result as $party)
        {
            $continents[] = $party['continent_id'];
        }
        $continents = array_unique(array_filter($continents));

        $continent_names = array(); $count = 0;
        foreach($continents as $continent)
        {
            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select('pfc.name as continentname')
                ->from('IFlairSoapBundle\Entity\partyfindercontinents', 'pfc')
                ->where("pfc.id =".$continent);
            $continent_data = $queryBuilder->getQuery()->getResult();
            $continent_names[$count]['continent'] = $continent_data['0']['continentname'];

            $conti_fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `continent_id` = '.$continent.' AND `user_id` = '.$user_id);
            $conti_fest_result = $conti_fest_stmt->fetchAll();
            $continents = array();
            $cont_fest = array(); $cont_city = array(); $cont_country = array(); 
            foreach($conti_fest_result as $fest)
            {
                $cont_fest[] = $fest['festival_id'];
                $cont_city[] = $fest['city_id'];
                $cont_country[] = $fest['country_id'];
            }

            $conti_party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `continent_id` = '.$continent.' AND `user_id` = '.$user_id);
            $conti_party_result = $conti_party_stmt->fetchAll();
            $cont_bar_party = array();$cont_nightclub_party = array();
            foreach($conti_party_result as $party)
            {
                
                if($party['club_type'] = 'Bar')
                    $cont_bar_party[] = $party['club_id'];
                if($party['club_type'] = 'Nightclub')
                    $cont_nightclub_party[] = $party['club_id'];                
                $cont_city[] = $party['city_id'];
                $cont_country[] = $party['country_id'];
            }
            $continent_names[$count]['continent_id']  = $continent;
            $continent_names[$count]['bar']  = count(array_unique(array_filter($cont_bar_party)));
            $continent_names[$count]['nightclub'] = count(array_unique(array_filter($cont_nightclub_party)));
            $continent_names[$count]['city'] = count(array_unique(array_filter($cont_city)));
            $continent_names[$count]['country'] = count(array_unique(array_filter($cont_country)));
            $continent_names[$count]['festival'] = count(array_unique(array_filter($cont_fest)));
            $bar  = count(array_unique(array_filter($cont_bar_party)));
            $club = count(array_unique(array_filter($cont_nightclub_party)));
            $fest = count(array_unique(array_filter($cont_fest)));
            $continent_names[$count]['total_count'] = ($bar + $club + $fest);
            $count++;
        }

        $myresponse = array(
            'message' => 'visited area',
            'continent_data' => $continent_names,
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
