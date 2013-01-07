<?php
namespace Avro\LocationBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Avro\LocationBundle\Document\Province;
use Avro\LocationBundle\Document\Country;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class ProvinceData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $provinces = array(
            "CA" => array(
                "AB"=>"Alberta",
                "BC"=>"British Columbia",
                "MB"=>"Manitoba",
                "NB"=>"New Brunswick",
                "NL"=>"Newfoundland and Labrador",
                "NT"=>"Northwest Territories",
                "NS"=>"Nova Scotia",
                "NU"=>"Nunavut",
                "ON"=>"Ontario",
                "PE"=>"Prince Edward Island",
                "QC"=>"Quebec",
                "SK"=>"Saskatchewan",
                "YT"=>"Yukon"
            ),
            "US" => array(
                'AL'=>"Alabama",
                'AK'=>"Alaska",
                'AZ'=>"Arizona",
                'AR'=>"Arkansas",
                'CA'=>"California",
                'CO'=>"Colorado",
                'CT'=>"Connecticut",
                'DE'=>"Delaware",
                'DC'=>"District Of Columbia",
                'FL'=>"Florida",
                'GA'=>"Georgia",
                'HI'=>"Hawaii",
                'ID'=>"Idaho",
                'IL'=>"Illinois",
                'IN'=>"Indiana",
                'IA'=>"Iowa",
                'KS'=>"Kansas",
                'KY'=>"Kentucky",
                'LA'=>"Louisiana",
                'ME'=>"Maine",
                'MD'=>"Maryland",
                'MA'=>"Massachusetts",
                'MI'=>"Michigan",
                'MN'=>"Minnesota",
                'MS'=>"Mississippi",
                'MO'=>"Missouri",
                'MT'=>"Montana",
                'NE'=>"Nebraska",
                'NV'=>"Nevada",
                'NH'=>"New Hampshire",
                'NJ'=>"New Jersey",
                'NM'=>"New Mexico",
                'NY'=>"New York",
                'NC'=>"North Carolina",
                'ND'=>"North Dakota",
                'OH'=>"Ohio",
                'OK'=>"Oklahoma",
                'OR'=>"Oregon",
                'PA'=>"Pennsylvania",
                'RI'=>"Rhode Island",
                'SC'=>"South Carolina",
                'SD'=>"South Dakota",
                'TN'=>"Tennessee",
                'TX'=>"Texas",
                'UT'=>"Utah",
                'VT'=>"Vermont",
                'VA'=>"Virginia",
                'WA'=>"Washington",
                'WV'=>"West Virginia",
                'WI'=>"Wisconsin",
                'WY'=>"Wyoming"
            )
        );


        foreach($provinces as $key => $val) {
            $country = $manager->getRepository('AvroLocationBundle:Country')->findOneBy(array('alias' => $key));

            foreach($val as $alias => $name) {
                $province = new Province();
                $province->setAlias($alias);
                $province->setName($name);
                $province->setCountry($country);

                $manager->persist($province);
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
