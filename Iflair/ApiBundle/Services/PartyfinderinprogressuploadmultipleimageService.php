<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IFlairSoapBundle\Entity\Settings;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindercountry;
use IFlairSoapBundle\Entity\Partyfindercity;
use IFlairSoapBundle\Entity\Musicgenre;
use IFlairSoapBundle\Entity\PartyfinderInprogressMultipleImageUpload;

class PartyfinderinprogressuploadmultipleimageService
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
    public function partyfinderinprogressuploadmultipleimageService($user_id,$party_finder_id,$images_path)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $party_finder_id));

        foreach($images_path as $path)
        {
            $PartyfinderInprogressMultipleImageUpload = new PartyfinderInprogressMultipleImageUpload();
            $PartyfinderInprogressMultipleImageUpload->setUserId($user);
            $PartyfinderInprogressMultipleImageUpload->setPartyfinderId($partyfinder);
            $PartyfinderInprogressMultipleImageUpload->setImagePath($path);
            $PartyfinderInprogressMultipleImageUpload->setIsApproved(0);
            $em->persist($PartyfinderInprogressMultipleImageUpload);
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
