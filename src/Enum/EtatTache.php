<?php

namespace App\Enum;

/**
 * Les états qu'un chef de projet peut choisir pour une tâche (enregistrés en base).
 * "En retard" n'est pas dans la liste : il est calculé automatiquement
 * (voir Tache::estEnRetard()) quand la date de fin est dépassée et que la tâche n'est pas faite.
 */
enum EtatTache: string
{
    case PasCommence = 'pas_commence';
    case EnCours = 'en_cours';
    case Fini = 'fini';

    /** Texte lisible, pratique dans Twig : {{ tache.etat.label }} */
    public function label(): string
    {
        return match ($this) {
            self::PasCommence => 'Pas commencé',
            self::EnCours => 'En cours',
            self::Fini => 'Fait',
        };
    }
}
