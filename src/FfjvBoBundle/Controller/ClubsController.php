<?php

namespace FfjvBoBundle\Controller;

use FfjvBoBundle\Entity\WeezeventApiLog;
use FfjvBoBundle\Form\WeezeventApiLogType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FfjvBoBundle\Entity\Clubs;
use FfjvBoBundle\Form\ClubsType;
use FfjvBoBundle\Entity\Messages;

/**
 * Clubs controller.
 *
 */
class ClubsController extends Controller
{

    /**
     * Lists all Clubs clubs.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clubs = $em->getRepository('FfjvBoBundle:Clubs')->findAll();

        return $this->render('FfjvBoBundle:Clubs:index.html.twig', array(
            'clubs' => $clubs,
        ));
    }
    /**
     * Creates a new Clubs club.
     *
     */
    public function createAction(Request $request)
    {
        $club = new Clubs();
        $form = $this->createCreateForm($club);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $club->setUser($this->getUser());
            $club->setIdZipCode($this->get('clubs')->getIdZipCode($club->getZipCode(), $club->getCountry()));

            $licence = $this->get('licences_clubs')->getNewLicences($club);
            $club->setLicence($licence);

            $em->persist($club);
            $em->flush();

            return $this->redirect($this->generateUrl('clubs_show', array('id' => $club->getId())));
        }

        return $this->render('FfjvBoBundle:Clubs:new.html.twig', array(
            'club' => $club,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Clubs club.
     *
     * @param Clubs $club The club
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Clubs $club)
    {
        $form = $this->createForm(ClubsType::class, $club, array(
            'action' => $this->generateUrl('clubs_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-success')));

        return $form;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $club = new Clubs();
        $form   = $this->createCreateForm($club);

        return $this->render('FfjvBoBundle:Clubs:new.html.twig', array(
            'club' => $club,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);
        $ligues = $em->getRepository('FfjvBoBundle:Clubs')->findLigueByClub($club->getId());
        $members = $this->getDoctrine()->getRepository('FfjvBoBundle:UserHasClubs')->findBy(array('club' => $club));
        $apiLog = $this->getDoctrine()->getRepository('FfjvBoBundle:WeezeventApiLog')->findOneBy(array('club' => $club));
        if(!$apiLog){
            $apiLog = new WeezeventApiLog();
            $apiLog->setUser($this->getUser());
            $apiLog->setClub($club);
        }

        if (!$club) {
            throw $this->createNotFoundException('Unable to find Clubs club.');
        }
        $editForm = $this->createEditForm($club);
        $apiLogForm = $this->getApiLogForm($apiLog);
        $contactForm = $this->getContactForm($this->generateUrl('clubs_contact'), array(
            'user' => $this->getUser()->getId(),
            'club' => $club->getId()
        ));
        return $this->render('FfjvBoBundle:Clubs:show.html.twig', array(
            'club'          => $club,
            'members'       => $members,
            'ligues'        => $ligues,
            'edit_form'     => $editForm->createView(),
            'contact_form'  => $contactForm->createView(),
            'weezevent_api_form' => $apiLogForm->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contactClubAction(Request $request){

        $form = $this->getContactForm();
        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $id = $data['club'];
            $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);
            if (!$club) {
                throw $this->createNotFoundException('Unable to find Clubs club.');
            }
            // create and save Mesage
            $message = new Messages();
            $message->setAuthorUser($this->getUser());
            $message->setMessage($data['content']);
            $message->setSubject($data['subject']);
            $message->setClub($club);
            $message->setEmail($club->getEmail());
            $em->persist($message);
            $em->flush();
            //send message
            if($this->get('contact')->contactClub($message)){
                $this->addFlash('success', 'Votre message a bien été envoyer');
            } else {
                $this->addFlash('error', 'Une erreur c\'est produite ! Votre message n\'a pus être envoyé .');
            }
            return $this->redirectToRoute('clubs_show', array('id' => $id));
        }
        $this->addFlash('error', 'Une erreur c\'est produite');
        return $this->redirectToRoute('user_show', array('id' => $this->getUser()->getId()));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);

        if (!$club) {
            throw $this->createNotFoundException('Unable to find Clubs club.');
        }

        $editForm = $this->createEditForm($club);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:Clubs:edit.html.twig', array(
            'club'      => $club,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Clubs $club
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Clubs $club)
    {
        $form = $this->createForm(ClubsType::class, $club, array(
            'action' => $this->generateUrl('clubs_update', array('id' => $club->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update',
            'attr' => array('class' => 'btn btn-success')));

        return $form;
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);

        if (!$club) {
            throw $this->createNotFoundException('Unable to find Clubs club.');
        }

        $editForm = $this->createEditForm($club);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $club->setLastUpdate(new \DateTime('now'));
            $club->setIdZipCode($this->get('clubs')->getIdZipCode($club->getZipCode(), $club->getCountry()));
            $em->persist($club);
            $em->flush();
            $this->addFlash('success', 'All data was successfully updated !');
            return $this->redirect($this->generateUrl('clubs_show', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('clubs_show', array('id' => $id)));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);

            if (!$club) {
                throw $this->createNotFoundException('Unable to find Clubs club.');
            }

            $em->remove($club);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('clubs'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clubs_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * @param string $url
     * @param array $data
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function getContactForm($url = '', $data = array()){
        $form = $this->get('contact')->getFormContactClub($url, $data);
        $form->add('submit', SubmitType::class, array(
            'label' => "bo.form.contact.club.send"
        ));
        return $form;
    }

    /**
     * @param WeezeventApiLog $apiLog
     * @return $this|\Symfony\Component\Form\FormInterface
     */
    private function getApiLogForm(WeezeventApiLog $apiLog){
        
        return $this->createForm(WeezeventApiLogType::class, $apiLog->toArray(), [
            'method' => 'POST',
            'action' => $this->generateUrl('weezeventapilog_new')
        ])->add('submit', SubmitType::class, [
            'label' => 'submit',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
    }
}
