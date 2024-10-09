<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20210414085417 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Exception
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE markets ADD warehouse_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE markets ADD CONSTRAINT FK_E5F9BCF6477777CC FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Exception
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE markets DROP FOREIGN KEY FK_E5F9BCF6477777CC');
        $this->addSql('ALTER TABLE markets DROP warehouse_id');

    }
}
