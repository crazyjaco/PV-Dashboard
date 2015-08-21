module.exports = function(grunt) {
  // time
  require('time-grunt')(grunt);

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    sass: {
      options: {
        // If you can't get source maps to work, run the following command in your terminal:
        // $ sass scss/foundation.scss:css/foundation.css --sourcemap
        // (see this link for details: http://thesassway.com/intermediate/using-source-maps-with-sass )
        sourceMap: true
      },

      dist: {
        options: {
          outputStyle: 'compressed'
        },
        files: {
          'css/dashboard.css': 'scss/style.scss'
        }
      }
    }

  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-string-replace');

  grunt.registerTask('build', ['copy', 'sass', 'concat', 'uglify']);
  grunt.registerTask('default', ['watch']);
};