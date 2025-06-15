<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250614205645 extends AbstractMigration
{
    public function up(Schema $schema): void
    {

        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL');

        $this->addSql('UPDATE user SET created_at = NOW() WHERE created_at IS NULL');
        

        $this->addSql('ALTER TABLE user MODIFY created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP created_at');
    }
}