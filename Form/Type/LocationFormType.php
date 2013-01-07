<?php
namespace Avro\LocationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\FilterDataEvent;
use Doctrine\ODM\EntityRepository;

/*
 * location Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class LocationFormType extends AbstractType
{
    protected $router;
    protected $country;
    protected $province;

    public function __construct($router, $country, $province)
    {
        $this->router = $router;
        $this->country = $country;
        $this->province = $province;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $router = $this->router;
        $country = $this->country;
        $province = $this->province;

            $options = array(
                'label' => 'Country',
                'class' =>'Avro\LocationBundle\Document\Country',
                'empty_value' => 'Select a Country',
                'required' => false,
                'property_path' => false,
                'attr' => array(
                    'title' => 'Select your country',
                    'class' => 'dynamic-select',
                    'data-name' => 'country',
                    'data-child' => 'province',
                    'data-url' => $router->generate('avro_location_province_get')
                )
            );
            if ($country) {
                $options = array_merge($options, array(
                    'preferred_choices' => array($country),
                ));
            }
            $builder->add('country', 'document', $options);
        ;

        $addProvince = function($form, $countryId) use ($builder, $router, $country, $province) {
            $options = array(
                'label' => 'Province',
                'class' =>'Avro\LocationBundle\Document\Province',
                'choices' => array(),
                'required' => false,
                'empty_value' => 'Select a Province',
                'attr' => array(
                    'title' => 'Select your province',
                    'data-name' => 'province',
                    'class' => 'dynamic-select',
                    'data-child' => 'city',
                    'data-url' => $router->generate('avro_location_city_get')
                )
            );
            if ($countryId) {
                unset($options['choices']);
                $options = array_merge($options, array(
//                    'preferred_choices' => array($province),
                    'query_builder' => function($repo) use ($countryId) {
                        $qb = $repo->createQueryBuilder();
                        $qb->field('country')->equals($countryId);

                        return $qb;
                    },
                ));
            }

            $form->add($builder->getFormFactory()->createNamed('province', 'document', null, $options));
        };

        $addCity = function($form, $provinceId) use ($builder, $province) {
            $options = array(
                'label' => 'City',
                'class' =>'Avro\LocationBundle\Document\City',
                'choices' => array(),
                'empty_value' => 'Select a City',
                'attr' => array(
                    'title' => 'Select your city',
                    'class' => 'last-select',
                    'data-name' => 'city',
                )
            );
            if ($provinceId) {
                unset($options['choices']);
                $options = array_merge($options, array(
                     'query_builder' => function($repo) use ($provinceId) {
                        $qb = $repo->createQueryBuilder();
                        $qb->field('province')->equals($provinceId);

                        return $qb;
                    },
                ));
            }

            $form->add($builder->getFormFactory()->createNamed('city', 'document', null, $options));
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (DataEvent $event) use ($addCity, $addProvince) {
            $form = $event->getForm();
            $data = $event->getData();

            $addProvince($form, false);
            $addCity($form, false);

            //if ($data === null) {
                //$addCountry($form, $country);
                //$addCity($form, $province);
                //$addProvince($form, $country);
                ////ld($form); exit;
                ////$form['country']->setData($country);
                ////$form['province']->setData($province);
            //} elseif (is_object($data)) {
                //$province = $data->getProvince();
                //$country = $data->getCountry();
                //if ($province) {
                    //$addCity($form, $province->getId());
                    //$addProvince($form, $country->getId());
                    //$addCountry($form, $country->getId());
                //} else {
                    //$addCity($form);
                    //$addProvince($form);
                    //$addCountry($form);
                //}
            //}
        });
        $builder->addEventListener(FormEvents::PRE_BIND, function (DataEvent $event) use ($addCity, $addProvince, $country, $province) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('city', $data)) {
                $addCity($form, $data['province']);
            }
            if (array_key_exists('province', $data)) {
                $addProvince($form, $data['country']);
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'Avro\LocationBundle\Document\location'
        ));
    }

    public function getName()
    {
        return 'avro_location_location';
    }
}
