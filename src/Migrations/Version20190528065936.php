<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190528065936 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE opening (id INT AUTO_INCREMENT NOT NULL, referent VARCHAR(60) NOT NULL, places INT NOT NULL, open DATETIME NOT NULL, close DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opening_user (opening_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6B161062464F291F (opening_id), INDEX IDX_6B161062A76ED395 (user_id), PRIMARY KEY(opening_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(320) NOT NULL, name VARCHAR(60) NOT NULL, password VARCHAR(60) NOT NULL, student_id VARCHAR(11) NOT NULL, token VARCHAR(32) NOT NULL, active INT NOT NULL, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE opening_user ADD CONSTRAINT FK_6B161062464F291F FOREIGN KEY (opening_id) REFERENCES opening (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE opening_user ADD CONSTRAINT FK_6B161062A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE opening_user DROP FOREIGN KEY FK_6B161062464F291F');
        $this->addSql('ALTER TABLE opening_user DROP FOREIGN KEY FK_6B161062A76ED395');
        $this->addSql('DROP TABLE opening');
        $this->addSql('DROP TABLE opening_user');
        $this->addSql('DROP TABLE user');
    }
}
