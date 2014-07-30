<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use App\UserBundle\Entity\User;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('birthDate', null , array('label' => 'Birthday'));
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
