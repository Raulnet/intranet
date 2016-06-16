<?php

namespace FfjvBoBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FfjvBoBundle\Entity\Ligues;
use FfjvBoBundle\Form\LiguesType;

/**
 * Ligues controller.
 *
 */
class LiguesController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligues = $em->getRepository('FfjvBoBundle:Ligues')->findAll();

        return $this->render('FfjvBoBundle:Ligues:index.html.twig', array(
            'ligues' => $ligues,
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $ligue = new Ligues();
        $form = $this->createCreateForm($ligue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ligue->setUser($this->getUser());
            $em->persist($ligue);
            $em->flush();

            return $this->redirect($this->generateUrl('ligues_show', array('id' => $ligue->getId())));
        }

        return $this->render('FfjvBoBundle:Ligues:new.html.twig', array(
            'ligue' => $ligue,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Ligues ligue.
     *
     * @param Ligues $ligue The ligue
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ligues $ligue)
    {
        $form = $this->createForm(LiguesType::class, $ligue, array(
            'action' => $this->generateUrl('ligues_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-success col-xs-12')));

        return $form;
    }

    /**
     * Displays a form to create a new Ligues ligue.
     *
     */
    public function newAction()
    {
        $ligue = new Ligues();
        $form   = $this->createCreateForm($ligue);

        return $this->render('FfjvBoBundle:Ligues:new.html.twig', array(
            'ligue' => $ligue,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ligues ligue.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ligue = $em->getRepository('FfjvBoBundle:Ligues')->find($id);

        if (!$ligue) {
            throw $this->createNotFoundException('Unable to find Ligues ligue.');
        }

        $editForm = $this->createEditForm($ligue);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:Ligues:show.html.twig', array(
            'ligue'      => $ligue,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Ligues ligue.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ligue = $em->getRepository('FfjvBoBundle:Ligues')->find($id);

        if (!$ligue) {
            throw $this->createNotFoundException('Unable to find Ligues ligue.');
        }

        $editForm = $this->createEditForm($ligue);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:Ligues:edit.html.twig', array(
            'ligue'      => $ligue,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Ligues ligue.
    *
    * @param Ligues $ligue The ligue
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ligues $ligue)
    {
        $form = $this->createForm(LiguesType::class, $ligue, array(
            'action' => $this->generateUrl('ligues_update', array('id' => $ligue->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Mettre à jours'));

        return $form;
    }
    /**
     * Edits an existing Ligues ligue.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $ligue = $em->getRepository('FfjvBoBundle:Ligues')->find($id);

        if (!$ligue) {
            throw $this->createNotFoundException('Unable to find Ligues ligue.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($ligue);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La ligue à été mis à jours');
            return $this->redirect($this->generateUrl('ligues_show', array('id' => $id)));
        }
        $this->addFlash('error', 'Une erreur c\'est produite !');
        return $this->render('FfjvBoBundle:Ligues:show.html.twig', array(
            'ligue'      => $ligue,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Ligues ligue.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ligue = $em->getRepository('FfjvBoBundle:Ligues')->find($id);

            if (!$ligue) {
                throw $this->createNotFoundException('Unable to find Ligues ligue.');
            }

            $em->remove($ligue);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ligues'));
    }

    /**
     * Creates a form to delete a Ligues ligue by id.
     *
     * @param mixed $id The ligue id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ligues_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
