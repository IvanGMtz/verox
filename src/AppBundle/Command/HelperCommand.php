<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelperCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:helper:run')
            ->setDescription('Show complete people')
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info>Calculates binary profit.

<info>php %command.full_name%</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      ini_set('memory_limit', '2048M');
      $em = $this->getContainer()->get('doctrine')->getManager();
      $now = new \DateTime();
      
    }
}