<?php

namespace Core\GameSessionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateGameSessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', array('attr' => array('rows' => '5','cols' => '30')) )
            ->add('save', 'submit')
            ->getForm()
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\GameSessionBundle\Entity\GameSession'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_gamesessionbundle_createGameSession';
    }
}
