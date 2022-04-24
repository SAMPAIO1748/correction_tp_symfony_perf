<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220424110224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D34584665A');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D382EA2E54');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D34584665A FOREIGN KEY (product_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D382EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D382EA2E54');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D34584665A');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D382EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D34584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
    }
}
