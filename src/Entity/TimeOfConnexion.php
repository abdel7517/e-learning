<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimeOfConnexionRepository")
 */
class TimeOfConnexion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time_co;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time_deco;

    /**
     * @ORM\Column(type="integer")
     */
    private $session_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $toDay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTimeCo(): ?\DateTimeInterface
    {
        return $this->time_co;
    }

    public function setTimeCo(\DateTimeInterface $time_co): self
    {
        $this->time_co = $time_co;

        return $this;
    }

    public function getTimeDeco(): ?\DateTimeInterface
    {
        return $this->time_deco;
    }

    public function setTimeDeco(?\DateTimeInterface $time_deco): self
    {
        $this->time_deco = $time_deco;

        return $this;
    }

    public function getSessionId(): ?int
    {
        return $this->session_id;
    }

    public function setSessionId(int $session_id): self
    {
        $this->session_id = $session_id;

        return $this;
    }

    public function getToDay(): ?\DateTimeInterface
    {
        return $this->toDay;
    }

    public function setToDay(\DateTimeInterface $toDay): self
    {
        $this->toDay = $toDay;

        return $this;
    }
}
