/**
 * Created by minus on 5/23/15.
 */
module.exports = function (grunt) {
    grunt.initConfig({
        uglify: {
            files: {
                src: 'Resources/JS/*.js',  // source files mask
                dest: 'Resources/JS.min/',    // destination folder
                expand: true,    // allow dynamic building
                flatten: true,   // remove all unnecessary nesting
                ext: '.min.js'   // replace .js to .min.js
            }
        },
        watch: {
            js: {files: 'Resources/JS/*.js', tasks: ['uglify']}
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['uglify']);
};