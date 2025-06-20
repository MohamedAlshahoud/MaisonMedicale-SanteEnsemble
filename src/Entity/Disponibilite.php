<?php

namespace App\Entity;

use App\Repository\DisponibiliteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisponibiliteRepository::class)]
class Disponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Le médecin auquel appartient cette disponibilité
     */
    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    /**
     * Début du créneau (date + heure)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $debut = null;

    /**
     * Fin du créneau (date + heure)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fin = null;

    /**
     * Indique si le créneau est libre (true) ou déjà pris (false)
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private ?bool $estLibre = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): static
    {
        $this->medecin = $medecin;
        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): static
    {
        $this->debut = $debut;
        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): static
    {
        $this->fin = $fin;
        return $this;
    }

    public function isEstLibre(): ?bool
    {
        return $this->estLibre;
    }

    public function setEstLibre(?bool $estLibre): static
    {
        $this->estLibre = $estLibre;
        return $this;
    }


    public function __toString(): string
    {
        return sprintf(
            '%s → %s',
            $this->debut->format('d/m/Y H:i'),
            $this->fin->format('H:i')
        );
    }

}
