<?php

namespace App\AdminAddons;

use App\Entity\OAuthClient;
use App\Helper\AccountHelper;
use App\Form\CreateDevAccount;
use App\Form\CreateDevApp;
use App\Helper\TokenGenerator;
use Psr\Container\ContainerInterface;

class Developer
{
    public static function __setupNavigation(ContainerInterface $container)
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $container->get('security.token_storage')->getToken()->getUser();

        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'developer',
                'title'  => 'Developer',
                'icon'   => 'fa fa-fw fa-code',
                'display' => $currentUser->getDeveloperStatus() ? true : false,
            ],
            [
                'type'   => 'link',
                'parent' => 'developer',
                'id'     => 'developer_create_application',
                'title'  => 'Create new application',
                'href'   => 'developer-create-application',
                'view'   => 'Developer::createApplication',
            ],
            [
                'type'   => 'link',
                'parent' => 'developer',
                'id'     => 'developer_applications',
                'title'  => 'Your applications',
                'href'   => 'developer-applications',
                'view'   => 'Developer::applicationList',
            ],
            [
                'type'   => 'link',
                'parent' => 'null',
                'id'     => 'developer_show_application',
                'title'  => 'Show application',
                'href'   => 'developer-show-application',
                'view'   => 'Developer::showApplication',
            ],
            [
                'type'    => 'link',
                'parent'  => 'root',
                'id'      => 'create_developer_account',
                'title'   => 'Create developer account',
                'href'    => 'developer-register',
                'view'    => 'Developer::register',
                'display' => $currentUser->getDeveloperStatus() ? false : true,
            ],
        ];
    }

    public static function __callNumber()
    {
        return 40;
    }

    public static function createApplication(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $router = $container->get('router');
        $form = $container->get('form.factory');
        $request = $container->get('request_stack')->getCurrentRequest();
        $twig = $container->get('twig');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        if ((int)$user->getDeveloperStatus() != 1) {
            header('Location: '.$router->generate('panel', ['p' => 'developer-register']));
            exit;
        }

        $createAppForm = $form->create(CreateDevApp::class, null, ['entity_manager' => $em]);

        $createAppForm->handleRequest($request);
        if ($createAppForm->isSubmitted() && $createAppForm->isValid()) {

            AccountHelper::addApp(
                $em,
                $createAppForm->get('client_name')->getData(),
                TokenGenerator::createRandomToken(['use_openssl' => false]),
                $createAppForm->get('redirect_uri')->getData(),
                $createAppForm->get('scopes')->getData(),
                $user->getId()
            );

            header('Location: '.$router->generate('panel', ['page' => 'developer-applications']));
            exit;
        }

        return $twig->render('panel/developer-create-applications.html.twig', array(
            'create_app_form' => $createAppForm->createView(),
            'current_user'    => $user,
        ));
    }

    public static function applicationList(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $router = $container->get('router');
        $twig = $container->get('twig');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        if ((int)$user->getDeveloperStatus() != 1) {
            header('Location: '.$router->generate('panel', ['p' => 'developer-register']));
            exit;
        }

        /** @var \App\Entity\OAuthClient[] $apps */
        $apps = $em->getRepository(OAuthClient::class)->findBy(['user_id' => $user->getId()]);

        return $twig->render('panel/developer-list-applications.html.twig', array(
            'current_user_dev_apps' => $apps,
        ));
    }

    public static function showApplication(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $router = $container->get('router');
        $twig = $container->get('twig');
        $request = $container->get('request_stack')->getCurrentRequest();
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        if ((int)$user->getDeveloperStatus() != 1) {
            header('Location: '.$router->generate('panel', ['p' => 'developer-register']));
            exit;
        }

        if (!$request->query->has('app')) {
            header('Location: '.$router->generate('panel', ['p' => 'developer-applications']));
            exit;
        }
        $appId = $request->query->get('app');
        /** @var \App\Entity\OAuthClient $appData */
        $appData = $em->getRepository(OAuthClient::class)->findOneBy(array('client_identifier' => $appId));

        if (is_null($appData)) {
            return $twig->render('panel/developer-app-not-found.html.twig');
        }

        return $twig->render('panel/developer-show-app.html.twig', array(
            'app' => $appData,
        ));
    }

    public static function register(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $router = $container->get('router');
        $form = $container->get('form.factory');
        $request = $container->get('request_stack')->getCurrentRequest();
        $twig = $container->get('twig');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        if ((int)$user->getDeveloperStatus() == 1) {
            header('Location: '.$router->generate('panel', ['p' => 'developer-applications']));
            exit;
        }

        $developerForm = $form->create(CreateDevAccount::class);

        $developerForm->handleRequest($request);
        if ($developerForm->isSubmitted()) {
            $user->setDeveloperStatus(true);
            $em->flush();
            header('Location: '.$router->generate('panel', ['page' => 'developer-applications']));
            exit;
        }

        return $twig->render('panel/developer-register.html.twig', array(
            'developer_form' => $developerForm->createView(),
            'current_user'   => $user,
        ));
    }
}
