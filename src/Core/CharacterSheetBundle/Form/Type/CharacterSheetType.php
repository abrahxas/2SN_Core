<?php

namespace Core\CharacterSheetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharacterSheetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', 'text')
            ->add('details', 'text')
            ->add('background', 'textarea')
            ->add('imageFile', 'file')
            ->add('sheetFile', 'file')
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
            'data_class' => 'Core\CharacterSheetBundle\Entity\CharacterSheet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_characterSheetbundle_characterSheet';
    }
}
