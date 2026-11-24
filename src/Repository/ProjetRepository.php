<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Les requêtes sur la table "projet".
 * Méthodes déjà disponibles sans rien écrire : find(), findAll(), findBy(), findOneBy().
 *
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    /**
     * Projets dont la période contient la date du jour (cahier des charges 7.1).
     * Triés par date de fin, chef de projet chargé dans la même requête.
     *
     * @return Projet[]
     */
    public function findEnCoursAujourdhui(): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('chef')
            ->join('p.chefDeProjet', 'chef')
            ->where('p.dateDebut <= :aujourdhui')
            ->andWhere('p.dateFin >= :aujourdhui')
            ->setParameter('aujourdhui', new \DateTimeImmutable('today'), Types::DATE_IMMUTABLE)
            ->orderBy('p.dateFin', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
