<?php

namespace App\Form;

use App\Service\AccountHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'reset.password_verify.do_not_match',
                    'first_options' => [
                        'label' => 'forgot.form_reset.password.label',
                        'attr' => [
                            'placeholder' => 'forgot.form_reset.password.placeholder',
                        ],
                        'constraints' => [
                            new NotBlank(['message' => 'reset.password.not_blank']),
                            new Length(
                                [
                                    'min' => AccountHelper::$settings['password']['min_length'],
                                    'minMessage' => 'reset.password.password_too_short',
                                ]
                            ),
                        ],
                    ],
                    'second_options' => [
                        'label' => 'forgot.form_reset.password_verify.label',
                        'attr' => [
                            'placeholder' => 'forgot.form_reset.password_verify.placeholder',
                        ],
                        'constraints' => [
                            new NotBlank(['message' => 'reset.password_verify.not_blank']),
                        ],
                    ],
                ]
            )
            ->add(
                'send',
                SubmitType::class,
                [
                    'label' => 'forgot.form_reset.send.label',
                ]
            );
    }
}
