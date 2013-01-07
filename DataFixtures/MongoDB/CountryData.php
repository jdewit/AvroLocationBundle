<?php
namespace Avro\LocationBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use Avro\LocationBundle\Document\Country;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class CountryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $countries = array(
            "CA" => "Canada",
            "US" => "United States"
        );

        foreach($countries as $alias => $name) {
            $country = new Country();
            $country->setAlias($alias);
            $country->setName($name);

            $manager->persist($country);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
