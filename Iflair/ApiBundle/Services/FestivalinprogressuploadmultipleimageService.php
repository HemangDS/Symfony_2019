<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IFlairSoapBundle\Entity\Settings;
use IFlairFestivalBundle\Models\Soapurls;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindercountry;
use IFlairSoapBundle\Entity\Partyfindercity;
use IFlairSoapBundle\Entity\Musicgenre;
use IFlairFestivalBundle\Entity\ContributionAdddFestival;
use IFlairFestivalBundle\Entity\ContributionAddRating;
use IFlairFestivalBundle\Entity\festivalFavourite;
use IFlairFestivalBundle\Entity\features;
use IFlairFestivalBundle\Entity\ContributionAddImage;
use IFlairFestivalBundle\Entity\ContributionAddFeature;
use IFlairFestivalBundle\Entity\ContributionAddMusic;
use IFlairFestivalBundle\Entity\ContributionStatus;
use IFlairFestivalBundle\Entity\FestivalInprogressMultipleImageUpload;

class FestivalinprogressuploadmultipleimageService
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
    public function festivalinprogressuploadmultipleimageService($user_id,$festival_id,$images_path)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $festival_id = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($festival_id);
        foreach($images_path as $path)
        {
            $FestivalInprogressMultipleImageUpload = new FestivalInprogressMultipleImageUpload();
            $FestivalInprogressMultipleImageUpload->setUserId($user);
            $FestivalInprogressMultipleImageUpload->setFestivalId($festival_id);
            $FestivalInprogressMultipleImageUpload->setImagePath($path);
            $FestivalInprogressMultipleImageUpload->setIsApproved(0);
            $em->persist($FestivalInprogressMultipleImageUpload);
            $em->flush();
        }

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Multiple Images uploaded.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
