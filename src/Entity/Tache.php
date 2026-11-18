<?php

namespace App\Entity;

use App\Enum\EtatTache;
use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Table "tache".
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

    // Stocké en texte dans la base ('pas_commence', 'en_cours', 'fini')
    #[ORM\Column(length: 20, enumType: EtatTache::class)]
    private EtatTache $etat = EtatTache::PasCommence;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateFin = null;

    /**
     * Relation : plusieurs tâches -> un projet (obligatoire).
     * Crée la colonne "projet_id". Si le projet est supprimé, ses tâches aussi.
     */
    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Projet $projet = null;

    /**
     * Relation : plusieurs tâches -> un utilisateur assigné (facultatif).
     * Crée la colonne "assigne_a_id". Si l'utilisateur est supprimé, la tâche reste mais n'est plus assignée.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $assigneA = null;

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

    public function getAssigneA(): ?User
    {
        return $this->assigneA;
    }

    public function setAssigneA(?User $assigneA): static
    {
        $this->assigneA = $assigneA;

        return $this;
    }
}
