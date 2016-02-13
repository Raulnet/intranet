<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 05/12/15
 * Time: 21:47
 */

namespace Ffjv\BoBundle\Service;

use Doctrine\ORM\EntityManager;
use Ffjv\BoBundle\Form\ContactClubType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Ffjv\BoBundle\Entity\User;
use Ffjv\BoBundle\Entity\Messages;

class ContactService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
    private $templating;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    /**
     * @var array
     */
    private $template = array(
        'default' => 'FfjvBoBundle:Emails:_default.html.twig',
        'contact_club' => 'FfjvBoBundle:Emails:_contactClub.html.twig',
        'request_join_club' => 'FfjvBoBundle:Emails:_requestJoinClub.html.twig'
    );

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * ContactService constructor.
     * @param EntityManager $em
     * @param RequestStack $request
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, RequestStack  $request, ContainerInterface $container)
    {
        $this->em           = $em;
        $this->request      = $request;
        $this->mailer       = $container->get('mailer');
        $this->templating   = $container->get('templating');
        $this->formFactory  = $container->get('form.factory');
    }

    /**
     * @param Messages $message
     * @param string $source
     * @return bool
     * @throws \Twig_Error
     */
    public function contactClub(Messages $message, $source = 'contact@ffjv.org')
    {

        $content =  $this->templating->render($this->template['default'], array('message' => $message->getMessage(), 'user' => $message->getAuthorUser()));

        return $this->sendMail($source, $message->getEmail(), $message->getSubject(), $content);
    }

    /**
     * @param Messages $message
     * @param string   $source
     *
     * @return bool
     * @throws \Twig_Error
     */
    public function sendRequestToJoinCLub(Messages $message, $source = 'contact@ffjv.org'){

        $content = $this->templating->render($this->template['request_join_club'], array('message' => $message->getMessage(), 'user' => $message->getAuthorUser()));
        return $this->sendMail($source, $message->getEmail(), $message->getSubject(), $content);
    }

    /** *********** METHOD ********** */

    /**
     * @param string $source
     * @param string $emailTo
     * @param string $subject
     * @param string $content
     * @return bool
     */
    private function sendMail($source = 'contact@ffjv.org', $emailTo = '', $subject = '', $content = '')
    {
        if($emailTo == '' || $subject == '' || $content == ''){
            return false;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($source)
            ->setTo($emailTo)
            ->setBody($content, 'text/html');
        $this->mailer->send($message);

        return true;
    }


}