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

class PartyfestivalalllistingService
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
    public function setpartyfestivalalllist()
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('fs.id','fs.title','fsdt.startDate','fsdt.endDate','fsimg.imageName')
            ->from('IFlairFestivalBundle\Entity\festival', 'fs')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_dates', 'fsdt',  Expr\Join::WITH, 'fsdt.festivalId = fs.id')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'")
            ->leftJoin('IFlairFestivalBundle\Entity\festival_location', 'fsli',  Expr\Join::WITH, 'fsli.id = fs.festivalLocationId')
            ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = fsli.cityId');
            $queryBuilder->addSelect('pfc.cityName  as partyfindercty')
            ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            $queryBuilder->addSelect('pfctr.countryName as partyfindercntry')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_musicgenre', 'fsmg',  Expr\Join::WITH, 'fsmg.festivalId = fs.id');
        $queryBuilder->join('IFlairSoapBundle\Entity\Musicgenre', 'msg', Expr\Join::WITH, 'msg.id = fsmg.musicGenreId');
        $queryBuilder->addSelect('group_concat(msg.name) as musicgenre_name');
        $queryBuilder->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, 'fstr.festivalId = fs.id');
        $queryBuilder->addSelect('avg(fstr.userRatings) as user_ratings')->groupBy('fs.id');
        $result = $queryBuilder->getQuery()->getResult();
        
        $new_result = array(); $count = 0;
        foreach($result as $data)
        {
            $new_result[$count] = $data;
            $data['musicgenre_name'] = explode(',',$data['musicgenre_name']);
            $data['musicgenre_name'] = array_unique($data['musicgenre_name']);
            $data['musicgenre_name'] = implode(',', $data['musicgenre_name']);
            $new_result[$count]['musicgenre_name'] = $data['musicgenre_name'];
            $count++;
        }
        $count = 0;
        foreach($new_result as $data)
        {
            $new_result[$count] = $data;
            $new_result[$count]['startDate'] = $data['startDate']->format('Y-m-d H:i:s');
            $new_result[$count]['endDate'] = $data['endDate']->format('Y-m-d H:i:s');
            $count++;
        }
        $count = 0;
        foreach($new_result as $data)
        {
            $new_result[$count] = $data;
            $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
            $image_path = $image_path.$data['imageName'];
            $new_result[$count]['imageName'] = $image_path;
            $count++;
        }
        $count = 0;
        foreach($new_result as $data)
        {
            $new_result[$count] = $data;
            $today = date("Y-m-d H:i:s");
            if($data['startDate'] > $today)
            {
                $new_result[$count]['festival_status'] = 'Up next!';
            }else{
                $new_result[$count]['festival_status'] = 'Recent!';
            }
            $count++;
        }
        
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'festival list successfully.',
            'festival_data' => $new_result
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}