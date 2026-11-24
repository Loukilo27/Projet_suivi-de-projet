<?php

namespace App\DataFixtures;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use App\Enum\EtatTache;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Données de test pour le développement.
 * Chargement : docker compose exec app php bin/console doctrine:fixtures:load
 * ⚠️ Cette commande VIDE la base avant de la remplir.
 *
 * Tous les comptes ont le mot de passe : password
 * Les dates sont relatives au jour du chargement.
 */
class AppFixtures extends Fixture
{
    public const MOT_DE_PASSE = 'password';

    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // ------------------------------------------------------------
        // 1. Comptes : 1 administrateur + des chefs de projet
        //    (les visiteurs n'ont pas de compte : le site est public)
        // ------------------------------------------------------------
        $this->creerUser($manager, 'Administrateur', 'admin@test.fr', ['ROLE_ADMIN'], '-1 day');

        $alice = $this->creerUser($manager, 'Alice Martin', 'alice@test.fr', ['ROLE_CHEF_PROJET'], '-2 hours');
        $bruno = $this->creerUser($manager, 'Bruno Petit', 'bruno@test.fr', ['ROLE_CHEF_PROJET'], '-3 days');

        // 10 chefs de projet en plus, sans projet, pour tester la pagination (7.11)
        for ($i = 1; $i <= 10; ++$i) {
            $this->creerUser(
                $manager,
                sprintf('Chef Test %02d', $i),
                sprintf('chef%02d@test.fr', $i),
                ['ROLE_CHEF_PROJET'],
                1 === $i % 3 ? null : sprintf('-%d days', $i * 4),   // certains ne se sont jamais connectés
            );
        }

        // ------------------------------------------------------------
        // 2. Projets
        // ------------------------------------------------------------
        // En cours, aujourd'hui dans la période -> visible sur la page d'accueil publique
        $site = $this->creerProjet($manager, 'Refonte du site web', '-1 month', '+2 months', $alice);

        // En cours, aujourd'hui dans la période, avec une tâche en retard -> marqué en retard
        $mobile = $this->creerProjet($manager, 'Application mobile', '-3 months', '+1 month', $bruno);

        // Terminé (toutes les tâches faites), période passée -> grisé, pas sur la page publique
        $serveurs = $this->creerProjet($manager, 'Migration des serveurs', '-6 months', '-2 months', $alice);

        // Période passée mais pas fini -> en retard, pas sur la page publique
        $intranet = $this->creerProjet($manager, 'Nouvel intranet', '-5 months', '-1 week', $bruno);

        // Pas encore commencé (période future) -> pas sur la page publique
        $formation = $this->creerProjet($manager, 'Formation des équipes', '+1 month', '+3 months', $alice);

        // ------------------------------------------------------------
        // 3. Tâches
        // ------------------------------------------------------------
        $this->creerTache($manager, $site, 'Réaliser les maquettes des pages principales', EtatTache::Fini, '-1 month', '-2 weeks');
        $this->creerTache($manager, $site, 'Intégrer la page d\'accueil', EtatTache::EnCours, '-2 weeks', '+1 week');
        $this->creerTache($manager, $site, 'Rédiger les contenus des pages services', EtatTache::PasCommence, '+1 week', '+1 month');
        $this->creerTache($manager, $site, 'Optimiser le référencement (SEO)', EtatTache::PasCommence, '+1 month', '+2 months');

        $this->creerTache($manager, $mobile, 'Définir les écrans de l\'application', EtatTache::Fini, '-3 months', '-2 months');
        $this->creerTache($manager, $mobile, 'Développer le module de rendez-vous', EtatTache::EnCours, '-2 months', '-1 week'); // en retard
        $this->creerTache($manager, $mobile, 'Publier sur les stores', EtatTache::PasCommence, '+1 week', '+1 month');

        $this->creerTache($manager, $serveurs, 'Inventaire des serveurs existants', EtatTache::Fini, '-6 months', '-5 months');
        $this->creerTache($manager, $serveurs, 'Migration des bases de données', EtatTache::Fini, '-5 months', '-3 months');
        $this->creerTache($manager, $serveurs, 'Tests et mise en service', EtatTache::Fini, '-3 months', '-2 months');

        $this->creerTache($manager, $intranet, 'Recueillir les besoins', EtatTache::Fini, '-5 months', '-4 months');
        $this->creerTache($manager, $intranet, 'Développer l\'annuaire', EtatTache::EnCours, '-4 months', '-1 week');

        $this->creerTache($manager, $formation, 'Préparer les supports', EtatTache::PasCommence, '+1 month', '+2 months');

        // Envoie tout à la base en une seule fois
        $manager->flush();
    }

    /**
     * @param string[] $roles
     */
    private function creerUser(
        ObjectManager $manager,
        string $nom,
        string $email,
        array $roles,
        ?string $derniereConnexion,
    ): User {
        $user = new User();
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setRoles($roles);
        // Le mot de passe est chiffré (haché) avant d'être enregistré
        $user->setPassword($this->hasher->hashPassword($user, self::MOT_DE_PASSE));
        $user->setDerniereConnexion(null === $derniereConnexion ? null : new \DateTimeImmutable($derniereConnexion));

        $manager->persist($user);

        return $user;
    }

    private function creerProjet(ObjectManager $manager, string $nom, string $debut, string $fin, User $chef): Projet
    {
        $projet = new Projet();
        $projet->setNom($nom);
        $projet->setDateDebut(new \DateTimeImmutable($debut));
        $projet->setDateFin(new \DateTimeImmutable($fin));
        $projet->setChefDeProjet($chef);

        $manager->persist($projet);

        return $projet;
    }

    private function creerTache(
        ObjectManager $manager,
        Projet $projet,
        string $description,
        EtatTache $etat,
        string $debut,
        string $fin,
    ): void {
        $tache = new Tache();
        $tache->setDescription($description);
        $tache->setEtat($etat);
        $tache->setDateDebut(new \DateTimeImmutable($debut));
        $tache->setDateFin(new \DateTimeImmutable($fin));

        $projet->addTache($tache);   // relie la tâche au projet

        $manager->persist($tache);
    }
}
