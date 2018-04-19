/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// Setting folder templates.
		dirs: {
			css: 'assets/css',
			fonts: 'assets/fonts',
			images: 'assets/images',
			js: 'assets/js'
		},

		// Sass linting with Stylelint.
		stylelint: {
			options: {
				configFile: '.stylelintrc'
			},
			all: [
				'<%= dirs.css %>/admin/*.scss'
			]
		},

		// Compile all .scss files.
		sass: {
			compile: {
				options: {
					sourceMap: 'none'
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.css %>/admin/',
					src: ['*.scss'],
					dest: '<%= dirs.css %>/admin/',
					ext: '.css'
				}]
			}
		},

		// Generate RTL .css files
		rtlcss: {
			woocommerce: {
				expand: true,
				cwd: '<%= dirs.css %>/admin/',
				src: [
					'*.css',
					'!select2.css',
					'!*-rtl.css'
				],
				dest: '<%= dirs.css %>/admin/',
				ext: '-rtl.css'
			}
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.css %>/admin/',
				src: ['*.css'],
				dest: '<%= dirs.css %>/admin/',
				ext: '.css'
			}
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: ['<%= dirs.css %>/admin/*.scss'],
				tasks: ['sass', 'rtlcss', 'cssmin']
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'i18n/languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://github.com/woocommerce/woocommerce-product-type-column/issues',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			dist: {
				options: {
					potFilename: 'woocommerce-product-type-column.pot',
					exclude: [
						'vendor/.*',
						'tests/.*',
						'tmp/.*'
					]
				}
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: 'woocommerce-product-type-column',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php',               // Include all files
					'!node_modules/**',       // Exclude node_modules/
					'!tests/**',              // Exclude tests/
					'!vendor/**',             // Exclude vendor/
					'!tmp/**'                 // Exclude tmp/
				],
				expand: true
			}
		},

		// Autoprefixer.
		postcss: {
			options: {
				processors: [
					require( 'autoprefixer' )({
						browsers: [
							'> 0.1%',
							'ie 8',
							'ie 9'
						]
					})
				]
			},
			dist: {
				src: [
					'<%= dirs.css %>/admin/*.css'
				]
			}
		}
	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-stylelint' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );

	// Register tasks
	grunt.registerTask( 'default', [
		'css',
		'i18n'
	]);

	grunt.registerTask( 'css', [
		'sass',
		'rtlcss',
		'postcss',
		'cssmin'
	]);

	// Only an alias to 'default' task.
	grunt.registerTask( 'dev', [
		'default'
	]);

	grunt.registerTask( 'i18n', [
		'checktextdomain',
		'makepot'
	]);
};
