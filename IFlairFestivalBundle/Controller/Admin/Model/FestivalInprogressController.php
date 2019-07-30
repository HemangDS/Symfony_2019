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
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;


class FestivalInprogressController extends BaseAdminController
{
	
	public function createFestivalInprogressEntityFormBuilder($entity, $view)
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
        
        $form->add('status', EntityType::class,
            array(
                'label' => 'Status',
                'class' => \IFlairSoapBundle\Entity\ContributionStatus::class,
                'choice_label' => 'statusName',
                'choice_value' => 'id'
            )
        );
    }

    function onPreSubmit(FormEvent $event) {

        $form = $event->getForm();
        $data = $event->getData();

        $entityManager = $this->getDoctrine()->getManager();

        if($event->getData()) {
            if($data['status'] == '1') {

                // Add festival_location
                $city_id = $entityManager->getRepository('IFlairSoapBundle:Partyfindercity')->find($festival_inprogress->getCityId());
                
                $festival_location = new festival_location();
                $festival_location->setCityId($city_id);
                $entityManager->persist($festival_location);
                $entityManager->flush();
                $festival_location_id = $festival_location->getId();

                // Add festival
                $user_id = $entityManager->getRepository('AppBundle:User')->find($festival_inprogress->getUserId());

                $festival_location = $entityManager->getRepository('IFlairFestivalBundle:festival_location')->find($festival_location_id);

                $festival = new festival();
                $festival->setFestivalLocationId($festival_location);
                $festival->setTitle($data['name']);
                $festival->setType(null);
                $festival->setHeldSince($festival_inprogress->getHeldSince());
                $festival->setStages($festival_inprogress->getStages());
                $festival->setUserAdmin($user_id);
                $festival->setDescription(null);

                $entityManager->persist($festival);
                $entityManager->flush();
                $festival_id = $festival->getId();

                $festival = $entityManager->getRepository('IFlairFestivalBundle:festival')->find($festival_id);

                // Add curreny
                if(isset($data['currency'])) {
                    foreach ($data['currency'] as $key => $value) {

                        $currency = $entityManager->getRepository('IFlairFestivalBundle:currency')->find($value['currency_id']);

                        $festival_currency = new festival_currency();
                        $festival_currency->setCurrencyId($currency);
                        $festival_currency->setFestivalId($festival);
                        $entityManager->persist($festival_currency);
                        $entityManager->flush();
                    }
                } 

                // Add dates
                if(isset($data['dates'])) {
                    foreach ($data['dates'] as $key => $value) {

                        $startdate = $value['start_dates'];
                        $enddate = $value['end_dates'];

                        $startdatetime = $startdate['year'].'-'.$startdate['month'].'-'.$startdate['day'].' 00:00:00';
                        $enddatetime = $enddate['year'].'-'.$enddate['month'].'-'.$enddate['day'].' 00:00:00';

                        $festival_dates = new festival_dates();
                        $festival_dates->setFestivalId($festival);
                        $festival_dates->setStartDate(new \DateTime(date('Y-m-d H:i:s', strtotime($startdatetime))));
                        $festival_dates->setEndDate(new \DateTime(date('Y-m-d H:i:s', strtotime($enddatetime))));
                        $entityManager->persist($festival_dates);
                        $entityManager->flush();
                    }
                }

                // Add features
                if(isset($data['features'])) {
                    foreach ($data['features'] as $key => $value) {

                        $features = $entityManager->getRepository('IFlairFestivalBundle:features')->find($value['feature_id']);

                        $festival_features = new festival_features();
                        $festival_features->setFeatureId($features);
                        $festival_features->setFestivalId($festival);
                        $festival_features->setStatus($value['status']);
                        $entityManager->persist($festival_features);
                        $entityManager->flush();
                    }
                }

                // Add musicgenre
                if(isset($data['festival_inprogress_musicgenre'])) {
                    foreach ($data['festival_inprogress_musicgenre'] as $key => $value) {

                        $musicgenreId = $entityManager->getRepository('IFlairSoapBundle:musicgenre')->find($value['musicgenreId']);

                        $musicgenre = new festival_musicgenre();
                        $musicgenre->setMusicGenreId($musicgenreId);
                        $musicgenre->setFestivalId($festival);
                        $entityManager->persist($musicgenre);
                        $entityManager->flush();
                    }
                }

                // Add payment
                if(isset($data['festival_inprogress_payments'])) {
                    foreach ($data['festival_inprogress_payments'] as $key => $value) {

                        $paymentId = $entityManager->getRepository('IFlairSoapBundle:Payments')->find($value['paymentId']);

                        $Festival_Payment = new Festival_Payment();
                        $Festival_Payment->setPaymentId($paymentId);
                        $Festival_Payment->setFestivalId($festival);
                        $Festival_Payment->setStatus('1');
                        $entityManager->persist($Festival_Payment);
                        $entityManager->flush();
                    }
                }

                // Add ratings
                if(isset($data['festival_inprogress_rating'])) {
                    foreach ($data['festival_inprogress_rating'] as $key => $value) {

                        $ratingId = $entityManager->getRepository('IFlairFestivalBundle:festival_rating_type')->find($value['ratingId']);

                        $musicgenre = new festival_type_ratings();
                        $musicgenre->setModifiedDate(new \DateTime());
                        $musicgenre->setFestivalId($festival);
                        $musicgenre->setFestivalTypeId($ratingId);
                        $musicgenre->setUserRatings($value['userRatings']);
                        $musicgenre->setAvgRatings('0');
                        $musicgenre->setUserId($user_id);
                        
                        $entityManager->persist($musicgenre);
                        $entityManager->flush();
                    }
                }

                // Update festival inprogress
                $festival_inprogress->setFestivalId($festival);
                $entityManager->persist($festival_inprogress);
                $entityManager->flush();
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
            'data_class' => 'IFlairFestivalBundle\Entity\FestivalInprogress'
        ));
    }
}