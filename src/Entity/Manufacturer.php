<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * A manufacturer.
 */
#[ORM\Entity]
#[ApiResource(
    paginationItemsPerPage: 5
)]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The name of the manufacturer.
     */
    #[ORM\Column]
    #[
        NotBlank,
        Groups(['product.read'])
    ]
    private string $name = '';

    /**
     * The description of the manufacturer.
     */
    #[ORM\Column(type: "text")]
    #[NotBlank]
    private string $description = '';

    /**
     * The country code of the manufacturer.
     */
    #[ORM\Column(length: 3)]
    #[NotBlank]
    private string $countryCode = '';

    /**
     * The date that the manufacturer was listed
     *
     */
    #[ORM\Column(type: "datetime")]
    #[NotNull]
    private ?\DateTimeInterface $listedDate = null;

    /**
     * Products of the manufacturer.
     */
    #[ORM\OneToMany(mappedBy: "manufacturer", targetEntity: "Product", cascade: ["persist", "remove"])]
    private iterable $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }


    public function getListedDate(): \DateTimeInterface
    {
        return $this->listedDate;
    }

    public function setListedDate(?\DateTimeInterface $listedDate): void
    {
        $this->listedDate = $listedDate;
    }

    /**
     * @return iterable
     */
    public function getProducts(): iterable
    {
        return $this->products;
    }
}
