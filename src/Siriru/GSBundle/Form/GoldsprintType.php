<?php

namespace Siriru\GSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class GoldsprintType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('location')
            ->add('description')
            ->add('date')
            ->add('players', 'entity', array(
                'class' => 'SiriruGSBundle:Player',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.enabled = TRUE')
                        ->orderBy('p.name', 'ASC');
                    },
                'property' => 'name',
                'expanded' => true,
                'multiple' => true
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Siriru\GSBundle\Entity\Goldsprint'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'siriru_gsbundle_goldsprint';
    }
}
