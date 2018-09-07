<?php

namespace App\Form;

use App\Service\AccountHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditAccountType extends AbstractType
{
    private $helper;

    public function __construct(AccountHelper $helper)
    {
        $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_username', TextType::class, [
                'label' => 'panel.form.update_account.new_username.label',
                'required' => false,
                'attr' => [
                    'placeholder' => 'panel.form.update_account.new_username.placeholder',
                ],
                'constraints' => [
                    new Length([
                        'min' => AccountHelper::$settings['username']['min_length'],
                        'minMessage' => 'panel.edit_account.new_username.username_short',
                        'max' => AccountHelper::$settings['username']['max_length'],
                        'maxMessage' => 'panel.edit_account.new_username.username_long',
                    ]),
                    new Regex([
                        'pattern' => AccountHelper::$settings['username']['pattern'],
                        'message' => 'panel.edit_account.new_username.regex',
                    ]),
                    new Callback(function ($object, ExecutionContextInterface $context, $payload) {
                        if ($this->helper->usernameExists($object)) {
                            $context->addViolation('panel.edit_account.new_username.username_exists');
                        }
                        if ($this->helper->usernameBlocked($object)) {
                            $context->addViolation('panel.edit_account.new_username.blocked_username');
                        }
                    }),
                ],
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'panel.edit_account.new_password_verify.do_not_match',
                'required' => false,
                'first_options' => [
                    'label' => 'panel.form.update_account.new_password.label',
                    'attr' => [
                        'placeholder' => 'panel.form.update_account.new_password.placeholder',
                    ],
                    'constraints' => [
                        new Length([
                            'min' => AccountHelper::$settings['password']['min_length'],
                            'minMessage' => 'panel.edit_account.new_password.password_too_short',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'panel.form.update_account.new_password_verify.label',
                    'attr' => [
                        'placeholder' => 'panel.form.update_account.new_password_verify.placeholder',
                    ],
                ],
            ])
            ->add('new_email', EmailType::class, [
                'label' => 'panel.form.update_account.new_email.label',
                'required' => false,
                'attr' => [
                    'placeholder' => 'panel.form.update_account.new_email.placeholder',
                ],
                'constraints' => [
                    new Email([
                        'mode' => 'strict',
                        'checkMX' => true,
                        'message' => 'panel.edit_account.new_email.valid',
                    ]),
                    new Callback(function ($object, ExecutionContextInterface $context, $payload) {
                        if ($this->helper->emailExists($object)) {
                            $context->addViolation('panel.edit_account.new_email.exists');
                        }
                    }),
                ],
            ])
            ->add('password_verify', PasswordType::class, [
                'label' => 'panel.form.update_profile.password_verify.label',
                'attr' => [
                    'placeholder' => 'panel.form.update_profile.password_verify.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'panel.edit_account.password_verify.not_blank']),
                    new UserPassword(['message' => 'panel.edit_account.password_verify.wrong_password']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'panel.form.update_profile.send.label',
            ]);
    }
}
