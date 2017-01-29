<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoteHistory
 *
 * @ORM\Table(name="note_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteHistoryRepository")
 */
class NoteHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * Many NoteHistories related to one Note.
     * @ORM\ManyToOne(targetEntity="Note")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id", nullable=false)
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return NoteHistory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return NoteHistory
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return NoteHistory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set note
     *
     * @param \AppBundle\Entity\Note $note
     *
     * @return NoteHistory
     */
    public function setNote(\AppBundle\Entity\Note $note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return \AppBundle\Entity\Note
     */
    public function getNote()
    {
        return $this->note;
    }
}
