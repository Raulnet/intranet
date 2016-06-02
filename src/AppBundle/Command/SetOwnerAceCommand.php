<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Class SetOwnerAceCommand
 * @package AppBundle\Command
 */
class SetOwnerAceCommand extends ContainerAwareCommand
{
    
    protected function configure(){
        $this->setName('acl:setOwnerAce')
            ->setDescription('Set permission Ace to author Club');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clubs = $this->getContainer()->get('clubs')->findAll();
        $acl = $this->getContainer()->get('permissions');
        foreach ($clubs as $club){
            $acl->setAcl($club, $club->getUser(), [MaskBuilder::MASK_OWNER]);
            $output->writeln('<info>Club '.$club->getTitle().' - Author '. $club->getUser() .'</info>');
        }
        $output->writeln('<info>done !</info>');
        return true;
    }
}