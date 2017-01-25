<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

        return $this->render('note/index.html.twig');
    }

    /**
     * @Route("/list", name="note_list", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $notes = $em->getRepository('AppBundle:Note')
            ->findBy([
                'user' => $this->getUser(),
                'isDeleted' => false
            ]);

        $noteResponse = [];
        foreach ($notes as $note) {
            $noteResponse[] = [
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'content' => $note->getContent(),
                'content_br' => nl2br($note->getContent()),
                'html' => $this->render("note/note.html.twig", [
                    'note' => $note
                ])->getContent()
            ];
        }

        $response = [
            'notes' => $noteResponse
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/save", name="note_save", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxSaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trans = $this->get('translator');

        $id = $request->request->get('id');
        if ($id) {
            $note = $em->getRepository('AppBundle:Note')->find($id);
        } else {
            $note = new Note();
            $note->setUser($this->getUser());
        }

        if (!$note) {
            $response = ['message' => $trans->trans('note.note_not_found')];
            return new JsonResponse($response, 404);
        }

        $title = $request->request->get('title');
        $content = $request->request->get('content');

        $note->setTitle($title);
        $note->setContent($content);
        $em->persist($note);
        $em->flush();

        $noteResponse = [
            'id' => $note->getId(),
            'title' => $note->getTitle(),
            'content' => $note->getContent(),
            'content_br' => nl2br($note->getContent())
        ];

        if (!$id) {
            $noteResponse['html'] = $this->render("note/note.html.twig", [
                'note' => $note
            ])->getContent();
        }

        $response = [
            'message' => $trans->trans('note.note_saved'),
            'note' => $noteResponse
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/delete", name="note_delete", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trans = $this->get('translator');

        $id = $request->request->get('id');
        if (!$id) {
            $response = ['message' => $trans->trans('note.note_not_found')];
            return new JsonResponse($response, 404);
        }

        $note = $em->getRepository('AppBundle:Note')->find($id);

        if (!$note) {
            $response = ['message' => $trans->trans('note.note_not_found')];
            return new JsonResponse($response, 404);
        }

        $note->setIsDeleted(true);
        $em->persist($note);
        $em->flush();

        $response = [
            'message' => $trans->trans('note.note_deleted'),
            'id' => $id
        ];

        return new JsonResponse($response);
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
