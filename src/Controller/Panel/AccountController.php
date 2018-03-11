<?php

namespace App\Controller\Panel;

use App\Service\AccountHelper;
use App\Entity\UserAddress;
use App\Form\AddAddressType;
use App\Form\EditAccountType;
use App\Form\EditProfileType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class AccountController extends Controller
{
    public static function __setupNavigation()
    {
        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'account',
                'title'  => 'Account',
                'icon'   => 'hs-admin-user',
            ],
            [
                'type'   => 'link',
                'parent' => 'account',
                'id'     => 'account_account',
                'title'  => 'Account details',
                'href'   => 'account',
                'view'   => 'AccountController::account',
            ],
            [
                'type'   => 'link',
                'parent' => 'account',
                'id'     => 'account_profile',
                'title'  => 'Personal information',
                'href'   => 'profile',
                'view'   => 'AccountController::profile',
            ],
            [
                'type'   => 'link',
                'parent' => 'account',
                'id'     => 'account_add_address',
                'title'  => 'Add new address',
                'href'   => 'add-address',
                'view'   => 'AccountController::addAddress',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 10;
    }

    public function account(ObjectManager $em, Request $request, TranslatorInterface $translator, AccountHelper $helper, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $editAccountForm = $this->createForm(EditAccountType::class);

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

            $errorMessages = [];
            if ($user->verifyPassword($formData['password_verify'])) {
                if ($changeUsername) {
                    if (strlen($newUsername) == 0) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.not_blank');
                    } elseif (strlen($newUsername) < AccountHelper::$settings['username']['min_length']) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.username_short');
                    } elseif (strlen($newUsername) > AccountHelper::$settings['username']['max_length']) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.username_long');
                    } elseif ($helper->usernameExists($newUsername)) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.username_exists');
                    } elseif ($helper->usernameBlocked($newUsername)) {
                        $errorMessages['new_username'] = $translator->trans('panel.form.update_account.new_username.constraints.blocked_username');
                    } elseif (!$helper->usernameValid($newUsername)) {
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

        return $this->render('panel/account.html.twig', [
            'navigation_links'  => $navigation,
            'edit_account_form' => $editAccountForm->createView(),
        ]);
    }

    public function profile(ObjectManager $em, Request $request, TranslatorInterface $translator, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $editProfileForm = $this->createForm(EditProfileType::class, null, [
            'name'     => $user->getProfile()->getName(),
            'surname'  => $user->getProfile()->getSurname(),
            'gender'   => $user->getProfile()->getGender(),
            'birthday' => !is_null($bd = $user->getProfile()->getBirthday()) ? $bd->format('d.m.Y') : null,
            'website'  => $user->getProfile()->getWebsite(),
        ]);

        $editProfileForm->handleRequest($request);
        if ($editProfileForm->isSubmitted()) {
            $formData = $editProfileForm->getData();

            $errorMessages = [];
            if ($user->verifyPassword($formData['password_verify'])) {
                $user->getProfile()->setName($formData['first_name']);
                $user->getProfile()->setSurname($formData['last_name']);
                $user->getProfile()->setGender($formData['gender']);
                if ($date = \DateTime::createFromFormat('d/m/Y', $formData['birthday'])) {
                    $user->getProfile()->setBirthday($date);
                } else {
                    $errorMessages['update_birthday'] = $translator->trans('panel.form.update_profile.birthday.constraints.date_not_created');
                }
                $user->getProfile()->setWebsite($formData['website']);

                $em->flush();

                return $this->redirectToRoute('panel', ['page' => 'profile']);
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

        return $this->render('panel/profile.html.twig', [
            'navigation_links'  => $navigation,
            'edit_profile_form' => $editProfileForm->createView(),
        ]);
    }

    public function addAddress(ObjectManager $em, Request $request, TranslatorInterface $translator, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $addAddressForm = $this->createForm(AddAddressType::class);

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

                return $this->redirectToRoute('panel', ['page' => 'profile']);
            } else {
                $errorMessage['password_verify'] = $translator->trans('panel.form.add_address.password_verify.constraints.wrong_password');
            }
        }

        return $this->render('panel/add-address.html.twig', [
            'navigation_links' => $navigation,
            'add_address_form' => $addAddressForm->createView(),
        ]);
    }
}
