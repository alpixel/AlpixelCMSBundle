<?php

namespace Alpixel\Bundle\CMSBundle\Command;

use Alpixel\Bundle\CronBundle\Annotation\CronJob;
use Presta\SitemapBundle\Command\DumpSitemapsCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 * @CronJob("P1D", startTime="today 1:00")
 */
class SitemapCommand extends DumpSitemapsCommand implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();
        $this->setName('alpixel:cms:sitemap');
        $this->setDescription('Alias of the presta:sitemaps:dump used for cronjobs');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $definition = $this->getDefinition();
        $arguments = $definition->getArguments();
        $arguments['target']->setDefault($this->container->getParameter('kernel.root_dir').'/../web/');
        $definition->setArguments($arguments);

        if (count($input->getArguments()) === 0) {
            $input = new ArgvInput(null, $definition);
            $input->setArgument('target', $this->container->getParameter('kernel.root_dir').'/../web/');
        }

        return parent::execute($input, $output);
    }
}
