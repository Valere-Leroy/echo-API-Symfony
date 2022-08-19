<?php

namespace App\Entity;





use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"echoList", "userShow", "notificationData", "userConversation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"echoList", "userShow", "notificationData", "userConversation"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("userShow")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $activationCode;

    /**
     * @ORM\Column(type="json")
     * @Groups("userShow")
     */
    private $role = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"echoList", "userShow"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("userShow")
     */
    private $adress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     * @Groups("userShow")
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     * @Groups("userShow")
     */
    private $longitude;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("userShow")
     */
    private $isActivated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user", cascade={"remove"})
     */
    private $notifications;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="users",cascade={"remove"})
     * @Groups("userShow")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StaffMessage", mappedBy="user",cascade={"remove"})
     */
    private $StaffMessages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EchoPost", mappedBy="user",cascade={"remove"})
     */
    private $EchoPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentary", mappedBy="user",cascade={"remove"})
     */
    private $commentaries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="user",cascade={"remove"})
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Conversation", mappedBy="author")
     * @ORM\OrderBy({"id" = "desc"})
     */
    private $conversations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Conversation", mappedBy="users")
     * @ORM\OrderBy({"id" = "desc"})
     */
    private $conversationsUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="user", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="users")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="followers")
     */
    private $users;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->StaffMessages = new ArrayCollection();
        $this->EchoPosts = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->isActivated = false;
        $this->createdAt = new \DateTime();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getActivationCode(): ?string
    {
        return $this->activationCode;
    }

    public function setActivationCode(string $activationCode): self
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

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
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|StaffMessage[]
     */
    public function getStaffMessages(): Collection
    {
        return $this->StaffMessages;
    }

    public function addStaffMessage(StaffMessage $staffMessage): self
    {
        if (!$this->StaffMessages->contains($staffMessage)) {
            $this->StaffMessages[] = $staffMessage;
            $staffMessage->setUser($this);
        }

        return $this;
    }

    public function removeStaffMessage(StaffMessage $staffMessage): self
    {
        if ($this->StaffMessages->contains($staffMessage)) {
            $this->StaffMessages->removeElement($staffMessage);
            // set the owning side to null (unless already changed)
            if ($staffMessage->getUser() === $this) {
                $staffMessage->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EchoPost[]
     */
    public function getEchoPosts(): Collection
    {
        return $this->EchoPosts;
    }

    public function addEchoPost(EchoPost $echoPost): self
    {
        if (!$this->EchoPosts->contains($echoPost)) {
            $this->EchoPosts[] = $echoPost;
            $echoPost->setUser($this);
        }

        return $this;
    }

    public function removeEchoPost(EchoPost $echoPost): self
    {
        if ($this->EchoPosts->contains($echoPost)) {
            $this->EchoPosts->removeElement($echoPost);
            // set the owning side to null (unless already changed)
            if ($echoPost->getUser() === $this) {
                $echoPost->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @SerializedName("echoCount")
     * @Groups("userShow")
     */
    public function getCountEchoPosts()
    {
        return count($this->EchoPosts);
    }

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Commentary $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setUser($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getUser() === $this) {
                $commentary->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setUser($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getUser() === $this) {
                $vote->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface Implementations
     */
    public function getRoles(): array
    {
        return $this->getRole();
    }

    public function setRoles(array $roles): self
    {
        $this->setRole($roles);
        return $this;
    }

    public function eraseCredentials()
    {
        
    }

    public function getSalt()
    {
        
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        //return $this->conversations;
        $collection = new ArrayCollection( array_unique(array_merge($this->conversations->toArray(), $this->conversationsUsers->toArray())));
        return $collection;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->setAuthor($this);
        }

        return $this;
    }
      public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->contains($conversation)) {
            $this->conversations->removeElement($conversation);
            // set the owning side to null (unless already changed)
            if ($conversation->getAuthor() === $this) {
                $conversation->setAuthor(null);
            }
        }

        return $this;
    }
  
  
     /*
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }
        return $this;
    }
  
      public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

      /**
       * @return Collection|self[]
       */
      public function getFollowers(): Collection
      {
          return $this->followers;
      }

      public function addFollower(self $follower): self
      {
          if (!$this->followers->contains($follower)) {
              $this->followers[] = $follower;
          }

          return $this;
      }

      public function removeFollower(self $follower): self
      {
          if ($this->followers->contains($follower)) {
              $this->followers->removeElement($follower);
          }

          return $this;
      }

      /**
       * @return Collection|self[]
       */
      public function getUsers(): Collection
      {
          return $this->users;
      }

      public function addUser(self $user): self
      {
          if (!$this->users->contains($user)) {
              $this->users[] = $user;
              $user->addFollower($this);
          }

          return $this;
      }

      public function removeUser(self $user): self
      {
          if ($this->users->contains($user)) {
              $this->users->removeElement($user);
              $user->removeFollower($this);
          }

          return $this;
      }
}
