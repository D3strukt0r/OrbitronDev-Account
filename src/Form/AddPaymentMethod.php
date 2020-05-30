<?php

namespace App\Form;

use App\Entity\UserPaymentMethods;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddPaymentMethod extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'Payment type',
                    'choices' => [
                        '-- Choose method --' => false,
                        'Mastercard' => UserPaymentMethods::PAYMENT_MASTERCARD,
                        'Visa' => UserPaymentMethods::PAYMENT_VISA,
                        'PayPal' => UserPaymentMethods::PAYMENT_PAYPAL,
                        'Maestro' => UserPaymentMethods::PAYMENT_MAESTRO,
                    ],
                ]
            )
            ->add('data', HiddenType::class)
            ->add(
                'send',
                SubmitType::class,
                [
                    'label' => 'panel.form.add_address.send.label',
                ]
            )
        ;
    }
}
