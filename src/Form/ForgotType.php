<?php

namespace App\Form;

use App\Form\Type\ReCaptchaType;
use App\Validator\Constraints\ReCaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForgotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'forgot.form.email.label',
                    'attr' => [
                        'placeholder' => 'forgot.form.email.placeholder',
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'forgot.email.not_blank']),
                        new Email(['message' => 'forgot.email.valid']),
                    ],
                ]
            )
            ->add(
                'recaptcha',
                ReCaptchaType::class,
                [
                    'attr' => [
                        'options' => [
                            'theme' => 'light',
                            'type' => 'image',
                            'size' => 'normal',
                            'defer' => true,
                            'async' => true,
                        ],
                    ],
                    'mapped' => false,
                    'constraints' => [
                        new ReCaptchaTrue(),
                    ],
                ]
            )
            ->add(
                'send',
                SubmitType::class,
                [
                    'label' => 'forgot.form.send.label',
                ]
            )
        ;
    }
}
