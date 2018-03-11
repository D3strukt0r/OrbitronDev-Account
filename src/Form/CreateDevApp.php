<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateDevApp extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_name', TextType::class, [
                'label'       => 'panel.form.create_dev_app.client_name.label',
                'constraints' => [
                    new NotBlank(['message' => 'panel.create_dev_app.client_name.not_blank']),
                ],
            ])
            ->add('redirect_uri', TextType::class, [
                'label'       => 'panel.form.create_dev_app.redirect_uri.label',
                'constraints' => [
                    new NotBlank(['message' => 'panel.create_dev_app.redirect_uri.not_blank']),
                ],
            ])
            ->add('scopes', ChoiceType::class, [
                'label'    => 'panel.form.create_dev_app.scopes.label',
                'choices'  => $options['scope_choices'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'panel.form.create_dev_app.send.label',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'scope_choices' => [],
        ]);
    }
}
