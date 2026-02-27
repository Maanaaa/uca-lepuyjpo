<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227095837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visiteur ADD departement_id INT NOT NULL, DROP departement');
        $this->addSql('ALTER TABLE visiteur ADD CONSTRAINT FK_4EA587B8CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_4EA587B8CCF9E01E ON visiteur (departement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visiteur DROP FOREIGN KEY FK_4EA587B8CCF9E01E');
        $this->addSql('DROP INDEX IDX_4EA587B8CCF9E01E ON visiteur');
        $this->addSql('ALTER TABLE visiteur ADD departement VARCHAR(255) NOT NULL, DROP departement_id');
    }
}
