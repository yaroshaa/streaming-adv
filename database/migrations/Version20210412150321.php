<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20210412150321 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Exception
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE holiday_events (
                        id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, 
                        title VARCHAR(255) NOT NULL,
                        date DATETIME DEFAULT NULL, 
                        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 
                        COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE holiday_events');
    }
}