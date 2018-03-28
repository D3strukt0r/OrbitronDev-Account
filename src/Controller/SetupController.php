<?php

namespace App\Controller;

use App\Entity\OAuthScope;
use App\Entity\SubscriptionType;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpKernel\KernelInterface;

class SetupController extends Controller
{
    public function setup(Request $request, KernelInterface $kernel, PdoSessionHandler $sessionHandlerService)
    {
        if ($request->query->get('key') == $this->getParameter('kernel.secret')) {
            $em = $this->getDoctrine()->getManager();
            $output = '';
            try {
                $application = new Application($kernel);
                $application->setAutoExit(false);
                $application->run(new ArrayInput(['command' => 'doctrine:schema:create', '--force']), new NullOutput());
                $output .= '[ <span style="color:green">OK</span> ] Database updated<br />';
            } catch (\Exception $exception) {
                $output .= '[<span style="color:red">FAIL</span>] Database updated ('.$exception->getMessage().')<br />';
            }
            try {
                $sessionHandlerService->createTable();
                $output .= '[ <span style="color:green">OK</span> ] Session table added<br />';
            } catch (\Exception $exception) {
                $output .= '[<span style="color:red">FAIL</span>] Session table added ('.$exception->getMessage().')<br />';
            }
            try {
                $subscriptions = [];
                $subscriptions[] = (new SubscriptionType())
                    ->setTitle('Basic')
                    ->setPrice('0')
                    ->setPermissions([]);
                $subscriptions[] = (new SubscriptionType())
                    ->setTitle('Premium')
                    ->setPrice('10')
                    ->setPermissions(['web_service', 'support']);
                $subscriptions[] = (new SubscriptionType())
                    ->setTitle('Enterprise')
                    ->setPrice('30')
                    ->setPermissions(['web_service', 'web_service_multiple', 'support']);

                foreach ($subscriptions as $item) {
                    $em->persist($item);
                }
                $em->flush();
                $output .= '[ <span style="color:green">OK</span> ] Default subscription types added<br />';
            } catch (\Exception $exception) {
                $output .= '[<span style="color:red">FAIL</span>] Default subscription types added ('.$exception->getMessage().')<br />';
            }
            try {
                $scope = [];
                $scope[] = (new OAuthScope())
                    ->setScope('user:id')
                    ->setName('User ID')
                    ->setDefault(true);
                $scope[] = (new OAuthScope())
                    ->setScope('user:username')
                    ->setName('Username')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:email')
                    ->setName('Email address')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:name')
                    ->setName('Profile -> First name')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:surname')
                    ->setName('Profile -> Surname')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:birthday')
                    ->setName('Profile -> Birthday')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:activeaddresses')
                    ->setName('Profile -> The address which is selected as default')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:addresses')
                    ->setName('Profile -> A list of all saved addresses')
                    ->setDefault(false);
                $scope[] = (new OAuthScope())
                    ->setScope('user:subscription')
                    ->setName('Current subscription status')
                    ->setDefault(false);

                foreach ($scope as $item) {
                    $em->persist($item);
                }
                $em->flush();
                $output .= '[ <span style="color:green">OK</span> ] Default OAuth2 scopes added<br />';
            } catch (\Exception $exception) {
                $output .= '[<span style="color:red">FAIL</span>] Default OAuth2 scopes added ('.$exception->getMessage().')<br />';
            }
            return new Response('<body style="background-color: black;color: white;"><pre>'.$output.'</pre></body>');
        }
        throw $this->createNotFoundException('Wrong key');
    }
}
