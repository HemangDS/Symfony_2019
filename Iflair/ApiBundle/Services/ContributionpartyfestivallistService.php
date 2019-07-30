<?php
namespace Iflair\ApiBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;


class ContributionpartyfestivallistService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * User Registartion method details, user will be register when called.
     * @param string $name 
     * @return mixed
     */
    public function getcontributionpartyfestivallist($user_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $queryBuilder1 = $em->createQueryBuilder();
        $queryBuilder1->select('pi.id','pi.name as locationName','pi.createdDate','pi.header as contributionpartyimage')
                    ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')
                    ->where("pi.typeId = '2'")->andwhere("pi.userId = $user_id")
                    ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pi.countryId')->addSelect('pfctr.countryName as festivalcountry')
                    ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pi.cityId')->addSelect('pfc.cityName  as festivalcity')
                    ->join('IFlairSoapBundle\Entity\ContributionStatus', 'cs', Expr\Join::WITH, 'cs.id = pi.status_id')->addSelect('cs.statusName  as festivalStatus');
        $bar_collection_result = $queryBuilder1->getQuery()->getResult();
        $bar_final = array(); $count = 0;
        foreach($bar_collection_result as $result)
        {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $image_path = explode("/web",$result['contributionpartyimage']);
            $image_url = $baseurl.$image_path[1];
            $bar_final[$count] = $result;
            $bar_final[$count]['contributionpartyimage'] = $image_url;
            $bar_final[$count]['createdDate'] = $result['createdDate']->format('Y-m-d H:i:s');
            $count++;
        }
        $queryBuildert = $em->createQueryBuilder();
        $queryBuildert->select('pi.id')
                    ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')
                    ->where("pi.typeId = '2'")->andwhere("pi.userId = $user_id")
        ->join('IFlairSoapBundle\Entity\Partyfinder', 'p', Expr\Join::WITH, 'p.id = pi.partyFinderId')->addSelect('p.id as verifiedid');
        $night_collection_res = $queryBuildert->getQuery()->getResult();
        if(!empty($night_collection_res))
        {
            foreach($bar_final as $k => $value1) {
                foreach ($night_collection_res as $value2) {
                    if(isset($value1['id']) && isset($value2['id'])){
                        if($value1['id'] == $value2['id'])
                        {
                            $bar_final[$k]['verifiedid'] = $value2['verifiedid'];
                        }else{
                            if(!isset($bar_final[$k]['verifiedid']))
                                $bar_final[$k]['verifiedid'] = 0;
                        }
                    }
                }
            }
        }else{
            foreach($bar_final as $k => $value1) {                
                $bar_final[$k]['verifiedid'] = 0;
            }
        }

        // Nightclub Collection        
        $queryBuilder2 = $em->createQueryBuilder();
        $queryBuilder2->select('pi.id','pi.name as locationName','pi.createdDate','pi.header as contributionpartyimage')
                    ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')
                    ->where("pi.typeId = '1'")->andwhere("pi.userId = $user_id")
                    ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pi.countryId')->addSelect('pfctr.countryName as festivalcountry')
                    ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pi.cityId')->addSelect('pfc.cityName  as festivalcity')
                    ->join('IFlairSoapBundle\Entity\ContributionStatus', 'cs', Expr\Join::WITH, 'cs.id = pi.status_id')->addSelect('cs.statusName  as festivalStatus');
        $nightclub_collection_result = $queryBuilder2->getQuery()->getResult();
        $nightclub_final = array(); $count = 0;
        foreach($nightclub_collection_result as $result)
        {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $image_path = explode("/web",$result['contributionpartyimage']);
            if(isset($image_path[1]))
                $image_url = $baseurl.$image_path[1];
            else
                $image_url = $baseurl;
            $nightclub_final[$count] = $result;
            $nightclub_final[$count]['contributionpartyimage'] = $image_url;
            $nightclub_final[$count]['createdDate'] = $result['createdDate']->format('Y-m-d H:i:s');
            $count++;
        }

        $queryBuildery = $em->createQueryBuilder();
        $queryBuildery->select('pi.id')
                    ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')
                    ->where("pi.typeId = '1'")->andwhere("pi.userId = $user_id")
        ->join('IFlairSoapBundle\Entity\Partyfinder', 'p', Expr\Join::WITH, 'p.id = pi.partyFinderId')->addSelect('p.id as verifiedid');
        $night_collection_result1 = $queryBuildery->getQuery()->getResult();
        if(!empty($night_collection_result1))
        {
            foreach($nightclub_final as $k => $value1) {
                foreach ($night_collection_result1 as $value2) {
                    if(isset($value1['id']) && isset($value2['id'])){
                        if($value1['id'] == $value2['id'])
                        {
                            $nightclub_final[$k]['verifiedid'] = $value2['verifiedid'];
                        }else{
                            if(!isset($nightclub_final[$k]['verifiedid']))
                                $nightclub_final[$k]['verifiedid'] = 0;
                        }
                    }
                }
            }
        }else{
            foreach($nightclub_final as $k => $value1) {                
                $nightclub_final[$k]['verifiedid'] = 0;
            }
        }


        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('finp.id','finp.name as festivalName','finp.createdDate','finp.header')
                     ->from('IFlairFestivalBundle\Entity\FestivalInprogress', 'finp')->where("finp.userId = $user_id")
                     ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = finp.countryId')->addSelect('pfctr.countryName as festivalcountry')
                     ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = finp.cityId')->addSelect('pfc.cityName  as festivalcity')
                     ->join('IFlairFestivalBundle\Entity\Festival_inprogress_status', 'fis', Expr\Join::WITH, 'fis.festivalInprogressId = finp.id')
                     ->join('IFlairSoapBundle\Entity\ContributionStatus', 'cs', Expr\Join::WITH, 'cs.id = finp.status')->addSelect('cs.statusName as festivalStatus')
                     ->groupBy('finp.name');
        $festival_result = $queryBuilder->getQuery()->getResult();
        $festival_final = array(); $count = 0;
        foreach($festival_result as $result)
        {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            if($result['header']){
                $image_path = explode("/web",$result['header']);
                $image_url = $baseurl.$image_path[1];
            }else{
                $image_url = '';
            }
            $festival_final[$count] = $result;
            $festival_final[$count]['contributionfestivalimage'] = $image_url;
            $festival_final[$count]['createdDate'] = $result['createdDate']->format('Y-m-d H:i:s');
            unset($festival_final[$count]['header']);
            $count++;
        }

        $queryBuilderz = $em->createQueryBuilder();
        $queryBuilderz->select('finp.id')
                     ->from('IFlairFestivalBundle\Entity\FestivalInprogress', 'finp')->where("finp.userId = $user_id")
                     ->join('IFlairFestivalBundle\Entity\festival', 'f', Expr\Join::WITH, 'f.id = finp.festivalId')->addSelect('f.id as verifiedid');
        $fest_collection_result1 = $queryBuilderz->getQuery()->getResult();
        if(!empty($fest_collection_result1))
        {
            foreach($festival_final as $k => $value1) {
                foreach ($fest_collection_result1 as $value2) {
                    if(isset($value1['id']) && isset($value2['id'])){
                        if($value1['id'] == $value2['id'])
                        {
                            $festival_final[$k]['verifiedid'] = $value2['verifiedid'];
                        }else{
                            if(!isset($festival_final[$k]['verifiedid']))
                                $festival_final[$k]['verifiedid'] = 0;
                        }
                    }
                }
            }
        }else{
            foreach($festival_final as $k => $value1) {                
                $festival_final[$k]['verifiedid'] = 0;
            }
        }


        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'party festival Contribution list.',
            'bar_list' => $bar_final,
            'nightclub_list' => $nightclub_final,
            'festival_list' => $festival_final
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
