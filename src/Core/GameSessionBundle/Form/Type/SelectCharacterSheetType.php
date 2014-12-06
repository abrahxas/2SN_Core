<?php

namespace Core\GameSessionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SelectCharacterSheetType extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user=$user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;
        $builder
            ->add('CharacterSheet','entity',array(
                'class' => 'Core\CharacterSheetBundle\Entity\CharacterSheet',
                'property' => 'id',
                'query_builder' => function(EntityRepository $ch) use ($user)
                {
                        return $ch->createQueryBuilder('ch')
                                        ->where('ch.user = :user')
                                        ->setParameter('user', $user);
                },
            ))
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
            'data_class' => 'Core\GameSessionBundle\Entity\Player'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_gamesessionbundle_SelectCharacterSheetType';
    }
}
