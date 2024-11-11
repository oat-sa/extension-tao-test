<?php

declare(strict_types=1);

namespace oat\taoTests\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\event\EventManager;
use oat\oatbox\reporting\Report;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\menu\SectionVisibilityFilter;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoTests\models\event\TestCreatedEvent;
use oat\taoTests\models\Translation\Listener\TestCreatedEventListener;
use oat\taoTests\models\user\TaoTestsRoles;

final class Version202409060743452141_taoTests extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new Test Translator Role, sync models and add new listener';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        AclProxy::applyRule($this->getRule());

        $this->addReport(Report::createSuccess('Applied rules for role ' . TaoTestsRoles::TEST_TRANSLATOR));

        /** @var SectionVisibilityFilter $sectionVisibilityFilter */
        $sectionVisibilityFilter = $this->getServiceManager()->get(SectionVisibilityFilter::SERVICE_ID);

        $sectionVisibilityFilter->showSectionByFeatureFlag(
            $sectionVisibilityFilter->createSectionPath(
                [
                    'manage_tests',
                    'test-translate'
                ]
            ),
            'FEATURE_FLAG_TRANSLATION_ENABLED'
        );
        $this->getServiceManager()->register(SectionVisibilityFilter::SERVICE_ID, $sectionVisibilityFilter);

        $this->addReport(
            Report::createSuccess('Hide test section for feature flag FEATURE_FLAG_TRANSLATION_ENABLED')
        );

        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->attach(
            TestCreatedEvent::class,
            [TestCreatedEventListener::class, 'populateTranslationProperties']
        );
        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);
    }

    public function down(Schema $schema): void
    {
        AclProxy::revokeRule($this->getRule());

        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->detach(
            TestCreatedEvent::class,
            [TestCreatedEventListener::class, 'populateTranslationProperties']
        );
        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);
    }

    private function getRule(): AccessRule
    {
        return new AccessRule(
            AccessRule::GRANT,
            TaoTestsRoles::TEST_TRANSLATOR,
            [
                'ext' => 'tao',
                'mod' => 'Translation'
            ]
        );
    }
}
