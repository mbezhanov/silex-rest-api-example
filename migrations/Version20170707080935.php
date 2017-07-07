<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170707080935 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE diary (id INTEGER NOT NULL, meal_id INTEGER NOT NULL, food_id INTEGER NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_917BEDE2639666D6 ON diary (meal_id)');
        $this->addSql('CREATE INDEX IDX_917BEDE2BA8E87C4 ON diary (food_id)');
        $this->addSql('CREATE TABLE meal (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP INDEX IDX_D43829F7A23B42D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__food AS SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein, sugar FROM food');
        $this->addSql('DROP TABLE food');
        $this->addSql('CREATE TABLE food (id INTEGER NOT NULL, manufacturer_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, servingSize VARCHAR(255) NOT NULL COLLATE BINARY, calories INTEGER NOT NULL, carbs INTEGER NOT NULL, fat INTEGER NOT NULL, protein INTEGER NOT NULL, sugar INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_D43829F7A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO food (id, manufacturer_id, name, servingSize, calories, carbs, fat, protein, sugar) SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein, sugar FROM __temp__food');
        $this->addSql('DROP TABLE __temp__food');
        $this->addSql('CREATE INDEX IDX_D43829F7A23B42D ON food (manufacturer_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE diary');
        $this->addSql('DROP TABLE meal');
        $this->addSql('DROP INDEX IDX_D43829F7A23B42D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__food AS SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein, sugar FROM food');
        $this->addSql('DROP TABLE food');
        $this->addSql('CREATE TABLE food (id INTEGER NOT NULL, manufacturer_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, servingSize VARCHAR(255) NOT NULL, calories INTEGER NOT NULL, carbs INTEGER NOT NULL, fat INTEGER NOT NULL, protein INTEGER NOT NULL, sugar INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO food (id, manufacturer_id, name, servingSize, calories, carbs, fat, protein, sugar) SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein, sugar FROM __temp__food');
        $this->addSql('DROP TABLE __temp__food');
        $this->addSql('CREATE INDEX IDX_D43829F7A23B42D ON food (manufacturer_id)');
    }
}
