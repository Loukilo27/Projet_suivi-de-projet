<?php

namespace App\Enum;

/**
 * Les états possibles d'un projet.
 * La valeur (à droite) est ce qui est enregistré dans la base.
 */
enum EtatProjet: string
{
    case EnCours = 'en_cours';
    case EnRetard = 'en_retard';
    case Fini = 'fini';

    /** Texte lisible, pratique pour l'affichage dans Twig : {{ projet.etat.label }} */
    public function label(): string
    {
        return match ($this) {
            self::EnCours => 'En cours',
            self::EnRetard => 'En retard',
            self::Fini => 'Fini',
        };
    }
}
