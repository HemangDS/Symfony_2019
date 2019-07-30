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
use Doctrine\ORM\Query\Expr;
use DoctrineExtensions\Tests\Query\Mysql;

class PartyvisitedcontinentlistService
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
    public function partyvisitedcontinentlist($user_id, $city_id, $type)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $con = $this->doctrine->getManager()->getConnection();

        if($type == "1") // 1. all list
        {        
            $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id.' AND `city_id` = '.$city_id);
            $fest_result = $fest_stmt->fetchAll();
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
            $final_festival_result = array(); $count = 0;
            foreach($fest_result as $fest) {            
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('fs.id','fs.title')->from('IFlairFestivalBundle\Entity\festival', 'fs');
                $queryBuilder->where("fs.id = '".$fest['festival_id']."'");
                $queryBuilder->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'");
                $festival_viewed_result = $queryBuilder->getQuery()->getResult();

                $queryBuilderx = $em->createQueryBuilder();
                $queryBuilderx->select('fstr.userRatings as user_ratings')->from('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr');
                $queryBuilderx->where('fstr.festivalId = '.$fest['festival_id'].' AND fstr.userId = '.$user_id);
                $festival_viewed_result1 = $queryBuilderx->getQuery()->getResult();
                
                $queryBuildery = $em->createQueryBuilder();            
                $queryBuildery->select('ff.id as favourite')->from('IFlairFestivalBundle\Entity\festivalFavourite', 'ff');
                $queryBuildery->where('ff.festivalId = '.$fest['festival_id'].' AND ff.userId = '.$user_id);
                $festival_viewed_result2 = $queryBuildery->getQuery()->getResult();

                if($festival_viewed_result1)
                {
                    $rating_total = 0;
                    foreach($festival_viewed_result1 as $rating)
                    {
                        $rating_total+=$rating['user_ratings'];
                    }
                    $rating_ = $rating_total / count($festival_viewed_result1);
                    $festival_viewed_result[0]['user_ratings'] = "$rating_";
                }else{
                    $festival_viewed_result[0]['user_ratings'] = "0";
                }

                if($festival_viewed_result2)
                {
                    $festival_viewed_result[0]['favourite'] = 'true';
                }else{
                    $festival_viewed_result[0]['favourite'] = "false";
                }

                $queryBuilder1 = $em->createQueryBuilder();

                $festival_organiser = $em->getRepository('IFlairFestivalBundle:festival_organizer')->findOneByFestivalId($fest['festival_id']);
                if(isset($festival_organiser))
                {
                    if($festival_organiser->getLatitude() != '')
                        $festival_viewed_result[0]['latitude'] = $festival_organiser->getLatitude();
                    else
                        $festival_viewed_result[0]['latitude'] = "";
                    if($festival_organiser->getLongitude() != '')
                        $festival_viewed_result[0]['longitude'] = $festival_organiser->getLongitude();
                    else
                        $festival_viewed_result[0]['longitude'] = "";
                }else{
                    $festival_viewed_result[0]['latitude'] = 0;
                    $festival_viewed_result[0]['longitude'] = 0;
                }
                
                $queryBuilder1->select('count(fv.id) as festival_visit, max(fv.viewedDate) as lastvieweddate')->from('IFlairFestivalBundle\Entity\festival_view', 'fv')->where('fv.festivalId = '.$fest['festival_id'].' AND fv.userId = '.$user_id);
                $festival_viewed_date = $queryBuilder1->getQuery()->getResult();
                $festival_viewed_result[0]['view'] = $festival_viewed_date[0]['festival_visit'];
                $festival_viewed_result[0]['lastvieweddate'] = $festival_viewed_date[0]['lastvieweddate'];
                $festival_viewed_result[0]['imageName'] = $image_path.$festival_viewed_result[0]['imageName'];
                $festival_viewed_result[0]['type'] = 'Festival';
                $festival_admin = $em->getRepository('IFlairFestivalBundle:festival')->findOneBy(array('id' => $fest['festival_id']));
                if(!empty($festival_admin))
                {
                    if($festival_admin->getUserAdmin()->getId() == $user_id)
                    {
                        $festival_viewed_result[0]['user_admin'] = 'true';
                    }else{
                        $festival_viewed_result[0]['user_admin'] = 'false';
                    }
                }else{
                    $festival_viewed_result[0]['user_admin'] = 'false';
                }
                $final_festival_result[$count] = $festival_viewed_result[0];
                $count++;
            }            
            
            //$user_id = '26'; $city_id = '122240'; $count = '0';
            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id.' AND `city_id` = '.$city_id);
            $party_result = $party_stmt->fetchAll();
            $final_party_result = array();
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
            foreach($party_result as $party) {
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pf.id','pf.clubTitle as title', 'e.latitude', 'e.longitude', 'pff.id as favourite')->from('IFlairSoapBundle\Entity\Partyfinder', 'pf');
                $queryBuilder->where("pf.id = '".$party['club_id']."'");            
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindertype', 'pft', Expr\Join::WITH, 'pf.clubTypeId = pft.id');
                $queryBuilder->addSelect('pft.name as type');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderfavorite', 'pff', Expr\Join::WITH, 'pff.partyFinderId = pf.id AND pff.userId ='.$user_id);
                $queryBuilder->join('IFlairSoapBundle\Entity\PartyTypeRatings', 'ptr', Expr\Join::WITH, 'ptr.partyFinderId = pf.id AND ptr.userId = '.$user_id);
                $queryBuilder->addSelect('avg(ptr.userRatings) as user_ratings')->groupBy('pf.id');
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfinderimage', 'pfi', Expr\Join::WITH, 'pfi.partyFinderId = pf.id');
                $queryBuilder->addSelect('pfi.imageName as imageName');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderviews', 'pfv', Expr\Join::WITH, 'pfv.partyFinderId = pf.id AND pfv.viewedUserId = '.$user_id);
                $queryBuilder->addSelect('count(pfv.partyFinderId) as view , max(pfv.createdDate) as lastvieweddate');
                $party_info = $queryBuilder->getQuery()->getResult();
                $party_info[0]['imageName'] = $image_path.$party_info[0]['imageName'];
                $partyfinder_admin = $em->getRepository('IFlairSoapBundle:Partyfinder')->findBy(array('id' => $party['club_id'], 'user_admin' => $user_id));
                $user_admin = 'false';
                $party_info[0]['user_admin'] = 'false';
                if(isset($partyfinder_admin))
                {
                    foreach($partyfinder_admin as $admin)
                    {
                        $party_info[0]['user_admin'] = 'true';
                    }
                }
                $final_party_result[$count] = $party_info[0];
                $count++;
            }

            $result = array_merge($final_festival_result, $final_party_result);
            $new_result = array();
            foreach($result as $data)
            {
                if($data['favourite'] != 'false')
                {
                    $data['favourite'] = 'true';
                }else{
                    $data['favourite'] = 'false';
                }
                $new_result[] = $data;
            }
            $result = $new_result;  
        }else if ($type == "2"){ // 2 = For Nightclubs
            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id.' AND `club_type` = "Nightclub"');
            $party_result = $party_stmt->fetchAll();
            $final_party_result = array();
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
            $count = 0;
            foreach($party_result as $party) {
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pf.id','pf.clubTitle as title', 'e.latitude', 'e.longitude', 'pff.id as favourite')->from('IFlairSoapBundle\Entity\Partyfinder', 'pf');
                $queryBuilder->where("pf.id = '".$party['club_id']."'");            
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindertype', 'pft', Expr\Join::WITH, 'pf.clubTypeId = pft.id');
                $queryBuilder->addSelect('pft.name as type');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderfavorite', 'pff', Expr\Join::WITH, 'pff.partyFinderId = pf.id AND pff.userId ='.$user_id);
                $queryBuilder->join('IFlairSoapBundle\Entity\PartyTypeRatings', 'ptr', Expr\Join::WITH, 'ptr.partyFinderId = pf.id AND ptr.userId = '.$user_id);
                $queryBuilder->addSelect('avg(ptr.userRatings) as user_ratings')->groupBy('pf.id');
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfinderimage', 'pfi', Expr\Join::WITH, 'pfi.partyFinderId = pf.id');
                $queryBuilder->addSelect('pfi.imageName as imageName');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderviews', 'pfv', Expr\Join::WITH, 'pfv.partyFinderId = pf.id AND pfv.viewedUserId = '.$user_id);
                $queryBuilder->addSelect('count(pfv.partyFinderId) as view , max(pfv.createdDate) as lastvieweddate');
                $party_info = $queryBuilder->getQuery()->getResult();
                $party_info[0]['imageName'] = $image_path.$party_info[0]['imageName'];
                $partyfinder_admin = $em->getRepository('IFlairSoapBundle:Partyfinder')->findBy(array('id' => $party['club_id'], 'user_admin' => $user_id));
                $user_admin = 'false';
                $party_info[0]['user_admin'] = 'false';
                if(isset($partyfinder_admin))
                {
                    foreach($partyfinder_admin as $admin)
                    {
                        $party_info[0]['user_admin'] = 'true';
                    }
                }
                $final_party_result[$count] = $party_info[0];
                $count++;
            }
            $result = $final_party_result;
            $new_result = array();
            foreach($result as $data)
            {
                if($data['favourite'])
                {
                    $data['favourite'] = 'true';
                }else{
                    $data['favourite'] = 'false';
                }
                $new_result[] = $data;
            }
            $result = $new_result;
        }else if ($type == "3"){ // 3= Bars
            $party_stmt = $con->executeQuery('SELECT * FROM view_visited_partyfinder where `user_id` = '.$user_id.' AND `club_type` = "Bar"');
            $party_result = $party_stmt->fetchAll();
            $final_party_result = array();
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
            $count = 0;
            foreach($party_result as $party) {
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('pf.id','pf.clubTitle as title', 'e.latitude', 'e.longitude', 'pff.id as favourite')->from('IFlairSoapBundle\Entity\Partyfinder', 'pf');
                $queryBuilder->where("pf.id = '".$party['club_id']."'");            
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindertype', 'pft', Expr\Join::WITH, 'pf.clubTypeId = pft.id');
                $queryBuilder->addSelect('pft.name as type');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
                $queryBuilder->join('IFlairSoapBundle\Entity\PartyTypeRatings', 'ptr', Expr\Join::WITH, 'ptr.partyFinderId = pf.id AND ptr.userId = '.$user_id);
                $queryBuilder->addSelect('avg(ptr.userRatings) as user_ratings')->groupBy('pf.id');
                $queryBuilder->join('IFlairSoapBundle\Entity\Partyfinderimage', 'pfi', Expr\Join::WITH, 'pfi.partyFinderId = pf.id');
                $queryBuilder->addSelect('pfi.imageName as imageName');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderviews', 'pfv', Expr\Join::WITH, 'pfv.partyFinderId = pf.id AND pfv.viewedUserId = '.$user_id);
                $queryBuilder->addSelect('count(pfv.partyFinderId) as view , max(pfv.createdDate) as lastvieweddate');
                $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderfavorite', 'pff', Expr\Join::WITH, 'pff.partyFinderId = pf.id AND pff.userId ='.$user_id);

                $party_info = $queryBuilder->getQuery()->getResult();
                $party_info[0]['imageName'] = $image_path.$party_info[0]['imageName'];
                $partyfinder_admin = $em->getRepository('IFlairSoapBundle:Partyfinder')->findBy(array('id' => $party['club_id'], 'user_admin' => $user_id));
                $user_admin = 'false';
                $party_info[0]['user_admin'] = 'false';
                if(isset($partyfinder_admin))
                {
                    foreach($partyfinder_admin as $admin)
                    {
                        $party_info[0]['user_admin'] = 'true';
                    }
                }

                $final_party_result[$count] = $party_info[0];
                $count++;
            }
            $result = $final_party_result;
            $new_result = array();
            foreach($result as $data)
            {
                if($data['favourite'])
                {
                    $data['favourite'] = 'true';
                }else{
                    $data['favourite'] = 'false';
                }
                $new_result[] = $data;
            }
            $result = $new_result;
        }else if ($type == "4"){ // 4 = Festivals
            
            $fest_stmt = $con->executeQuery('SELECT * FROM view_visited_festival where `user_id` = '.$user_id);
            $fest_result = $fest_stmt->fetchAll();
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
            $final_festival_result = array(); $count = 0;
            foreach($fest_result as $fest) {            
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select('fs.id','fs.title','fsimg.imageName')->from('IFlairFestivalBundle\Entity\festival', 'fs');
                $queryBuilder->where("fs.id = '".$fest['festival_id']."'");
                $queryBuilder->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'");
                $queryBuilder->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, 'fstr.festivalId = fs.id AND fstr.userId = '.$user_id);
                $queryBuilder->addSelect('avg(fstr.userRatings) as user_ratings')->groupBy('fs.id');

                $queryBuilder->join('IFlairFestivalBundle\Entity\festival_organizer', 'fo', Expr\Join::WITH, 'fo.festivalId = fs.id AND fstr.userId = '.$user_id);
                
                $festival_viewed_result = $queryBuilder->getQuery()->getResult();
                $queryBuilder1 = $em->createQueryBuilder();
                $festival_organiser = $em->getRepository('IFlairFestivalBundle:festival_organizer')->findOneByFestivalId($fest['festival_id']);
                if(isset($festival_organiser))
                {
                    $festival_viewed_result[0]['latitude'] = $festival_organiser->getLatitude();
                    $festival_viewed_result[0]['longitude'] = $festival_organiser->getLongitude();
                }else{
                    $festival_viewed_result[0]['latitude'] = 0;
                    $festival_viewed_result[0]['longitude'] = 0;
                }

                $festival_favourite = $em->getRepository('IFlairFestivalBundle:festivalFavourite')->findBy(array('festivalId' => $fest['festival_id'],'userId' => $user_id));

                if(!empty($festival_favourite))
                {
                    foreach($festival_favourite as $fav)
                    {
                        $festival_viewed_result[0]['favourite'] =  'true';
                    }
                }else{
                    $festival_viewed_result[0]['favourite'] =  'false';
                }

                $festival_admin = $em->getRepository('IFlairFestivalBundle:festival')->findOneBy(array('id' => $fest['festival_id']));
                if(!empty($festival_admin))
                {
                    if($festival_admin->getUserAdmin()->getId() == $user_id)
                    {
                        $festival_viewed_result[0]['user_admin'] = 'true';
                    }else{
                        $festival_viewed_result[0]['user_admin'] = 'false';
                    }
                }else{
                    $festival_viewed_result[0]['user_admin'] = 'false';
                }
                if($fest['festival_id'])
                {
                    $queryBuilder1->select('count(fv.id) as festival_visit, max(fv.viewedDate) as lastvieweddate')->from('IFlairFestivalBundle\Entity\festival_view', 'fv')->where('fv.festivalId = '.$fest['festival_id'].' AND fv.userId = '.$user_id);
                    $festival_viewed_date = $queryBuilder1->getQuery()->getResult();
                    $festival_viewed_result[0]['view'] = $festival_viewed_date[0]['festival_visit'];
                }else{
                    $festival_viewed_result[0]['view'] = 0;
                }

                $festival_viewed_result[0]['lastvieweddate'] = $festival_viewed_date[0]['lastvieweddate'];
                if(isset($festival_viewed_result[0]['imageName']))
                    $festival_viewed_result[0]['imageName'] = $image_path.$festival_viewed_result[0]['imageName'];
                else
                    $festival_viewed_result[0]['imageName'] = $image_path;
                $festival_viewed_result[0]['type'] = 'Festival';
                $final_festival_result[$count] = $festival_viewed_result[0];
                $count++;
            }

            $result = $final_festival_result;
            $check_result = array();
            foreach($result as $res)
            {
                if(!empty($res['id']))
                {
                    $check_result[] = $res;
                }
            }
            $result = $check_result;
        }

        /*$myfile = fopen("/opt/lampp/htdocs/test.txt", "w") or die("Unable to open file!");
        fwrite($myfile, 'comes');
        fwrite($myfile, json_encode($result));*/

        
        
        $myresponse = array(
            'message' => 'visited area',
            'continent_data' => $result,
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
