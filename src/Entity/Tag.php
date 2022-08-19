<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"echoList", "tagList", "userShow", "userConversation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"echoList", "tagList","userShow", "userConversation"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="tags")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EchoPost", mappedBy="tags")
     */
    private $echoPosts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Conversation", mappedBy="tags")
     */
    private $conversations;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->echoPosts = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->conversations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addTag($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTag($this);
        }

        return $this;
    }

    /**
     * @SerializedName("followers")
     * @Groups({"tagList","userShow"})
     */
    public function getCountUsers()
    {
        return count($this->users);
    }

    /**
     * @return Collection|EchoPost[]
     */
    public function getEchoPosts(): Collection
    {
        return $this->echoPosts;
    }

    public function addEchoPost(EchoPost $echoPost): self
    {
        if (!$this->echoPosts->contains($echoPost)) {
            $this->echoPosts[] = $echoPost;
            $echoPost->addTag($this);
        }

        return $this;
    }

    public function removeEchoPost(EchoPost $echoPost): self
    {
        if ($this->echoPosts->contains($echoPost)) {
            $this->echoPosts->removeElement($echoPost);
            $echoPost->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->addTag($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->contains($conversation)) {
            $this->conversations->removeElement($conversation);
            $conversation->removeTag($this);
        }

        return $this;
    }
}
