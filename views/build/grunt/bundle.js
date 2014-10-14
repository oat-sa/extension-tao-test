module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);

    /**
     * Remove bundled and bundling files
     */
    clean.taotestsbundle = ['output',  root + '/taoTests/views/js/controllers.min.js'];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taotestsbundle = {
        options: {
            baseUrl : '../js',
            dir : 'output',
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoTests' : root + '/taoTests/views/js' },
            modules : [{
                name: 'taoTests/controller/routes',
                include : ext.getExtensionsControllers(['taoTests']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taotestsbundle = {
        files: [
            { src: ['output/taoTests/controller/routes.js'],  dest: root + '/taoTests/views/js/controllers.min.js' },
            { src: ['output/taoTests/controller/routes.js.map'],  dest: root + '/taoTests/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taotestsbundle', ['clean:taotestsbundle', 'requirejs:taotestsbundle', 'copy:taotestsbundle']);
};
