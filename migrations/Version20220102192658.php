<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220102192658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D576088722503E5');
        $this->addSql('DROP INDEX UNIQ_9D576088722503E5 ON modul');
        $this->addSql('ALTER TABLE modul DROP skript_id');
        $this->addSql('ALTER TABLE skript ADD modul_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skript ADD CONSTRAINT FK_2CF1CCFB19952065 FOREIGN KEY (modul_id) REFERENCES modul (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CF1CCFB19952065 ON skript (modul_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE modul ADD skript_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088722503E5 FOREIGN KEY (skript_id) REFERENCES skript (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D576088722503E5 ON modul (skript_id)');
        $this->addSql('ALTER TABLE skript DROP FOREIGN KEY FK_2CF1CCFB19952065');
        $this->addSql('DROP INDEX UNIQ_2CF1CCFB19952065 ON skript');
        $this->addSql('ALTER TABLE skript DROP modul_id');
    }
}
