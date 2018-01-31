<?php

namespace App\Form;

use App\Entity\OAuthScope;
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
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $options['entity_manager'];
        /** @var \App\Entity\OAuthScope[] $scopes */
        $scopes = $em->getRepository(OAuthScope::class)->findAll();

        $scope_choices = [];
        foreach ($scopes as $scope) {
            $scope_choices[$scope->getName()] = $scope->getScope();
        }

        $builder
            ->add('client_name', TextType::class, [
                'label'       => 'panel.form.create_dev_app.client_name.label',
                'constraints' => [
                    new NotBlank(['message' => 'panel.form.create_dev_app.client_name.constraints.not_blank']),
                ],
            ])
            ->add('redirect_uri', TextType::class, [
                'label'       => 'panel.form.create_dev_app.redirect_uri.label',
                'constraints' => [
                    new NotBlank(['message' => 'panel.form.create_dev_app.redirect_uri.constraints.not_blank']),
                ],
            ])
            ->add('scopes', ChoiceType::class, [
                'label'    => 'panel.form.create_dev_app.scopes.label',
                'choices'  => $scope_choices,
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
            'entity_manager' => null,
        ]);
    }
}
