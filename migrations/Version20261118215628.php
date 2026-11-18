<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261118215628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, descriptif LONGTEXT DEFAULT NULL, etat VARCHAR(20) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, chef_de_projet_id INT NOT NULL, INDEX IDX_50159CA92C9E1458 (chef_de_projet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projet_membre (projet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D41D4B7FC18272 (projet_id), INDEX IDX_D41D4B7FA76ED395 (user_id), PRIMARY KEY (projet_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, etat VARCHAR(20) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, projet_id INT NOT NULL, assigne_a_id INT DEFAULT NULL, INDEX IDX_93872075C18272 (projet_id), INDEX IDX_93872075BB1B0F33 (assigne_a_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA92C9E1458 FOREIGN KEY (chef_de_projet_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE projet_membre ADD CONSTRAINT FK_D41D4B7FC18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_membre ADD CONSTRAINT FK_D41D4B7FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075BB1B0F33 FOREIGN KEY (assigne_a_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA92C9E1458');
        $this->addSql('ALTER TABLE projet_membre DROP FOREIGN KEY FK_D41D4B7FC18272');
        $this->addSql('ALTER TABLE projet_membre DROP FOREIGN KEY FK_D41D4B7FA76ED395');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075C18272');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075BB1B0F33');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE projet_membre');
        $this->addSql('DROP TABLE tache');
    }
}
