<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'label'    => 'panel.form.update_profile.first_name.label',
                'attr'     => [
                    'placeholder' => $options['name'],
                    'value'       => $options['name'],
                ],
                'required' => false,
            ])
            ->add('last_name', TextType::class, [
                'label'    => 'panel.form.update_profile.last_name.label',
                'attr'     => [
                    'placeholder' => $options['surname'],
                    'value'       => $options['surname'],
                ],
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'label'   => 'panel.form.update_profile.gender.label',
                'choices' => [
                    'panel.form.update_profile.gender.option.none'   => 0,
                    'panel.form.update_profile.gender.option.male'   => 1,
                    'panel.form.update_profile.gender.option.female' => 2,
                ],
                'data'    => $options['gender'],
            ])
            ->add('birthday', TextType::class, [
                'label'    => 'panel.form.update_profile.birthday.label',
                'attr'     => [
                    'value' => $options['birthday'],
                ],
                'required' => false,
            ])
            ->add('website', TextType::class, [
                'label'    => 'panel.form.update_profile.website.label',
                'attr'     => [
                    'value' => $options['website'],
                ],
                'required' => false,
            ])
            ->add('password_verify', PasswordType::class, [
                'label'       => 'panel.form.update_profile.password_verify.label',
                'attr'        => [
                    'placeholder' => 'panel.form.update_profile.password_verify.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'panel.edit_profile.password_verify.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'panel.form.update_profile.send.label',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'name'     => '',
            'surname'  => '',
            'gender'   => 0,
            'birthday' => null,
            'website'  => '',
        ]);
    }
}
