<?php

declare(strict_types=1);

namespace oat\taoTests\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoTests\models\user\TaoTestsRoles;

final class Version202506050743452143_taoTests extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new Test Manager Role to access tests preview';
    }

    public function up(Schema $schema): void
    {
        AclProxy::applyRule($this->getRule());
    }

    public function down(Schema $schema): void
    {
        AclProxy::revokeRule($this->getRule());
    }

    private function getRule(): AccessRule
    {
        return new AccessRule(
            AccessRule::GRANT,
            TaoTestsRoles::TEST_AUTHOR,
            ['ext' => 'taoTests', 'mod' => 'Tests', 'act' => 'preview']
        );
    }
}
