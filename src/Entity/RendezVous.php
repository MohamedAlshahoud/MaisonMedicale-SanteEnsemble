<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Disponibilite $disponibilite = null;

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

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getDisponibilite(): ?Disponibilite
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?Disponibilite $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    // ✅ Méthode utilitaire pour accéder à la date/heure du rendez-vous
    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->disponibilite?->getDebut();
    }
}
