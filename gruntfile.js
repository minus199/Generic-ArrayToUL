/**
 * Created by minus on 5/23/15.
 */
module.exports = function (grunt) {
    grunt.initConfig({
        copy: {
            build: {
                cwd: 'Resources/JS',
                src: ['*.js'],
                dest: 'Resources/JS/build',
                expand: true
            }
        },
        clean: {
            build: {
                src: ['Resources/JS/build']
            },
            scripts: {
                src: ['Resources/JS/build/*.js', '!build/application.js']
            }
        },
        uglify: {
            files: {
                src: 'Resources/JS/*.js',  // source files mask
                dest: 'Resources/js-min/',    // destination folder
                expand: true,    // allow dynamic building
                flatten: true,   // remove all unnecessary nesting
                ext: '.js'   // replace .js to .min.js
            }
        },
        watch: {
            js: {
                files: 'Resources/JS/*.js',
                tasks: ['uglify']
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask(
        'default',
        'Compiles all of the assets and copies the files to the build directory.',
        ['copy', 'uglify', 'clean']
    );
};