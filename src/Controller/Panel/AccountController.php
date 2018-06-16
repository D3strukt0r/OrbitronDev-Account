<?php

namespace App\Controller\Panel;

use App\Controller\OAuthController;
use App\Entity\UserAddress;
use App\Form\AddAddressType;
use App\Form\EditAccountType;
use App\Form\EditProfileType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{
    public static function __setupNavigation()
    {
        return [
            [
                'type' => 'group',
                'parent' => 'root',
                'id' => 'account',
                'title' => 'Account',
                'icon' => 'hs-admin-user',
            ],
            [
                'type' => 'link',
                'parent' => 'account',
                'id' => 'account_account',
                'title' => 'Account details',
                'href' => 'account',
                'view' => 'AccountController::account',
            ],
            [
                'type' => 'link',
                'parent' => 'account',
                'id' => 'account_profile',
                'title' => 'Personal information',
                'href' => 'profile',
                'view' => 'AccountController::profile',
            ],
            [
                'type' => 'link',
                'parent' => 'account',
                'id' => 'account_add_address',
                'title' => 'Add new address',
                'href' => 'add-address',
                'view' => 'AccountController::addAddress',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 10;
    }

    public function account(ObjectManager $em, Request $request, $navigation)
    {
        $editAccountForm = $this->createForm(EditAccountType::class);
        $editAccountForm->handleRequest($request);
        if ($editAccountForm->isSubmitted() && $editAccountForm->isValid()) {
            $formData = $editAccountForm->getData();

            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            if (null !== ($newUsername = $formData['new_username'])) {
                $user->setUsername($newUsername);
            }
            if (null !== ($newPassword = $formData['new_password'])) {
                $user->setPassword($newPassword);
            }
            if (null !== ($newEmail = $formData['new_email'])) {
                $user->setEmail($newEmail);
                $user->setEmailVerified(false);
            }
            $em->flush();

            OAuthController::sendCallback($em, $user);
        }

        return $this->render('panel/account.html.twig', [
            'navigation_links' => $navigation,
            'edit_account_form' => $editAccountForm->createView(),
        ]);
    }

    public function profile(ObjectManager $em, Request $request, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $editProfileForm = $this->createForm(EditProfileType::class, null, [
            'name' => $user->getProfile()->getName(),
            'surname' => $user->getProfile()->getSurname(),
            'gender' => $user->getProfile()->getGender(),
            'birthday' => null !== ($bd = $user->getProfile()->getBirthday()) ? $bd->format('d/m/Y') : null,
            'website' => $user->getProfile()->getWebsite(),
        ]);
        $editProfileForm->handleRequest($request);
        if ($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {
            $formData = $editProfileForm->getData();

            $user->getProfile()
                ->setName($formData['first_name'])
                ->setSurname($formData['last_name'])
                ->setGender($formData['gender'])
                ->setBirthday(\DateTime::createFromFormat('d/m/Y', $formData['birthday']) ?: null)
                ->setWebsite($formData['website']);

            $em->flush();

            OAuthController::sendCallback($em, $user);
            return $this->redirectToRoute('panel', ['page' => 'profile']);
        }

        return $this->render('panel/profile.html.twig', [
            'navigation_links' => $navigation,
            'edit_profile_form' => $editProfileForm->createView(),
        ]);
    }

    public function addAddress(ObjectManager $em, Request $request, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $addAddressForm = $this->createForm(AddAddressType::class);

        $addAddressForm->handleRequest($request);
        if ($addAddressForm->isSubmitted() && $addAddressForm->isValid()) {
            $formData = $addAddressForm->getData();

            $newAddress = (new UserAddress())
                ->setStreet($formData['location_street'])
                ->setHouseNumber($formData['location_street_number'])
                ->setZipCode($formData['location_postal_code'])
                ->setCity($formData['location_city'])
                ->setCountry($formData['location_country']);
            $user->getProfile()->addAddress($newAddress);

            $em->flush();

            OAuthController::sendCallback($em, $user);
            return $this->redirectToRoute('panel', ['page' => 'profile']);
        }

        return $this->render('panel/add-address.html.twig', [
            'navigation_links' => $navigation,
            'add_address_form' => $addAddressForm->createView(),
        ]);
    }
}
