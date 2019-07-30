<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Searchlogs.
 *
 * @ORM\Table(name="searchlogs", indexes={@ORM\Index(name="idClient", columns={"idClient"}), @ORM\Index(name="idCity", columns={"idCity"}), @ORM\Index(name="cleanedTerm", columns={"cleanedTerm"}), @ORM\Index(name="vertical", columns={"vertical"}), @ORM\Index(name="inetAtonIP", columns={"ipAddress"}), @ORM\Index(name="internalSearch", columns={"internalSearch"}), @ORM\Index(name="app", columns={"searchApp"})})
 * @ORM\Entity
 */
class Searchlogs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idClient", type="integer", nullable=true)
     */
    private $idclient;

    /**
     * @var int
     *
     * @ORM\Column(name="idCity", type="integer", nullable=true)
     */
    private $idcity;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=6, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=10, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="term", type="string", length=250, nullable=true)
     */
    private $term = '';

    /**
     * @var string
     *
     * @ORM\Column(name="cleanedTerm", type="string", length=250, nullable=true)
     */
    private $cleanedterm;

    /**
     * @var int
     *
     * @ORM\Column(name="results", type="smallint", nullable=true)
     */
    private $results;

    /**
     * @var bool
     *
     * @ORM\Column(name="searchFrom", type="boolean", nullable=true)
     */
    private $searchfrom = '1';

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
    private $searchapp = 'desktop';

    /**
     * @var string
     *
     * @ORM\Column(name="ipAddress", type="string", length=20, nullable=true)
     */
    private $ipaddress;

    /**
     * @var bool
     *
     * @ORM\Column(name="internalSearch", type="boolean", nullable=true)
     */
    private $internalsearch = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="searchedDate", type="datetime", nullable=false)
     */
    private $searcheddate = 'CURRENT_TIMESTAMP';

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
     * Set idclient.
     *
     * @param int $idclient
     *
     * @return Searchlogs
     */
    public function setIdclient($idclient)
    {
        $this->idclient = $idclient;

        return $this;
    }

    /**
     * Get idclient.
     *
     * @return int
     */
    public function getIdclient()
    {
        return $this->idclient;
    }

    /**
     * Set idcity.
     *
     * @param int $idcity
     *
     * @return Searchlogs
     */
    public function setIdcity($idcity)
    {
        $this->idcity = $idcity;

        return $this;
    }

    /**
     * Get idcity.
     *
     * @return int
     */
    public function getIdcity()
    {
        return $this->idcity;
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
     * Set cleanedterm.
     *
     * @param string $cleanedterm
     *
     * @return Searchlogs
     */
    public function setCleanedterm($cleanedterm)
    {
        $this->cleanedterm = $cleanedterm;

        return $this;
    }

    /**
     * Get cleanedterm.
     *
     * @return string
     */
    public function getCleanedterm()
    {
        return $this->cleanedterm;
    }

    /**
     * Set results.
     *
     * @param int $results
     *
     * @return Searchlogs
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Get results.
     *
     * @return int
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Set searchfrom.
     *
     * @param bool $searchfrom
     *
     * @return Searchlogs
     */
    public function setSearchfrom($searchfrom)
    {
        $this->searchfrom = $searchfrom;

        return $this;
    }

    /**
     * Get searchfrom.
     *
     * @return bool
     */
    public function getSearchfrom()
    {
        return $this->searchfrom;
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
     * Set searchapp.
     *
     * @param string $searchapp
     *
     * @return Searchlogs
     */
    public function setSearchapp($searchapp)
    {
        $this->searchapp = $searchapp;

        return $this;
    }

    /**
     * Get searchapp.
     *
     * @return string
     */
    public function getSearchapp()
    {
        return $this->searchapp;
    }

    /**
     * Set ipaddress.
     *
     * @param int $ipaddress
     *
     * @return Searchlogs
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * Get ipaddress.
     *
     * @return int
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * Set internalsearch.
     *
     * @param bool $internalsearch
     *
     * @return Searchlogs
     */
    public function setInternalsearch($internalsearch)
    {
        $this->internalsearch = $internalsearch;

        return $this;
    }

    /**
     * Get internalsearch.
     *
     * @return bool
     */
    public function getInternalsearch()
    {
        return $this->internalsearch;
    }

    /**
     * Set searcheddate.
     *
     * @param \DateTime $searcheddate
     *
     * @return Searchlogs
     */
    public function setSearcheddate($searcheddate)
    {
        $this->searcheddate = $searcheddate;

        return $this;
    }

    /**
     * Get searcheddate.
     *
     * @return \DateTime
     */
    public function getSearcheddate()
    {
        return $this->searcheddate;
    }
}
