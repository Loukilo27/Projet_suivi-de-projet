<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261124210139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet_membre DROP FOREIGN KEY `FK_D41D4B7FA76ED395`');
        $this->addSql('ALTER TABLE projet_membre DROP FOREIGN KEY `FK_D41D4B7FC18272`');
        $this->addSql('DROP TABLE projet_membre');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY `FK_50159CA92C9E1458`');
        $this->addSql('ALTER TABLE projet DROP etat, CHANGE nom nom VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA92C9E1458 FOREIGN KEY (chef_de_projet_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY `FK_93872075BB1B0F33`');
        $this->addSql('DROP INDEX IDX_93872075BB1B0F33 ON tache');
        $this->addSql('ALTER TABLE tache DROP assigne_a_id');
        $this->addSql('ALTER TABLE user ADD derniere_connexion DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projet_membre (projet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D41D4B7FA76ED395 (user_id), INDEX IDX_D41D4B7FC18272 (projet_id), PRIMARY KEY (projet_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE projet_membre ADD CONSTRAINT `FK_D41D4B7FA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_membre ADD CONSTRAINT `FK_D41D4B7FC18272` FOREIGN KEY (projet_id) REFERENCES projet (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA92C9E1458');
        $this->addSql('ALTER TABLE projet ADD etat VARCHAR(20) NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT `FK_50159CA92C9E1458` FOREIGN KEY (chef_de_projet_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE tache ADD assigne_a_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT `FK_93872075BB1B0F33` FOREIGN KEY (assigne_a_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_93872075BB1B0F33 ON tache (assigne_a_id)');
        $this->addSql('ALTER TABLE user DROP derniere_connexion');
    }
}
