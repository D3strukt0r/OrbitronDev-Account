<?php

namespace App\Controller\Panel;

use App\Form\DeleteAccountType;
use App\Helper\AccountHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public static function __setupNavigation()
    {
        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'security',
                'title'  => 'Security',
                'icon'   => 'fa fa-fw fa-lock',
            ],
            [
                'type'   => 'link',
                'parent' => 'security',
                'id'     => 'inactivity',
                'title'  => 'Inactivity',
                'href'   => 'inactivity',
                'view'   => 'SecurityController::inactivity',
            ],
            [
                'type'   => 'link',
                'parent' => 'security',
                'id'     => 'log',
                'title'  => 'Login log',
                'href'   => 'login-log',
                'view'   => 'SecurityController::loginLog',
            ],
            [
                'type'   => 'link',
                'parent' => 'security',
                'id'     => 'delete',
                'title'  => sprintf('%sDelete Account%s', '<b><span class="text-danger">', '</span></b>'),
                'href'   => 'delete-account',
                'view'   => 'SecurityController::deleteAccount',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 30;
    }

    public function inactivity()
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', ['status_code' => '', 'status_text' => '']);
    }

    public function loginLog()
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', ['status_code' => '', 'status_text' => '']);
    }

    public function deleteAccount(Request $request, $navigation)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $deleteAccountForm = $this->createForm(DeleteAccountType::class);

        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            AccountHelper::removeUser($em, $user);
            return $this->redirectToRoute('logout');
        }

        return $this->render('panel/delete-account.html.twig', [
            'navigation_links'    => $navigation,
            'delete_account_form' => $deleteAccountForm->createView(),
        ]);
    }
}
