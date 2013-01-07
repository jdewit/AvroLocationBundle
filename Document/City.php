<?php
namespace Avro\LocationBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ODM\Document
 */
class City {

    /**
     * @ODM\Id(strategy="auto")
     */
    public $id;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ODM\String
     */
    protected $slug;

    /**
     * City name.
     *
     * @ODM\String
     */
    protected $name;

    /**
     * Province.
     *
     * @ODM\ReferenceOne(targetDocument="Avro\LocationBundle\Document\Province", simple=true)
     */
    protected $province;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }


    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return City
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


    /**
     * Set province
     *
     * @param Avro\LocationBundle\Document\Province $province
     * @return City
     */
    public function setProvince(\Avro\LocationBundle\Document\Province $province)
    {
        $this->province = $province;
        return $this;
    }

    /**
     * Get province
     *
     * @return Avro\LocationBundle\Document\Province $province
     */
    public function getProvince()
    {
        return $this->province;
    }

    public function __toString()
    {
        return $this->name;
    }
}
