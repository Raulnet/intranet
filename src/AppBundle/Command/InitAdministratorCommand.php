<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 17/06/16
 * Time: 22:21
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use FfjvBoBundle\Entity\User;


class InitAdministratorCommand extends ContainerAwareCommand
{
    const USERNAME = 'Administrator';
    const FIRSTNAME = 'Administrator';
    const LASTNAME = 'FFJV';


    protected function configure(){
        $this->setName('init:administrator')
            ->setDescription('Create Adminitrator')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'email'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'get password'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($this->getContainer()->get('user')->findOneByUsername(self::USERNAME)){

            $output->writeln('<error>Administrator AllReady Registered</error>');
            $output->writeln('<info>done !</info>');
            return true;
        }
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $user = new User();
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');
        
        $user->setUsername(self::USERNAME);
        $user->setFirstName(self::FIRSTNAME);
        $user->setLastName(self::LASTNAME);
        $user->setGender('M');
        $user->setZipCode('00101');
        $user->setIdZipCode('01');
        $user->setCity('Paris');
        $user->setEmail($email);
        $user->setBirthday(new \DateTime('now'));
        $user->setCgu(true);
        $user->setAddress1('Intranet - FFJV');
        $user->setNationality('FR');
        $user->setCountryAddress('FR');
        $user->setRoles([
            'ROLE_USER',
            'ROLE_USER_CONFIRMED',
            'ROLE_ADMIN',
            'ROLE_SUPER_ADMIN'
        ]);
        $user->setStatus(true);

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $user->setActivationCode(base_convert(md5(uniqid(mt_rand(), true)), 16, 36));
        //persiste and flush
        $em->persist($user);
        $em->flush();





        $output->writeln('<info>done !</info>');
        return true;
    }
}