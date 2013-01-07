<?php
namespace Avro\LocationBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ODM\Document
 */
class Province {

    /**
     * @ODM\Id(strategy="auto")
     */
    public $id;

    /**
     * Province name.
     *
     * @ODM\String
     */
    protected $name;

    /**
     * alias.
     *
     * @ODM\String
     */
    protected $alias;

    /**
     * Country.
     *
     * @ODM\ReferenceOne(targetDocument="Avro\LocationBundle\Document\Country", simple=true)
     */
    protected $country;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Province
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set country
     *
     * @param Avro\LocationBundle\Document\Country $country
     * @return Province
     */
    public function setCountry(\Avro\LocationBundle\Document\Country $country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return Avro\LocationBundle\Document\Country $country
     */
    public function getCountry()
    {
        return $this->country;
    }
}
