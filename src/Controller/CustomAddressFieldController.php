<?php

declare(strict_types=1);
namespace Vanssa\AddressFieldPerCountryPlugin\Controller;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vanssa\AddressFieldPerCountryPlugin\Entity\Country;

class CustomAddressFieldController  extends ResourceController
{
    protected  $temmplate = '@VanssaAddressFieldPerCountryPlugin/Fields/_text_field.twig.html.twig';

    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function choiceOrTextFieldFormActionAjax(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->isHtmlRequest() || null === $countryCode = $request->query->get('countryCode')) {
            throw new AccessDeniedException();
        }

        /** @var Country $country */
        if (!$country = $this->get('sylius.repository.country')->findOneBy(['code' => $countryCode])) {
            return new JsonResponse([
                'code' => 201
            ]);
        }

        if ($country->hasAdditionalField()) {
            $base_data = $request->query->get('base_data');
            $additional_fields_data = [];
            if($countryCode == $base_data['baseCountryCode']){
                $additional_fields_data = $base_data['additionalFieldsData'];
            }

            $form = $this->createTextForm($country->getAdditionalFields(),$additional_fields_data);

            $view = View::create()
                ->setData([
                    'metadata' => $this->metadata,
                    'form' => $form->createView(),
                ])
                ->setTemplate($this->temmplate)
            ;

            return new JsonResponse([
                'content' => $this->viewHandler->handle($configuration, $view)->getContent(),
            ]);
        }

        return new JsonResponse([
            'code' => 201
        ]);
    }
    public function choiceOrTextFieldFormAction(Request $request) : Response{
        return $request->isXmlHttpRequest() ?$this->choiceOrTextFieldFormActionAjax($request):$this->choiceOrTextFieldFormActionHtml($request);
    }
    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function choiceOrTextFieldFormActionHtml(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->isHtmlRequest() || null === $countryCode = $request->query->get('countryCode')) {
            throw new AccessDeniedException();
        }

        /** @var Country $country */
        if (!$country = $this->get('sylius.repository.country')->findOneBy(['code' => $countryCode])) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if ($country->hasAdditionalField()) {
            $form = $this->createTextForm($country->getAdditionalFields());



            $view = View::create()
                ->setData([
                    'metadata' => $this->metadata,
                    'form' => $form->createView(),
                ])
                ->setTemplate($this->temmplate)
            ;

        }

        return $this->viewHandler->handle($configuration, $view);


    }

    protected function createTextForm(array $fields,?array $additional_fields_data =null): FormInterface
    {
        $form = $this->get('form.factory')->createNamed('sylius_address',FormType::class,null,['auto_initialize'=>false]);
        $embeded =  $this->get('form.factory')->createNamed('additional_fields',FormType::class,null,['auto_initialize'=>false]);
        foreach ($fields AS $field){
            $field_config = [
                'label' => $field['label'],
                'required' => $field['has_required']
            ];

            if(is_array($additional_fields_data) AND isset($additional_fields_data[$field['code']])){
                $field_config['attr'] = [
                    'value' => $additional_fields_data[$field['code']]
                ];
            }

            $embeded->add($field['code'],TextType::class,$field_config);
        }
        $form->add($embeded);
       return  $form;
    }
}
