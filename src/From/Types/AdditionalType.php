<?php
declare(strict_types=1);

namespace Vanssa\AddressFieldPerCountryPlugin\From\Types;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdditionalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'sylius.ui.code',
                'mapped' => true,

            ])
            ->add('label', TextType::class, [
                'label' => 'sylius.ui.name',
                'mapped' => true,

            ])
            ->add('has_required', CheckboxType::class, [
                'label' => 'Has Required ?',
                'mapped' => true,
            ]);

    }

    public function getName()
    {
        return 'address_field_per_country_type';
    }


    public function getBlockPrefix(): string
    {
        return 'address_field_per_country_type';
    }
}
