<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260312144714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inscription_immersion (id INT AUTO_INCREMENT NOT NULL, inscrit_le DATETIME NOT NULL, visiteur_id INT NOT NULL, journee_immersion_id INT NOT NULL, INDEX IDX_BC9B90AA7F72333D (visiteur_id), INDEX IDX_BC9B90AAD5F057C9 (journee_immersion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE inscription_immersion ADD CONSTRAINT FK_BC9B90AA7F72333D FOREIGN KEY (visiteur_id) REFERENCES visiteur (id)');
        $this->addSql('ALTER TABLE inscription_immersion ADD CONSTRAINT FK_BC9B90AAD5F057C9 FOREIGN KEY (journee_immersion_id) REFERENCES journee_immersion (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription_immersion DROP FOREIGN KEY FK_BC9B90AA7F72333D');
        $this->addSql('ALTER TABLE inscription_immersion DROP FOREIGN KEY FK_BC9B90AAD5F057C9');
        $this->addSql('DROP TABLE inscription_immersion');
    }
}
