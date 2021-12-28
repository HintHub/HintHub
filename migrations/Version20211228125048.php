<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211228125048 extends AbstractMigration
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
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D576088AFC2B591');
        $this->addSql('DROP INDEX IDX_9D576088AFC2B591 ON modul');
        $this->addSql('ALTER TABLE modul CHANGE module_id tutor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9D576088208F64F1 ON modul (tutor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_modul');
        $this->addSql('ALTER TABLE fehler CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE modul DROP FOREIGN KEY FK_9D576088208F64F1');
        $this->addSql('DROP INDEX IDX_9D576088208F64F1 ON modul');
        $this->addSql('ALTER TABLE modul CHANGE tutor_id module_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modul ADD CONSTRAINT FK_9D576088AFC2B591 FOREIGN KEY (module_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9D576088AFC2B591 ON modul (module_id)');
    }
}
