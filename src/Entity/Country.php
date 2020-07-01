<?php
declare(strict_types=1);

namespace Vanssa\AddressFieldPerCountryPlugin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Addressing\Model\Country as BaseCountry;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_country")
 */
class Country extends BaseCountry
{
    use AdditionalAddressFieldTrait;

}
