<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EchoPostRepository")
 */
class EchoPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"echoList", "echoShow", "notificationData"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups({"echoList", "echoShow", "notificationData"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"echoList", "echoShow"})
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"echoList", "echoShow"})
     */
    private $isModerated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"echoList", "echoShow"})
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"echoList", "echoShow"})
     */
    private $adress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     * @Groups({"echoList", "echoShow"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8)
     * @Groups({"echoList", "echoShow"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"echoList", "echoShow"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="EchoPosts")
     * @Groups({"echoList", "echoShow", "notificationData"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="echoPosts")
     * @Groups({"echoList", "echoShow"})
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentary", mappedBy="echoPost", orphanRemoval=true)
     * @Groups("echoShow")
     */
    private $commentaries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="echoPost", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="echoPost", cascade={"remove"})
     */
    private $notifications;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->isModerated = false;
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @SerializedName("createdAt")
     * @Groups({"echoList", "echoShow"})
     */
    public function getCreatedAtFormat()
    {
        $date = $this->createdAt;
        $daysFR = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $dayName =  $daysFR[$date->format('N') - 1];
        $day = $date->format('d');
        $monthsFR = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $monthName = $monthsFR[$date->format('n') - 1];
        $year = $date->format('Y');
        $hour = $date->format('H');
        $minutes = $date->format('i');
        return $dayName.' '.$day.' '.$monthName.' '.$year.' à '.$hour.'h'.$minutes;
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
            $commentary->setEchoPost($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getEchoPost() === $this) {
                $commentary->setEchoPost(null);
            }
        }

        return $this;
    }

    /**
     * @SerializedName("comments")
     * @Groups({"echoList", "echoShow"})
     */
    public function getCountCommentaries()
    {
        return count($this->commentaries);
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
            $vote->setEchoPost($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getEchoPost() === $this) {
                $vote->setEchoPost(null);
            }
        }

        return $this;
    }

    /**
     * @SerializedName("vote")
     * @Groups({"echoList", "echoShow"})
     */
    public function getCountVotes()
    {
        return count($this->votes);
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
            $notification->setEchoPost($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getEchoPost() === $this) {
                $notification->setEchoPost(null);
            }
        }

        return $this;
    }
}
