<?php
namespace IFlairFestivalBundle\Controller\Admin\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use IFlairSoapBundle\Entity\Partyfindercountry;
use IFlairFestivalBundle\Entity\festival;
use IFlairFestivalBundle\Entity\festival_currency;
use IFlairFestivalBundle\Entity\festival_features;
use IFlairFestivalBundle\Entity\festival_musicgenre;
use IFlairFestivalBundle\Entity\Festival_Payment;
use IFlairFestivalBundle\Entity\festival_type_ratings;
use IFlairFestivalBundle\Entity\festival_dates;
use IFlairFestivalBundle\Entity\festival_location;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;


class FestivalController extends BaseAdminController
{
	
	public function createFestivalEntityFormBuilder($entity, $view)
    {
        $builder = parent::createEntityFormBuilder($entity, $view);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

        return $builder;
    }

    protected function addElements(FormInterface $form, Partyfindercountry $country = null) {
        
        // 4. Add the province element
        $form->add('countryId', EntityType::class, array(
            'required' => true,
            'placeholder' => 'Select a Country...',
            'class' => 'IFlairSoapBundle:Partyfindercountry'
        ));
        
        // Neighborhoods empty, unless there is a selected City (Edit View)
        
        $neighborhoods = array();
        $country_id = 0;

        $em = $this->getDoctrine()->getManager();
        if($country)
        {   
            $countrydata = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneBy(['countryName' => trim($country)]);
             
            $country_id = $countrydata->getId();
        }
        // If there is a city stored in the Person entity, load the neighborhoods of it

        if ($country) {
            
            // Fetch Neighborhoods of the City if there's a selected city
            $repoNeighborhood = $this->em->getRepository('IFlairSoapBundle:Partyfindercity');
            
            $neighborhoods = $repoNeighborhood->createQueryBuilder("q")
                ->where("q.countryId = :countryid")
                ->setParameter("countryid", $country->getId())
                ->getQuery()
                ->getResult();
        }

        // Add the Neighborhoods field with the properly data
        $form->add('cityId', EntityType::class, array(
            'required' => false,
            'placeholder' => 'Select a Country first ...',
            'class' => 'IFlairSoapBundle:Partyfindercity',
            'choices' => $neighborhoods
        ));
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        $entityManager = $this->getDoctrine()->getManager();

        if($event->getData()) {
            if(isset($data['id'])) {
                
                $festivalId = $data['id'];

                $festival = $entityManager->getRepository('IFlairFestivalBundle:festival')->find($festivalId);

                // Update the currency
                if(isset($data['festival_currency'])) {
                    $form_currency_count = count($data['festival_currency']);
                    
                    $festival_currency = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_currency')->findBy(array('festivalId' => $festivalId));

                    $db_currency_count = count($festival_currency);

                    if($form_currency_count != $db_currency_count) {
                        $id_array = array();
                        if(!empty($festival_currency)) {
                            foreach ($festival_currency as $key => $value) {
                                $currency = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_currency')->find($value->getId());
                                $id_array[] = $currency->getCurrencyId()->getId();
                                $entityManager->remove($currency);
                                $entityManager->flush();
                            }
                        }
                        for($i = 0; $i < $form_currency_count; $i++)
                        {
                            if(isset($data['festival_currency'][$i]['currency_id']))
                            {
                                if(!in_array($data['festival_currency'][$i]['currency_id'], $id_array))
                                {
                                    $currency = $entityManager->getRepository('IFlairFestivalBundle:currency')->find($data['festival_currency'][$i]['currency_id']);
                                    $festival_currency1 = new festival_currency();
                                    $festival_currency1->setCurrencyId($currency);
                                    $festival_currency1->setFestivalId($festival);
                                    $entityManager->persist($festival_currency1);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                } else {
                    $festival_currency = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_currency')->findBy(array('festivalId' => $festivalId));

                    if(!empty($festival_currency)) {
                        foreach ($festival_currency as $key => $value) {
                            $currency = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_currency')->find($value->getId());
                            $entityManager->remove($currency);
                            $entityManager->flush();
                        }
                    }
                }

                // Update the features
                if(isset($data['festival_features'])) {
                    $festival_features = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_features')->findBy(array('festivalId' => $festivalId));

                    $form_feature_count = count($data['festival_features']);
                    $db_currency_count = count($festival_features);

                    if($form_feature_count != $db_currency_count) {
                        $id_array = array();
                        if(!empty($festival_features)) {
                            foreach ($festival_features as $key => $value) {
                                $features = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_features')->find($value->getId());
                                $id_array[] = $features->getFeatureId()->getId();
                                $entityManager->remove($features);
                                $entityManager->flush();
                            }
                        }

                        for($i = 0; $i < $form_feature_count; $i++)
                        {
                            if(isset($data['festival_features'][$i]['feature_id']))
                            {
                                if(!in_array($data['festival_features'][$i]['feature_id'], $id_array))
                                {
                                    $feature = $entityManager->getRepository('IFlairFestivalBundle:features')->find($data['festival_features'][$i]['feature_id']);

                                    $festival_features1 = new festival_features();
                                    $festival_features1->setFeatureId($feature);
                                    $festival_features1->setStatus($data['festival_features'][$i]['status']);
                                    $festival_features1->setFestivalId($festival);
                                    $entityManager->persist($festival_features1);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                } else {
                    $festival_features = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_features')->findBy(array('festivalId' => $festivalId));

                    if(!empty($festival_features)) {
                        foreach ($festival_features as $key => $value) {
                            $features = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_features')->find($value->getId());
                            $entityManager->remove($features);
                            $entityManager->flush();
                        }
                    }
                }

                // Update the musicgenre
                if(isset($data['festival_musicgenre'])) {
                    $festival_musicgenre = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_musicgenre')->findBy(array('festivalId' => $festivalId));

                    $form_mugicgenre_count = count($data['festival_musicgenre']);
                    $db_mugicgenre_count = count($festival_musicgenre);

                    if($form_mugicgenre_count != $db_mugicgenre_count) {
                        $id_array = array();
                        if(!empty($festival_musicgenre)) {
                            foreach ($festival_musicgenre as $key => $value) {
                                $musicgenre = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_musicgenre')->find($value->getId());
                                $id_array[] = $musicgenre->getMusicGenreId()->getId();
                                $entityManager->remove($musicgenre);
                                $entityManager->flush();
                            }
                        }

                        for($i = 0; $i < $form_mugicgenre_count; $i++)
                        {
                            if(isset($data['festival_musicgenre'][$i]['musicgenreId']))
                            {
                                if(!in_array($data['festival_musicgenre'][$i]['musicgenreId'], $id_array))
                                {
                                    $musicgenreId = $entityManager->getRepository('IFlairSoapBundle:Musicgenre')->find($data['festival_musicgenre'][$i]['musicgenreId']);

                                    $festival_musicgenre1 = new festival_musicgenre();
                                    $festival_musicgenre1->setMusicGenreId($musicgenreId);
                                    $festival_musicgenre1->setFestivalId($festival);
                                    $entityManager->persist($festival_musicgenre1);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                } else {
                    $festival_musicgenre = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_musicgenre')->findBy(array('festivalId' => $festivalId));

                    if(!empty($festival_musicgenre)) {
                        foreach ($festival_musicgenre as $key => $value) {
                            $features = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_musicgenre')->find($value->getId());
                            $entityManager->remove($features);
                            $entityManager->flush();
                        }
                    }
                }

                // Update the payment
                if(isset($data['festival_payment'])) {
                    $festival_payment = $this->getDoctrine()->getRepository('IFlairFestivalBundle:Festival_Payment')->findBy(array('festivalId' => $festivalId));

                    $form_payment_count = count($data['festival_payment']);
                    $db_payment_count = count($festival_payment);

                    if($form_payment_count != $db_payment_count) {
                        $id_array = array();
                        if(!empty($festival_payment)) {
                            foreach ($festival_payment as $key => $value) {
                                $payment = $this->getDoctrine()->getRepository('IFlairFestivalBundle:Festival_Payment')->find($value->getId());
                                $id_array[] = $payment->getPaymentId()->getId();
                                $entityManager->remove($payment);
                                $entityManager->flush();
                            }
                        }

                        for($i = 0; $i < $form_payment_count; $i++)
                        {
                            if(isset($data['festival_payment'][$i]['paymentId']))
                            {
                                if(!in_array($data['festival_payment'][$i]['paymentId'], $id_array))
                                {
                                    $paymentId = $entityManager->getRepository('IFlairSoapBundle:Payments')->find($data['festival_payment'][$i]['paymentId']);

                                    $festival_payment1 = new Festival_Payment();
                                    $festival_payment1->setPaymentId($paymentId);
                                    $festival_payment1->setFestivalId($festival);
                                    $entityManager->persist($festival_payment1);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                } else {
                    $festival_payment = $this->getDoctrine()->getRepository('IFlairFestivalBundle:Festival_Payment')->findBy(array('festivalId' => $festivalId));

                    if(!empty($festival_payment)) {
                        foreach ($festival_payment as $key => $value) {
                            $payment = $this->getDoctrine()->getRepository('IFlairFestivalBundle:Festival_Payment')->find($value->getId());
                            $entityManager->remove($payment);
                            $entityManager->flush();
                        }
                    }
                }

                // Update the rating
                if(isset($data['festival_type_ratings'])) {
                    $festival_type_ratings = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_type_ratings')->findBy(array('festivalId' => $festivalId));

                    $form_rating_count = count($data['festival_type_ratings']);
                    $db_rating_count = count($festival_type_ratings);

                    if($form_rating_count != $db_rating_count) {
                        $id_array = array();
                        if(!empty($festival_type_ratings)) {
                            foreach ($festival_type_ratings as $key => $value) {
                                $rating = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_type_ratings')->find($value->getId());
                                $id_array[] = $rating->getFestivalTypeId()->getId();
                                $entityManager->remove($rating);
                                $entityManager->flush();
                            }
                        }

                        for($i = 0; $i < $form_rating_count; $i++)
                        {
                            if(isset($data['festival_type_ratings'][$i]['festivalTypeId']))
                            {
                                if(!in_array($data['festival_type_ratings'][$i]['festivalTypeId'], $id_array))
                                {
                                    $festivalTypeId = $entityManager->getRepository('IFlairFestivalBundle:festival_rating_type')->find($data['festival_type_ratings'][$i]['festivalTypeId']);      

                                    $festival_type_ratings1 = new festival_type_ratings();
                                    $festival_type_ratings1->setModifiedDate(new \DateTime());
                                    $festival_type_ratings1->setFestivalId($festival);
                                    $festival_type_ratings1->setFestivalTypeId($festivalTypeId);
                                    $festival_type_ratings1->setUserRatings($data['festival_type_ratings'][$i]['userRatings']);
                                    $festival_type_ratings1->setAvgRatings('0');
                                    $entityManager->persist($festival_type_ratings1);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                } else {
                    $festival_type_ratings = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_type_ratings')->findBy(array('festivalId' => $festivalId));

                    if(!empty($festival_type_ratings)) {
                        foreach ($festival_type_ratings as $key => $value) {
                            $rating = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_type_ratings')->find($value->getId());
                            $entityManager->remove($rating);
                            $entityManager->flush();
                        }
                    }
                }

                // Update the dates
                if(isset($data['festival_dates'])) {
                    $festival_dates = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_dates')->findBy(array('festivalId' => $festivalId));

                    $form_date_count = count($data['festival_dates']);
                    $db_date_count = count($festival_dates);

                    if($form_date_count != $db_date_count) {
                        $id_array = array();
                        if(!empty($festival_dates)) {
                            foreach ($festival_dates as $key => $value) {
                                $date = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_dates')->find($value->getId());
                                $id_array[] = $date->getStartDate()->format('Y-m-d H:i:s');
                                $entityManager->remove($date);
                                $entityManager->flush();
                            }
                        }

                        for($i = 0; $i < $form_date_count; $i++)
                        {
                            if(isset($data['festival_dates'][$i]['startDate']))
                            {
                                echo "<pre>";
                                echo $data['festival_dates'][$i]['startDate'];
                                echo "--";
                                print_r($id_array);
                                die();
                                if(!in_array($data['festival_dates'][$i]['startDate'], $id_array))
                                {  
                                    $startdate = $data['festival_dates'][$i]['startDate'];
                                    $enddate = $data['festival_dates'][$i]['endDate'];

                                    $startdatetime = $startdate['year'].'-'.$startdate['month'].'-'.$startdate['day'].' 00:00:00';
                                    $enddatetime = $enddate['year'].'-'.$enddate['month'].'-'.$enddate['day'].' 00:00:00';

                                    $festival_dates1 = new festival_dates();
                                    $festival_dates1->setFestivalId($festival);
                                    $festival_dates1->setStartDate(new \DateTime(date('Y-m-d H:i:s', strtotime($startdatetime))));
                                    $festival_dates1->setEndDate(new \DateTime(date('Y-m-d H:i:s', strtotime($enddatetime))));
                                    $entityManager->persist($festival_dates1);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                } else {
                    $festival_dates = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_dates')->findBy(array('festivalId' => $festivalId));

                    if(!empty($festival_dates)) {
                        foreach ($festival_dates as $key => $value) {
                            $date = $this->getDoctrine()->getRepository('IFlairFestivalBundle:festival_dates')->find($value->getId());
                            $entityManager->remove($date);
                            $entityManager->flush();
                        }
                    }
                }
            }
        }

        // Search for selected City and convert it into an Entity
        $country = $this->em->getRepository('IFlairSoapBundle:Partyfindercountry')->find($data['countryId']);

        $this->addElements($form, $country);
    }

    function onPreSetData(FormEvent $event) {

        $person = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the Country is always empty
        if($person == null){
            $country = null;
        }
        else{
            $country = $person->getCountryId() ? $person->getCountryId() : null;
        }
        
        $this->addElements($form, $country);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IFlairFestivalBundle\Entity\festival'
        ));
    }
}