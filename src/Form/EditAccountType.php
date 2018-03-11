<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_username', TextType::class, [
                'label'    => 'panel.form.update_account.new_username.label',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'panel.form.update_account.new_username.placeholder',
                ],
            ])
            ->add('new_password', PasswordType::class, [
                'label'    => 'panel.form.update_account.new_password.label',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'panel.form.update_account.new_password.placeholder',
                ],
            ])
            ->add('new_password_verify', PasswordType::class, [
                'label'    => 'panel.form.update_account.new_password_verify.label',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'panel.form.update_account.new_password_verify.placeholder',
                ],
            ])
            ->add('new_email', EmailType::class, [
                'label'       => 'panel.form.update_account.new_email.label',
                'required'    => false,
                'attr'        => [
                    'placeholder' => 'panel.form.update_account.new_email.placeholder',
                ],
                'constraints' => [
                    new Email(['message' => 'panel.edit_account.new_email.valid']),
                ],
            ])
            ->add('password_verify', PasswordType::class, [
                'label'       => 'panel.form.update_profile.password_verify.label',
                'attr'        => [
                    'placeholder' => 'panel.form.update_profile.password_verify.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'panel.edit_account.password_verify.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'panel.form.update_profile.send.label',
            ]);
    }
}
