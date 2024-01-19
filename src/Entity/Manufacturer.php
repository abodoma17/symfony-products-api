<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;



/**
 * Manufacturer
 * 
 * @ORM\Entity
 */
#[ApiResource]
class Manufacturer
{
    /** 
     * The id of the manufacturer
     *
     * @ORM\Id 
     * @ORM\GeneratedValue
    */
    private ?int $id = null;

    /** 
     * The name of the manufacturer 
     * 
     * @ORM\String
     */
    private string $name = '';

    /** 
     * The description of the manufacturer
     * 
     * @ORM\String
     */
    private string $description = '';

    /** 
     * The country code of the manufacturer 
     * 
     * @ORM\String
     */
    private string $countryCode = '';

    /** The date that the manufacturer was listed */
    private ?\DateTimeInterface $listedDate = null;



    /**
     * Get the value of name
     */ 
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

    public function getCountryCode()
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
}
