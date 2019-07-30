<?php
namespace Iflair\ApiBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;


class ContributionpartyfestivalviewService
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
	public function getcontributionpartyfestivalview($view_type, $view_id,$varified_id)
	{
		$request = $this->request->getCurrentRequest();
		$em = $this->doctrine->getManager();
		$view_result = array();

		if($view_type == 'bar'){
				$queryBuilder1 = $em->createQueryBuilder();
		        $queryBuilder1->select('pi.id','pi.name','pi.createdDate', 'pi.header  as contributionpartyimage')
		                    ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')
		                    ->where("pi.typeId = '2'")->andwhere("pi.id = $view_id")
		                    ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pi.countryId')->addSelect('pfctr.countryName as festivalcountry')
		                    ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pi.cityId')->addSelect('pfc.cityName  as festivalcity')
		                    ->join('IFlairSoapBundle\Entity\ContributionStatus', 'cs', Expr\Join::WITH, 'cs.id = pi.status_id')->addSelect('cs.statusName  as festivalStatus');
		        $bar_collection_result = $queryBuilder1->getQuery()->getResult();
		        $bar_final = array();
		        foreach($bar_collection_result as $result)
		        {
		            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
		            $image_path = explode("/web",$result['contributionpartyimage']);
		            $image_url = $baseurl.$image_path[1];
		            $bar_final = $result;
		            $bar_final['contributionpartyimage'] = $image_url;
		            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
		            $country_logo = $image_path.strtolower(str_replace(' ', '_', $result['festivalcountry'])).'.png';
		            $bar_final['createdDate'] = $result['createdDate']->format('Y-m-d H:i:s');
		            $bar_final['country_logo'] = $country_logo;
		        }
		        $queryBuilder1 = $em->createQueryBuilder();
		        $queryBuilder1->select('pi.id')->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')->where("pi.id = $view_id")
		        ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pi.partyFinderId');
		        $queryBuilder1->addSelect('pf.id as partyfinderid');
		        $queryBuilder1->leftJoin('IFlairSoapBundle\Entity\Partyfinderfavorite', 'pff', Expr\Join::WITH, 'pff.partyFinderId = pf.id');
		        $queryBuilder1->addSelect('count(pff.partyFinderId) as partyfindercount');
		        $partyfindercount = $queryBuilder1->getQuery()->getResult();

		        $queryBuilder2 = $em->createQueryBuilder();
		        $queryBuilder2->select('pi.id')->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')->where("pi.id = $view_id")
		        ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pi.partyFinderId');
		        $queryBuilder2->addSelect('pf.id as partyfinderid');
		        $queryBuilder2->leftJoin('IFlairSoapBundle\Entity\Partyfinderviews', 'pfv', Expr\Join::WITH, 'pfv.partyFinderId = pf.id');
		        $queryBuilder2->addSelect('count(pfv.partyFinderId) as partyfinderviews');
		        $partyfinderviews = $queryBuilder2->getQuery()->getResult();
		        
		        $bar_final['partyfindercount'] = $partyfindercount[0]['partyfindercount'];
		        $bar_final['partyfinderviews'] = $partyfinderviews[0]['partyfinderviews'];

		        if($varified_id != 0)
		        {
			        $queryBuilder5 = $em->createQueryBuilder();
			        $queryBuilder5->select('pinp.createdDate','pinp.updatedFields')
			                     ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pinp')->where("pinp.partyFinderId = $varified_id");
			        
			        if($queryBuilder5->getQuery()->getResult())
			        {
				        $party_result1 = $queryBuilder5->getQuery()->getResult();
				        $count = 0;
				        foreach($party_result1 as $party_res)
				        {
				            $edited_field = str_replace('["', '', $party_res['updatedFields']);
				            $edited_field1 = str_replace('"]', '', $edited_field);
				            $edited_field2 = str_replace('[', '', $edited_field1);
				            $edited_field3 = str_replace(']', '', $edited_field2);
				            $edited_field4 = str_replace('"', '', $edited_field3);
				            $edited_field5 = str_replace('"', '', $edited_field4);
				            if($edited_field5 != '')
				            {
				                $party_res1[$count]['date'] = $party_res['createdDate']->format('Y-m-d H:i:s');
				                $party_res1[$count]['edited'] = $edited_field5;
				                $count++;
				            }
				        }
				        $bar_final['revision'] = $party_res1;
				    }
				}
		}else if($view_type == 'nightclub'){
			/*NIGHT CLUB*/
			$queryBuilder1 = $em->createQueryBuilder();
	        $queryBuilder1->select('pi.id','pi.name','pi.createdDate', 'pi.header  as contributionpartyimage')
	            ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')
	            ->where("pi.typeId = '1'")->andwhere("pi.id = $view_id")
	            ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pi.countryId')->addSelect('pfctr.countryName as festivalcountry')
	            ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pi.cityId')->addSelect('pfc.cityName  as festivalcity')
	            ->join('IFlairSoapBundle\Entity\ContributionStatus', 'cs', Expr\Join::WITH, 'cs.id = pi.status_id')->addSelect('cs.statusName  as festivalStatus');
	        $nightclub_collection_result = $queryBuilder1->getQuery()->getResult();
	        $nightclub_final = array();
	        foreach($nightclub_collection_result as $result)
	        {
	            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
	            $image_path = explode("/web",$result['contributionpartyimage']);
	            if(isset($image_path[1]))
	                $image_url = $baseurl.$image_path[1];
	            else
	                $image_url = '';
	            $nightclub_final = $result;
	            $nightclub_final['contributionpartyimage'] = $image_url;
	            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
	            $country_logo = $image_path.strtolower(str_replace(' ', '_', $result['festivalcountry'])).'.png';
	            $nightclub_final['createdDate'] = $result['createdDate']->format('Y-m-d H:i:s');
	            $nightclub_final['country_logo'] = $country_logo;
	        }

	        $queryBuilder1 = $em->createQueryBuilder();
	        $queryBuilder1->select('pi.id')->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')->where("pi.id = $view_id")
	        ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pi.partyFinderId');
	        $queryBuilder1->addSelect('pf.id as partyfinderid');
	        $queryBuilder1->leftJoin('IFlairSoapBundle\Entity\Partyfinderfavorite', 'pff', Expr\Join::WITH, 'pff.partyFinderId = pf.id');
	        $queryBuilder1->addSelect('count(pff.partyFinderId) as partyfindercount');
	        $partyfindercount = $queryBuilder1->getQuery()->getResult();

	        $queryBuilder2 = $em->createQueryBuilder();
	        $queryBuilder2->select('pi.id')->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pi')->where("pi.id = $view_id")
	        ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pi.partyFinderId');
	        $queryBuilder2->addSelect('pf.id as partyfinderid');
	        $queryBuilder2->leftJoin('IFlairSoapBundle\Entity\Partyfinderviews', 'pfv', Expr\Join::WITH, 'pfv.partyFinderId = pf.id');
	        $queryBuilder2->addSelect('count(pfv.partyFinderId) as partyfinderviews');
	        $partyfinderviews = $queryBuilder2->getQuery()->getResult();
	        
	        $nightclub_final['partyfindercount'] = $partyfindercount[0]['partyfindercount'];
	        $nightclub_final['partyfinderviews'] = $partyfinderviews[0]['partyfinderviews'];

	        if($varified_id != 0)
	        {
		        $queryBuilder5 = $em->createQueryBuilder();
		        $queryBuilder5->select('pinp.createdDate','pinp.updatedFields')
		                     ->from('IFlairSoapBundle\Entity\PartyfinderInprogress', 'pinp')->where("pinp.partyFinderId = $varified_id");
		        if($queryBuilder5->getQuery()->getResult())
		        {
			        $party_result1 = $queryBuilder5->getQuery()->getResult();
			        $count = 0;
			        foreach($party_result1 as $party_res)
			        {
			            $edited_field = str_replace('["', '', $party_res['updatedFields']);
			            $edited_field1 = str_replace('"]', '', $edited_field);
			            $edited_field2 = str_replace('[', '', $edited_field1);
			            $edited_field3 = str_replace(']', '', $edited_field2);
			            $edited_field4 = str_replace('"', '', $edited_field3);
			            $edited_field5 = str_replace('"', '', $edited_field4);
			            if($edited_field5 != '')
			            {
			                $party_res1[$count]['date'] = $party_res['createdDate']->format('Y-m-d H:i:s');
			                $party_res1[$count]['edited'] = $edited_field5;
			                $count++;
			            }
			        }
			        $nightclub_final['revision'] = $party_res1;
			    }
			}
		}else{				
				$queryBuilder = $em->createQueryBuilder();
				$queryBuilder->select('finp.id','finp.name as name','finp.createdDate','finp.header')
							 ->from('IFlairFestivalBundle\Entity\FestivalInprogress', 'finp')->where("finp.id = $view_id")
							 ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = finp.countryId')->addSelect('pfctr.countryName as festivalcountry')
							 ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = finp.cityId')->addSelect('pfc.cityName  as festivalcity')
							 ->join('IFlairFestivalBundle\Entity\Festival_inprogress_status', 'fis', Expr\Join::WITH, 'fis.festivalInprogressId = finp.id')
							 ->join('IFlairSoapBundle\Entity\ContributionStatus', 'cs', Expr\Join::WITH, 'cs.id = fis.statusId')->addSelect('cs.statusName as festivalStatus');
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
					$festival_final[$count]['contributionpartyimage'] = $image_url;
					$festival_final[$count]['createdDate'] = $result['createdDate']->format('Y-m-d H:i:s');
					unset($festival_final[$count]['header']);
					$image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
					$country_logo = $image_path.strtolower(str_replace(' ', '_', $result['festivalcountry'])).'.png';
					$festival_final[$count]['country_logo'] = $country_logo;
					$count++;
				}
				// REVISION Query 
				$queryBuilder1 = $em->createQueryBuilder();
		        $queryBuilder1->select('finp.id')->from('IFlairFestivalBundle\Entity\FestivalInprogress', 'finp')->where("finp.id = $view_id")
		        ->leftJoin('IFlairFestivalBundle\Entity\festival', 'f', Expr\Join::WITH, 'f.id = finp.festivalId');                
		        $queryBuilder1->addSelect('f.id as festival_id');
		        $queryBuilder1->leftJoin('IFlairFestivalBundle\Entity\festivalFavourite', 'ff', Expr\Join::WITH, 'ff.festivalId = finp.festivalId');
		        $queryBuilder1->addSelect('count(ff.festivalId) as festivalcount');
		        $festival_count = $queryBuilder1->getQuery()->getResult();

		        $queryBuilder2 = $em->createQueryBuilder();
		        $queryBuilder2->select('finp.id')->from('IFlairFestivalBundle\Entity\FestivalInprogress', 'finp')->where("finp.id = $view_id")
		        ->leftJoin('IFlairFestivalBundle\Entity\festival', 'f', Expr\Join::WITH, 'f.id = finp.festivalId');                
		        $queryBuilder2->addSelect('f.id as festival_id');
		        $queryBuilder2->leftJoin('IFlairFestivalBundle\Entity\festival_view', 'fv', Expr\Join::WITH, 'fv.festivalId = finp.festivalId');
		        $queryBuilder2->addSelect('count(fv.festivalId) as festivalviews');
		        $festival_view = $queryBuilder2->getQuery()->getResult();

		        $festival_final[0]['festivalcount'] = $festival_count[0]['festivalcount'];
                $festival_final[0]['festivalviews'] = $festival_view[0]['festivalviews'];

                if($varified_id != 0)
                {
	                $queryBuilder1 = $em->createQueryBuilder();
			        $queryBuilder1->select('finp.createdDate','finp.updatedFields')
			                     ->from('IFlairFestivalBundle\Entity\FestivalInprogress', 'finp')->where("finp.festivalId = $varified_id");
			        $festival_result1 = $queryBuilder1->getQuery()->getResult();
					$count = 0;
			        foreach($festival_result1 as $festival_res)
			        {
			            $edited_field = str_replace('["', '', $festival_res['updatedFields']);
			            $edited_field1 = str_replace('"]', '', $edited_field);
			            $edited_field2 = str_replace('[', '', $edited_field1);
			            $edited_field3 = str_replace(']', '', $edited_field2);
			            $edited_field4 = str_replace('"', '', $edited_field3);
			            $edited_field5 = str_replace('"', '', $edited_field4);
			            if($edited_field5 != '')
			            {
			                $festival_res1[$count]['date'] = $festival_res['createdDate']->format('Y-m-d H:i:s');
			                $festival_res1[$count]['edited'] = $edited_field5;
			                $count++;
			            }
			        }
			        $festival_final[0]['revision'] = $festival_res1;
			        //$festival_final = $festival_final[0];
			    }

		}
		if(empty($bar_final)){
			$bar_final = '';
		}
		if(empty($nightclub_final))
		{
			$nightclub_final = '';
		}
		if(empty($festival_final)){
			$festival_final = '';
		}
		
		$myresponse = array(
			'success' => true,
			'status' => Response::HTTP_OK,
			'message' => 'Contribution view',
			'bar_final' => $bar_final,
			'nightclub_final' => $nightclub_final,
			'festival_final' => $festival_final
		);
		$finalResponse = json_encode($myresponse);
		return $finalResponse;
	}
}
