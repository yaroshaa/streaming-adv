<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20210202095248 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_variants DROP FOREIGN KEY FK_782839764584665A');
        $this->addSql('DROP TABLE order_product_variants');
        $this->addSql('DROP TABLE products');
        $this->addSql('ALTER TABLE orders DROP city');
        $this->addSql('DROP INDEX product_variant_product_id_foreign ON product_variants');
        $this->addSql('ALTER TABLE product_variants DROP product_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_product_variants (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, order_id BIGINT UNSIGNED DEFAULT NULL, product_variant_id BIGINT UNSIGNED DEFAULT NULL, qty INT NOT NULL, profit DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX order_product_variant_order_id_foreign (order_id), INDEX order_product_variant_product_variant_id_foreign (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE products (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, remote_id BIGINT NOT NULL, INDEX product_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_product_variants ADD CONSTRAINT FK_1A3E2D6A8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE order_product_variants ADD CONSTRAINT FK_1A3E2D6AA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variants (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE orders ADD city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product_variants ADD product_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_variants ADD CONSTRAINT FK_782839764584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX product_variant_product_id_foreign ON product_variants (product_id)');
    }
}
