<?php

namespace AppBundle\Service;


use AppBundle\Entity\Note;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NoteManager
{
    protected $container;

    protected $em;

    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function save(Note $note, $title, $content, $id = null)
    {
        $em = $this->em;

        $contentLower = mb_strtolower($content);
        $tags = [];
        $tagsCount = preg_match_all('/#[a-z]+\w/', $contentLower, $tags);
        $tagsClean = [];
        foreach ($tags[0] as $tag) {
            $tagsClean[] = ltrim($tag, '#');
        }
        $tags = $tagsClean;

        if ($tagsCount > 0) {
            $noteTags = $note->getTags();
            foreach ($tags as $tagAlias) {
                $tag = $em->getRepository('AppBundle:Tag')
                    ->findOneBy(['alias' => $tagAlias]);
                if (!$tag) {
                    $tag = new Tag();
                    $tag->setAlias($tagAlias);
                    $em->persist($tag);
                }

                if (!$noteTags->contains($tag)) {
                    $note->addTag($tag);
                }
            }
            foreach ($noteTags as $noteTag) {
                $noteTagAlias = $noteTag->getAlias();
                if (!in_array($noteTagAlias, $tags)) {
                    $note->removeTag($noteTag);
                }
            }
        }

        $note->setTitle($title);
        $note->setContent($content);
        $em->persist($note);
        $em->flush();

        $contentBr = $this->highlightTag($note->getContent());

        $noteResponse = [
            'id' => $note->getId(),
            'title' => $note->getTitle(),
            'content' => $note->getContent(),
            'content_br' => nl2br($contentBr)
        ];

        if (!$id) {
            $noteResponse['html'] = $this->container->get('templating')
                ->renderResponse("note/note.html.twig", [
                    'note' => $note
                ])->getContent();
        }

        return $noteResponse;
    }

    public function highlightTag($content)
    {
        $content = preg_replace('/#[A-Za-z]+\w/', '<span class="link">$0</span>', $content);

        return $content;
    }
}