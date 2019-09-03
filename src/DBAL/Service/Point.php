<?php
namespace DV\Doctrine\DBAL\Service;


class Point
{
    protected $latitude ;
    protected $longitude ;
    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}