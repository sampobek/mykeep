<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tag controller.
 * @Route("/tag")
 */
class TagController extends Controller
{
    /**
     * @Route("/list", name="tag_list", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('AppBundle:Tag')->getTags();

        $noteResponse = [];
        foreach ($tags as $tag) {
            $noteResponse[] = [
                'id' => $tag->getId(),
                'alias' => $tag->getAlias(),
                'html' => $this->render("tag/tag.html.twig", [
                    'tag' => $tag
                ])->getContent()
            ];
        }

        $response = [
            'tags' => $noteResponse
        ];

        return new JsonResponse($response);
    }
}
