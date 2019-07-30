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

class PartysearchfestivalService
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
    public function setpartysearchfestival()
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('fs.id','fs.title','fsimg.imageName')
            ->from('IFlairFestivalBundle\Entity\festival', 'fs')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'")
            ->leftJoin('IFlairFestivalBundle\Entity\festival_location', 'fsli',  Expr\Join::WITH, 'fsli.id = fs.festivalLocationId')
            ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = fsli.cityId');
            $queryBuilder->addSelect('pfc.cityName  as partyfindercty')
            ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder->addSelect('pfctr.countryName as partyfindercntry')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_musicgenre', 'fsmg',  Expr\Join::WITH, 'fsmg.festivalId = fs.id');
        $queryBuilder->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, 'fstr.festivalId = fs.id');
        $queryBuilder->addSelect('avg(fstr.userRatings) as user_ratings')->groupBy('fs.id');
        $festival_result = $queryBuilder->getQuery()->getResult();

        $count = 0;
        foreach($festival_result as $data)
        {
            $festival_result[$count] = $data;
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
            $image_path = $image_path.$data['imageName'];
            $festival_result[$count]['imageName'] = $image_path;
            $count++;
        }        
        /* Country Code start */
        $country = array(); $count = 0;
        foreach($festival_result as $result)
        {
            $country[$count][$result['partyfindercntry']] = $result;
            $count++;
        }
        
        $inarray = array(); $count1 = 0; $country_list = array();
        foreach($country as $key => $cnt)
        {
            foreach($cnt as $k => $c)
            {
                $country_list[$k][$count1] = $c;
                $count1++;
            }
        }
        $counry_lst = array(); $count = 0; $contry = array();
        //echo "<pre>";
        foreach($country_list as $K => $ctr)
        {
            $fest_cnt = 0; $avg_rating = 0;
            foreach($ctr as $val)
            {
                $avg_rating += $val['user_ratings'];
                $fest_cnt++;
            }
            $avg_rating = $avg_rating/$fest_cnt;
            
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
            $counry_lst[$count]['country_logo'] = $image_path.strtolower(str_replace(' ', '_', $K)).'.png';
            $counry_lst[$count]['country_name'] = $K;
            $counry_lst[$count]['total_no_of_festival'] = $fest_cnt;
            $counry_lst[$count]['avg_rating'] = $avg_rating;
            $count++;
        }

        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'festival_list' => $festival_result,
            'country_list' => $counry_lst,
            'message' => 'search festival list successfully',
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}