<?php

namespace App\Entity;

use App\Repository\HolidayQueryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HolidayQueryRepository::class)
 */
class HolidayQuery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $from_date;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $to_date;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $country_code;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(type="json")
     */
    private $result = [];

    public function getId(): ?int {
        return $this->id;
    }

    public function getFromDate(): ?string {
        return $this->from_date;
    }

    public function setFromDate(string $from_date): self {
        $this->from_date = $from_date;

        return $this;
    }

    public function getToDate(): ?string {
        return $this->to_date;
    }

    public function setToDate(string $to_date): self {
        $this->to_date = $to_date;

        return $this;
    }

    public function getCountryCode(): ?string {
        return $this->country_code;
    }

    public function setCountryCode(string $country_code): self {
        $this->country_code = $country_code;

        return $this;
    }

    public function getRegion(): ?string {
        return $this->region;
    }

    public function setRegion(?string $region): self {
        $this->region = $region;

        return $this;
    }

    public function getResult(): ?array {
        return $this->result;
    }

    public function setResult(array $result): self {
        $this->result = $result;

        return $this;
    }
}
