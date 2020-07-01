<?php
declare(strict_types=1);
namespace Vanssa\AddressFieldPerCountryPlugin\From\Admin;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Vanssa\AddressFieldPerCountryPlugin\From\Types\AdditionalType;

class AddCustomFieldCollectionTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('additional_fields',CollectionType::class,[
            'entry_type'=> AdditionalType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false
        ]);

    }

    public function getExtendedTypes()
    {
       yield \Sylius\Bundle\AddressingBundle\Form\Type\CountryType::class;

    }
}
