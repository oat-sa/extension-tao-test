<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoTests\models\Translation\Form\Modifier;

use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\form\Modifier\AbstractFormModifier;
use oat\tao\model\TaoOntology;
use oat\taoTests\models\TaoTestOntology;
use tao_helpers_form_Form as Form;
use tao_helpers_Uri;

class TranslationFormModifier extends AbstractFormModifier
{
    private FeatureFlagCheckerInterface $featureFlagChecker;

    public function __construct(FeatureFlagCheckerInterface $featureFlagChecker)
    {
        $this->featureFlagChecker = $featureFlagChecker;
    }

    public function modify(Form $form, array $options = []): void
    {
        if (!$this->featureFlagChecker->isEnabled('FEATURE_TRANSLATION_ENABLED')) {
            $form->removeElement(tao_helpers_Uri::encode(TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION));
        }

        $translationTypeValue = $form->getValue(tao_helpers_Uri::encode(TaoOntology::PROPERTY_TRANSLATION_TYPE));

        if (
            empty($translationTypeValue)
            || $translationTypeValue === TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL
        ) {
            $form->removeElement(tao_helpers_Uri::encode(TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION));
        }
    }
}
