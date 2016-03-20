<?php
namespace AppBundle\Command;
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 20/03/16
 * Time: 17:16
 */

use Ffjv\BoBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class createAdminCommand extends ContainerAwareCommand
{
    const ADMIN_USERNAME = 'administrator';

    protected function configure(){
        $this
            ->setName('intranet:create:admin')
            ->setDescription('Create admin Account')
            ->addArgument('password', InputArgument::REQUIRED, 'super admin password')
            ->addArgument('email', InputArgument::REQUIRED, 'super admin email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = true;
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        if(!$password){
            $data = false;
            $output->writeln('<error>password not provided.</error>');
        }
        if(!$email){
            $data = false;
            $output->writeln('<error>email not provided.</error>');
        }
        if(!$data) {
            exit();
        }
        $user = new User();
        $userService = $this->getContainer()->get('user');
        if($userService->findOneByUsername(self::ADMIN_USERNAME)){
            $output->writeln('<error>Administrator already created !!!</error>');
            exit();
        }
        $user->setEmail($email);
        $user->setUsername(self::ADMIN_USERNAME);
        $user->setFirstName('Ffjv');
        $user->setLastName('Intranet');
        $user->setCgu(true);
        $user->setStatus(true);
        $user->setRoles(['ROLE_USER',
                        'ROLE_USER_CONFIRMED',
                        'ROLE_ADMIN',
                        'ROLE_SUPER_ADMIN']);
        $user->setGender('m');
        $user->setNationality('FR');
        $user->setAddress1('france');
        $user->setZipCode('01000');
        $user->setCity('admin');
        $user->setCountryAddress('admin');
        
        $encoder = $this->getContainer()->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);

        $userService->saveUserEntity($user);
        $output->writeln('<info>Administrator inserer</info>');
        
    }
}