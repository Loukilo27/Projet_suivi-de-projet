<?php

namespace App\Entity;

use App\Enum\EtatProjet;
use App\Enum\EtatTache;
use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Table "projet".
 * Cahier des charges : désignation (200 caractères max), date de début, date de fin, chef de projet.
 */
#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // La "désignation" du cahier des charges
    #[ORM\Column(length: 200)]
    private ?string $nom = null;

    // Facultatif (pas demandé par le cahier des charges, gardé pour plus tard)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptif = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateFin = null;

    /**
     * Relation : plusieurs projets -> un chef de projet (User).
     * Si le chef de projet est supprimé, ses projets le sont aussi (cahier des charges 7.13).
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $chefDeProjet = null;

    /**
     * Relation : un projet -> plusieurs tâches, triées par date de début.
     *
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'projet', orphanRemoval: true)]
    #[ORM\OrderBy(['dateDebut' => 'ASC'])]
    private Collection $taches;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
    }

    // ------------------------------------------------------------------
    // État calculé (pas de colonne en base)
    // ------------------------------------------------------------------

    /**
     * Terminé = le projet a des tâches et elles sont toutes faites (cahier des charges 7.3).
     */
    public function estTermine(): bool
    {
        if ($this->taches->isEmpty()) {
            return false;
        }

        foreach ($this->taches as $tache) {
            if (EtatTache::Fini !== $tache->getEtat()) {
                return false;
            }
        }

        return true;
    }

    /**
     * En retard = pas terminé, et (date de fin dépassée ou au moins une tâche en retard).
     */
    public function estEnRetard(): bool
    {
        if ($this->estTermine()) {
            return false;
        }

        if (null !== $this->dateFin && $this->dateFin < new \DateTimeImmutable('today')) {
            return true;
        }

        foreach ($this->taches as $tache) {
            if ($tache->estEnRetard()) {
                return true;
            }
        }

        return false;
    }

    /** Utilisable dans Twig : {{ projet.etat.label }} */
    public function getEtat(): EtatProjet
    {
        return match (true) {
            $this->estTermine() => EtatProjet::Fini,
            $this->estEnRetard() => EtatProjet::EnRetard,
            default => EtatProjet::EnCours,
        };
    }

    // ------------------------------------------------------------------
    // Getters / setters
    // ------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): static
    {
        $this->descriptif = $descriptif;

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

    public function getChefDeProjet(): ?User
    {
        return $this->chefDeProjet;
    }

    public function setChefDeProjet(?User $chefDeProjet): static
    {
        $this->chefDeProjet = $chefDeProjet;

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setProjet($this);
        }

        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getProjet() === $this) {
                $tache->setProjet(null);
            }
        }

        return $this;
    }
}
