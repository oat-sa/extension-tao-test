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
 * Copyright (c) 2018 Open Assessment Technologies SA ;
 */

/**
 * Test runner entry point
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoTests/runner/runnerComponent',
], function(_, runnerComponentFactory) {
    'use strict';

    var runnerController = {
        start : function start(config){

            console.log(config);

            var testRunnerConfig = {
                test : {
                    definition : config.testDefinition,
                    compilation: config.testCompilation,
                    serviceCallId  : config.serviceCallId
                },
                providers: {
                    runner : {
                        available : config.providers,
                        selected  : config.provider
                    },
                    proxy : {
                    },
                },
                plugins: config.plugins,
                options: {
                    "exitUrl" : config.exitUrl,
                    "fullScreen" : false,
                    "bootstrap" : config.bootstrap,
                    "themes": {
                        "items": {
                            "base": "https://taoce.krampstud.io/taoQtiItem/views/css/qti-runner.css?buster=5bebf5594bb0e",
                            "available": [{
                                "id": "tao",
                                "name": "TAO",
                                "path": "https://taoce.krampstud.io/taoQtiItem/views/css/themes/default.css?buster=5bebf5594bb0e"
                            }],
                            "default": "tao"
                        },
                        "timerWarning": {
                            "assessmentItemRef": null,
                            "assessmentSection": null,
                            "testPart": null,
                            "assessmentTest": null
                        },
                        "catEngineWarning": null,
                        "progressIndicator": {
                            "type": "percentage",
                            "renderer": "percentage",
                            "scope": "test",
                            "forced": false,
                            "showLabel": true,
                            "showTotal": true,
                            "categories": []
                        },
                        "review": {
                            "enabled": true,
                            "scope": "test",
                            "useTitle": true,
                            "forceTitle": false,
                            "forceInformationalTitle": false,
                            "showLegend": true,
                            "defaultOpen": true,
                            "itemTitle": "Item %d",
                            "informationalItemTitle": "Instructions",
                            "preventsUnseen": true,
                            "canCollapse": false,
                            "displaySubsectionTitle": true
                        },
                        "exitButton": false,
                        "nextSection": false,
                        "plugins": {
                            "answer-masking": {
                                "restoreStateOnToggle": true,
                                "restoreStateOnMove": true
                            },
                            "overlay": {
                                "full": false
                            },
                            "collapser": {
                                "collapseTools": true,
                                "collapseNavigation": false,
                                "collapseInOrder": false,
                                "hover": false,
                                "collapseOrder": []
                            },
                            "magnifier": {
                                "zoomMin": 2,
                                "zoomMax": 8,
                                "zoomStep": 0.5
                            },
                            "calculator": {
                                "template": ""
                            }
                        },
                        "security": {
                            "csrfToken": true
                        },
                        "timer": {
                            "target": "server",
                            "resetAfterResume": false,
                            "keepUpToTimeout": false,
                            "restoreTimerFromClient": false
                        },
                        "enableAllowSkipping": true,
                        "enableValidateResponses": true,
                        "checkInformational": true,
                        "enableUnansweredItemsWarning": true,
                        "allowShortcuts": true,
                        "shortcuts": {
                            "calculator": {
                                "toggle": "C"
                            },
                            "zoom": {
                                "in": "I",
                                "out": "O"
                            },
                            "comment": {
                                "toggle": "A"
                            },
                            "itemThemeSwitcher": {
                                "toggle": "T"
                            },
                            "review": {
                                "toggle": "R",
                                "flag": "M"
                            },
                            "keyNavigation": {
                                "previous": "Shift+Tab",
                                "next": "Tab"
                            },
                            "next": {
                                "trigger": "J"
                            },
                            "previous": {
                                "trigger": "K"
                            },
                            "dialog": [],
                            "magnifier": {
                                "toggle": "L",
                                "in": "Shift+I",
                                "out": "Shift+O",
                                "close": "esc"
                            },
                            "highlighter": {
                                "toggle": "Shift+U"
                            },
                            "area-masking": {
                                "toggle": "Y"
                            },
                            "line-reader": {
                                "toggle": "G"
                            },
                            "answer-masking": {
                                "toggle": "D"
                            }
                        },
                        "itemCaching": {
                            "enabled": false,
                            "amount": 3
                        },
                        "guidedNavigation": false
                    }
                }
            };

            runnerComponentFactory(document.querySelector('.runner'), testRunnerConfig);
        }
    };
    return runnerController;
});
