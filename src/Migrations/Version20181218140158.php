<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181218140158 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(320) NOT NULL, ADD name VARCHAR(60) NOT NULL, ADD password VARCHAR(60) NOT NULL, ADD student_id VARCHAR(11) NOT NULL, ADD token VARCHAR(32) NOT NULL, ADD active INT NOT NULL, ADD status INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(320) NOT NULL COLLATE utf8mb4_unicode_ci, ine VARCHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, password VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, privilege INT NOT NULL, token INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user DROP email, DROP name, DROP password, DROP student_id, DROP token, DROP active, DROP status');
    }
}
