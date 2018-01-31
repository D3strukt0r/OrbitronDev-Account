<?php

namespace App\AdminAddons;

use App\Helper\AccountHelper;
use App\Entity\UserAddress;
use App\Form\AddAddressType;
use App\Form\EditAccountType;
use App\Form\EditProfileType;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormError;

class Account
{
    public static function __setupNavigation()
    {
        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'account',
                'title'  => 'Account',
                'icon'   => 'fa fa-fw fa-user',
            ],
            [
                'type'   => 'link',
                'parent' => 'account',
                'id'     => 'account_account',
                'title'  => 'Account details',
                'href'   => 'account',
                'view'   => 'Account::account',
            ],
            [
                'type'   => 'link',
                'parent' => 'account',
                'id'     => 'account_profile',
                'title'  => 'Personal information',
                'href'   => 'profile',
                'view'   => 'Account::profile',
            ],
            [
                'type'   => 'link',
                'parent' => 'account',
                'id'     => 'account_add_address',
                'title'  => 'Add new address',
                'href'   => 'add-address',
                'view'   => 'Account::addAddress',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 10;
    }

    public static function account(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $form = $container->get('form.factory');
        $request = $container->get('request_stack')->getCurrentRequest();
        $translator = $container->get('translator');
        $twig = $container->get('twig');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        $editAccountForm = $form->create(EditAccountType::class, null, ['user' => $user]);

        $editAccountForm->handleRequest($request);
        if ($editAccountForm->isSubmitted() && $editAccountForm->isValid()) {
            $formData = $editAccountForm->getData();
            if (strlen($newUsername = $formData['new_username']) > 0) {
                $changeUsername = true;
            } else {
                $changeUsername = false;
            }
            if (strlen($newPassword = $formData['new_password']) > 0) {
                $changePassword = true;
            } else {
                $changePassword = false;
            }
            if (strlen($newEmail = $formData['new_email']) > 0) {
                $changeEmail = true;
            } else {
                $changeEmail = false;
            }

            $errorMessages = array();
            if ($user->verifyPassword($formData['password_verify'])) {
                if ($changeUsername) {
                    if (strlen($newUsername) == 0) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.not_blank');
                    } elseif (strlen($newUsername) < AccountHelper::$settings['username']['min_length']) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.username_short');
                    } elseif (strlen($newUsername) > AccountHelper::$settings['username']['max_length']) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.username_long');
                    } elseif (AccountHelper::usernameExists($em, $newUsername)) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.username_exists');
                    } elseif (AccountHelper::usernameBlocked($newUsername)) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.blocked_username');
                    } elseif (!AccountHelper::usernameValid($newUsername)) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.not_valid');
                    }
                }
                if ($changePassword) {
                    $verifyNewPassword = $formData['new_password_verify'];
                    if (strlen($newPassword) == 0) {
                        $errorMessages['new_password'] = $translator->trans('panel.form.update_account.new_password.constraints.not_blank');
                    } elseif (strlen($newPassword) < AccountHelper::$settings['password']['min_length']) {
                        $errorMessages['new_password'] = $translator->trans('panel.form.update_account.new_password.constraints.password_too_short');
                    } elseif ($newPassword !== $verifyNewPassword) {
                        $errorMessages['new_password_verify'] = $translator->trans('panel.form.update_account.new_password_verify.constraints.passwords_do_not_match');
                    }
                }
                if ($changeEmail) {
                    if (strlen($newEmail) == 0) {
                        $errorMessages['new_email'] = $translator->trans('panel.form.update_account.new_email.constraints.not_blank');
                    }
                }

                if (count($errorMessages) == 0) {
                    if ($changeUsername) {
                        $user->setUsername($newUsername);
                    }
                    if ($changePassword) {
                        $user->setPassword($newPassword);
                    }
                    if ($changeEmail) {
                        $user->setEmail($newEmail);
                        $user->setEmailVerified(false);
                    }
                    $em->flush();
                }
            } else {
                $errorMessages['password_verify'] = $translator->trans('panel.form.update_account.password_verify.constraints.wrong_password');
            }

            // Save all errors in form
            if (count($errorMessages)) {
                foreach ($errorMessages as $field => $message) {
                    if ($field == 'form') {
                        $editAccountForm->addError(new FormError($message));
                    } else {
                        $editAccountForm->get($field)->addError(new FormError($message));
                    }
                }
            }
        }

        return $twig->render('panel/account.html.twig', array(
            'edit_account_form' => $editAccountForm->createView(),
            'current_user'      => $user,
        ));
    }

    public static function profile(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $form = $container->get('form.factory');
        $request = $container->get('request_stack')->getCurrentRequest();
        $translator = $container->get('translator');
        $twig = $container->get('twig');
        $router = $container->get('router');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        $editProfileForm = $form->create(EditProfileType::class, null, ['user' => $user]);

        $editProfileForm->handleRequest($request);
        if ($editProfileForm->isSubmitted()) {
            $formData = $editProfileForm->getData();

            $errorMessages = array();
            if ($user->verifyPassword($formData['password_verify'])) {
                $user->getProfile()->setName($formData['first_name']);
                $user->getProfile()->setSurname($formData['last_name']);
                $user->getProfile()->setGender($formData['gender']);
                if ($date = \DateTime::createFromFormat('d.m.Y', $formData['birthday'])) {
                    $user->getProfile()->setBirthday($date);
                } else {
                    $errorMessages['update_birthday'] = $translator->trans('panel.form.update_profile.birthday.constraints.date_not_created');
                }
                $user->getProfile()->setWebsite($formData['website']);

                $em->flush();

                header('Location: '.$router->generate('panel', ['page' => 'profile']));
                exit;
            } else {
                $errorMessages['password_verify'] = $translator->trans('panel.form.update_profile.password_verify.constraints.wrong_password');
            }

            // Save all errors in form
            if (count($errorMessages)) {
                foreach ($errorMessages as $field => $message) {
                    if ($field == 'form') {
                        $editProfileForm->addError(new FormError($message));
                    } else {
                        $editProfileForm->get($field)->addError(new FormError($message));
                    }
                }
            }
        }

        return $twig->render('panel/profile.html.twig', array(
            'edit_profile_form' => $editProfileForm->createView(),
            'current_user'      => $user,
        ));
    }

    public static function addAddress(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $form = $container->get('form.factory');
        $request = $container->get('request_stack')->getCurrentRequest();
        $translator = $container->get('translator');
        $twig = $container->get('twig');
        $router = $container->get('router');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        $addAddressForm = $form->create(AddAddressType::class);

        $addAddressForm->handleRequest($request);
        if ($addAddressForm->isSubmitted()) {
            $formData = $addAddressForm->getData();

            $errorMessage = [];
            if ($user->verifyPassword($formData['password_verify'])) {

                $newAddress = new UserAddress();
                $newAddress->setStreet($formData['location_street']);
                $newAddress->setHouseNumber($formData['location_street_number']);
                $newAddress->setZipCode($formData['location_postal_code']);
                $newAddress->setCity($formData['location_city']);
                $newAddress->setCountry($formData['location_country']);

                $user->getProfile()->addAddress($newAddress);

                $em->flush();

                header('Location: '.$router->generate('panel', ['page' => 'profile']));
                exit;
            } else {
                $errorMessage['password_verify'] = $translator->trans('panel.form.add_address.password_verify.constraints.wrong_password');
            }
        }

        return $twig->render('panel/add-address.html.twig', array(
            'add_address_form' => $addAddressForm->createView(),
            'current_user'     => $user,
        ));
    }
}
