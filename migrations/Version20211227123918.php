<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211227123918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler CHANGE einreicher_id einreicher_id INT DEFAULT NULL, CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
        $this->addSql('ALTER TABLE kommentar CHANGE einreicher_id einreicher_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler CHANGE einreicher_id einreicher_id INT NOT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE kommentar CHANGE einreicher_id einreicher_id INT NOT NULL');
    }
}
