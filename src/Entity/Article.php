<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Cascade;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text', length: 2550)]
    private $description;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $createdate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedate;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'articles')]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $img;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Commentaire::class)]
    private $commentaires;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'fav')]
    #[ORM\JoinTable(name: 'user_article')]
    private $likedby;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->likedby = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedate(): ?\DateTimeInterface
    {
        return $this->createdate;
    }

    public function setCreatedate(?\DateTimeInterface $createdate): self
    {
        $this->createdate = $createdate;

        return $this;
    }

    public function getUpdatedate(): ?\DateTimeInterface
    {
        return $this->updatedate;
    }

    public function setUpdatedate(?\DateTimeInterface $updatedate): self
    {
        $this->updatedate = $updatedate;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setArticle($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikedby(): Collection
    {
        return $this->likedby;
    }

    public function addLikedby(User $likedby): self
    {
        if (!$this->likedby->contains($likedby)) {
            $this->likedby[] = $likedby;
            $likedby->addFav($this);
        }

        return $this;
    }

    public function removeLikedby(User $likedby): self
    {
        if ($this->likedby->removeElement($likedby)) {
            $likedby->removeFav($this);
        }

        return $this;
    }
}
