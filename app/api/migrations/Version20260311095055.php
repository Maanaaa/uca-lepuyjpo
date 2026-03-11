<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260311095055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE push_subscription (id INT AUTO_INCREMENT NOT NULL, endpoint LONGTEXT DEFAULT NULL, p256dh VARCHAR(255) DEFAULT NULL, auth VARCHAR(255) DEFAULT NULL, utilisateur_id INT DEFAULT NULL, INDEX IDX_562830F3FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE push_subscription ADD CONSTRAINT FK_562830F3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE push_subscription DROP FOREIGN KEY FK_562830F3FB88E14F');
        $this->addSql('DROP TABLE push_subscription');
    }
}
