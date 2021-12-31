<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211231201145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_modul (user_id INT NOT NULL, modul_id INT NOT NULL, INDEX IDX_7F959BD0A76ED395 (user_id), INDEX IDX_7F959BD019952065 (modul_id), PRIMARY KEY(user_id, modul_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_modul ADD CONSTRAINT FK_7F959BD0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_modul ADD CONSTRAINT FK_7F959BD019952065 FOREIGN KEY (modul_id) REFERENCES modul (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fehler CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D57608886238C2B');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D576088208F64F1');
        $this->addSql('DROP INDEX UNIQ_9D57608886238C2B ON modul');
        $this->addSql('ALTER TABLE modul CHANGE aktuelles_skript_id skript_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088722503E5 FOREIGN KEY (skript_id) REFERENCES skript (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D576088722503E5 ON modul (skript_id)');
        $this->addSql('ALTER TABLE skript DROP FOREIGN KEY FK_2CF1CCFB19952065');
        $this->addSql('DROP INDEX IDX_2CF1CCFB19952065 ON skript');
        $this->addSql('ALTER TABLE skript DROP modul_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_modul');
        $this->addSql('ALTER TABLE fehler CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D576088722503E5');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D576088208F64F1');
        $this->addSql('DROP INDEX UNIQ_9D576088722503E5 ON modul');
        $this->addSql('ALTER TABLE modul CHANGE skript_id aktuelles_skript_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D57608886238C2B FOREIGN KEY (aktuelles_skript_id) REFERENCES skript (id)');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D57608886238C2B ON modul (aktuelles_skript_id)');
        $this->addSql('ALTER TABLE skript ADD modul_id INT NOT NULL');
        $this->addSql('ALTER TABLE skript ADD CONSTRAINT FK_2CF1CCFB19952065 FOREIGN KEY (modul_id) REFERENCES modul (id)');
        $this->addSql('CREATE INDEX IDX_2CF1CCFB19952065 ON skript (modul_id)');
    }
}