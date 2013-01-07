<?php
namespace Avro\LocationBundle\Util;

use Avro\LocationBundle\Document\Address;

class CoordinateFinder
{
    protected $apiKey;
    protected $mapUrl;

    public function __construct($apiKey, $mapUrl)
    {
        $this->apiKey = $apiKey;
        $this->mapUrl = $mapUrl;
    }

    /*
     * Get latitude and longitude for an address
     *
     * @param string $address
     * @param string $city
     * @param string $province
     * @param string $country
     *
     * @return array [$lat, $lng]
     */
    public function getCoordinates($address, $city, $province = null, $country = null)
    {
        $address = $address. ', '.$city.', '.$province.', '.$country;
        $url = $this->mapUrl.'?address='.urlencode($address).'&sensor=false';
        $result = json_decode(file_get_contents($url), true);
        if (array_key_exists(0, $result['results'])) {
            $lat = $result['results'][0]['geometry']['location']['lat'];
            $lng = $result['results'][0]['geometry']['location']['lng'];
        } else {
            $lat = null;
            $lng = null;
        }

        return array($lat, $lng);
    }

    /*
     * Set coordinates on an address
     *
     * @param Address $address
     *
     * @return Address
     */
    public function setCoordinates(Address $address)
    {
        list($lat, $lng) = $this->getCoordinates($address->getAddress(), $address->getCity(), $address->getProvince(), $address->getCountry());

        $address->setLat($lat);
        $address->setLng($lng);

        return $address;
    }


    /*
     * Get linear distance between two addresses
     *
     * @return int $distance
     */
    public function getDistanceBetweenCoordinates(Address $address1, Address $address2)
    {
        $earth_radius = 6371; #  km
        $lat1 = $address1->getLat();
        $lng1 = $address1->getLng();
        $lat2 = $address2->getLat();
        $lng2 = $address2->getLng();

        $delta_lat = $lat2 - $lat1 ;
        $delta_lng = $lng2 - $lng1 ;

        $alpha    = $delta_lat/2;
        $beta     = $delta_lng/2;
        $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
        $c        = asin(min(1, sqrt($a)));
        $distance = 2*$earth_radius * $c;
        $distance = round($distance, 4);

        return $distance;
    }

}
