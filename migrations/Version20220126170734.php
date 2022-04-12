<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220126170734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE benachrichtigung ADD user_id INT NOT NULL, ADD fehler_id INT NOT NULL, ADD text LONGTEXT DEFAULT NULL, ADD datum_erstellt DATETIME NOT NULL, ADD datum_geschlossen DATETIME DEFAULT NULL, ADD datum_letzte_aenderung DATETIME NOT NULL');
        $this->addSql('ALTER TABLE benachrichtigung ADD CONSTRAINT FK_3EA84296A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE benachrichtigung ADD CONSTRAINT FK_3EA842969FF003F4 FOREIGN KEY (fehler_id) REFERENCES fehler (id)');
        $this->addSql('CREATE INDEX IDX_3EA84296A76ED395 ON benachrichtigung (user_id)');
        $this->addSql('CREATE INDEX IDX_3EA842969FF003F4 ON benachrichtigung (fehler_id)');
        $this->addSql('ALTER TABLE fehler CHANGE status status ENUM(\'CLOSED\', \'ESCALATED\', \'OPEN\', \'REJECTED\', \'WAITING\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE benachrichtigung DROP FOREIGN KEY FK_3EA84296A76ED395');
        $this->addSql('ALTER TABLE benachrichtigung DROP FOREIGN KEY FK_3EA842969FF003F4');
        $this->addSql('DROP INDEX IDX_3EA84296A76ED395 ON benachrichtigung');
        $this->addSql('DROP INDEX IDX_3EA842969FF003F4 ON benachrichtigung');
        $this->addSql('ALTER TABLE benachrichtigung DROP user_id, DROP fehler_id, DROP text, DROP datum_erstellt, DROP datum_geschlossen, DROP datum_letzte_aenderung');
        $this->addSql('ALTER TABLE fehler CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
