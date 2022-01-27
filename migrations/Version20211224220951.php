<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211224220951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler ADD skript_id INT NOT NULL, CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
        $this->addSql('ALTER TABLE fehler ADD CONSTRAINT FK_2763D783722503E5 FOREIGN KEY (skript_id) REFERENCES skript (id)');
        $this->addSql('CREATE INDEX IDX_2763D783722503E5 ON fehler (skript_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler DROP FOREIGN KEY FK_2763D783722503E5');
        $this->addSql('DROP INDEX IDX_2763D783722503E5 ON fehler');
        $this->addSql('ALTER TABLE fehler DROP skript_id, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
