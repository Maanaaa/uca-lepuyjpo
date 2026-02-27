<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227093322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CD5F057C9 FOREIGN KEY (journee_immersion_id) REFERENCES journee_immersion (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CD5F057C9 ON cours (journee_immersion_id)');
        $this->addSql('ALTER TABLE etudiant ADD departement_id INT NOT NULL');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_717E22E3CCF9E01E ON etudiant (departement_id)');
        $this->addSql('ALTER TABLE journee_immersion ADD CONSTRAINT FK_D0A8ECCDCCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_D0A8ECCDCCF9E01E ON journee_immersion (departement_id)');
        $this->addSql('ALTER TABLE visite ADD departement_id INT NOT NULL');
        $this->addSql('ALTER TABLE visite ADD CONSTRAINT FK_B09C8CBB7F72333D FOREIGN KEY (visiteur_id) REFERENCES visiteur (id)');
        $this->addSql('ALTER TABLE visite ADD CONSTRAINT FK_B09C8CBBCCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE visite ADD CONSTRAINT FK_B09C8CBBDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('CREATE INDEX IDX_B09C8CBB7F72333D ON visite (visiteur_id)');
        $this->addSql('CREATE INDEX IDX_B09C8CBBCCF9E01E ON visite (departement_id)');
        $this->addSql('CREATE INDEX IDX_B09C8CBBDDEAB1A3 ON visite (etudiant_id)');
        $this->addSql('ALTER TABLE visiteur ADD ville VARCHAR(255) NOT NULL, ADD departement VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CD5F057C9');
        $this->addSql('DROP INDEX IDX_FDCA8C9CD5F057C9 ON cours');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3CCF9E01E');
        $this->addSql('DROP INDEX IDX_717E22E3CCF9E01E ON etudiant');
        $this->addSql('ALTER TABLE etudiant DROP departement_id');
        $this->addSql('ALTER TABLE journee_immersion DROP FOREIGN KEY FK_D0A8ECCDCCF9E01E');
        $this->addSql('DROP INDEX IDX_D0A8ECCDCCF9E01E ON journee_immersion');
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBB7F72333D');
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBBCCF9E01E');
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBBDDEAB1A3');
        $this->addSql('DROP INDEX IDX_B09C8CBB7F72333D ON visite');
        $this->addSql('DROP INDEX IDX_B09C8CBBCCF9E01E ON visite');
        $this->addSql('DROP INDEX IDX_B09C8CBBDDEAB1A3 ON visite');
        $this->addSql('ALTER TABLE visite DROP departement_id');
        $this->addSql('ALTER TABLE visiteur DROP ville, DROP departement');
    }
}
