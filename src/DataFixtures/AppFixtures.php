<?php

namespace App\DataFixtures;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use App\Enum\EtatProjet;
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
        // 1. Utilisateurs
        // ------------------------------------------------------------
        $this->creerUser($manager, 'Admin', 'admin@test.fr', ['ROLE_ADMIN']);

        $alice = $this->creerUser($manager, 'Alice Martin', 'alice@test.fr', ['ROLE_CHEF_PROJET']);
        $bruno = $this->creerUser($manager, 'Bruno Petit', 'bruno@test.fr', ['ROLE_CHEF_PROJET']);

        // Utilisateurs simples : pas de rôle à préciser, ROLE_USER est ajouté automatiquement
        $chloe = $this->creerUser($manager, 'Chloé Durand', 'chloe@test.fr');
        $david = $this->creerUser($manager, 'David Leroy', 'david@test.fr');
        $emma = $this->creerUser($manager, 'Emma Moreau', 'emma@test.fr');

        // ------------------------------------------------------------
        // 2. Projets (les dates sont relatives à aujourd'hui)
        // ------------------------------------------------------------
        $site = $this->creerProjet(
            $manager,
            'Refonte du site web',
            'Nouvelle version du site vitrine : design, contenus et référencement.',
            EtatProjet::EnCours,
            '-1 month',
            '+2 months',
            $alice,
            [$chloe, $david],
        );

        $mobile = $this->creerProjet(
            $manager,
            'Application mobile',
            'Application mobile de prise de rendez-vous pour les clients.',
            EtatProjet::EnRetard,
            '-4 months',
            '-1 week',
            $bruno,
            [$david, $emma],
        );

        $serveurs = $this->creerProjet(
            $manager,
            'Migration des serveurs',
            'Passage des serveurs internes vers un hébergement cloud.',
            EtatProjet::Fini,
            '-6 months',
            '-2 months',
            $alice,
            [$emma],
        );

        // ------------------------------------------------------------
        // 3. Tâches
        // ------------------------------------------------------------
        // Projet "Refonte du site web"
        $this->creerTache($manager, $site, 'Réaliser les maquettes des pages principales', EtatTache::Fini, '-1 month', '-2 weeks', $chloe);
        $this->creerTache($manager, $site, 'Intégrer la page d\'accueil', EtatTache::EnCours, '-2 weeks', '+1 week', $david);
        $this->creerTache($manager, $site, 'Rédiger les contenus des pages services', EtatTache::PasCommence, '+1 week', '+1 month', $chloe);
        $this->creerTache($manager, $site, 'Optimiser le référencement (SEO)', EtatTache::PasCommence, '+1 month', '+2 months', null);

        // Projet "Application mobile"
        $this->creerTache($manager, $mobile, 'Définir les écrans de l\'application', EtatTache::Fini, '-4 months', '-3 months', $emma);
        $this->creerTache($manager, $mobile, 'Développer le module de rendez-vous', EtatTache::EnCours, '-3 months', '-2 weeks', $david);
        $this->creerTache($manager, $mobile, 'Publier sur les stores', EtatTache::PasCommence, '-2 weeks', '-1 week', $bruno);

        // Projet "Migration des serveurs"
        $this->creerTache($manager, $serveurs, 'Inventaire des serveurs existants', EtatTache::Fini, '-6 months', '-5 months', $emma);
        $this->creerTache($manager, $serveurs, 'Migration des bases de données', EtatTache::Fini, '-5 months', '-3 months', $emma);
        $this->creerTache($manager, $serveurs, 'Tests et mise en service', EtatTache::Fini, '-3 months', '-2 months', $alice);

        // Envoie tout à la base en une seule fois
        $manager->flush();
    }

    /**
     * @param string[] $roles
     */
    private function creerUser(ObjectManager $manager, string $nom, string $email, array $roles = []): User
    {
        $user = new User();
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setRoles($roles);
        // Le mot de passe est chiffré (haché) avant d'être enregistré
        $user->setPassword($this->hasher->hashPassword($user, self::MOT_DE_PASSE));

        $manager->persist($user);

        return $user;
    }

    /**
     * @param User[] $membres
     */
    private function creerProjet(
        ObjectManager $manager,
        string $nom,
        string $descriptif,
        EtatProjet $etat,
        string $debut,
        string $fin,
        User $chef,
        array $membres,
    ): Projet {
        $projet = new Projet();
        $projet->setNom($nom);
        $projet->setDescriptif($descriptif);
        $projet->setEtat($etat);
        $projet->setDateDebut(new \DateTimeImmutable($debut));
        $projet->setDateFin(new \DateTimeImmutable($fin));
        $projet->setChefDeProjet($chef);

        foreach ($membres as $membre) {
            $projet->addMembre($membre);
        }

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
        ?User $assigneA,
    ): void {
        $tache = new Tache();
        $tache->setDescription($description);
        $tache->setEtat($etat);
        $tache->setDateDebut(new \DateTimeImmutable($debut));
        $tache->setDateFin(new \DateTimeImmutable($fin));
        $tache->setAssigneA($assigneA);

        $projet->addTache($tache);   // relie la tâche au projet

        $manager->persist($tache);
    }
}