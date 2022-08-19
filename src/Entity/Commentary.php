<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaryRepository")
 */
class Commentary
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("echoShow")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("echoShow")
     */
    private $isModerated;

    /**
     * @ORM\Column(type="text")
     * @Groups("echoShow")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("echoShow")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentaries")
     * @Groups("echoShow")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EchoPost", inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $echoPost;

    public function __construct()
    {
        $this->isModerated = false;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsModerated(): ?bool
    {
        return $this->isModerated;
    }

    public function setIsModerated(bool $isModerated): self
    {
        $this->isModerated = $isModerated;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEchoPost(): ?EchoPost
    {
        return $this->echoPost;
    }

    public function setEchoPost(?EchoPost $echoPost): self
    {
        $this->echoPost = $echoPost;

        return $this;
    }
}
