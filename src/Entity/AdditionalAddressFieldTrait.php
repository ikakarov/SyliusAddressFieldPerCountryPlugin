<?php


namespace Vanssa\AddressFieldPerCountryPlugin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

trait AdditionalAddressFieldTrait
{
    /**
     * @ORM\Column(type="json",nullable=true)
     * @var ArrayCollection
     */
    protected $additional_fields = null;

    /**
     * @return ArrayCollection
     */
    public function getAdditionalFields() :? array
    {
        return $this->additional_fields;
    }

    /**
     * @param ArrayCollection $additional_fields
     */
    public function setAdditionalFields(array $additional_fields): void
    {
        $this->additional_fields = $additional_fields;
    }

    public function hasAdditionalField():bool {
        return (bool) $this->getAdditionalFields();
    }

    public function getFieldByKey($key){
        if($this->hasAdditionalField() && isset($this->getAdditionalFields()[$key])){
            return $this->getAdditionalFields()[$key];
        }
        return null;
    }

}
