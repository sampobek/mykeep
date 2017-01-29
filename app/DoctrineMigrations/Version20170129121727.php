<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170129121727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Orange', 'fbd5ae')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Yellow', 'fcfcc6')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Green', 'e2ff9e')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Turquoise', 'aee8fb')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Blue', 'aec2fb')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Purple', 'd9c4ed')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Pink', 'fcc6d4')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Red', 'faaf95')");
        $this->addSql("INSERT INTO color (title, hex) VALUES ('Dark Green', 'b2ff9e')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM color WHERE title = 'Orange' AND hex = 'fbd5ae'");
        $this->addSql("DELETE FROM color WHERE title = 'Yellow' AND hex = 'fcfcc6'");
        $this->addSql("DELETE FROM color WHERE title = 'Green' AND hex = 'e2ff9e'");
        $this->addSql("DELETE FROM color WHERE title = 'Turquoise' AND hex = 'aee8fb'");
        $this->addSql("DELETE FROM color WHERE title = 'Blue' AND hex = 'aec2fb'");
        $this->addSql("DELETE FROM color WHERE title = 'Purple' AND hex = 'd9c4ed'");
        $this->addSql("DELETE FROM color WHERE title = 'Pink' AND hex = 'fcc6d4'");
        $this->addSql("DELETE FROM color WHERE title = 'Red' AND hex = 'faaf95'");
        $this->addSql("DELETE FROM color WHERE title = 'Dark Green' AND hex = 'b2ff9e'");
    }
}
