<?php

declare(strict_types=1);

namespace oat\taoTests\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoTests\models\user\TaoTestsRoles;
use taoTests_actions_TestExport;
use taoTests_actions_TestImport;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202306141424132365_taoTests extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_RULES => [
            TaoTestsRoles::TEST_EXPORTER => [
                ['ext' => 'taoTests', 'mod' => 'TestExport']
            ],
            TaoTestsRoles::TEST_IMPORTER => [
                ['ext' => 'taoTests', 'mod' => 'TestImport']
            ],
        ],
    ];

    public function getDescription(): string
    {
        return 'Add access rules to new roles for tests';
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
