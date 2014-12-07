<?php

namespace Core\FriendListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SelectGroupType extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;
        $builder
            ->add('name', 'entity', array(
                'class' => 'Core\FriendListBundle\Entity\FriendGroups',
                'property' => 'name',
                'query_builder' => function (EntityRepository $fg) use ($user) {
                        return $fg->createQueryBuilder('fg')
                                        ->where('fg.user = :user')
                                        ->setParameter('user', $user)
                                        ->andWhere('fg.name != :wait')
                                        ->setParameter('wait', 'wait');
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
            'data_class' => 'Core\FriendListBundle\Entity\FriendGroups',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_friendListbundle_friendGroups';
    }
}
