<?php

namespace App\Controller\Panel;

use App\Entity\OAuthClient;
use App\Entity\OAuthScope;
use App\Service\AccountHelper;
use App\Form\CreateDevAccount;
use App\Form\CreateDevApp;
use App\Service\TokenGenerator;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DeveloperController extends Controller
{
    public static function __setupNavigation(ContainerInterface $container)
    {
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        return [
            [
                'type'    => 'group',
                'parent'  => 'root',
                'id'      => 'developer',
                'title'   => 'Developer',
                'icon'    => 'hs-admin-plug',
                'display' => $user->getDeveloperStatus() ? true : false,
            ],
            [
                'type'   => 'link',
                'parent' => 'developer',
                'id'     => 'developer_create_application',
                'title'  => 'Create new application',
                'href'   => 'developer-create-application',
                'view'   => 'DeveloperController::createApplication',
            ],
            [
                'type'   => 'link',
                'parent' => 'developer',
                'id'     => 'developer_applications',
                'title'  => 'Your applications',
                'href'   => 'developer-applications',
                'view'   => 'DeveloperController::applicationList',
            ],
            [
                'type'   => 'link',
                'parent' => 'null',
                'id'     => 'developer_show_application',
                'title'  => 'Show application',
                'href'   => 'developer-show-application',
                'view'   => 'DeveloperController::showApplication',
            ],
            [
                'type'    => 'link',
                'parent'  => 'root',
                'id'      => 'create_developer_account',
                'title'   => 'Create developer account',
                'href'    => 'developer-register',
                'view'    => 'DeveloperController::register',
                'display' => $user->getDeveloperStatus() ? false : true,
            ],
        ];
    }

    public static function __callNumber()
    {
        return 40;
    }

    public function createApplication(ObjectManager $em, Request $request, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user->getDeveloperStatus()) {
            return $this->redirectToRoute('panel', ['page' => 'developer-register']);
        }

        /** @var \App\Entity\OAuthScope[] $scopes */
        $scopes = $em->getRepository(OAuthScope::class)->findAll();

        $scope_choices = [];
        foreach ($scopes as $scope) {
            $scope_choices[$scope->getName()] = $scope->getScope();
        }
        $createAppForm = $this->createForm(CreateDevApp::class, null, ['scope_choices' => $scope_choices]);
        $createAppForm->handleRequest($request);
        if ($createAppForm->isSubmitted() && $createAppForm->isValid()) {
            $formData = $createAppForm->getData();

            $addClient = new OAuthClient();
            $addClient
                ->setClientIdentifier($formData['client_name'])
                ->setClientSecret(TokenGenerator::createRandomToken(['use_openssl' => false]))
                ->setRedirectUri($formData['redirect_uri'])
                ->setScopes($formData['scopes'])
                ->setUsers($user->getId());

            $em->persist($addClient);
            $em->flush();

            return $this->redirectToRoute('panel', ['page' => 'developer-applications']);
        }

        return $this->render('panel/developer-create-applications.html.twig', [
            'navigation_links' => $navigation,
            'create_app_form'  => $createAppForm->createView(),
        ]);
    }

    public function applicationList(ObjectManager $em, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user->getDeveloperStatus()) {
            return $this->redirectToRoute('panel', ['page' => 'developer-register']);
        }

        /** @var \App\Entity\OAuthClient[] $apps */
        $apps = $em->getRepository(OAuthClient::class)->findBy(['user_id' => $user->getId()]);

        return $this->render('panel/developer-list-applications.html.twig', [
            'navigation_links' => $navigation,
            'app_list'         => $apps,
        ]);
    }

    public function showApplication(ObjectManager $em, Request $request, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user->getDeveloperStatus()) {
            return $this->redirectToRoute('panel', ['page' => 'developer-register']);
        }

        if (!$request->query->has('app')) {
            return $this->redirectToRoute('panel', ['page' => 'developer-applications']);
        }
        $appId = $request->query->get('app');
        /** @var \App\Entity\OAuthClient $appData */
        $appData = $em->getRepository(OAuthClient::class)->findOneBy(['client_identifier' => $appId]);

        if (is_null($appData)) {
            return $this->render('panel/developer-app-not-found.html.twig', [
                'navigation_links' => $navigation,
            ]);
        }

        return $this->render('panel/developer-show-app.html.twig', [
            'navigation_links' => $navigation,
            'app_data'         => $appData,
        ]);
    }

    public function register(ObjectManager $em, Request $request, $navigation)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user->getDeveloperStatus()) {
            return $this->redirectToRoute('panel', ['page' => 'developer-applications']);
        }

        $developerForm = $this->createForm(CreateDevAccount::class);
        $developerForm->handleRequest($request);
        if ($developerForm->isSubmitted()) {
            $user->setDeveloperStatus(true);
            $em->flush();
            return $this->redirectToRoute('panel', ['page' => 'developer-applications']);
        }

        return $this->render('panel/developer-register.html.twig', [
            'navigation_links' => $navigation,
            'developer_form'   => $developerForm->createView(),
        ]);
    }
}
