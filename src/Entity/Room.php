<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use App\ValueObject\RoomCapacity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: "integer")]
    private int $capacity;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'rooms')]
    private Collection $equipment;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
        $this->isActive = true;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): RoomCapacity
    {
        return new RoomCapacity($this->capacity);
    }

    public function setCapacity(RoomCapacity $capacity): static
    {
        $this->capacity = $capacity->getValue();
        return $this;
    }

    public function setCapacityInt(int $capacity): static
    {
        $this->capacity = (new RoomCapacity($capacity))->getValue();
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }
}
