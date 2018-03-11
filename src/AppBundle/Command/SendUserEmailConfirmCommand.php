<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 12/03/18
 * Time: 00:03
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use FfjvBoBundle\Entity\User;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class SendUserEmailConfirmCommand
 *
 * @package AppBundle\Command
 */
class SendUserEmailConfirmCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('send:mail:user:confirm')->setDescription('Send mail user confirm')
             ->addArgument('email', InputArgument::OPTIONAL, 'email');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em    = $this->getContainer()->get('doctrine.orm.entity_manager');
        $email = $input->getArgument('email');
        if (empty($email)) {
            $users = $em->getRepository('FfjvBoBundle:User')->findBy(['status' => 0]);
        } else {
            $users = $em->getRepository('FfjvBoBundle:User')->findBy(['status' => 0, 'email' => $email]);
        }
        $progressBar = new ProgressBar($output, count($users));
        $progressBar->start();
        foreach ($users as $user) {
            /** @var User $user */
            if ($user->getStatus() == 0) {
                $user->setActivationCode(base_convert(md5(uniqid(mt_rand(), true)), 16, 36));
                $em->persist($user);
                $em->flush();
                //SendMail to confirm email
                $this->sendMailToConfirmation($user);
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $output->writeln('<info>done !</info>');
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function sendMailToConfirmation(User $user){

        $message = \Swift_Message::newInstance()
                                 ->setSubject('FFjv Email de confirmation')
                                 ->setFrom('contact@ffjv.org')
                                 ->setTo($user->getEmail())
                                 ->setBody(
                                     $this->getContainer()->get('twig')->render('FfjvFoBundle:Emails:registration.html.twig',
                                         array('name' => $user->getUsername(), 'code' => $user->getActivationCode())
                                     ),
                                     'text/html'
                                 );
        $this->getContainer()->get('mailer')->send($message);

        return true;
    }

}