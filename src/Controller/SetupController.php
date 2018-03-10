<?php

namespace App\Controller;

use App\Service\AccountHelper;
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
    public function oneTimeSetup(Request $request, KernelInterface $kernel, PdoSessionHandler $sessionHandlerService, AccountHelper $helper)
    {
        if ($request->query->get('key') == $this->getParameter('kernel.secret')) {

            // Create database entities
            $application = new Application($kernel);
            $application->setAutoExit(false);

            if ($request->query->get('action') == 'drop-schema') {
                $input = new ArrayInput([
                    'command' => 'doctrine:schema:drop',
                ]);
                try {
                    $application->run($input, new NullOutput());
                } catch (\Exception $exception) {
                    return new Response('Database schema NOT dropped. <br/>'.$exception->getMessage());
                }
                return new Response('Database schema dropped');
            }
            if ($request->query->get('action') == 'create-schema') {
                $input = new ArrayInput([
                    'command' => 'doctrine:schema:create',
                ]);
                try {
                    $application->run($input, new NullOutput());
                } catch (\Exception $exception) {
                    return new Response('Database schema NOT created. <br/>'.$exception->getMessage());
                }
                return new Response('Database schema created');
            }
            if ($request->query->get('action') == 'update-schema') {
                $input = new ArrayInput([
                    'command' => 'doctrine:schema:update',
                ]);
                try {
                    $application->run($input, new NullOutput());
                } catch (\Exception $exception) {
                    return new Response('Database schema NOT updated. <br/>'.$exception->getMessage());
                }
                return new Response('Database schema updated');
            }
            if ($request->query->get('action') == 'add-default-entries') {
                // Add default values
                $text = '';
                $helper->addDefaultSubscriptionTypes();
                $text .= 'Subscription types added<br />';
                $helper->addDefaultScopes();
                $text .= 'Scopes added<br />';

                return new Response($text);
            }
            if ($request->query->get('action') == 'add-default-scopes') {
                $helper->addDefaultScopes();

                return new Response('Scopes added');
            }
            if ($request->query->get('action') == 'add-session-table') {
                try {
                    $sessionHandlerService->createTable();
                    return new Response('Session database created');
                } catch (\PDOException $e) {
                    // the table could not be created for some reason
                    return new Response('Session database not created');
                }
            }
            throw $this->createNotFoundException('Action not known');
        }
        throw $this->createNotFoundException('Wrong key');
    }
}
