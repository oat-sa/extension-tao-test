module.exports = function (grunt) {
    'use strict';

    var clean     = grunt.config('clean') || {};
    var copy      = grunt.config('copy') || {};
    var ext;
    var libs      = grunt.option('mainlibs');
    var out       = 'output';
    var requirejs = grunt.config('requirejs') || {};
    var root      = grunt.option('root');

    ext = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);

    /**
     * Compile tao files into a bundle
     */
    requirejs.taotestsbundle = {
        options: {
            exclude: ['mathJax'].concat(libs),
            include: ext.getExtensionsControllers(['taoTests']),
            out: out + '/taoTests/bundle.js',
            paths: { 'taoTests' : root + '/taoTests/views/js' },
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taotestsbundle = {
        files: [
            { src: [out + '/taoTests/bundle.js'],     dest: root + '/taoTests/views/dist/controllers.min.js' },
            { src: [out + '/taoTests/bundle.js.map'], dest: root + '/taoTests/views/dist/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taotestsbundle', ['clean:bundle', 'requirejs:taotestsbundle', 'copy:taotestsbundle']);
};
