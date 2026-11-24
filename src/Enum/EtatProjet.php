<?php

namespace App\Enum;

/**
 * Les états d'un projet.
 * Ils ne sont PAS enregistrés en base : ils sont calculés à partir des tâches
 * et des dates (voir Projet::getEtat()), comme le demande le cahier des charges.
 */
enum EtatProjet: string
{
    case EnCours = 'en_cours';
    case EnRetard = 'en_retard';
    case Fini = 'fini';

    /** Texte lisible, pratique dans Twig : {{ projet.etat.label }} */
    public function label(): string
    {
        return match ($this) {
            self::EnCours => 'En cours',
            self::EnRetard => 'En retard',
            self::Fini => 'Terminé',
        };
    }
}
