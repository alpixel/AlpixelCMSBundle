<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class AdminCommandController extends Controller
{
    public function commandAction()
    {
        $request     = $this->getRequest();
        $query       = $request->query;
        $command     = $query->get('name');
        $environment = $this->get('kernel')->getEnvironment();

        if ($query->has('flush_cache') && $query->getInt('flush_cache') === 1) {
            $responseFlush = $this->commandFlushCache();
            if ($environment === 'dev') {
                if ($responseFlush->isSuccessful()) {
                    $this->addFlash('success', 'Les caches ont bien été vidés');
                } else {
                    $this->addFlash('error', 'Une erreur est survenue lors de la supression des caches !');
                }
            }
        }

        $responseCommand = $this->executeCommand(['command' => $command]);
        if ($responseCommand->isSuccessful()) {
            $this->addFlash(
                'success',
                sprintf('La commande "%s" a bien été exécuté.', $command)
            );
        } else {
            $this->addFlash(
                'error',
                sprintf('Une erreur est survenue lors de l\'exécution de la commande "%s"!', $command)
            );
        }

        return $this->redirectTo($this->admin->generateUrl('list'));
    }

    public function listAction()
    {
        $request = $this->getRequest();

        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if ($preResponse !== null) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        return $this->render('AlpixelCMSBundle:admin/page:base_list.html.twig', array(
            'object'     => null,
            'action'     => 'list',
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ), null, $request);
    }

    protected function commandFlushCache()
    {
        $environment = $this->get('kernel')->getEnvironment();
        $inputs      = [
            'command' => 'cache:clear',
            '--env'   => $environment,
        ];

        return $this->executeCommand($inputs);
    }

    protected function executeCommand(array $inputs)
    {
        if (empty($inputs) || !isset($inputs['command'])) {
            throw new \Exception('The array is empty or "command" index is missing');
        }

        $kernel      = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input  = new ArrayInput($inputs);
        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true
        );

        $application->run($input, $output);
        $content = $output->fetch();

        return new Response($content);
    }
}
