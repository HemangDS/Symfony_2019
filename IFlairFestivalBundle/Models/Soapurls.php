<?php

namespace IFlairFestivalBundle\Models;
use Symfony\Component\HttpFoundation\Request;

class Soapurls
{
	public function getFestivalListingURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partyfestivallisting';
		
	}
	public function getFestivalListingLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partyfestivallisting';
	}
	public function getSearchfestivalURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partysearchfestival';
		
	}
	public function getSearchfestivalLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partysearchfestival';
	}
	public function getviewfestivalURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partyviewfestival';
		
	}
	public function getviewfestivalLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partyviewfestival';
	}
	public function getsearchlistfestivalURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partysearchlistfestival';
	}
	public function getsearchlistfestivalLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/partysearchlistfestival';
	}
	public function getSFestivalFavouriteURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalsetfavourite';
		
	}
	public function getSFestivalFavouriteLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalsetfavourite';
	}
	public function getFestivalVisitURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalvisited';
		
	}
	public function getSFestivalVisitLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalvisited';
	}
	public function getContributionadddfestivalURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/contributionadddfestival';
	}
	public function getContributionadddfestivalLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/contributionadddfestival';
	}
	public function getFestivalinfoeditconfirmationURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinfoeditconfirmation';
	}
	public function getFestivalinfoeditconfirmationLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinfoeditconfirmation';
	}
	public function getFestivalinnverviewURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinnerview';
	}
	public function getFestivalinnverviewLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinnerview';
	}
	public function getFestivalinprogressuploadmultipleimageURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinprogressuploadmultipleimage';
	}
	public function getFestivalinprogressuploadmultipleimageLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinprogressuploadmultipleimage';
	}
	public function getFestivalinprogresssaveURI(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinprogresssave';
	}
	public function getFestivalinprogresssaveLocation(Request $request)
	{
		return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/api/soap/festivalinprogresssave';
	}
}