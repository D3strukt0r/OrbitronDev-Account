<?php

namespace App\Controller\Panel;

use App\Entity\User;
use App\Form\DeleteAccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    public static function __setupNavigation()
    {
        return [
            [
                'type' => 'group',
                'parent' => 'root',
                'id' => 'security',
                'title' => 'Security',
                'icon' => 'hs-admin-lock',
            ],
            [
                'type' => 'link',
                'parent' => 'security',
                'id' => 'inactivity',
                'title' => 'Inactivity',
                'href' => 'inactivity',
                'view' => 'SecurityController::inactivity',
            ],
            [
                'type' => 'link',
                'parent' => 'security',
                'id' => 'log',
                'title' => 'Activity history',
                'href' => 'activity-history',
                'view' => 'SecurityController::activityHistory',
            ],
            [
                'type' => 'link',
                'parent' => 'security',
                'id' => 'delete',
                'title' => sprintf('%sDelete Account%s', '<b><span class="text-danger">', '</span></b>'),
                'href' => 'delete-account',
                'view' => 'SecurityController::deleteAccount',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 30;
    }

    public function inactivity($navigation)
    {
        return $this->forward(
            'App\\Controller\\Panel\\DefaultController::notFound',
            [
                'navigation' => $navigation,
            ]
        );
    }

    public function activityHistory($navigation)
    {
        return $this->forward(
            'App\\Controller\\Panel\\DefaultController::notFound',
            [
                'navigation' => $navigation,
            ]
        );
    }

    public function deleteAccount(Request $request, $navigation)
    {
        /** @var User $user */
        $user = $this->getUser();

        $deleteAccountForm = $this->createForm(DeleteAccountType::class);
        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            // TODO: Logout causes an error! Probably because the user doesn't exist then anymore.
            return $this->redirectToRoute('logout');
        }

        return $this->render(
            'panel/delete-account.html.twig',
            [
                'navigation_links' => $navigation,
                'delete_account_form' => $deleteAccountForm->createView(),
            ]
        );
    }
}
