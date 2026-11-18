<?php

namespace App\Enum;

/**
 * Les états possibles d'une tâche.
 * La valeur (à droite) est ce qui est enregistré dans la base.
 */
enum EtatTache: string
{
    case PasCommence = 'pas_commence';
    case EnCours = 'en_cours';
    case Fini = 'fini';

    /** Texte lisible, pratique pour l'affichage dans Twig : {{ tache.etat.label }} */
    public function label(): string
    {
        return match ($this) {
            self::PasCommence => 'Pas commencé',
            self::EnCours => 'En cours',
            self::Fini => 'Fini',
        };
    }
}
