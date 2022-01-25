<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220125215955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE benachrichtigung (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fehler DROP FOREIGN KEY FK_2763D783D700B9F1');
        $this->addSql('ALTER TABLE fehler CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
        $this->addSql('ALTER TABLE fehler ADD CONSTRAINT FK_2763D783D700B9F1 FOREIGN KEY (einreicher_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE kommentar DROP FOREIGN KEY FK_74C17A16D700B9F1');
        $this->addSql('ALTER TABLE kommentar ADD CONSTRAINT FK_74C17A16D700B9F1 FOREIGN KEY (einreicher_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE skript DROP FOREIGN KEY FK_2CF1CCFB19952065');
        $this->addSql('ALTER TABLE skript ADD CONSTRAINT FK_2CF1CCFB19952065 FOREIGN KEY (modul_id) REFERENCES modul (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE benachrichtigung');
        $this->addSql('ALTER TABLE fehler DROP FOREIGN KEY FK_2763D783D700B9F1');
        $this->addSql('ALTER TABLE fehler CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fehler ADD CONSTRAINT FK_2763D783D700B9F1 FOREIGN KEY (einreicher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE kommentar DROP FOREIGN KEY FK_74C17A16D700B9F1');
        $this->addSql('ALTER TABLE kommentar ADD CONSTRAINT FK_74C17A16D700B9F1 FOREIGN KEY (einreicher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE skript DROP FOREIGN KEY FK_2CF1CCFB19952065');
        $this->addSql('ALTER TABLE skript ADD CONSTRAINT FK_2CF1CCFB19952065 FOREIGN KEY (modul_id) REFERENCES modul (id)');
    }
}
