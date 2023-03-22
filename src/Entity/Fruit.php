<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FruitRepository::class)]
#[ORM\Table(name: 'symfony_demo_fruit')]
class Fruit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $family = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $genus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plant_order = null;

    #[ORM\Column(nullable: true)]
    private ?int $calories = null;

    #[ORM\Column(nullable: true)]
    private ?int $carbohydrates = null;

    #[ORM\Column(nullable: true)]
    private ?int $fat = null;

    #[ORM\Column(nullable: true)]
    private ?int $protein = null;

    #[ORM\Column(nullable: true)]
    private ?int $sugar = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    private bool|null $isLiked;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, cascade: [ 'persist' ])]
    #[ORM\JoinTable(name: 'symfony_demo_fruit_like')]
    #[ORM\OrderBy([ 'id' => 'ASC' ])]
    private Collection $likeUsers;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->likeUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(?string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    public function getPlantOrder(): ?string
    {
        return $this->plant_order;
    }

    public function setPlantOrder(?string $plant_order): self
    {
        $this->plant_order = $plant_order;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(?int $calories): self
    {
        $this->calories = $calories;

        return $this;
    }

    public function getCarbohydrates(): ?int
    {
        return $this->carbohydrates;
    }

    public function setCarbohydrates(?int $carbohydrates): self
    {
        $this->carbohydrates = $carbohydrates;

        return $this;
    }

    public function getFat(): ?int
    {
        return $this->fat;
    }

    public function setFat(?int $fat): self
    {
        $this->fat = $fat;

        return $this;
    }

    public function getProtein(): ?int
    {
        return $this->protein;
    }

    public function setProtein(?int $protein): self
    {
        $this->protein = $protein;

        return $this;
    }

    public function getSugar(): ?int
    {
        return $this->sugar;
    }

    public function setSugar(?int $sugar): self
    {
        $this->sugar = $sugar;

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

    public function addLike(User ...$likeUsers): void
    {
        foreach ($likeUsers as $likeUser) {
            if (!$this->likeUsers->contains($likeUser)) {
                $this->likeUsers->add($likeUser);
            }
        }
    }

    public function removeLike(User $user): void
    {
        $this->likeUsers->removeElement($user);
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikeUsers(): Collection
    {
        return $this->likeUsers;
    }
}
