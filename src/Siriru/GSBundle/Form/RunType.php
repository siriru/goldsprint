<?php

namespace Siriru\GSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RunType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time1', 'gs_time')
            ->add('time2', 'gs_time')
            ->add('player1', 'entity', array(
                'class' => 'SiriruGSBundle:Player',
                'property' => 'name',
                'expanded' => false,
                'multiple' => false
            ))
            ->add('player2', 'entity', array(
                'class' => 'SiriruGSBundle:Player',
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Siriru\GSBundle\Entity\Run'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'siriru_gsbundle_run';
    }
}
