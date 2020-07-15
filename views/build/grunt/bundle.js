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
 * Copyright (c) 2014-2019 (original work) Open Assessment Technologies SA;
 */

/**
 * configure the extension bundles
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 *
 * @param {Object} grunt - the grunt object (by convention)
 */
module.exports = function(grunt) {
    'use strict';

    grunt.config.merge({
        bundle : {
            taotests : {
                options : {
                    extension : 'taoTests',
                    outputDir : 'loader',
                    paths: require('./paths.json'),
                    dependencies : ['taoItems'],
                    bundles : [{
                        name : 'taoTests',
                        default : true,
                        babel : true
                    }, {
                        name : 'taoTestsRunner',
                        babel : true,
                        include : [
                            'taoTests/runner/**/*'
                        ]
                    }]
                }
            }
        }
    });

    // bundle task
    grunt.registerTask('taotestsbundle', ['bundle:taotests']);
};
