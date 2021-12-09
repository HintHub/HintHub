<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211130223552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fehler (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(1) NOT NULL, seite INT NOT NULL, datum_erstellt DATETIME NOT NULL, datum_geschlossen DATETIME NOT NULL, datum_letzte_aenderung DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fehler_fehler (fehler_source INT NOT NULL, fehler_target INT NOT NULL, INDEX IDX_8BE276E2F30BDE12 (fehler_source), INDEX IDX_8BE276E2EAEE8E9D (fehler_target), PRIMARY KEY(fehler_source, fehler_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kommentar (id INT AUTO_INCREMENT NOT NULL, fehler_id INT NOT NULL, text LONGTEXT NOT NULL, datum_erstellt DATETIME NOT NULL, datum_geschlossen DATETIME NOT NULL, datum_letzte_aenderung DATETIME NOT NULL, INDEX IDX_74C17A169FF003F4 (fehler_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modul (id INT AUTO_INCREMENT NOT NULL, aktuelles_skript_id INT DEFAULT NULL, tutor_id INT DEFAULT NULL, kuerze�l VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9D57608886238C2B (aktuelles_skript_id), INDEX IDX_9D576088208F64F1 (tutor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skript (id INT AUTO_INCREMENT NOT NULL, modul_id INT NOT NULL, version INT NOT NULL, INDEX IDX_2CF1CCFB19952065 (modul_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fehler_fehler ADD CONSTRAINT FK_8BE276E2F30BDE12 FOREIGN KEY (fehler_source) REFERENCES fehler (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fehler_fehler ADD CONSTRAINT FK_8BE276E2EAEE8E9D FOREIGN KEY (fehler_target) REFERENCES fehler (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kommentar ADD CONSTRAINT FK_74C17A169FF003F4 FOREIGN KEY (fehler_id) REFERENCES fehler (id)');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D57608886238C2B FOREIGN KEY (aktuelles_skript_id) REFERENCES skript (id)');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE skript ADD CONSTRAINT FK_2CF1CCFB19952065 FOREIGN KEY (modul_id) REFERENCES modul (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fehler_fehler DROP FOREIGN KEY FK_8BE276E2F30BDE12');
        $this->addSql('ALTER TABLE fehler_fehler DROP FOREIGN KEY FK_8BE276E2EAEE8E9D');
        $this->addSql('ALTER TABLE kommentar DROP FOREIGN KEY FK_74C17A169FF003F4');
        $this->addSql('ALTER TABLE skript DROP FOREIGN KEY FK_2CF1CCFB19952065');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D57608886238C2B');
        $this->addSql('DROP TABLE fehler');
        $this->addSql('DROP TABLE fehler_fehler');
        $this->addSql('DROP TABLE kommentar');
        $this->addSql('DROP TABLE modul');
        $this->addSql('DROP TABLE skript');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
    }
}
