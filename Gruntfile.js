'use strict';

module.exports = function(grunt) {
  require('time-grunt')(grunt);
  require('jit-grunt')(grunt);

  grunt.loadNpmTasks('grunt-composer');

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    phplint: {
      options: {
        swapPath: '/tmp'
      },
      application: [
        'src/**/*.php',
        'tests/**/*.php'
      ]
    },
    phpcs: {
      options: {
        bin: 'vendor/bin/phpcs',
        standard: 'PSR2'
      },
      application: {
        dir: [
          'src',
          'tests'
        ]
      }
    },
    phpmd: {
      options: {
        bin: 'vendor/bin/phpmd',
        rulesets: 'unusedcode,naming,design,controversial,codesize',
        reportFormat: 'text'
      },
      application: {
        dir: 'src'
      }
    },
    phpcpd: {
      options: {
        bin: 'vendor/bin/phpcpd',
        quiet: false,
        ignoreExitCode: true
      },
      application: {
        dir: 'src'
      }
    },
    phpunit: {
      options: {
        bin: 'vendor/bin/phpunit',
        coverage: true
      },
      application: {
        coverageHtml: 'build/coverage'
      }
    },
    composer : {
      options : {
        cwd: '.'
      }
    },
    security_checker: {
      options: {
        bin: 'vendor/bin/security-checker',
        format: 'text'
      },
      application: {
        file: 'composer.lock'
      }
    }
  });

  grunt.registerTask('qa', ['phplint', 'phpcs', 'phpmd', 'phpcpd']);
  grunt.registerTask('security', ['composer:outdated:direct', 'security_checker']);
  grunt.registerTask('test', ['phpunit']);

  grunt.registerTask('default', ['qa', 'test']);
};
