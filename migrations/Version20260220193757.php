<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260220193757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE category category VARCHAR(50) DEFAULT NULL, CHANGE status status VARCHAR(20) DEFAULT \'pending\' NOT NULL, CHANGE location location VARCHAR(100) DEFAULT NULL, CHANGE `condition` `condition` VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product CHANGE image image VARCHAR(255) DEFAULT \'NULL\', CHANGE category category VARCHAR(50) DEFAULT \'NULL\', CHANGE status status VARCHAR(20) DEFAULT \'\'\'pending\'\'\' NOT NULL, CHANGE location location VARCHAR(100) DEFAULT \'NULL\', CHANGE `condition` `condition` VARCHAR(20) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` DROP created_at, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE first_name first_name VARCHAR(255) DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\'');
    }
}
