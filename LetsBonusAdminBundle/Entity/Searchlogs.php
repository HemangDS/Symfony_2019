<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Searchlogs.
 *
 * @ORM\Table(name="lb_searchlogs")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\SearchlogsRepository")
 */
class Searchlogs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idClient", type="integer", length=11, nullable=true)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="idCity", type="string", length=255, nullable=true)
     */
    private $idCity;

    /**
     * @var decimal
     *
     * @ORM\Column(name="latitude", type="decimal", nullable=true, precision=10, scale=2)
     */
    private $latitude;

    /**
     * @var decimal
     *
     * @ORM\Column(name="longitude", type="decimal", nullable=true, precision=10, scale=2)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="term", type="string", length=255, nullable=true)
     */
    private $term;

    /**
     * @var string
     *
     * @ORM\Column(name="cleanedTerm", type="string", length=250, nullable=true)
     */
    private $cleanedTerm;

    /**
     * @var smallint
     *
     * @ORM\Column(name="searchFrom", type="smallint", length=4, nullable=true)
     */
    private $searchFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="vertical", type="string", length=20, nullable=true)
     */
    private $vertical;

    /**
     * @var string
     *
     * @ORM\Column(name="breadcrumb", type="string", length=255, nullable=true)
     */
    private $breadcrumb;

    /**
     * @var string
     *
     * @ORM\Column(name="searchApp", type="string", nullable=true)
     */
    private $searchApp;

    /**
     * @var int
     *
     * @ORM\Column(name="ipAddress", type="integer", nullable=true)
     */
    private $ipAddress;

    /**
     * @var int
     *
     * @ORM\Column(name="internalSearch", type="integer", length=1, nullable=true)
     */
    private $internalSearch;

    /**
     * @var datetime
     *
     * @ORM\Column(name="searchedDate", type="datetime", nullable=true)
     */
    private $searchedDate;

    /**
     * @var int
     *
     * @ORM\Column(name="num_search", type="integer", nullable=true)
     */
    private $numSearch;

    /**
     * @var int
     *
     * @ORM\Column(name="num_results", type="integer", nullable=true)
     */
    private $numResults;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Searchlogs
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set idClient.
     *
     * @param string $idClient
     *
     * @return Searchlogs
     */
    public function setIdClient($idClient = null)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient.
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set idCity.
     *
     * @param string $idCity
     *
     * @return Searchlogs
     */
    public function setIdCity($idCity)
    {
        $this->idCity = $idCity;

        return $this;
    }

    /**
     * Get idCity.
     *
     * @return string
     */
    public function getIdCity()
    {
        return $this->idCity;
    }

    /**
     * Set latitude.
     *
     * @param string $latitude
     *
     * @return Searchlogs
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string $longitude
     *
     * @return Searchlogs
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set term.
     *
     * @param string $term
     *
     * @return Searchlogs
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term.
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set cleanedTerm.
     *
     * @param string $cleanedTerm
     *
     * @return Searchlogs
     */
    public function setCleanedTerm($cleanedTerm)
    {
        $this->cleanedTerm = $cleanedTerm;

        return $this;
    }

    /**
     * Get cleanedTerm.
     *
     * @return string
     */
    public function getCleanedTerm()
    {
        return $this->cleanedTerm;
    }

    /**
     * Set searchFrom.
     *
     * @param int $searchFrom
     *
     * @return Searchlogs
     */
    public function setSearchFrom($searchFrom)
    {
        $this->searchFrom = $searchFrom;

        return $this;
    }

    /**
     * Get searchFrom.
     *
     * @return int
     */
    public function getSearchFrom()
    {
        return $this->searchFrom;
    }

    /**
     * Set vertical.
     *
     * @param string $vertical
     *
     * @return Searchlogs
     */
    public function setVertical($vertical)
    {
        $this->vertical = $vertical;

        return $this;
    }

    /**
     * Get vertical.
     *
     * @return string
     */
    public function getVertical()
    {
        return $this->vertical;
    }

    /**
     * Set breadcrumb.
     *
     * @param string $breadcrumb
     *
     * @return Searchlogs
     */
    public function setBreadcrumb($breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * Get breadcrumb.
     *
     * @return string
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * Set searchApp.
     *
     * @param string $searchApp
     *
     * @return Searchlogs
     */
    public function setSearchApp($searchApp)
    {
        $this->searchApp = $searchApp;

        return $this;
    }

    /**
     * Get searchApp.
     *
     * @return string
     */
    public function getSearchApp()
    {
        return $this->searchApp;
    }

    /**
     * Set ipAddress.
     *
     * @param string $ipAddress
     *
     * @return Searchlogs
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set internalSearch.
     *
     * @param datetime $internalSearch
     *
     * @return Searchlogs
     */
    public function setInternalSearch($internalSearch)
    {
        $this->internalSearch = $internalSearch;

        return $this;
    }

    /**
     * Get internalSearch.
     *
     * @return datetime
     */
    public function getInternalSearch()
    {
        return $this->internalSearch;
    }

    /**
     * Set searchedDate.
     *
     * @param datetime $searchedDate
     *
     * @return Searchlogs
     */
    public function setSearchedDate($searchedDate)
    {
        $this->searchedDate = $searchedDate;

        return $this;
    }

    /**
     * Get searchedDate.
     *
     * @return string
     */
    public function getSearchedDate()
    {
        return $this->searchedDate;
    }

    /**
     * Set numSearch.
     *
     * @param int $numSearch
     *
     * @return Searchlogs
     */
    public function setNumSearch($numSearch)
    {
        $this->numSearch = $numSearch;

        return $this;
    }

    /**
     * Get numSearch.
     *
     * @return int
     */
    public function getNumSearch()
    {
        return $this->numSearch;
    }

    /**
     * Set numResults.
     *
     * @param int $numResults
     *
     * @return Searchlogs
     */
    public function setNumResults($numResults)
    {
        $this->numResults = $numResults;

        return $this;
    }

    /**
     * Get numResults.
     *
     * @return int
     */
    public function getNumResults()
    {
        return $this->numResults;
    }
}
