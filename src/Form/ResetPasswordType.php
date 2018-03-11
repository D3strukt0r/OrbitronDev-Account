<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'label'       => 'forgot.form_reset.password.label',
                'attr'        => [
                    'placeholder' => 'forgot.form_reset.password.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'reset.password.not_blank']),
                ],
            ])
            ->add('password_verify', PasswordType::class, [
                'label'       => 'forgot.form_reset.password_verify.label',
                'attr'        => [
                    'placeholder' => 'forgot.form_reset.password_verify.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'reset.password_verify.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'forgot.form_reset.send.label',
            ]);
    }
}
