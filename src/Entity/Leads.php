<?php

namespace App\Entity;

use App\Repository\LeadsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeadsRepository::class)
 */
class Leads
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $landing_id;

    /**
     * @ORM\Column(type="json")
     */
    private $data = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;


   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLandingId(): ?int
    {
        return $this->landing_id;
    }

    public function setLandingId(int $landing_id): self
    {
        $this->landing_id = $landing_id;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAdded(): ?\DateTime
    {
        return $this->added;
    }

    public function setAdded(\DateTime $added): self
    {
        $this->added = $added;

        return $this;
    }

  
}
