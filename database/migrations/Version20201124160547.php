<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20201124160547 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE currencies (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, INDEX currency_remote_id_idx (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, remote_id BIGINT NOT NULL, INDEX customer_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_rates (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, from_id BIGINT UNSIGNED DEFAULT NULL, to_id BIGINT UNSIGNED DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX exchange_rate_from_id_foreign (from_id), INDEX exchange_rate_to_id_foreign (to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE markets (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, remote_id BIGINT NOT NULL, INDEX market_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_product_variants (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, order_id BIGINT UNSIGNED DEFAULT NULL, product_variant_id BIGINT UNSIGNED DEFAULT NULL, INDEX order_product_variant_order_id_foreign (order_id), INDEX order_product_variant_product_variant_id_foreign (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_statuses (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, remote_id BIGINT NOT NULL, INDEX order_status_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, currency_id BIGINT UNSIGNED DEFAULT NULL, customer_id BIGINT UNSIGNED DEFAULT NULL, market_id BIGINT UNSIGNED DEFAULT NULL, order_status_id BIGINT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, remote_id BIGINT NOT NULL, INDEX orders_currency_id_foreign (currency_id), INDEX orders_customer_id_foreign (customer_id), INDEX orders_market_id_foreign (market_id), INDEX orders_order_status_id_foreign (order_status_id), INDEX currency_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variants (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, product_id BIGINT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, weight DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, remote_id BIGINT NOT NULL, INDEX product_variant_product_id_foreign (product_id), INDEX product_variant_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, remote_id BIGINT NOT NULL, INDEX product_remote_id_idx (remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exchange_rates ADD CONSTRAINT FK_5AE3E77478CED90B FOREIGN KEY (from_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE exchange_rates ADD CONSTRAINT FK_5AE3E77430354A65 FOREIGN KEY (to_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE order_product_variants ADD CONSTRAINT FK_1A3E2D6A8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_product_variants ADD CONSTRAINT FK_1A3E2D6AA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variants (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE38248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE622F3F37 FOREIGN KEY (market_id) REFERENCES markets (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEED7707B45 FOREIGN KEY (order_status_id) REFERENCES order_statuses (id)');
        $this->addSql('ALTER TABLE product_variants ADD CONSTRAINT FK_782839764584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exchange_rates DROP FOREIGN KEY FK_5AE3E77478CED90B');
        $this->addSql('ALTER TABLE exchange_rates DROP FOREIGN KEY FK_5AE3E77430354A65');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE38248176');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE622F3F37');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEED7707B45');
        $this->addSql('ALTER TABLE order_product_variants DROP FOREIGN KEY FK_1A3E2D6A8D9F6D38');
        $this->addSql('ALTER TABLE order_product_variants DROP FOREIGN KEY FK_1A3E2D6AA80EF684');
        $this->addSql('ALTER TABLE product_variants DROP FOREIGN KEY FK_782839764584665A');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE exchange_rates');
        $this->addSql('DROP TABLE markets');
        $this->addSql('DROP TABLE order_product_variants');
        $this->addSql('DROP TABLE order_statuses');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE product_variants');
        $this->addSql('DROP TABLE products');
    }
}
