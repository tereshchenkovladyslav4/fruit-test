<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322131305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE symfony_demo_fruit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, genus VARCHAR(255) DEFAULT NULL, plant_order VARCHAR(255) DEFAULT NULL, calories INTEGER DEFAULT NULL, carbohydrates INTEGER DEFAULT NULL, fat INTEGER DEFAULT NULL, protein INTEGER DEFAULT NULL, sugar INTEGER DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE symfony_demo_fruit_like (fruit_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(fruit_id, user_id), CONSTRAINT FK_D548AFCABAC115F0 FOREIGN KEY (fruit_id) REFERENCES symfony_demo_fruit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D548AFCAA76ED395 FOREIGN KEY (user_id) REFERENCES symfony_demo_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D548AFCABAC115F0 ON symfony_demo_fruit_like (fruit_id)');
        $this->addSql('CREATE INDEX IDX_D548AFCAA76ED395 ON symfony_demo_fruit_like (user_id)');
        $this->addSql('CREATE TABLE symfony_demo_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8FB094A1F85E0677 ON symfony_demo_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8FB094A1E7927C74 ON symfony_demo_user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE symfony_demo_fruit');
        $this->addSql('DROP TABLE symfony_demo_fruit_like');
        $this->addSql('DROP TABLE symfony_demo_user');
    }
}
