<?php

declare(strict_types=1);

namespace Vanssa\AddressFieldPerCountryPlugin\From\Extension;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class AddressTypeExtensions extends AbstractTypeExtension
{
    /**
     * @var EventSubscriberInterface
     */
    private $buildAddressFormSubscriber;

    /**
     * @param EventSubscriberInterface $buildAddressFormSubscriber
     */
    public function __construct(EventSubscriberInterface $buildAddressFormSubscriber)
    {

        $this->buildAddressFormSubscriber = $buildAddressFormSubscriber;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->buildAddressFormSubscriber);
    }

    public function getExtendedTypes()
    {
        yield \Sylius\Bundle\AddressingBundle\Form\Type\AddressType::class;

    }
}
