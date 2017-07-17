<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170717082743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__profile AS SELECT id, username, password FROM profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('CREATE TABLE profile (id INTEGER NOT NULL, username VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO profile (id, username, password) SELECT id, username, password FROM __temp__profile');
        $this->addSql('DROP TABLE __temp__profile');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FF85E0677 ON profile (username)');
        $this->addSql('DROP INDEX IDX_D43829F7A23B42D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__food AS SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein FROM food');
        $this->addSql('DROP TABLE food');
        $this->addSql('CREATE TABLE food (id INTEGER NOT NULL, manufacturer_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, servingSize VARCHAR(255) NOT NULL COLLATE BINARY, calories INTEGER NOT NULL, carbs INTEGER NOT NULL, fat INTEGER NOT NULL, protein INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_D43829F7A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO food (id, manufacturer_id, name, servingSize, calories, carbs, fat, protein) SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein FROM __temp__food');
        $this->addSql('DROP TABLE __temp__food');
        $this->addSql('CREATE INDEX IDX_D43829F7A23B42D ON food (manufacturer_id)');
        $this->addSql('DROP INDEX IDX_917BEDE2BA8E87C4');
        $this->addSql('DROP INDEX IDX_917BEDE2639666D6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__diary AS SELECT id, meal_id, food_id, date, quantity FROM diary');
        $this->addSql('DROP TABLE diary');
        $this->addSql('CREATE TABLE diary (id INTEGER NOT NULL, meal_id INTEGER NOT NULL, food_id INTEGER NOT NULL, date DATE NOT NULL, quantity INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_917BEDE2639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_917BEDE2BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO diary (id, meal_id, food_id, date, quantity) SELECT id, meal_id, food_id, date, quantity FROM __temp__diary');
        $this->addSql('DROP TABLE __temp__diary');
        $this->addSql('CREATE INDEX IDX_917BEDE2BA8E87C4 ON diary (food_id)');
        $this->addSql('CREATE INDEX IDX_917BEDE2639666D6 ON diary (meal_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_917BEDE2639666D6');
        $this->addSql('DROP INDEX IDX_917BEDE2BA8E87C4');
        $this->addSql('CREATE TEMPORARY TABLE __temp__diary AS SELECT id, meal_id, food_id, date, quantity FROM diary');
        $this->addSql('DROP TABLE diary');
        $this->addSql('CREATE TABLE diary (id INTEGER NOT NULL, meal_id INTEGER NOT NULL, food_id INTEGER NOT NULL, date DATE NOT NULL, quantity INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO diary (id, meal_id, food_id, date, quantity) SELECT id, meal_id, food_id, date, quantity FROM __temp__diary');
        $this->addSql('DROP TABLE __temp__diary');
        $this->addSql('CREATE INDEX IDX_917BEDE2639666D6 ON diary (meal_id)');
        $this->addSql('CREATE INDEX IDX_917BEDE2BA8E87C4 ON diary (food_id)');
        $this->addSql('DROP INDEX IDX_D43829F7A23B42D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__food AS SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein FROM food');
        $this->addSql('DROP TABLE food');
        $this->addSql('CREATE TABLE food (id INTEGER NOT NULL, manufacturer_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, servingSize VARCHAR(255) NOT NULL, calories INTEGER NOT NULL, carbs INTEGER NOT NULL, fat INTEGER NOT NULL, protein INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO food (id, manufacturer_id, name, servingSize, calories, carbs, fat, protein) SELECT id, manufacturer_id, name, servingSize, calories, carbs, fat, protein FROM __temp__food');
        $this->addSql('DROP TABLE __temp__food');
        $this->addSql('CREATE INDEX IDX_D43829F7A23B42D ON food (manufacturer_id)');
        $this->addSql('DROP INDEX UNIQ_8157AA0FF85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__profile AS SELECT id, username, password FROM profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('CREATE TABLE profile (id INTEGER NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO profile (id, username, password) SELECT id, username, password FROM __temp__profile');
        $this->addSql('DROP TABLE __temp__profile');
    }
}
