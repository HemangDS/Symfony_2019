<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// ENTITY
use Doctrine\ORM\Query\Expr;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfinderviews;
use IFlairSoapBundle\Entity\Partyfinder;

class PartyratingsService
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
    public function getPartyclubratings($user_id, $type, $offset, $limit, $sort_name)
    {
        $party_type = array('Nightclub','Bar');
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        
        if($type != 'Festival')
        {
            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder
                ->select('pfr.userRatings', 'pfr.modifiedDate as modifiedTime' , 'pf.id', 'pf.clubTitle', 't.name')
                ->from('IFlairSoapBundle\Entity\PartyTypeRatings', 'pfr')
                ->where("pfr.userId = '".$user_id."'")
                ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pfr.partyFinderId');
                $queryBuilder->addSelect('avg(pfr.userRatings) as userRatings')->groupBy('pf.id');
                if(in_array($type, $party_type)) {
                    $queryBuilder->join("IFlairSoapBundle\Entity\Partyfindertype", "t", Expr\Join::WITH, "pf.clubTypeId = t.id AND t.name = '".$type."' AND t.name IS NOT NULL");
                }else{
                    $queryBuilder->join("IFlairSoapBundle\Entity\Partyfindertiming", "tm", Expr\Join::WITH, "date(tm.timingDate) = date(pfr.modifiedTime)")->distinct();
                }
            $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit);

           switch ($sort_name) {
                case 'name': 
                    $queryBuilder->orderBy('pf.clubTitle', 'ASC');
                    break; // other filters are soonest_date and latest_date
                case 'rating': 
                    $queryBuilder->orderBy('userRatings', 'DESC');
                    break; // other filters are soonest_date and latest_date
                case 'date':
                    $queryBuilder->orderBy('pfr.modifiedDate', 'DESC');
                    break; // other filters are soonest_date and latest_date
            }

            $result = $queryBuilder->getQuery()->getResult();

            $count = 0;
            foreach($result as $club)
            {
                $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
                $result[$count]['club_logo'] = $image_name;
                $count++;
            }
            $count = 0;
            foreach($result as $club)
            {
                $result[$count]['modifiedTime'] = $club['modifiedTime']->format('Y-m-d H:i:s');
                $count++;
            }
            $party_data = array();
            $count  = 0;
            foreach($result as $res)
            {
                $party_data[$count] = $res;
                $location = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($res['id']);
                foreach($location as $loc)
                {
                    $latitude = $loc->getClubLocationId()->getLatitude();
                    $longitude =  $loc->getClubLocationId()->getLongitude();
                    $party_data[$count]['latitude'] = $latitude;
                    $party_data[$count]['longitude'] = $longitude;
                }
                $count++;
            }

             // Code for favourite 
            $count = 0; $party_data_array = array();
            foreach($party_data as $data)
            {
                $party_data_array[$count] = $data;
                $favourite = $em->getRepository('IFlairSoapBundle:Partyfinderfavorite')->findBy(array('userId' => $user_id, 'partyFinderId' => $data['id']));
                if($favourite)
                {
                    foreach($favourite as $fvurite)
                    {
                        $favourite_id = $fvurite->getId();
                    }
                    $party_data_array[$count]['fvtStatus'] = 'true';
                }else{
                    $party_data_array[$count]['fvtStatus'] = 'false';
                }
                $count++;
            }
             // Code for favourite
        }
// ===========================COUNT NIGHTCLUB==========================================
        $Nightclub_data_count = 0; $p_type = 'Nightclub';
        $queryBuilder1 = $em->createQueryBuilder();
        $queryBuilder1
            ->select('pfr.userRatings', 'pf.id', 'pf.clubTitle', 'pfr.modifiedDate as modifiedTime', 't.name')
            ->from('IFlairSoapBundle\Entity\PartyTypeRatings', 'pfr')
            ->where("pfr.userId = '".$user_id."'")
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pfr.partyFinderId');
            $queryBuilder1->addSelect('avg(pfr.userRatings) as userRatings')->groupBy('pf.id');
            if(in_array($p_type, $party_type)) {
                $queryBuilder1->join("IFlairSoapBundle\Entity\Partyfindertype", "t", Expr\Join::WITH, "pf.clubTypeId = t.id AND t.name = '".$p_type."' AND t.name IS NOT NULL");
            }
        $total_clubresult = $queryBuilder1->getQuery()->getResult();
        $Nightclub_data_count = count($total_clubresult);
// ====================================COUNT BAR========================================
        $total_barresult = 0; $prt_type = 'Bar';
        $queryBuilder2 = $em->createQueryBuilder();
        $queryBuilder2
            ->select('pfr.userRatings', 'pf.id', 'pf.clubTitle', 'pfr.modifiedDate as modifiedTime', 't.name')
            ->from('IFlairSoapBundle\Entity\PartyTypeRatings', 'pfr')
            ->where("pfr.userId = '".$user_id."'")
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pfr.partyFinderId');
            $queryBuilder2->addSelect('avg(pfr.userRatings) as userRatings')->groupBy('pf.id');
            if(in_array($prt_type, $party_type)) {
                $queryBuilder2->join("IFlairSoapBundle\Entity\Partyfindertype", "t", Expr\Join::WITH, "pf.clubTypeId = t.id AND t.name = '".$prt_type."' AND t.name IS NOT NULL");
            }
        $total_barresult = $queryBuilder2->getQuery()->getResult();
        $Bar_data_count = count($total_barresult);
// =================================FESTIVAL DATA====================================== 

        if($type == 'Festival')
        {
            $queryBuilderx = $em->createQueryBuilder();
            $queryBuilderx->select('fs.id','fs.title as clubTitle','fstr.modifiedDate as modifiedTime','fsimg.imageName as club_logo','fsli.fesLatitude as latitude','fsli.fesLongitude as longitude');
            $queryBuilderx->from('IFlairFestivalBundle\Entity\festival', 'fs');
            $queryBuilderx->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, "fstr.festivalId = fs.id AND fstr.userId = '".$user_id."'");
            $queryBuilderx->addSelect('avg(fstr.userRatings) as userRatings')->groupBy('fs.id')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'")
            ->leftJoin('IFlairFestivalBundle\Entity\festival_location', 'fsli',  Expr\Join::WITH, 'fsli.id = fs.festivalLocationId');
            $queryBuilderx
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            switch ($sort_name) {
                case 'name': 
                    $queryBuilderx->orderBy('clubTitle', 'ASC');
                    break; // other filters are soonest_date and latest_date
                case 'rating': 
                    $queryBuilderx->orderBy('userRatings', 'DESC');
                    break; // other filters are soonest_date and latest_date
                case 'date':
                    $queryBuilderx->orderBy('modifiedTime', 'DESC');
                    break; // other filters are soonest_date and latest_date
            }
            $festival_result = $queryBuilderx->getQuery()->getResult();
            $count = 0;
            foreach($festival_result as $data)
            {
                $festival_result[$count] = $data;                
                $festival_result[$count]['name'] = 'Festival';
                $count++;
            }
            $count = 0;
            $fest_result = array();
            foreach($festival_result as $festival)
            {
                $fest_result[$count] = $festival;
                $fest_result[$count]['modifiedTime'] = $festival['modifiedTime']->format('Y-m-d H:i:s');
                $count++;
            }
            $count = 0;
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
            foreach($fest_result as $data)
            {
                $fest_result[$count] = $data;                
                $fest_result[$count]['club_logo'] = $image_path.$data['club_logo'];
                $count++;
            }
            
            /* Code for favourite */
            $count = 0;
            foreach($fest_result as $data)
            {
                $fest_result[$count] = $data;
                $favourite = $em->getRepository('IFlairFestivalBundle:festivalFavourite')->findBy(array('userId' => $user_id, 'festivalId' => $data['id']));
                if($favourite)
                {
                    foreach($favourite as $fvurite)
                    {
                        $favourite_id = $fvurite->getId();
                    }
                    $fest_result[$count]['fvtStatus'] = 'true';
                }else{
                    $fest_result[$count]['fvtStatus'] = 'false';
                }
                $count++;
            }
            /* Code for favourite */
        }
/*=================================FESTIVAL COUNT====================================== */
        $queryBuildercount = $em->createQueryBuilder();
        //$queryBuildercount->select('fs.id','fs.title','fstr.modifiedDate','fsimg.imageName','fsli.fesLatitude','fsli.fesLongitude');
        $queryBuildercount->select('fs.id','fs.title as clubTitle','fstr.modifiedDate as modifiedTime','fsimg.imageName as club_logo','fsli.fesLatitude as latitude','fsli.fesLongitude as longitude');
        $queryBuildercount->from('IFlairFestivalBundle\Entity\festival', 'fs');
        $queryBuildercount->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, "fstr.festivalId = fs.id AND fstr.userId = '".$user_id."'");
        $queryBuildercount->addSelect('avg(fstr.userRatings) as userRatings')->groupBy('fs.id')
        ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'")
        ->leftJoin('IFlairFestivalBundle\Entity\festival_location', 'fsli',  Expr\Join::WITH, 'fsli.id = fs.festivalLocationId');
        $festival_count = $queryBuildercount->getQuery()->getResult();





        if($type == 'Festival')
        {
            $myresponse = array(
                'partydata' => $fest_result,
                'total_nightclub' => $Nightclub_data_count,
                'total_bar' => $Bar_data_count,
                'total_festival' => count($festival_count),
                'message' => 'rated data',
                'success' => true,
                'status' => Response::HTTP_OK,
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }else{
            $myresponse = array(
                'partydata' => $party_data_array,
                'total_nightclub' => $Nightclub_data_count,
                'total_bar' => $Bar_data_count,
                'total_festival' => count($festival_count),
                'message' => 'rated data',
                'success' => true,
                'status' => Response::HTTP_OK,
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }
    }
}