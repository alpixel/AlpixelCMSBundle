<?php

namespace Alpixel\Bundle\CMSBundle\Command;

use Happyr\TranslationBundle\Http\RequestManager;
use Happyr\TranslationBundle\Service\Loco;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class TranslationDownloadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('alpixel:cms:translations:download');
        $this->setDescription('Download translations from Loco service');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $requestManager = new RequestManager(new Client(), new GuzzleMessageFactory());
        $fileSystem = $container->get('happyr.translation.filesystem');

        $languages = $container->getParameter('enabled_locales');
        $domains = ['messages', 'validators', 'routes'];

        $query = [
            'key'      => $container->getParameter('loco_api_key'),
            'fallback' => $container->getParameter('default_locale'),
            'index'    => 'text',
            'format'   => 'symfony',
        ];

        $uri = [];
        foreach ($languages as $language) {
            foreach ($domains as $domain) {
                $realQuery = $query;
                if ($domain === 'routes') {
                    unset($realQuery['index']);
                }
                $url = Loco::BASE_URL.'export/locale/'.$language.'.xlf?'.http_build_query(array_merge($realQuery, [
                        'filter' => $domain,
                    ]));
                $uri[$url] = $domain.'.'.$language.'.xlf';
            }
        }

        $requestManager->downloadFiles($fileSystem, $uri);
        $output->writeln('Translations downloaded');
    }
}
