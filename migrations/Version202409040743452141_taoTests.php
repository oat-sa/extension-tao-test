<?php

declare(strict_types=1);

namespace oat\taoTests\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoTests\models\user\TaoTestsRoles;

final class Version202409040743452141_taoTests extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new Test Translator Role';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        AclProxy::applyRule($this->getRule());

        $this->addReport(Report::createSuccess('Applied rules for role ' . TaoTestsRoles::TEST_TRANSLATOR));
    }

    public function down(Schema $schema): void
    {
        AclProxy::revokeRule($this->getRule());
    }

    private function getRule(): AccessRule
    {
        return new AccessRule(
            AccessRule::GRANT,
            TaoTestsRoles::TEST_TRANSLATOR,
            [
                'ext' => 'taoTests',
                'mod' => 'Translation'
            ]
        );
    }
}
