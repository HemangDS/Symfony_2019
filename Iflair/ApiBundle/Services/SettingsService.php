<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use IFlairSoapBundle\Entity\Settings;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingsService
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
    public function setsettings($user_id,$distance,$currency,$standard_range,$show_closed_location,$show_afterhour_clubs,$prefered_music_genres,$allow_gps,$allow_push_messages,$receive_newsletter,$date_format,$date_hours)
    {
        $em = $this->doctrine->getManager();
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $setting_loaded = $this->doctrine->getRepository('IFlairSoapBundle:Settings')->findOneBy(array('userId' => $user));
        
        if($setting_loaded){
            $setting_loaded->setDistance($distance);
            $setting_loaded->setCurrency($currency);
            $setting_loaded->setStandardRange($standard_range);
            $setting_loaded->setShowClosedLocation($show_closed_location);
            $setting_loaded->setShowAfterhourClubs($show_afterhour_clubs);
            $setting_loaded->setPreferedMusicGenres($prefered_music_genres);
            $setting_loaded->setAllowGps($allow_gps);
            $setting_loaded->setAllowPushMessages($allow_push_messages);
            $setting_loaded->setReceiveNewsletter($receive_newsletter);
            $setting_loaded->setDateFormat($date_format);
            $setting_loaded->setDateHours($date_hours);
            $setting_loaded->setModifiedTime(new \DateTime());
            $setting_loaded->setUserId($user);
            $em->persist($setting_loaded);
            $em->flush();
            $myresponse = array(
                'success' => true,
                'status' => Response::HTTP_OK,            
                'content' => array(
                 'secondary_content' => 'User settings updated Sucessfully.'
                )
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }else{
            $myresponse = array(
                'success' => false,
                'status' => Response::HTTP_BAD_REQUEST,
                'content' => array(
                 'secondary_content' => 'User not available.'
                )
            );
            $finalResponse = json_encode($myresponse);
            return $finalResponse;
        }
    }
    public function getsettings($userid)
    {
        $em = $this->doctrine->getManager();
        $cuser = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $userid));
        $setting_loaded1 = $this->doctrine->getRepository('IFlairSoapBundle:Settings')->findOneBy(array('userId' => $cuser));
        $myresponses1 = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'id' => $last_inserted_id,
            'distance' => $setting_loaded1->getDistance(),
            'currency' => $setting_loaded1->getCurrency(),
            'standard_range' => $setting_loaded1->getStandardRange(),
            'show_closed_location' => $setting_loaded1->getShowClosedLocation(),
            'show_afterhour_clubs' => $setting_loaded1->getShowAfterhourClubs(),
            'prefered_music_genres' => $setting_loaded1->getPreferedMusicGenres(),
            'allow_gps' => $setting_loaded1->getAllowGps(),
            'allow_push_messages' => $setting_loaded1->getAllowPushMessages(),
            'receive_newsletter' => $setting_loaded1->getReceiveNewsletter(),
            'date_format' => $setting_loaded1->getDateFormat(),
            'date_hours' => $setting_loaded1->getDateHours()
        );
        $finalResponses = json_encode($myresponses1);
        return $finalResponses;
    }
}
