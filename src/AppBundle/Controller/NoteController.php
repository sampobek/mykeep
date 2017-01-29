<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Note controller.
 */
class NoteController extends Controller
{
    /**
     *
     * Lists all note entities.
     *
     * @Route("/", name="homepage")
     * @Route("/", name="note_index", options={"expose"=true})
     * @Route("/t/{tag}", name="note_index_tag")
     * @Method("GET")
     *
     * @param null $tag
     * @return RedirectResponse|Response
     */
    public function indexAction($tag = null)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $em = $this->getDoctrine()->getManager();

        $colors = $em->getRepository('AppBundle:Color')->findAll();

        return $this->render('note/index.html.twig', ['colors' => $colors, 'tag' => $tag]);
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
        $tag = $request->query->get('tag');

        $user = $this->getUser();
        if ($tag) {
            $notes = $em->getRepository('AppBundle:Note')
                ->getByTag($user, $tag);
        } else {
            $notes = $em->getRepository('AppBundle:Note')
                ->findBy([
                    'user' => $user,
                    'isDeleted' => false
                ]);
        }

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

        $noteResponse = $this->get('note.manager')
            ->save($note, $title, $content, $id);

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
     * @Route("/set-color", name="note_set_color", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxSetColorAction(Request $request)
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

        $id = $request->request->get('colorId', 0);
        $color = $em->getRepository('AppBundle:Color')->find($id);
        if (!$color) {
            $color = null;
        }

        $note->setColor($color);
        $em->persist($note);
        $em->flush();

        $response = [
            'message' => $trans->trans('note.note_color_changed'),
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
