<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20201212100928 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_product_variants ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE product_variants DROP price, DROP sale_price');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_product_variants DROP price');
        $this->addSql('ALTER TABLE product_variants ADD price DOUBLE PRECISION NOT NULL, ADD sale_price DOUBLE PRECISION DEFAULT NULL');
    }
}
