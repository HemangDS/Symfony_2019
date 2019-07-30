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

class PartyfestivallistingService
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
    public function setpartyfestivallist($all = 'false',$sort_name = 'default',$music, $offset, $limit, $upcoming = 'true', $country = '')
    {
        $request = $this->request->getCurrentRequest();

        $em = $this->doctrine->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('fs.id','fs.title','fsdt.startDate','fsdt.endDate','fsimg.imageName')
            ->from('IFlairFestivalBundle\Entity\festival', 'fs');
            if($upcoming == 'true'){
                $queryBuilder->join('IFlairFestivalBundle\Entity\festival_dates', 'fsdt',  Expr\Join::WITH, 'fsdt.festivalId = fs.id AND fsdt.startDate > now()');
            }
            else{
                $queryBuilder->leftJoin('IFlairFestivalBundle\Entity\festival_dates', 'fsdt',  Expr\Join::WITH, 'fsdt.festivalId = fs.id');
            }
            $queryBuilder
            ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'")
            ->leftJoin('IFlairFestivalBundle\Entity\festival_location', 'fsli',  Expr\Join::WITH, 'fsli.id = fs.festivalLocationId')
            ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = fsli.cityId');
            $queryBuilder->addSelect('pfc.cityName  as partyfindercty')
            ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
            if($country != ''){
                $queryBuilder->addSelect('pfctr.countryName as partyfindercntry')->having('partyfindercntry = :country')->setParameter('country', $country);
            }else{
                $queryBuilder->addSelect('pfctr.countryName as partyfindercntry');
            }
            $queryBuilder->leftJoin('IFlairFestivalBundle\Entity\festival_musicgenre', 'fsmg',  Expr\Join::WITH, 'fsmg.festivalId = fs.id');
        $queryBuilder->join('IFlairSoapBundle\Entity\Musicgenre', 'msg', Expr\Join::WITH, 'msg.id = fsmg.musicGenreId');
        $queryBuilder->addSelect('group_concat(msg.name) as musicgenre_name');
        $queryBuilder->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, 'fstr.festivalId = fs.id');
        $queryBuilder->addSelect('avg(fstr.userRatings) as user_ratings')->groupBy('fs.id');

        if($music != '')
        {
            if(strtolower($music) != 'all'){
                if (strpos($music, ',') !== false) {
                    $datas = explode(',', $music);
                    $count = 0;
                    foreach($datas as $data)
                    {
                        if($count == 0){
                            $queryBuilder->where('msg.name LIKE :word')->setParameter('word', '%'.$data.'%');
                        }else{
                            $queryBuilder->orWhere("msg.name LIKE '%$data%'");
                        }
                        $count++;
                    }
                }else{
                    $queryBuilder->where('msg.name LIKE :word')->setParameter('word', '%'.$music.'%');
                }
                $music_filter = 'true';
            }
        }
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit);

        switch ($sort_name) {
            case 'rating': 
                $queryBuilder->orderBy('user_ratings', 'DESC');
                break; // other filters are soonest_date and latest_date
            case 'soonest_date':
                $queryBuilder->orderBy('fsdt.startDate', 'ASC');
                break; // other filters are soonest_date and latest_date
        }
        
        $result = $queryBuilder->getQuery()->getResult();
        
        /** COUNT START **/
            $queryBuilder1 = $em->createQueryBuilder();
            $queryBuilder1->select('fs.id','fs.title','fsdt.startDate','fsdt.endDate','fsimg.imageName')
                ->from('IFlairFestivalBundle\Entity\festival', 'fs');
                
                if($upcoming == 'true'){
                    $queryBuilder1->join('IFlairFestivalBundle\Entity\festival_dates', 'fsdt',  Expr\Join::WITH, 'fsdt.festivalId = fs.id AND fsdt.startDate > now()');
                }
                else{
                    $queryBuilder1->leftJoin('IFlairFestivalBundle\Entity\festival_dates', 'fsdt',  Expr\Join::WITH, 'fsdt.festivalId = fs.id');
                }
                $queryBuilder1
                ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fsimg',  Expr\Join::WITH, "fsimg.festivalId = fs.id AND fsimg.imageType = 'logo'")
                ->leftJoin('IFlairFestivalBundle\Entity\festival_location', 'fsli',  Expr\Join::WITH, 'fsli.id = fs.festivalLocationId')
                ->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = fsli.cityId');
                $queryBuilder1->addSelect('pfc.cityName  as partyfindercty')
                ->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
                if($country != ''){
                    $queryBuilder1->addSelect('pfctr.countryName as partyfindercntry')->having('partyfindercntry = :country')->setParameter('country', $country);
                }else{
                    $queryBuilder1->addSelect('pfctr.countryName as partyfindercntry');
                }
                $queryBuilder1->leftJoin('IFlairFestivalBundle\Entity\festival_musicgenre', 'fsmg',  Expr\Join::WITH, 'fsmg.festivalId = fs.id');

            $queryBuilder1->join('IFlairSoapBundle\Entity\Musicgenre', 'msg', Expr\Join::WITH, 'msg.id = fsmg.musicGenreId');
            $queryBuilder1->addSelect('group_concat(msg.name) as musicgenre_name');
            $queryBuilder1->join('IFlairFestivalBundle\Entity\festival_type_ratings', 'fstr', Expr\Join::WITH, 'fstr.festivalId = fs.id');
            $queryBuilder1->addSelect('avg(fstr.userRatings) as user_ratings')->groupBy('fs.id');

            if($music != '')
            {
                if(strtolower($music) != 'all'){
                    if (strpos($music, ',') !== false) {
                        $datas = explode(',', $music);
                        $count = 0;
                        foreach($datas as $data)
                        {
                            if($count == 0){
                                $queryBuilder1->where('msg.name LIKE :word')->setParameter('word', '%'.$data.'%');
                            }else{
                                $queryBuilder1->orWhere("msg.name LIKE '%$data%'");
                            }
                            $count++;
                        }
                    }else{
                        $queryBuilder1->where('msg.name LIKE :word')->setParameter('word', '%'.$music.'%');
                    }
                    $music_filter = 'true';
                }
            }
            switch ($sort_name) {
                case 'rating': 
                    $queryBuilder1->orderBy('user_ratings', 'DESC');
                    break; // other filters are soonest_date and latest_date
            }
            $result1 = $queryBuilder1->getQuery()->getResult();
            $total_result = count($result1);
        /** COUNT END **/
        $flag = 0;
        if($music_filter == 'true')
        {
            foreach($result as $res)
            {
                $festival_musics = $em->getRepository('IFlairFestivalBundle:festival_musicgenre')->findBy(array('festivalId' => $res['id']));
                $music_array = array();
                foreach($festival_musics as $musics)
                {
                    $music_array[] = $musics->getmusicGenreId()->getName();
                }
                $musicc = implode(',', $music_array);
                $result[$flag]['musicgenre_name'] = $musicc;
                $flag++;
            }
        }

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
        if($all != 'true'){
            $final_result = array(); $count = 0;
            foreach($new_result as $result)
            {
                if($result['festival_status'] == 'Up next!') 
                {
                    $final_result[$count] = $result;
                    $count++;
                }
            }
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => 'festival list successfully.',
                'festival_data' => $final_result,
                'total_count' => $total_result
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;    
        }else{
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => 'festival list successfully.',
                'festival_data' => $new_result,
                'total_count' => $total_result
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;    
        }
        
        
    }
}