<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202151852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler ADD einreicher_id INT NOT NULL, CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
        $this->addSql('ALTER TABLE fehler ADD CONSTRAINT FK_2763D783D700B9F1 FOREIGN KEY (einreicher_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2763D783D700B9F1 ON fehler (einreicher_id)');
        $this->addSql('ALTER TABLE kommentar ADD einreicher_id INT NOT NULL');
        $this->addSql('ALTER TABLE kommentar ADD CONSTRAINT FK_74C17A16D700B9F1 FOREIGN KEY (einreicher_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_74C17A16D700B9F1 ON kommentar (einreicher_id)');
        $this->addSql('ALTER TABLE modul CHANGE tutor_id tutor_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler DROP FOREIGN KEY FK_2763D783D700B9F1');
        $this->addSql('DROP INDEX IDX_2763D783D700B9F1 ON fehler');
        $this->addSql('ALTER TABLE fehler DROP einreicher_id, CHANGE status status VARCHAR(1) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE kommentar DROP FOREIGN KEY FK_74C17A16D700B9F1');
        $this->addSql('DROP INDEX IDX_74C17A16D700B9F1 ON kommentar');
        $this->addSql('ALTER TABLE kommentar DROP einreicher_id');
        $this->addSql('ALTER TABLE modul CHANGE tutor_id tutor_id INT DEFAULT NULL');
    }
}
