<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305133243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY `FK_1D1C63B3C1C5DC59`');
        $this->addSql('DROP INDEX IDX_1D1C63B3C1C5DC59 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP statut, DROP visite_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD statut TINYINT DEFAULT NULL, ADD visite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT `FK_1D1C63B3C1C5DC59` FOREIGN KEY (visite_id) REFERENCES visite (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3C1C5DC59 ON utilisateur (visite_id)');
    }
}
