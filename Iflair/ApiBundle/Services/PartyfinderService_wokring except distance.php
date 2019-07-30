<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\Query\Expr;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindertiming;

class PartyfinderService
{
	protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * Listing all Night Club and Bar when called.
     * @param string $name 
     * @return mixed
     */
    public function partyfinder($requseted_date, $offset, $limit, $partytype = 'all', $order = 'default', $cur_lat = '', $cur_lag = '')
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
            ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');

            if($partytype != 'all')
            {
                if($partytype  == 'Nightclub')
                {
                    $queryBuilder->join("pf.clubTypeId","t")->where("t.name = 'Nightclub'");
                }else if($partytype  == 'Bar')
                {
                    $queryBuilder->join("pf.clubTypeId","t")->where("t.name = 'Bar'");
                }else{}
            }

            $queryBuilder->addSelect('avg(pfr.userRatings) as user_ratings')
            ->groupBy('pf.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->leftJoin("pf.clubLocationId",'e')->addSelect('e.latitude','e.longitude');
        switch ($order) {
            case 'name':
            default:
                $queryBuilder->orderBy('pf.clubTitle', 'ASC');
                break;
            case 'rating':
                $queryBuilder->orderBy('user_ratings', 'DESC');
                break;
        }
        $result = $queryBuilder->getQuery()->getResult();

        /*=============== Query for total count no of record ===============*/
        $queryBuilder1 = $em->createQueryBuilder();
        $queryBuilder1
            ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
            ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');

            if($partytype != 'all')
            {
                if($partytype  == 'Nightclub')
                {
                    $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Nightclub'");
                }else if($partytype  == 'Bar')
                {
                    $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Bar'");
                }else{}
            }

            $queryBuilder1->addSelect('avg(pfr.userRatings) as user_ratings')
            ->groupBy('pf.id')
            ->leftJoin("pf.clubLocationId ",'e')->addSelect('e.latitude','e.longitude');
        switch ($order) {
            case 'name':
            default:
                $queryBuilder1->orderBy('pf.clubTitle', 'ASC');
                break;
            case 'rating':
                $queryBuilder1->orderBy('user_ratings', 'DESC');
                break;
        }
        $result_count = $queryBuilder1->getQuery()->getResult();
        $total_party = count($result_count);

        /*=============== Query for total count no of record ===============*/
        /* Club Logo */
        $count = 0;
        foreach($result as $club)
        {
            $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
            $result[$count]['club_logo'] = $image_name;
            $count++;
        }
        /* Start time, End time, Special Occassion */
        $count = 0;
        foreach($result as $club)
        {
            $start_time = $em->getRepository('IFlairSoapBundle:Partyfinder')->getStarttime($em, $requseted_date, $club['id'], $request);
            $result[$count]['start_time'] = $start_time['start_time'];
            $result[$count]['end_time'] = $start_time['end_time'];
            if($start_time['occassion'])
                $result[$count]['special_occassion'] = $start_time['occassion'];
            $count++;
        }
        /* Total Distance in Kilo Meter */
        if($order == 'distance'){
            if($cur_lat != '' && $cur_lag != '')
            {
                $count = 0;
                foreach($result as $club)
                {
                    $distance_in_km = $em->getRepository('IFlairSoapBundle:Partyfinder')->getDistance($cur_lat,$cur_lag,$club["latitude"],$club["longitude"]);
                    $result[$count]['distanceinkm'] = $distance_in_km;
                    $count++;
                }
                /* Extra code for sorting */
                $sort = array();
                foreach($result as $k=>$v) {
                    $sort['distanceinkm'][$k] = $v['distanceinkm'];
                }
                array_multisort($sort['distanceinkm'], SORT_ASC,$result);
            }
        }else{
            if($cur_lat != '' && $cur_lag != '')
            {
                $count = 0;
                foreach($result as $club)
                {
                    $distance_in_km = $em->getRepository('IFlairSoapBundle:Partyfinder')->getDistance($cur_lat,$cur_lag,$club["latitude"],$club["longitude"]);
                    $result[$count]['distanceinkm'] = $distance_in_km;
                    $count++;
                }
            }
        }

        $myresponse = array(
                'message' => 'listing successfully..',
                'total_club_count' => $total_party,
                'total_no_of_clubs' => count($result),
                'success' => true,
                'status' => Response::HTTP_OK,
                'content' => array(
                 'listing_data' => $result
                )
            );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}