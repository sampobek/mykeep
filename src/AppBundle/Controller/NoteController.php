<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Note controller.
 */
class NoteController extends Controller
{
    /**
     * Lists all note entities.
     *
     * @Route("/", name="homepage")
     * @Route("/", name="note_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $em = $this->getDoctrine()->getManager();

        $notes = $em->getRepository('AppBundle:Note')
            ->findBy([
                'user' => $this->getUser(),
                'isDeleted' => false
            ]);

        return $this->render('note/index.html.twig', array(
            'notes' => $notes,
        ));
    }

    /**
     * Creates a new note entity.
     *
     * @Route("/new", name="note_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $note = new Note();
        $form = $this->createForm('AppBundle\Form\NoteType', $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $note->setUser($this->getUser());
            $em->persist($note);
            $em->flush($note);

            return $this->redirectToRoute('note_index', array('id' => $note->getId()));
        }

        return $this->render('note/new.html.twig', array(
            'note' => $note,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing note entity.
     *
     * @Route("/{id}/edit", name="note_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Note $note)
    {
        $deleteForm = $this->createDeleteForm($note);
        $editForm = $this->createForm('AppBundle\Form\NoteType', $note);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/edit.html.twig', array(
            'note' => $note,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a note entity.
     *
     * @Route("/{id}", name="note_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Note $note)
    {
        $form = $this->createDeleteForm($note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $note->setIsDeleted(true);
            $em->persist($note);
            $em->flush();
        }

        return $this->redirectToRoute('note_index');
    }

    /**
     * Creates a form to delete a note entity.
     *
     * @param Note $note The note entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Note $note)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('note_delete', array('id' => $note->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Lists all note entities in Trash.
     *
     * @Route("/trash", name="note_trash")
     * @Method("GET")
     */
    public function trashAction()
    {
        $em = $this->getDoctrine()->getManager();

        $notes = $em->getRepository('AppBundle:Note')
            ->findBy([
                'user' => $this->getUser(),
                'isDeleted' => true
            ]);

        return $this->render('note/trash.html.twig', array(
            'notes' => $notes,
        ));
    }
}
