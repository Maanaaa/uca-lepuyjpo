<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305132808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY `FK_717E22E3CCF9E01E`');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('ALTER TABLE utilisateur ADD statut TINYINT DEFAULT NULL, ADD visite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3C1C5DC59 FOREIGN KEY (visite_id) REFERENCES visite (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3C1C5DC59 ON utilisateur (visite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_uca1400_ai_ci`, departement_id INT NOT NULL, INDEX IDX_717E22E3CCF9E01E (departement_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_uca1400_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT `FK_717E22E3CCF9E01E` FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3C1C5DC59');
        $this->addSql('DROP INDEX IDX_1D1C63B3C1C5DC59 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP statut, DROP visite_id');
    }
}
