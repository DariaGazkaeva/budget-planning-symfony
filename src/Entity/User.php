<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 256)]
    private ?string $email = null;

    #[ORM\Column(length: 256)]
    private ?string $password = null;

    #[ORM\Column(length: 256)]
    private ?string $name = null;

    #[ORM\Column]
    private float $balance = 0;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Category::class)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: MoneyOperation::class, orphanRemoval: true)]
    private Collection $moneyOperations;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Limit::class, orphanRemoval: true)]
    private Collection $limits;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    private Collection $roles;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->moneyOperations = new ArrayCollection();
        $this->limits = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setOwner($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getOwner() === $this) {
                $category->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MoneyOperation>
     */
    public function getMoneyOperations(): Collection
    {
        return $this->moneyOperations;
    }

    public function addMoneyOperation(MoneyOperation $moneyOperation): static
    {
        if (!$this->moneyOperations->contains($moneyOperation)) {
            $this->moneyOperations->add($moneyOperation);
            $moneyOperation->setOwner($this);
        }

        return $this;
    }

    public function removeMoneyOperation(MoneyOperation $moneyOperation): static
    {
        if ($this->moneyOperations->removeElement($moneyOperation)) {
            // set the owning side to null (unless already changed)
            if ($moneyOperation->getOwner() === $this) {
                $moneyOperation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Limit>
     */
    public function getLimits(): Collection
    {
        return $this->limits;
    }

    public function addLimit(Limit $limit): static
    {
        if (!$this->limits->contains($limit)) {
            $this->limits->add($limit);
            $limit->setOwner($this);
        }

        return $this;
    }

    public function removeLimit(Limit $limit): static
    {
        if ($this->limits->removeElement($limit)) {
            // set the owning side to null (unless already changed)
            if ($limit->getOwner() === $this) {
                $limit->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = array();
//        foreach ($this->roles as $role) {
//            $roles[] = $role->name;
//        }
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        $this->roles->removeElement($role);

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
