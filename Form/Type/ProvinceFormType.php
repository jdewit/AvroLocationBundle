<?php
namespace Avro\LocationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/*
 * Province Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ProvinceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Name',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the name',
                    'class' => 'capitalize',
                )
            ))
            ->add('country', 'document', array(
                'label' => 'Country',
                'class' => 'Avro\LocationBundle\Document\Country',
                'error_bubbling' => true,
//                'query_builder' => function($repo) {
//                    return $repo->createQueryBuilder()
//                        ->sort('name', 'asc');
//                },
                'attr' => array(
                    'class' => 'add-option',
                    'data-text' => 'Create a new country',
                    'data-route' => 'avro_user_country_new',
                )
            ))


        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Avro\LocationBundle\Document\Province'
        ));
    }

    public function getName()
    {
        return 'avro_user_province';
    }
}
