<?php

namespace Core\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use App\UserBundle\Entity\User;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('birthDate', 'birthday' , array('label' => 'Birthday','years' => range(date('Y'),1900)));
    }

    public function getName()
    {
        return 'core_user_registration';
    }
}
