<?php

namespace App\Entity;

use App\Enum\EtatTache;
use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Table "tache".
 * Cahier des charges : une description, une date de début, une date de fin, un état.
 */
#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // Par défaut : "pas commencé" (cahier des charges 7.7)
    #[ORM\Column(length: 20, enumType: EtatTache::class)]
    private EtatTache $etat = EtatTache::PasCommence;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateFin = null;

    /**
     * Relation : plusieurs tâches -> un projet (obligatoire).
     * Si le projet est supprimé, ses tâches aussi (cahier des charges 7.5).
     */
    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Projet $projet = null;

    /**
     * En retard = date de fin dépassée et tâche pas encore faite.
     * Calculé, pas enregistré en base.
     */
    public function estEnRetard(): bool
    {
        return EtatTache::Fini !== $this->etat
            && null !== $this->dateFin
            && $this->dateFin < new \DateTimeImmutable('today');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEtat(): EtatTache
    {
        return $this->etat;
    }

    public function setEtat(EtatTache $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeImmutable $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): static
    {
        $this->projet = $projet;

        return $this;
    }
}
