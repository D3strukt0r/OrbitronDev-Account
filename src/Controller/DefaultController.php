<?php

namespace App\Controller;

use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Entity\UserProfiles;
use App\Entity\UserSubscription;
use App\Form\ConfirmEmailType;
use App\Form\ForgotType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Service\AccountHelper;
use App\Service\AdminControlPanel;
use App\Service\TokenGenerator;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel_default');
        }
        throw $this->createAccessDeniedException();
        //////////// END TEST IF USER IS LOGGED IN ////////////
    }

    /**
     * @Route("/login", name="login")
     *
     * @param Request             $request   The request
     * @param AuthenticationUtils $authUtils Authentication utilities
     *
     * @return RedirectResponse|Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel_default');
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        $redirectUrl = $request->query->get('_target_path', $this->generateUrl('panel_default'));

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render(
            'login.html.twig',
            [
                'redirect_url' => $redirectUrl,
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
     * @Route("/logout", name="logout")
     *
     * @throws Exception
     */
    public function logout(): void
    {
        throw new Exception('This should never be reached!');
    }

    /**
     * @Route("/register", name="register")
     *
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher
     * @param Request                  $request         The request
     * @param Swift_Mailer             $mailer          The mailer
     *
     * @throws Exception
     *
     * @return RedirectResponse|Response
     */
    public function register(EventDispatcherInterface $eventDispatcher, Request $request, Swift_Mailer $mailer)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel_default');
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        $registerForm = $this->createForm(RegisterType::class);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $formData = $registerForm->getData();

            // Add user to database
            $user = (new User())
                ->setUsername($formData['username'])
                ->setPassword($formData['password'])
                ->setEmail($formData['email'])
                ->setEmailVerified(false)
                ->setCreatedOn(new \DateTime())
                ->setLastOnlineAt(new \DateTime())
                ->setCreatedIp($request->getClientIp())
                ->setLastIp($request->getClientIp())
                ->setDeveloperStatus(false)
            ;

            $userProfile = (new UserProfiles())->setUser($user);
            $user->setProfile($userProfile);

            /** @var SubscriptionType $defaultSubscription */
            $defaultSubscription = $this->getDoctrine()->getRepository(SubscriptionType::class)->find(
                AccountHelper::$settings['subscription']['default']
            )
            ;

            $userSubscription = (new UserSubscription())
                ->setUser($user)
                ->setSubscription($defaultSubscription)
                ->setActivatedAt(new \DateTime())
                ->setExpiresAt(new \DateTime())
            ;
            $user->setSubscription($userSubscription);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Send verification email
            $tokenGenerator = new TokenGenerator($entityManager);
            $token = $tokenGenerator->generateToken('confirm_email', (new \DateTime())->modify('+1 day'));

            $message = (new Swift_Message())
                ->setSubject('[Account] Email activation')
                ->setFrom(['no-reply-account@orbitrondev.org' => 'OrbitronDev'])
                ->setTo([$formData['email']])
                ->setBody(
                    $this->renderView(
                        'mail/confirm-email.html.twig',
                        [
                            'email' => $formData['email'],
                            'token' => $token,
                        ]
                    ),
                    'text/html'
                )
            ;
            $mailSent = $mailer->send($message);

            if ($mailSent) {
                $this->addFlash('successful', 'register.confirmation_mail.sent');
            } else {
                $this->addFlash('failed', 'register.confirmation_mail.not_sent');
            }
            $url = $request->query->has('page') ? urldecode($request->query->get('page')) : $this->generateUrl(
                'panel',
                ['page' => 'home']
            );

            //Handle getting or creating the user entity likely with a posted form
            // The third parameter "main" can change according to the name of your firewall in security.yml
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            // Fire the login event manually
            $eventDispatcher->dispatch(new InteractiveLoginEvent($request, $token));

            return $this->redirect($url);
        }

        return $this->render(
            'register.html.twig',
            [
                'register_form' => $registerForm->createView(),
            ]
        );
    }

    /**
     * @Route("/p/{page}", name="panel")
     *
     * @param KernelInterface $kernel  The kernel
     * @param Request         $request The request
     * @param string          $page    The page
     *
     * @return Response
     */
    public function panel(KernelInterface $kernel, Request $request, string $page)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        AdminControlPanel::loadLibs($kernel->getProjectDir(), $this->container);

        $navigationLinks = AdminControlPanel::getTree();

        $view = 'DefaultController::notFound';

        $list = AdminControlPanel::getFlatTree();

        $key = null;
        while ($item = current($list)) {
            if (isset($item['href']) && $item['href'] === $page) {
                $key = key($list);
            }
            next($list);
        }

        if (null !== $key) {
            if (is_callable('\\App\\Controller\\Panel\\'.$list[$key]['view'])) {
                $view = $list[$key]['view'];
            }
        }

        return $this->forward(
            'App\\Controller\\Panel\\'.$view,
            [
                'navigation' => $navigationLinks,
                'request' => $request,
            ]
        );
    }

    /**
     * @Route("/api/{function}", name="api")
     *
     * @param Request $request  The request
     * @param string  $function The function
     *
     * @return Response
     */
    public function api(Request $request, string $function)
    {
        // Get function name
        $functionProcess = explode('_', $function);
        foreach ($functionProcess as $key => $item) {
            $functionProcess[$key] = ucfirst($item);
        }
        $function = implode('', $functionProcess);
        $function = lcfirst($function);

        return $this->forward(
            'App\\Controller\\ApiController::'.$function,
            [
                'request' => $request,
            ]
        );
    }

    /**
     * @Route("/forgot", name="forgot")
     *
     * @param Request $request The request
     *
     * @throws Exception
     *
     * @return RedirectResponse|Response
     */
    public function forgot(Request $request)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel_default');
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        $forgotForm = $this->createForm(ForgotType::class);
        $entityManager = $this->getDoctrine()->getManager();

        if (null !== ($token = $request->query->get('token'))) {
            // Token was received
            $token = new TokenGenerator($entityManager, $token);
            $job = $token->getJob();
            if (null === $job || false === $job) {
                if (is_string($job) && 'reset_password' !== $job) {
                    // Wrong token
                    $forgotForm->addError(
                        new FormError('This token is not for resetting a password')
                    ); // TODO: Missing translation
                } else {
                    $forgotForm->addError(new FormError('Token not found')); // TODO: Missing translation
                }
            } else {
                $resetForm = $this->createForm(ResetPasswordType::class);
                $resetForm->handleRequest($request);
                if ($resetForm->isSubmitted() && $resetForm->isValid()) {
                    // Reset Email
                    $formData = $resetForm->getData();

                    $userId = $token->getInformation()['user_id'];
                    /** @var User|null $user */
                    $user = $entityManager->getRepository(User::class)->find($userId);
                    $user->setPassword($formData['password']);
                    $entityManager->flush();
                    $token->remove();

                    $this->addFlash('success', 'Successfully changed your password'); // TODO: Missing translation

                    return $this->render(
                        'forgot-password-form.html.twig',
                        [
                            'reset_form' => $resetForm->createView(),
                            'redirect' => $this->generateUrl('login'),
                        ]
                    );
                }

                return $this->render(
                    'forgot-password-form.html.twig',
                    [
                        'reset_form' => $resetForm->createView(),
                    ]
                );
            }
        }

        // Ask for email
        $forgotForm->handleRequest($request);
        if ($forgotForm->isSubmitted() && $forgotForm->isValid()) {
            $formData = $forgotForm->getData();

            /** @var User|null $user */
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $formData['email']]);
            if (null !== $user) {
                $tokenGenerator = new TokenGenerator($entityManager);
                $token = $tokenGenerator->generateToken(
                    'reset_password',
                    (new \DateTime())->modify('+1 day'),
                    ['user_id' => $user->getId()]
                );

                $message = (new Swift_Message())
                    ->setSubject('[Account] Reset password')
                    ->setFrom(['no-reply-account@orbitrondev.org' => 'OrbitronDev'])
                    ->setTo([$user->getEmail()])
                    ->setBody(
                        $this->renderView(
                            'mail/reset-password.html.twig',
                            [
                                'email' => $user->getEmail(),
                                'token' => $token,
                            ]
                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                // Email sent
                $this->addFlash('success', 'Email sent'); // TODO: Missing translation
            } else {
                // Email does not exist
                $forgotForm->get('email')->addError(
                    new FormError('A user with this email does not exist.')
                )
                ; // TODO: Missing translation
            }
        }

        // Enter email to send mail
        return $this->render(
            'forgot-password.html.twig',
            [
                'forgot_form' => $forgotForm->createView(),
            ]
        );
    }

    /**
     * @Route("/confirm-email", name="confirm")
     *
     * @param Request $request The request
     *
     * @return Response
     */
    public function confirm(Request $request)
    {
        /** @var User|null $user */
        $user = $this->getUser();

        $sendEmailForm = $this->createForm(ConfirmEmailType::class);
        $entityManager = $this->getDoctrine()->getManager();

        if (null !== ($token = $request->query->get('token'))) {
            // Token was received
            $token = new TokenGenerator($entityManager, $token);
            $job = $token->getJob();
            if (null === $job || false === $job) {
                $errorMessage = 'Token not found';
                if (is_string($job) && 'confirm_email' !== $job) {
                    $errorMessage = 'This token is not for email activation'; // TODO: Missing translation
                }
                $this->addFlash('failure', $errorMessage);
            } else {
                $user->setEmailVerified(true);
                $entityManager->flush();
                $token->remove();
                $this->addFlash('success', 'Successfully verified your email'); // TODO: Missing translation

                return $this->render('confirm-email.html.twig');
            }
        }

        // Show send window
        $sendEmailForm->handleRequest($request);
        if ($sendEmailForm->isSubmitted()) {
            $tokenGenerator = new TokenGenerator($entityManager);
            $token = $tokenGenerator->generateToken('confirm_email', (new \DateTime())->modify('+1 day'));

            $message = (new Swift_Message())
                ->setSubject('[Account] Email activation')
                ->setFrom(['no-reply-account@orbitrondev.org' => 'OrbitronDev'])
                ->setTo([$user->getEmail()])
                ->setBody(
                    $this->renderView(
                        'mail/confirm-email.html.twig',
                        [
                            'email' => $user->getEmail(),
                            'token' => $token,
                        ]
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            $this->addFlash('success', 'Email sent'); // TODO: Missing translation
        }

        return $this->render(
            'confirm-email.html.twig',
            [
                'send_email_form' => $sendEmailForm->createView(),
            ]
        );
    }
}
