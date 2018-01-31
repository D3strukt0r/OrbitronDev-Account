<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location_street', TextType::class, [
                'label' => 'panel.form.add_address.location_street.label',
                'attr'  => [
                    'placeholder' => 'panel.form.add_address.location_street.placeholder',
                ],
            ])
            ->add('location_street_number', TextType::class, [
                'label' => 'panel.form.add_address.location_street_number.label',
                'attr'  => [
                    'placeholder' => 'panel.form.add_address.location_street_number.placeholder',
                ],
            ])
            ->add('location_postal_code', TextType::class, [
                'label' => 'panel.form.add_address.location_postal_code.label',
                'attr'  => [
                    'placeholder' => 'panel.form.add_address.location_postal_code.placeholder',
                ],
            ])
            ->add('location_city', TextType::class, [
                'label' => 'panel.form.add_address.location_city.label',
                'attr'  => [
                    'placeholder' => 'panel.form.add_address.location_city.placeholder',
                ],
            ])
            ->add('location_country', TextType::class, [
                'label' => 'panel.form.add_address.location_country.label',
                'attr'  => [
                    'placeholder' => 'panel.form.add_address.location_country.placeholder',
                ],
            ])
            ->add('password_verify', PasswordType::class, [
                'label'       => 'panel.form.add_address.password_verify.label',
                'attr'        => [
                    'placeholder' => 'panel.form.add_address.password_verify.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'panel.form.add_address.password_verify.constraints.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'panel.form.add_address.send.label',
            ]);
    }
}
