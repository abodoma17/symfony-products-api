<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * A product.
 */
#[ORM\Entity]
#[  ApiResource(
        operations: [
            new GetCollection(),
            new Get(),
            new Post(security: 'is_granted("ROLE_ADMIN")'),
            new Patch(security: 'is_granted("ROLE_ADMIN")'),
            new Put(security: 'is_granted("ROLE_ADMIN") and object.getOwner() == user', securityMessage: "This product can only be edited by the user that created it."),
            new Delete(security: 'is_granted("ROLE_ADMIN")')
    ],
    normalizationContext: ['groups' => ['product.read']],
    denormalizationContext: ['groups' => ['product.write']],
    paginationItemsPerPage: 5,
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'name' => SearchFilter::STRATEGY_PARTIAL,
            'description' => SearchFilter::STRATEGY_PARTIAL,
            'manufacturer.countryCode' => SearchFilter::STRATEGY_EXACT
    ]),
    ApiFilter(
        OrderFilter::class,
        properties: ['issueDate']
    )
]
class Product
{
    /**
     * The id of the product.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    /**
     * The MPN (Manufacturer Part Number) of the product.
     */
    #[ORM\Column]
    #[
        NotNull,
        Groups(['product.read', 'product.write'])
    ]
    private ?string $mpn = null;

    /**
     * The name of the product.
     */
    #[ORM\Column]
    #[
        NotBlank,
        Groups(['product.read', 'product.write'])
    ]
    private string $name = '';

    /**
     * The description of the product.
     */
    #[ORM\Column(type: "text")]
    #[  NotBlank,
        Groups(['product.read', 'product.write'])
    ]
    private string $description = '';

    /**
     * The date of issue of the product.
     */
    #[ORM\Column(type: "datetime")]
    #[
        NotNull,
        Groups(['product.read', 'product.write'])
    ]
    private ?\DateTimeInterface $issueDate = null;

    /**
     * The manufacturer of the product
     */
    #[ORM\ManyToOne(targetEntity: "Manufacturer", inversedBy: "products")]
    #[
        NotNull,
        Groups(['product.read', 'product.write'])
    ]
    private ?Manufacturer $manufacturer = null;

    #[ORM\ManyToOne]
    #[
        NotNull,
        Groups(['product.read', 'product.write'])
    ]
    private ?User $owner = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    /**
     * @param string|null $mpn
     */
    public function setMpn(?string $mpn): void
    {
        $this->mpn = $mpn;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    /**
     * @param \DateTimeInterface|null $issueDate
     */
    public function setIssueDate(?\DateTimeInterface $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    /**
     * @return Manufacturer|null
     */
    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer|null $manufacturer
     */
    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
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

}