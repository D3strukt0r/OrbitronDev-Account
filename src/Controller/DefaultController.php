<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConfirmEmailType;
use App\Form\ForgotType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Helper\AdminControlPanel;
use App\Helper\AccountApi;
use App\Helper\AccountHelper;
use App\Helper\TokenGenerator;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Translation\TranslatorInterface;

class DefaultController extends Controller
{
    public function index()
    {
        // If user is logged in, redirect to panel
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel', ['page' => 'home']);
        } else {
            return $this->redirectToRoute('login');
        }
    }

    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        // If user is logged in, redirect to panel
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel', ['page' => 'home']);
        }

        $redirectUrl = $request->query->has('_target_path') ? $request->query->get('_target_path') : $this->generateUrl('panel', ['page' => 'home']);

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'redirect_url'  => $redirectUrl,
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    public function register(Request $request, TranslatorInterface $translator, Swift_Mailer $mailer)
    {
        // If user is logged in, redirect to panel
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel', ['page' => 'home']);
        }

        $registerForm = $this->createForm(RegisterType::class);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $resultCodes = [
                'user_exists'            => $translator->trans('register.form.username.error.user_exists'),
                'blocked_name'           => $translator->trans('register.form.username.error.blocked_name'),
                'passwords_do_not_match' => $translator->trans('register.form.password_verify.error.passwords_do_not_match'),
            ];

            $registerData = $registerForm->getData();
            $registerResult = AccountHelper::addUser(
                $this->getDoctrine()->getManager(),
                $request,
                $registerData['username'],
                $registerData['password'],
                $registerData['password_verify'],
                $registerData['email']
            );

            if ($registerResult instanceof UserInterface) {
                $user = $registerResult;
                $tokenGenerator = new TokenGenerator($this->getDoctrine()->getManager());
                $token = $tokenGenerator->generateToken('confirm_email', (new \DateTime())->modify('+1 day'));

                $message = (new Swift_Message())
                    ->setSubject('[Account] Email activation')
                    ->setFrom(['no-reply-account@orbitrondev.org' => 'OrbitronDev'])
                    ->setTo([$registerData['email']])
                    ->setBody($this->renderView('mail/confirm-email.html.twig', [
                        'email'    => $registerData['email'],
                        'token'    => $token,
                    ]), 'text/html');
                $mailSent = $mailer->send($message);

                if ($mailSent) {
                    $this->addFlash('successful', 'register.confirmation_mail.sent');
                } else {
                    $this->addFlash('failed', 'register.confirmation_mail.not_sent');
                }
                $url = $request->query->has('page') ? urldecode($request->query->get('page')) : $this->generateUrl('panel', ['page' => 'home']);

                //Handle getting or creating the user entity likely with a posted form
                // The third parameter "main" can change according to the name of your firewall in security.yml
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);

                // Fire the login event manually
                $event = new InteractiveLoginEvent($request, $token);
                $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

                return $this->redirect($url);
            } elseif ($registerResult === false) {
                $this->addFlash('error', $translator->trans('register.unknown_error'));
            } else {
                $errorMessage = explode(':', $registerResult);
                $registerErrorMessage = $resultCodes[$errorMessage[1]];
                if ($errorMessage[0] == 'form') {
                    $registerForm->addError(new FormError($registerErrorMessage));
                } else {
                    $registerForm->get($errorMessage[0])->addError(new FormError($registerErrorMessage));
                }
            }
        }

        return $this->render('register.html.twig', [
            'register_form' => $registerForm->createView(),
        ]);
    }

    public function panel($page)
    {
        // If user is logged in, redirect to panel
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }

        $params = [];
        $params['user_id'] = $user->getId();
        $params['current_user'] = $user;
        $params['view_navigation'] = '';

        AdminControlPanel::loadLibs($this->get('kernel')->getProjectDir(), $this->container);

        $params['view_navigation'] = AdminControlPanel::getTree();

        $view = 'AdminDefault::notFound';

        $list = AdminControlPanel::getFlatTree();

        $key = null;
        while ($item = current($list)) {
            if (isset($item['href']) && $item['href'] === $page) {
                $key = key($list);
            }
            next($list);
        }

        if (!is_null($key)) {
            if (is_callable('\\App\\AdminAddons\\'.$list[$key]['view'])) {
                $view = $list[$key]['view'];
            }
        }
        $response = call_user_func('\\App\\AdminAddons\\'.$view, $this->container);
        if (is_string($response)) {
            $params['view_body'] = $response;
        }

        return $this->render('panel.html.twig', $params);
    }

    public function api($function)
    {
        // Get function name
        $functionProcess = explode('_', $function);
        foreach ($functionProcess as $key => $item) {
            $functionProcess[$key] = ucfirst($item);
        }
        $function = implode('', $functionProcess);
        $function = lcfirst($function);

        $result = AccountApi::$function($this->container);
        if (is_array($result)) {
            return $this->json($result);
        } else {
            return $result;
        }
    }

    public function forgot(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // If user is logged in, redirect to panel
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('panel', ['page' => 'home']);
        }

        $forgotForm = $this->createForm(ForgotType::class);

        if (!is_null($token = $request->query->get('token'))) {
            $token = new TokenGenerator($em, $token);
            $job = $token->getJob();
            if (is_null($job) || $job === false) {
                if (is_string($job) && $job != 'reset_password') {
                    // Wrong token
                    $forgotForm->addError(new FormError('This token is not for resetting a password'));
                } else {
                    $forgotForm->addError(new FormError('Token not found'));
                }

                return $this->render('forgot-password.html.twig', [
                    'forgot_form' => $forgotForm->createView(),
                ]);
            } else {
                $resetForm = $this->createForm(ResetPasswordType::class);
                $resetForm->handleRequest($request);
                if ($resetForm->isSubmitted()) {
                    // Reset Email
                    $password = trim($resetForm->get('password')->getData());
                    $passwordVerify = trim($resetForm->get('password_verify')->getData());

                    if (strlen($password) == 0) {
                        $resetForm->get('password')->addError(new FormError('You have to insert a password'));

                        return $this->render('forgot-password-form.html.twig', [
                            'reset_form' => $resetForm->createView(),
                        ]);
                    } elseif (strlen($password) < AccountHelper::$settings['password']['min_length']) {
                        $resetForm->get('password')->addError(new FormError('Your password is too short (min. 7 characters)'));

                        return $this->render('forgot-password-form.html.twig', [
                            'reset_form' => $resetForm->createView(),
                        ]);
                    } elseif ($password != $passwordVerify) {
                        $resetForm->get('password_verify')->addError(new FormError('Your passwords don\'t match'));

                        return $this->render('forgot-password-form.html.twig', [
                            'reset_form' => $resetForm->createView(),
                        ]);
                    }

                    $userId = $token->getInformation()['user_id'];
                    /** @var \App\Entity\User $user */
                    $user = $em->find(User::class, $userId);
                    $user->setPassword($password);
                    $em->flush();
                    $token->remove();

                    $this->addFlash('success', 'Successfully changed your password');

                    return $this->render('forgot-password-form.html.twig', [
                        'reset_form' => $resetForm->createView(),
                        'redirect'   => $this->generateUrl('login'),
                    ]);
                }

                return $this->render('forgot-password-form.html.twig', [
                    'reset_form' => $resetForm->createView(),
                ]);

            }
        } else {
            $forgotForm->handleRequest($request);
            if ($forgotForm->isSubmitted() && $forgotForm->isValid()) {
                if (AccountHelper::emailExists($em, $forgotForm->get('email')->getData())) {
                    /** @var \App\Entity\User $user */
                    $user = $em->getRepository(User::class)->findOneBy(['email' => $forgotForm->get('email')->getData()]);
                    $tokenGenerator = new TokenGenerator($em);
                    $token = $tokenGenerator->generateToken('reset_password', (new \DateTime())->modify('+1 day'), ['user_id' => $user->getId()]);

                    $message = (new Swift_Message())
                        ->setSubject('[Account] Reset password')
                        ->setFrom(['no-reply-account@orbitrondev.org' => 'OrbitronDev'])
                        ->setTo([$user->getEmail()])
                        ->setBody($this->renderView('mail/reset-password.html.twig', [
                            'email' => $user->getEmail(),
                            'token' => $token,
                        ]), 'text/html');
                    $this->get('mailer')->send($message);

                    // Email sent
                    $this->addFlash('success', 'Email sent');

                    return $this->render('forgot-password.html.twig', [
                        'forgot_form' => $forgotForm->createView(),
                    ]);
                } else {
                    // Email does not exist
                    $forgotForm->get('email')->addError(new FormError('A user with this email does not exist.'));
                }
            }

            // Enter email to send mail
            return $this->render('forgot-password.html.twig', [
                'forgot_form' => $forgotForm->createView(),
            ]);
        }
    }

    public function confirm(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // If user is logged in, redirect to panel
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $sendEmailForm = $this->createForm(ConfirmEmailType::class);

        if (!is_null($token = $request->query->get('token'))) {
            $token = new TokenGenerator($em, $token);
            $job = $token->getJob();
            if (is_null($job) || $job === false) {
                $errorMessage = 'Token not found';
                if (is_string($job) && $job != 'confirm_email') {
                    $errorMessage = 'This token is not for email activation';
                }
                $this->addFlash('failure', $errorMessage);

                return $this->render('confirm-email.html.twig', [
                    'send_email_form' => $sendEmailForm->createView(),
                ]);
            } else {
                $user->setEmailVerified(true);
                $em->flush();
                $token->remove();
                $this->addFlash('success', 'Successfully verified your email');

                return $this->render('confirm-email.html.twig');
            }
        } else {
            $sendEmailForm->handleRequest($request);
            if ($sendEmailForm->isSubmitted()) {
                $tokenGenerator = new TokenGenerator($em);
                $token = $tokenGenerator->generateToken('confirm_email', (new \DateTime())->modify('+1 day'));

                $message = (new Swift_Message())
                    ->setSubject('[Account] Email activation')
                    ->setFrom(['no-reply-account@orbitrondev.org' => 'OrbitronDev'])
                    ->setTo([$user->getEmail()])
                    ->setBody($this->renderView('mail/confirm-email.html.twig', [
                        'email'    => $user->getEmail(),
                        'token'    => $token,
                    ]), 'text/html');
                $this->get('mailer')->send($message);

                $this->addFlash('success', 'Email sent');
            }

            return $this->render('confirm-email.html.twig', [
                'send_email_form' => $sendEmailForm->createView(),
            ]);
        }
    }
}
