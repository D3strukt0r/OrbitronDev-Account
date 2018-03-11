<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('redirect', HiddenType::class, [
                'data' => $options['redirect'],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'login.form.email.label',
                'attr'        => [
                    'placeholder' => 'login.form.email.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'login.email.not_blank']),
                    new Email(['message' => 'login.email.valid']),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label'       => 'login.form.password.label',
                'attr'        => [
                    'placeholder' => 'login.form.password.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'login.password.not_blank']),
                ],
            ])
            ->add('remember', CheckboxType::class, [
                'label'    => 'login.form.remember',
                'required' => false,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'login.form.login',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'redirect' => null,
        ]);
    }
}
