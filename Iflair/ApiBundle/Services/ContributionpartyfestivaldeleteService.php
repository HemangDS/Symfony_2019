<?php
namespace Iflair\ApiBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;


class ContributionpartyfestivaldeleteService
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
    public function getcontributionpartyfestivaldelete($view_type, $view_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        if($view_type == 'festival')
        {
            $festival_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneBy(array('id' => $view_id));
            if($festival_data)
            {
                $view_id = $festival_data->getId();
                // Delete Currency
                $currency_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogressCurrency')->findBy(array('festivalInprogressId' => $view_id));
                foreach($currency_data as $currency)
                {
                    $currency_record = $em->getReference('IFlairFestivalBundle:FestivalInprogressCurrency', $currency->getId());
                    $em->remove($currency_record);
                    $em->flush();
                }

                // Delete Dates
                $dates_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogressDates')->findBy(array('festivalInprogressId' => $view_id));
                foreach($dates_data as $date)
                {
                    $date_record = $em->getReference('IFlairFestivalBundle:FestivalInprogressDates', $date->getId());
                    $em->remove($date_record);
                    $em->flush();
                }

                // Delete Features
                $features_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogressFeatures')->findBy(array('festivalInprogressId' => $view_id));
                foreach($features_data as $feature)
                {
                    $feature_record = $em->getReference('IFlairFestivalBundle:FestivalInprogressFeatures', $feature->getId());
                    $em->remove($feature_record);
                    $em->flush();
                }

                // Delete Music Genre
                $music_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogressMusicgenre')->findBy(array('festivalInprogressId' => $view_id));
                foreach($music_data as $music)
                {
                    $music_record = $em->getReference('IFlairFestivalBundle:FestivalInprogressMusicgenre', $music->getId());
                    $em->remove($music_record);
                    $em->flush();
                }

                // Delete Payment
                $payment_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogressPayments')->findBy(array('festivalInprogressId' => $view_id));
                foreach($payment_data as $payment)
                {
                    $payment_record = $em->getReference('IFlairFestivalBundle:FestivalInprogressPayments', $payment->getId());
                    $em->remove($payment_record);
                    $em->flush();
                }

                // Delete Ratings
                $rating_data = $em->getRepository('IFlairFestivalBundle:FestivalInprogressRatings')->findBy(array('festivalInprogressId' => $view_id));
                foreach($rating_data as $rating)
                {
                    $rating_record = $em->getReference('IFlairFestivalBundle:FestivalInprogressRatings', $rating->getId());
                    $em->remove($rating_record);
                    $em->flush();
                }

                // Delete Status
                $status_data = $em->getRepository('IFlairFestivalBundle:Festival_inprogress_status')->findBy(array('festivalInprogressId' => $view_id));
                foreach($status_data as $status)
                {
                    $status_record = $em->getReference('IFlairFestivalBundle:Festival_inprogress_status', $status->getId());
                    $em->remove($status_record);
                    $em->flush();
                }
                
                $festivalinprogress = $em->getReference('IFlairFestivalBundle:FestivalInprogress', $festival_data->getId());
                $em->remove($festivalinprogress);
                $em->flush();
            }
        }else{
            $party_data = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneBy(array('id' => $view_id));
            if($party_data)
            {
                $view_id = $party_data->getId();
                // Delete Feature Data
                $feature_data = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressFeatures')->findBy(array('partyfinderInprogressId' => $view_id));
                if($feature_data)
                {
                    foreach($feature_data as $feature)
                    {
                        if($feature->getId())
                        {
                            $feature_record = $em->getReference('IFlairSoapBundle:PartyfinderInprogressFeatures', $feature->getId());
                            $em->remove($feature_record);
                            $em->flush();
                        }
                    }
                }        
                // Dalete Rating Data
                $rating_data = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressRatings')->findBy(array('partyfinderInprogressId' => $view_id));
                if($rating_data)
                {
                    foreach($rating_data as $r_data)
                    {
                        if($r_data->getId())
                        {
                            $rating_record = $em->getReference('IFlairSoapBundle:PartyfinderInprogressRatings', $r_data->getId());
                            $em->remove($rating_record);
                            $em->flush();
                        }
                    }
                }            
                // Delete MusicGenre Data
                $music_data = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressMusicgenre')->findBy(array('partyfinderInprogressId' => $view_id));
                if($music_data)
                {
                    foreach($music_data as $music)
                    {
                        if($music->getId())
                        {
                            $music_record = $em->getReference('IFlairSoapBundle:PartyfinderInprogressMusicgenre', $music->getId());
                            $em->remove($music_record);
                            $em->flush();
                        }
                    }
                }
                // Delete Payment Data
                $payment_data = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressPayments')->findBy(array('partyfinderInprogressId' => $view_id));
                if($payment_data)
                {
                    foreach($payment_data as $payment)
                    {
                        if($payment->getId())
                        {
                            $payment_record = $em->getReference('IFlairSoapBundle:PartyfinderInprogressPayments', $payment->getId());
                            $em->remove($payment_record);
                            $em->flush();
                        }
                    }
                }
                // Delete Timing Data
                $timing_data = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressTiming')->findBy(array('partyfinderInprogressId' => $view_id));
                if($timing_data)
                {
                    foreach($timing_data as $timing)
                    {
                        if($timing->getId())
                        {
                            $timing_record = $em->getReference('IFlairSoapBundle:PartyfinderInprogressTiming', $timing->getId());
                            $em->remove($timing_record);
                            $em->flush();
                        }
                    }
                }
                // Delete Party Finder Inprogress Data
                $party = $em->getReference('IFlairSoapBundle:PartyfinderInprogress', $party_data->getId());
                $em->remove($party);
                $em->flush();
            }
        }


        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Contribution deleted',
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
