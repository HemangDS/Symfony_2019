<?php
namespace Iflair\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Zend\Soap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class SoapController extends Controller
{
    public function init()
    {
        ini_set('soap.wsdl_cache_enable', 0);
        ini_set('soap.wsdl_cache_ttl', 0);
        libxml_disable_entity_loader(false);
    }

    public function checkAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_check', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'check_service');
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_check', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'check_service');
        }
    }

    public function registrationAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_registration', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'registration_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_registration', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'registration_service'); 
        }
    }

    public function getcountrylistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getcountrylist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'getcountrylist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getcountrylist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'getcountrylist_service'); 
        }
    }

    public function getcitylistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getcitylist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'getcitylist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getcitylist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'getcitylist_service'); 
        }
    }

    public function getmusicgenreAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getmusicgenre', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'getmusicgenre_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getmusicgenre', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'getmusicgenre_service'); 
        }
    }

    public function createfestivalratingAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_createfestivalrating', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'createfestivalrating_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_createfestivalrating', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'createfestivalrating_service'); 
        }
    }

    public function createfestivalfeatureAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_createfestivalfeature', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'createfestivalfeature_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_createfestivalfeature', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'createfestivalfeature_service'); 
        }
    }    

    public function contributionadddfestivalAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_contributionadddfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionadddfestival_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_contributionadddfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionadddfestival_service'); 
        }
    }

    public function getcontributionpartymusicgenreAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getcontributionpartymusicgenre', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartygetmusicgenre_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getcontributionpartymusicgenre', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartygetmusicgenre_service'); 
        }
    }

    public function getcontributionpartygetfeatureAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getcontributionpartygetfeature', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartygetfeature_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getcontributionpartygetfeature', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartygetfeature_service'); 
        }
    }

    public function getcontributionpartygetratingAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getcontributionpartygetrating', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartygetrating_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getcontributionpartygetrating', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartygetrating_service'); 
        }
    }

    public function getcontributionpartyAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getcontributionparty', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionparty_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getcontributionparty', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionparty_service'); 
        }
    }

    public function checkusernameAction()
    {        
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_checkusername', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'checkusername_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_checkusername', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'checkusername_service'); 
        }
    }

    public function finalusernameAction()
    {        
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_finalusername', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'checkusername_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_finalusername', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'checkusername_service'); 
        }
    }

    public function setsettingsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_setsettings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'settings_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_setsettings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'settings_service'); 
        }
    }

    public function getsettingsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_getsettings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'settings_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_getsettings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'settings_service'); 
        }
    }

    public function sendreportcontentAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_reportcontent', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'reportcontent_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_reportcontent', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'reportcontent_service'); 
        }
    }

    public function partyfinderlistingAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderlisting', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinder_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderlisting', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinder_service'); 
        }
    }
    
    public function partyfinderviewAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyview_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyview_service'); 
        }
    }

    public function partysetfavouriteAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partysetfavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfavourite_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partysetfavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfavourite_service'); 
        }
    }

    public function partydeletefavouriteAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partydeletefavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfavourite_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partydeletefavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfavourite_service'); 
        }
    }

    public function partyfinderviewcountAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderviewcount', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyviewcount_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderviewcount', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyviewcount_service'); 
        }
    }
    
    public function partyfavouritelistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partylistfavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfavouritelist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partylistfavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfavouritelist_service'); 
        }
    }

    public function partyvisitedareasAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisitedareas', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedarea_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisitedareas', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedarea_service'); 
        }
    }

    public function partyvisitedcontinentAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisitedcontinent', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinent_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisitedcontinent', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinent_service'); 
        }
    }

    public function partyvisitedcontinentcountryAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisitedcontinentcountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinentcountry_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisitedcontinentcountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinentcountry_service'); 
        }
    }

    public function partyvisitedcontinentcityAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisitedcontinentcity', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinentcity_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisitedcontinentcity', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinentcity_service'); 
        }
    }

    public function partyvisitedcontinentlistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisitedcontinentlist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinentlist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisitedcontinentlist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcontinentlist_service'); 
        }
    }

    public function partyvisitedcountryAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisitedcountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcountry_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisitedcountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisitedcountry_service'); 
        }
    }

    public function partyclubratingsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyclubratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyratings_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyclubratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyratings_service'); 
        }
    }

    public function partybarratingsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partybarratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyratings_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partybarratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyratings_service'); 
        }
    }

    public function partyfestivallistingAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfestivallisting', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfestivallisting_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfestivallisting', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfestivallisting_service'); 
        }
    }

    public function partysearchfestivalAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partysearchfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partysearchfestival_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partysearchfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partysearchfestival_service'); 
        }
    }

    public function partyviewfestivalAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyviewfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyviewfestival_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyviewfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyviewfestival_service'); 
        }
    }

    public function partysearchlistfestivalAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partysearchlistfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partysearchlistfestival_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partysearchlistfestival', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partysearchlistfestival_service'); 
        }
    }

    public function globalclubbinglistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_globalclubbinglist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbinglist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_globalclubbinglist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbinglist_service'); 
        }
    }

    public function globalclubbingAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_globalclubbing', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbing_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_globalclubbing', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbing_service'); 
        }
    }

    public function festivalsetfavouriteAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_festivalsetfavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalfavourite_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_festivalsetfavourite', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalfavourite_service'); 
        }
    }

    public function festivalvisitedAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_festivalvisited', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalvisited_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_festivalvisited', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalvisited_service'); 
        }
    }

    public function globalclubbingviewAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_globalclubbingview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbingview_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_globalclubbingview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbingview_service'); 
        }
    }

    public function globalclubbingsearchAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_globalclubbingsearch', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbingsearch_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_globalclubbingsearch', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbingsearch_service'); 
        }
    }

    public function globalclubbingsearchlistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_globalclubbingsearchlist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbingsearchlist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_globalclubbingsearchlist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'globalclubbingsearchlist_service'); 
        }
    }

    public function contributionpartyfestivallistAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_contributionpartyfestivallist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartyfestivallist_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_contributionpartyfestivallist', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartyfestivallist_service'); 
        }
    }

    public function contributionpartyfestivalviewAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_contributionpartyfestivalview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartyfestivalview_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_contributionpartyfestivalview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartyfestivalview_service'); 
        }
    }

    public function contributionpartyfestivaldeleteAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_contributionpartyfestivaldelete', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartyfestivaldelete_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_contributionpartyfestivalview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'contributionpartyfestivaldelete_service'); 
        }
    }

    public function festivalinfoeditconfirmationAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_festivalinfoeditconfirmation', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinfoeditconfirmation_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_festivalinfoeditconfirmation', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinfoeditconfirmation_service'); 
        }
    }

    public function festivalinnerviewAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_festivalinnerview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinnerview_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_festivalinnerview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinnerview_service'); 
        }
    }

    public function partyinfoeditconfirmationAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyinfoeditconfirmation', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyinfoeditconfirmation_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyinfoeditconfirmation', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyinfoeditconfirmation_service'); 
        }
    }

    public function partyinnerviewAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyinnerview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyinnerview_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyinnerview', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyinnerview_service'); 
        }
    }

    public function festivalinprogressuploadmultipleimageAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_festivalinprogressuploadmultipleimage', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinprogressuploadmultipleimage_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_festivalinprogressuploadmultipleimage', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinprogressuploadmultipleimage_service'); 
        }
    }

    public function partyfinderinprogressuploadmultipleimageAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderinprogressuploadmultipleimage', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinderinprogressuploadmultipleimage_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderinprogressuploadmultipleimage', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinderinprogressuploadmultipleimage_service'); 
        }
    }

    public function paymentsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_payments', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'payments_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_payments', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'payments_service'); 
        }
    }

    public function featuresAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_features', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'features_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_features', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'features_service'); 
        }
    }

    public function currencyAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_currency', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'currency_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_currency', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'currency_service'); 
        }
    }
    
    public function festivalinprogresssaveAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_festivalinprogresssave', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinprogresssave_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_festivalinprogresssave', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'festivalinprogresssave_service'); 
        }
    }
    public function visitedlocationcountryAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_visitedlocationcountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'visitedlocationcountry_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_visitedlocationcountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'visitedlocationcountry_service'); 
        }
    }
    public function saveusercountryAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_saveusercountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'saveusercountry_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_saveusercountry', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'saveusercountry_service'); 
        }
    }
    public function partyfinderinprogresssaveAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderinprogresssave', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinderinprogresssave_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderinprogresssave', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinderinprogresssave_service'); 
        }
    }
    public function partyvisitedAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyvisited', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisited_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyvisited', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyvisited_service'); 
        }
    }

    public function partyfinderratingsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinderratings_service'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'partyfinderratings_service'); 
        }
    }

    public function partyfinderdeleteratingsAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('iflair_api_soap_partyfinderdeleteratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'PartyfinderdeleteratingsService'); 
        } else {
            return $this->handleSOAP($this->generateUrl('iflair_api_soap_partyfinderdeleteratings', array(), UrlGeneratorInterface::ABSOLUTE_URL), 'PartyfinderdeleteratingsService'); 
        }
    }

    /**
    * return the WSDL
    */
    public function handleWSDL($uri, $class)
    {
        // Soap auto discover
        $autodiscover = new Soap\AutoDiscover();
        $autodiscover->setClass($this->get($class));
        $autodiscover->setUri($uri);

       // Response
       $response = new Response();
       $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
       ob_start();

       // Handle Soap
       $autodiscover->handle();
       $response->setContent(ob_get_clean());
       return $response;
    }

    /**
     * execute SOAP request
     */
    public function handleSOAP($uri, $class)
    {
        // Soap server
        $soap = new Soap\Server(null,
            array('location' => $uri,
            'uri' => $uri,
        ));
        $soap->setClass($this->get($class));

        // Response
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        // Handle Soap
        $soap->handle();
        $response->setContent(ob_get_clean());
        return $response;
    }
}
