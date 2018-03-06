<?php

namespace App\Form;

use App\Form\Type\ReCaptchaType;
use App\Service\AccountHelper;
use App\Validator\Constraints\ReCaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                    new NotBlank(['message' => 'register.form.username.constraints.not_blank']),
                    new Length([
                        'min' => AccountHelper::$settings['username']['min_length'],
                        'minMessage' => 'register.form.username.constraints.username_short',
                        'max' => AccountHelper::$settings['username']['max_length'],
                        'maxMessage' => 'register.form.username.constraints.username_long',
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'register.form.email.label',
                'attr'        => [
                    'placeholder' => 'register.form.email.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'register.form.email.constraints.not_blank']),
                    new Email(['message' => 'register.form.email.constraints.valid']),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label'       => 'register.form.password.label',
                'attr'        => [
                    'placeholder' => 'register.form.password.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'register.form.password.constraints.not_blank']),
                    new Length([
                        'min' => AccountHelper::$settings['password']['min_length'],
                        'minMessage' => 'register.form.password.constraints.password_too_short',
                    ])
                ],
            ])
            ->add('password_verify', PasswordType::class, [
                'label'       => 'register.form.password_verify.label',
                'attr'        => [
                    'placeholder' => 'register.form.password_verify.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'register.form.password_verify.constraints.not_blank']),
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
                ]
            ])
            ->add('terms', CheckboxType::class, [
                'label'       => 'register.form.terms.label',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'register.form.terms.constraints.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, array(
                'label' => 'register.form.send.label',
            ));
    }
}
