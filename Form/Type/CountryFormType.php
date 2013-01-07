<?php
namespace Avro\LocationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/*
 * Country Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CountryFormType extends AbstractType
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

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Avro\LocationBundle\Document\Country'
        ));
    }

    public function getName()
    {
        return 'avro_user_country';
    }
}
