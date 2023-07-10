<?php

declare(strict_types=1);

namespace oat\taoTests\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoTests\models\user\TaoTestsRoles;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202307100801342363_taoTests extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_RULES => [
            TaoTestsRoles::RESTRICTED_TEST_AUTHOR => [
                ['ext' => 'taoTests', 'mod' => 'Tests']
            ]
        ],
    ];
    public function getDescription(): string
    {
        return 'Add role access to restricted test author';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();
        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }

    public function down(Schema $schema): void
    {
        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_REVOKE,
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }
}
