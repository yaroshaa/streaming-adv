<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20210329094320 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_variants CHANGE remote_id remote_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE remote_id remote_id VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_variants CHANGE remote_id remote_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE remote_id remote_id BIGINT NOT NULL');
    }
}
