<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Vanssa\AddressFieldPerCountryPlugin\From\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Vanssa\AddressFieldPerCountryPlugin\Entity\Address;
use Vanssa\AddressFieldPerCountryPlugin\Entity\Country;

/**
 * @internal
 */
final class BuildAddressFormSubscriber implements EventSubscriberInterface
{
    /** @var ObjectRepository */
    private $countryRepository;

    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(ObjectRepository $countryRepository, FormFactoryInterface $factory)
    {
        $this->countryRepository = $countryRepository;
        $this->formFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }


    public function preSetData(FormEvent $event): void
    {
        /** @var Address $address */
        $address = $event->getData();
        if (null === $address) {
            return;
        }

        $countryCode = $address->getCountryCode();
        if (null === $countryCode) {
            return;
        }

        /** @var Country $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);
        if (null === $country) {
            return;
        }
        if($country->hasAdditionalField()) {
            $form = $event->getForm();
            $form->add($this->createTextForm($country->getAdditionalFields(),$event->getData()));
        }


    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('countryCode', $data)) {
            return;
        }

        if ('' === $data['countryCode']) {
            return;
        }

        /** @var Country $country */
        $country = $this->countryRepository->findOneBy(['code' => $data['countryCode']]);
        if (null === $country) {
            return;
        }

        $form = $event->getForm();

        if ($country->hasAdditionalField()) {
            $form->add($this->createTextForm($country->getAdditionalFields(),$data));

        }
    }


    protected function createTextForm($fields,  $address = null): FormInterface
    {
        $embeded = $this->formFactory->createNamed('additional_fields',FormType::class,null,['auto_initialize'=>false,'label'=>false]);
        $address_id = null;
        if($address instanceof Address AND $address->getId()){
            $address_id = $address->getId();
        }
        foreach ($fields AS $field){
            $additional_fields = [
                'label' => $field['label'],
                'required' => $field['has_required']
            ];

            if($address_id){
                $additional_fields = [
                    'attr'=>[
                        'value' => $address->getFieldByKey($field['code'])
                    ]
                ];
            }
            $embeded->add($field['code'],TextType::class,$additional_fields);
        }
        return $embeded;

    }

}
