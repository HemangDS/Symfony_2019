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
use Doctrine\ORM\Query\Expr;
use DoctrineExtensions\Tests\Query\Mysql;

class GlobalclubbinglistService
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
    public function globalclubbinglist($partytype, $offset, $limit, $range_type, $cur_lat, $cur_log, $order, $searched_text,$is_open, $is_all)
    {
        //die('comesyyy');
        /* If open set status of open and close */
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        if($is_all == 'false'){
            $queryBuilder = $em->createQueryBuilder();
                /* Open close status */
                if($is_open == 'true')
                {
                    $queryBuilder->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle','pft.startTime','pft.endTime');
                }else{
                    $queryBuilder->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle');
                }

                /* Open close status */
                $queryBuilder
                ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
                if($partytype  == 'Nightclub')
                {
                    $queryBuilder->join("pf.clubTypeId","t")->where("t.name = 'Nightclub'")->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
                }else if($partytype  == 'Bar'){
                        $queryBuilder->join("pf.clubTypeId","t")->where("t.name = 'Bar'")->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
                }else{}
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
                /* Open close status */
                if($is_open == 'true')
                {
                    $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfindertiming', 'pft',  Expr\Join::WITH, 'pft.partyFinderId = pf.id');
                }
                /* Open close status */
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
                $queryBuilder->addSelect('pfc.cityName  as partyfindercty');

                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
                $queryBuilder->addSelect('pfctr.countryName as partyfindercntry');

                $queryBuilder->addSelect('avg(pfr.userRatings) as user_ratings')
                ->groupBy('pf.id')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
                
                if($range_type == 'Km')
                {            
                $queryBuilder->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
                }else{
                $queryBuilder->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
                }

            switch ($order) {
                case 'name':
                default:
                    $queryBuilder->orderBy('pf.clubTitle', 'ASC');
                    break;
                case 'rating':
                    $queryBuilder->orderBy('user_ratings', 'DESC');
                    break;
                case 'distance':
                    $queryBuilder->orderBy('distanceinkm', 'ASC');
                    break; 
            }
            $result = $queryBuilder->getQuery()->getResult();
            $count = 0;
            foreach($result as $club)
            {
                $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
                $result[$count]['club_logo'] = $image_name;
                $count++;
            }
            /* Open close status */
            if($is_open == 'true')
            {
                $count = 0; $result_open = array();
                foreach($result as $timing)
                {
                    $start_date = $timing['startTime'];
                    $end_date = $timing['endTime'];                
                    date_default_timezone_set("Asia/Kolkata");
                    if (time() >= strtotime($start_date) && time() <= strtotime($end_date)) {
                      $result[$count]['club_status'] = 'open';
                      $result_open = $result;
                    }else{
                        $result[$count]['club_status'] = 'close';
                    }
                    $count++;
                }
            }
            /* Open close status */

            /*COUNT*/
            $queryBuilder1 = $em->createQueryBuilder();
                /* Open close status */
                if($is_open == 'true')
                {
                    $queryBuilder->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle','pft.startTime','pft.endTime');
                }else{
                    $queryBuilder->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle');
                }
                /* Open close status */
                $queryBuilder1
                ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
                if($partytype  == 'Nightclub')
                {
                    $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Nightclub'")->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
                }else if($partytype  == 'Bar'){
                        $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Bar'")->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
                }else{}
                $queryBuilder1->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
                /* Open close status */
                if($is_open == 'true')
                {
                    $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfindertiming', 'pft',  Expr\Join::WITH, 'pft.partyFinderId = pf.id');
                }
                /* Open close status */
                $queryBuilder1->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
                $queryBuilder1->addSelect('pfc.cityName  as partyfindercty');

                $queryBuilder1->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
                $queryBuilder1->addSelect('pfctr.countryName as partyfindercntry');

                $queryBuilder1->addSelect('avg(pfr.userRatings) as user_ratings')
                ->groupBy('pf.id')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
                
                if($range_type == 'Km')
                {            
                $queryBuilder1->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
                }else{
                $queryBuilder1->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
                }
            $total_count = count($queryBuilder1->getQuery()->getResult());

            if($is_open == 'true'){
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $result_open,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else{
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $result,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }
            /*COUNT*/
        }else{
            /* search by city name*/
            $queryBuilder3 = $em->createQueryBuilder();
            $queryBuilder3->select('pfc.cityName as partyfindercty','e.latitude', 'e.longitude','pfc.id as city_id','pfl.id as partyfinderlocation_id','pf.id as id','pf.clubTitle', 'pft.name as partytype')
                ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc')
                ->where("pfc.cityName LIKE '%$searched_text%'");
            $queryBuilder3->join('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfl', Expr\Join::WITH, 'pfl.cityId = pfc.id');
            $queryBuilder3->join('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.clubLocationId = pfl.id');
            $queryBuilder3->join('IFlairSoapBundle\Entity\Partyfindertype', 'pft', Expr\Join::WITH, 'pf.clubTypeId = pft.id');
            $queryBuilder3->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder3->addSelect('pfctr.countryName as partyfindercntry');
            $queryBuilder3->join('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id')->groupBy('pf.id');
            $queryBuilder3->addSelect('avg(pfr.userRatings) as user_ratings');
            $queryBuilder3->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            if($range_type == 'Km')
            {            
            $queryBuilder3->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
            $queryBuilder3->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            switch ($order) {
                case 'name':
                default:
                    $queryBuilder3->orderBy('pf.clubTitle', 'ASC');
                    break;
                case 'rating':
                    $queryBuilder3->orderBy('user_ratings', 'DESC');
                    break;
                case 'distance':
                    $queryBuilder3->orderBy('distanceinkm', 'ASC');
                    break; 
            }
            $city_search = $queryBuilder3->getQuery()->getResult();
            $count = 0;
            foreach($city_search as $club)
            {
                $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
                $city_search[$count]['club_logo'] = $image_name;
                $count++;
            }
            

            /* City Count */
            $queryBuilder5 = $em->createQueryBuilder();
            $queryBuilder5->select('pfc.cityName as partyfindercty','e.latitude', 'e.longitude','pfc.id as city_id','pfl.id as partyfinderlocation_id','pf.id as id','pf.clubTitle')
                ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc')
                ->where("pfc.cityName LIKE '%$searched_text%'");
            $queryBuilder5->join('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfl', Expr\Join::WITH, 'pfl.cityId = pfc.id');
            $queryBuilder5->join('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.clubLocationId = pfl.id');
            $queryBuilder5->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder5->addSelect('pfctr.countryName as partyfindercntry');
            $queryBuilder5->join('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id')->groupBy('pf.id');
            $queryBuilder5->addSelect('avg(pfr.userRatings) as user_ratings');
            $queryBuilder5->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            if($range_type == 'Km')
            {            
            $queryBuilder5->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
            $queryBuilder5->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            $city_count = count($queryBuilder5->getQuery()->getResult());
            /* Country data */
            /* Search by country name */
            $queryBuilder4 = $em->createQueryBuilder();
            $queryBuilder4->select('pfctr.countryName as partyfindercntry','pfc.cityName as partyfindercty','pf.id as id','e.latitude', 'e.longitude','pf.clubTitle','pft.name as partytype')
                ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr')
                ->where("pfctr.countryName LIKE '%$searched_text%'");
            $queryBuilder4->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.countryId = pfctr.id');
            $queryBuilder4->join('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfl', Expr\Join::WITH, 'pfl.cityId = pfc.id');
            $queryBuilder4->join('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.clubLocationId = pfl.id');
            $queryBuilder4->join('IFlairSoapBundle\Entity\Partyfindertype', 'pft', Expr\Join::WITH, 'pf.clubTypeId = pft.id');
            $queryBuilder4->join('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id')->groupBy('pf.id');
            $queryBuilder4->addSelect('avg(pfr.userRatings) as user_ratings');
            $queryBuilder4->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            if($range_type == 'Km')
            {            
            $queryBuilder4->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
            $queryBuilder4->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            //$order = 'name';
            switch ($order) {
                case 'name':
                default:
                    $queryBuilder4->orderBy('pf.clubTitle', 'ASC');
                    break;
                case 'rating':
                    $queryBuilder4->orderBy('user_ratings', 'DESC');
                    break;
                case 'distance':
                    $queryBuilder4->orderBy('distanceinkm', 'ASC');
                    break; 
            }
            $country_search = $queryBuilder4->getQuery()->getResult();
            $count = 0;
            foreach($country_search as $club)
            {
                $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
                $country_search[$count]['club_logo'] = $image_name;
                $count++;
            }
            /* Country Count */
            $queryBuilder6 = $em->createQueryBuilder();
            $queryBuilder6->select('pfctr.countryName as partyfindercntry','pfc.cityName as partyfindercty','pf.id as id','e.latitude', 'e.longitude','pf.clubTitle')
                ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr')
                ->where("pfctr.countryName LIKE '%$searched_text%'");
            $queryBuilder6->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.countryId = pfctr.id');
            $queryBuilder6->join('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfl', Expr\Join::WITH, 'pfl.cityId = pfc.id');
            $queryBuilder6->join('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.clubLocationId = pfl.id');            
            $queryBuilder6->join('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id')->groupBy('pf.id');
            $queryBuilder6->addSelect('avg(pfr.userRatings) as user_ratings');
            $queryBuilder6->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            if($range_type == 'Km')
            {            
            $queryBuilder6->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
            $queryBuilder6->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            $country_count = $queryBuilder6->getQuery()->getResult();

            /*----------------------*/
            // nightclub data
            $queryBuilder7 = $em->createQueryBuilder();
            $queryBuilder7
                ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
                ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf');
            $queryBuilder7->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
            $queryBuilder7->join("pf.clubTypeId","t")
                ->where("t.name = 'Nightclub'")
                ->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
            $queryBuilder7->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
            $queryBuilder7->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
            $queryBuilder7->addSelect('pfc.cityName  as partyfindercty');
            $queryBuilder7->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder7->addSelect('pfctr.countryName as partyfindercntry');
            $queryBuilder7->addSelect('avg(pfr.userRatings) as user_ratings')->groupBy('pf.id');
            if($range_type == 'Km')
            {            
                $queryBuilder7->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
                $queryBuilder7->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            $queryBuilder7->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            switch ($order) {
                case 'name':
                default:
                    $queryBuilder7->orderBy('pf.clubTitle', 'ASC');
                    break;
            }
            $nightclub_data = $queryBuilder7->getQuery()->getResult();
            
            $count = 0;
            foreach($nightclub_data as $club)
            {
                $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
                $nightclub_data[$count]['club_logo'] = $image_name;
                $count++;
            }

            /* Count for nightclub */
            $queryBuilder0 = $em->createQueryBuilder();
            $queryBuilder0
                ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
                ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
            $queryBuilder0->join("pf.clubTypeId","t")
                ->where("t.name = 'Nightclub'")
                ->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
            $queryBuilder0->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
            $queryBuilder0->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
            $queryBuilder0->addSelect('pfc.cityName  as partyfindercty');
            $queryBuilder0->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder0->addSelect('pfctr.countryName as partyfindercntry');
            $queryBuilder0->addSelect('avg(pfr.userRatings) as user_ratings')->groupBy('pf.id');
            if($range_type == 'Km')
            {            
                $queryBuilder0->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
                $queryBuilder0->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            $queryBuilder0->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            switch ($order) {
                case 'name':
                default:
                    $queryBuilder0->orderBy('pf.clubTitle', 'ASC');
                    break;
            }
            $nightclub_count = $queryBuilder0->getQuery()->getResult();

            /* Bar Data */
            $queryBuilder1 = $em->createQueryBuilder();
            $queryBuilder1
                ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
                ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
            $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Bar'")->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
            $queryBuilder1->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
            $queryBuilder1->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
            $queryBuilder1->addSelect('pfc.cityName  as partyfindercty');
            $queryBuilder1->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder1->addSelect('pfctr.countryName as partyfindercntry');
            $queryBuilder1->addSelect('avg(pfr.userRatings) as user_ratings')->groupBy('pf.id');
            if($range_type == 'Km')
            {            
                $queryBuilder1->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
                $queryBuilder1->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            $queryBuilder1->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
            switch ($order) {
                case 'name':
                default:
                    $queryBuilder1->orderBy('pf.clubTitle', 'ASC');
                    break;
            }
            $bar_data = $queryBuilder1->getQuery()->getResult();

            /* Extra code for image */
            $count = 0;
            foreach($bar_data as $club)
            {
                $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
                $bar_data[$count]['club_logo'] = $image_name;
                $count++;
            }
            /* Extra code for image */
            
            // Bar count
            $queryBuilder2 = $em->createQueryBuilder();
            $queryBuilder2
                ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
                ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
            $queryBuilder2->join("pf.clubTypeId","t")->where("t.name = 'Bar'")
                ->andwhere('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_text.'%');
            $queryBuilder2->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
            $queryBuilder2->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
            $queryBuilder2->addSelect('pfc.cityName  as partyfindercty');
            $queryBuilder2->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder2->addSelect('pfctr.countryName as partyfindercntry');
            $queryBuilder2->addSelect('avg(pfr.userRatings) as user_ratings')->groupBy('pf.id');
            if($range_type == 'Km')
            {
                $queryBuilder2->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }else{
                $queryBuilder2->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_log . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm');
            }
            $queryBuilder2->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');        
            switch ($order) {
                case 'name':
                default:
                    $queryBuilder2->orderBy('pf.clubTitle', 'ASC');
                    break;
            }
            $bar_count = $queryBuilder2->getQuery()->getResult();
            foreach($country_search as $data)
            {
                if($data['partytype'] == 'Nightclub')
                {
                    $nightclub_data[] = $data;
                }
                if($data['partytype'] == 'Bar')
                {
                    $bar_data[] = $data;
                }
            }
            foreach($city_search as $data)
            {
                if($data['partytype'] == 'Nightclub')
                {
                    $nightclub_data[] = $data;
                }
                if($data['partytype'] == 'Bar')
                {
                    $bar_data[] = $data;
                }
            }
        }



        if(strtolower($partytype) == 'bar' && $is_all == 'false'){
            if($order == 'name')
            {
                array_multisort(array_column($bar_data, 'clubTitle'),SORT_ASC,$bar_data); // sorty by name
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $bar_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'rating')
            {
                array_multisort(array_column($bar_data, 'user_ratings'),SORT_ASC,$bar_data); // sorty by name
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $bar_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'distance')
            {
                array_multisort(array_column($bar_data, 'distanceinkm'),SORT_ASC,$bar_data); //sory by distance
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $bar_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }
        }else if(strtolower($partytype) == 'bar' && $is_all == 'true'){
            $check_data = array();
            $club_data = array();
            foreach($bar_data as $data)
            {
                if(!in_array($data['clubTitle'], $check_data))
                {
                    array_push($check_data, $data['clubTitle']);
                    $club_data[] = $data;
                }
            }
            foreach($country_search as $data)
            {
                if(!in_array($data['clubTitle'], $check_data))
                {
                    if($data['partytype'] != 'Nightclub')
                    {
                        array_push($check_data, $data['clubTitle']);
                        $club_data[] = $data;
                    }
                }
            }
            foreach($city_search as $data)
            {
                if(!in_array($data['clubTitle'], $check_data))
                {
                    if($data['partytype'] != 'Nightclub')
                    {
                        array_push($check_data, $data['clubTitle']);
                        $club_data[] = $data;
                    }
                }
            }
            if($order == 'name')
            {
                array_multisort(array_column($club_data, 'clubTitle'),SORT_ASC,$club_data); // sorty by name
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $club_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'rating')
            {
                array_multisort(array_column($club_data, 'user_ratings'),SORT_DESC,$club_data); // sory by rating
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $club_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'distance')
            {
                array_multisort(array_column($club_data, 'distanceinkm'),SORT_ASC,$club_data); //sory by distance
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $club_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }
        }else if(strtolower($partytype) == 'nightclub' && $is_all == 'false'){
            if($order == 'name')
            {
                array_multisort(array_column($nightclub_data, 'clubTitle'),SORT_ASC,$nightclub_data); // sorty by name
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $nightclub_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'rating')
            {
                array_multisort(array_column($nightclub_data, 'user_ratings'),SORT_DESC,$nightclub_data); // sory by rating
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $nightclub_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'distance')
            {
                array_multisort(array_column($nightclub_data, 'distanceinkm'),SORT_ASC,$nightclub_data); //sory by distance
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $nightclub_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }
        }else if(strtolower($partytype) == 'nightclub' && $is_all == 'true'){
            $check_data = array();
            $club_data = array();
            foreach($nightclub_data as $data)
            {
                if(!in_array($data['clubTitle'], $check_data))
                {
                    array_push($check_data, $data['clubTitle']);
                    $club_data[] = $data;
                }
            }
            foreach($country_search as $data)
            {
                if(!in_array($data['clubTitle'], $check_data))
                {
                    if($data['partytype'] == 'Nightclub')
                    {
                        array_push($check_data, $data['clubTitle']);
                        $club_data[] = $data;
                    }
                }
            }
            foreach($city_search as $data)
            {
                if(!in_array($data['clubTitle'], $check_data))
                {
                    if($data['partytype'] == 'Nightclub')
                    {
                        array_push($check_data, $data['clubTitle']);
                        $club_data[] = $data;
                    }
                }
            }
            if($order == 'name')
            {
                array_multisort(array_column($club_data, 'clubTitle'),SORT_ASC,$club_data); // sorty by name
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $club_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'rating')
            {
                array_multisort(array_column($club_data, 'user_ratings'),SORT_DESC,$club_data); // sory by rating
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $club_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }else if($order == 'distance')
            {
                array_multisort(array_column($club_data, 'distanceinkm'),SORT_ASC,$club_data); //sory by distance
                $myresponse = array(
                    'success' => true,
                    'status' => Response::HTTP_OK,
                    'message' => 'clubbing list successfully.',
                    'clubbing_data' => $club_data,
                    'clubbing_count' => $total_count            
                );
                $finalResponse = json_encode($myresponse);
                return $finalResponse;
            }
        }
        /** COUNT END **/
    }
}