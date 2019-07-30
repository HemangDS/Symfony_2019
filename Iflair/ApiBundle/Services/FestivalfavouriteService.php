<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use IFlairSoapBundle\Entity\Settings;
use AppBundle\Entity\User;
use IFlairFestivalBundle\Entity\festivalFavourite;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FestivalfavouriteService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * Set user wise Applications settings.
     * @return mixed
     */
    public function setfestivalfavourite($user_id,$festival_id,$status)
    {
        $em = $this->doctrine->getManager();
        
        if($status == 'true')
        {
            $festivalfinder = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($festival_id);
            $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
            $favourite = new festivalFavourite();
            $favourite->setFestivalId($festivalfinder);
            $favourite->setUserId($user);
            $em->persist($favourite);
            $em->flush();
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => 'Set Favourite festival Successfully.'
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }else{
            $favourite = $em->getRepository('IFlairFestivalBundle:festivalFavourite')->findBy(array('userId' => $user_id, 'festivalId' => $festival_id));
            $favourite_id = '';
            foreach($favourite as $fvurite)
            {
                $favourite_id = $fvurite->getId();
            }
            $favourite = $em->getReference('IFlairFestivalBundle:festivalFavourite', $favourite_id);
            $em->remove($favourite);
            $em->flush();
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => 'removed favourite successfully.'
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }
    }
}