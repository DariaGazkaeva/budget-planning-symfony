<?php

namespace App\Entity;

use App\Repository\LimitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LimitRepository::class)]
#[ORM\Table(name: '`limit`')]
class Limit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'limits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?float $total_sum = null;

    #[ORM\Column]
    private ?float $current_sum = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTotalSum(): ?float
    {
        return $this->total_sum;
    }

    public function setTotalSum(float $total_sum): static
    {
        $this->total_sum = $total_sum;

        return $this;
    }

    public function getCurrentSum(): ?float
    {
        return $this->current_sum;
    }

    public function setCurrentSum(float $current_sum): static
    {
        $this->current_sum = $current_sum;

        return $this;
    }
}
