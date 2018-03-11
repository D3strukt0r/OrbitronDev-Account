<?php

namespace App\Form;

use App\Form\Type\ReCaptchaType;
use App\Service\AccountHelper;
use App\Validator\Constraints\ReCaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label'       => 'register.form.username.label',
                'attr'        => [
                    'placeholder' => 'register.form.username.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'register.username.not_blank']),
                    new Length([
                        'min'        => AccountHelper::$settings['username']['min_length'],
                        'minMessage' => 'register.username.username_short',
                        'max'        => AccountHelper::$settings['username']['max_length'],
                        'maxMessage' => 'register.username.username_long',
                    ]),
                    new Regex([
                        'pattern' => AccountHelper::$settings['username']['pattern'],
                        'message' => 'register.username.regex',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'register.form.email.label',
                'attr'        => [
                    'placeholder' => 'register.form.email.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'register.email.not_blank']),
                    new Email([
                        'strict'  => true,
                        'checkMX' => true,
                        'message' => 'register.email.valid',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'register.password_verify.do_not_match',
                'first_options'   => [
                    'label'       => 'register.form.password.label',
                    'attr'        => [
                        'placeholder' => 'register.form.password.placeholder',
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'register.password.not_blank']),
                        new Length([
                            'min'        => AccountHelper::$settings['password']['min_length'],
                            'minMessage' => 'register.password.password_too_short',
                        ]),
                    ],
                ],
                'second_options'  => [
                    'label'       => 'register.form.password_verify.label',
                    'attr'        => [
                        'placeholder' => 'register.form.password_verify.placeholder',
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'register.password_verify.not_blank']),
                    ],
                ],
            ])
            ->add('recaptcha', ReCaptchaType::class, [
                'attr'        => [
                    'options' => [
                        'theme' => 'light',
                        'type'  => 'image',
                        'size'  => 'normal',
                        'defer' => true,
                        'async' => true,
                    ],
                ],
                'mapped'      => false,
                'constraints' => [
                    new ReCaptchaTrue(),
                ],
            ])
            ->add('terms', CheckboxType::class, [
                'label'       => 'register.form.terms.label',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'register.terms.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'register.form.send.label',
            ]);
    }
}
