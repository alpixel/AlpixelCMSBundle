<?php


namespace Alpixel\Bundle\CMSBundle\Command;

use Presta\SitemapBundle\Command\DumpSitemapsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Alpixel\Bundle\CronBundle\Annotation\CronJob;
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
        $target = $this->container->getParameter('kernel.root_dir') . '/../web/';
        $input->setArgument('target', $target);
        parent::execute($input, $output);
    }
}