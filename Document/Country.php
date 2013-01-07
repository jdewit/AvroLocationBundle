<?php
namespace Avro\LocationBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ODM\Document
 */
class Country {

    /**
     * @ODM\Id(strategy="auto")
     */
    public $id;

    /**
     * Country name.
     *
     * @ODM\String
     */
    protected $name;

    /**
     * Country name.
     *
     * @ODM\String
     */
    protected $alias;

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

}
