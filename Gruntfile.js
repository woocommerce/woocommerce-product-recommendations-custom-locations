/* jshint node: true */

/**
 * GruntJS task manager config.
 */
module.exports = function( grunt ) {

	'use strict';

	var request = require( 'request' ),
		sass    = require( 'sass' );

	/**
	 * Init config.
	 */
	grunt.initConfig( {

		// Get access to package.json.
		pkg: grunt.file.readJSON( 'package.json' ),

		// Setting dir paths.
		dirs: {
			css:    'assets/css',
			sass:   'assets/sass',
			js:     'assets/js',
			deploy: 'deploy' // create a deploy/ and ignore it.
		},

		// JavaScript linting with JSHint.
		jshint: {
			options: {
				'force': true,
				'boss': true,
				'curly': true,
				'eqeqeq': false,
				'eqnull': true,
				'es3': false,
				'expr': false,
				'immed': true,
				'noarg': true,
				'onevar': true,
				'quotmark': 'single',
				'trailing': true,
				'undef': true,
				'unused': true,
				'sub': false,
				'browser': true,
				'maxerr': 1000,
				globals: {
					'jQuery': false,
					'$': false,
					'Backbone': false,
					'_': false,
					'wp': false,
					'wc_prl_params': false,
					'wc_prl_admin_params': false,
					'woocommerce_addons_params': false,
					'woocommerce_params': false,
					'woocommerce_admin_meta_boxes': true,
					'woocommerce_writepanel_params': false
				},
			},
			all: [
				'!Gruntfile.js',
				'<%= dirs.js %>/frontend/src/*.js',
				'!<%= dirs.js %>/frontend/*.min.js',
				'<%= dirs.js %>/admin/src/*.js',
				'!<%= dirs.js %>/admin/select2.js',
				'!<%= dirs.js %>/admin/*.min.js'
			]
		},

		// Compile SASS.
		sass: {
			dev: {
				options: {
					implementation: sass,
					sourceMap: true,
					outputStyle: 'expanded'
				},
				files: [
					{
						expand: true,
						cwd: '<%= dirs.sass %>/admin',
						src: [ 'wc-prl-cl-admin.scss' ],
						dest: '<%= dirs.css %>/admin',
						ext: '.css'
					}
				]
			},
			dist: {
				options: {
					implementation: sass,
					sourceMap: false,
					outputStyle: 'compressed'
				},
				files: [

					{
						expand: true,
						cwd: '<%= dirs.sass %>/admin',
						src: [ 'wc-prl-cl-admin.scss' ],
						dest: '<%= dirs.css %>/admin',
						ext: '.css'
					}
				]
			}
		},

		// Autoprefixer.
		postcss: {
			options: {
				processors: [
					require( 'autoprefixer' )( {
						overrideBrowserslist: [
							'> 0.1%',
							'ie 8',
							'ie 9'
						]
					} )
				]
			},
			dist: {
				src: [
					'<%= dirs.css %>/admin/*.css',
					'<%= dirs.css %>/frontend/*.css'
				]
			}
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: [
					'<%= dirs.sass %>/admin/*.scss',
					'<%= dirs.sass %>/frontend/*.scss',
				],
				tasks: [ 'sass:dev', 'postcss' ]
			},
			js: {
				files: [
					'<%= dirs.js %>/admin/src/*js',
				],
				tasks: [ 'copy:assets', 'uglify' ]
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://woocommerce.com/my-account/create-a-ticket/',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			go: {
				options: {
					potFilename: 'woocommerce-product-recommendations-custom-locations.pot',
					exclude: [
						'languages/.*',
						'assets/.*',
						'node-modules/.*',
						'woo-includes/.*'
					]
				}
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: [ 'woocommerce', 'woocommerce-product-recommendations', 'woocommerce-product-recommendations-custom-locations' ],
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
					'**/*.php', // Include all files
					'!deploy/**', // Exclude deploy.
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true
			}
		},

		rtlcss: {
			options: {
				config: {
					swapLeftRightInUrl: false,
					swapLtrRtlInUrl: false,
					autoRename: false,
					preserveDirectives: true
				},
				properties : [
					{
						name: 'swap-fontawesome-left-right-angles',
						expr: /content/im,
						action: function( prop, value ) {
							if ( value === '"\\f105"' ) { // fontawesome-angle-left
								value = '"\\f104"';
							}
							if ( value === '"\\f178"' ) { // fontawesome-long-arrow-right
								value = '"\\f177"';
							}
							return { prop: prop, value: value };
						}
					}
				]
			},
			main: {
				expand: true,
				ext: '-rtl.css',
				src: [
					'assets/css/admin/admin.css'
				]
			}
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: false
			},
			admin: {
				files: [ {
					expand: true,
					cwd: '<%= dirs.js %>/admin/',
					src: [
						'wc-prl-cl-admin.js'
					],
					dest: '<%= dirs.js %>/admin/',
					ext: '.min.js'
				} ]
			}
		},

		exec: {
			options: {
				shell: '/bin/bash'
			},
			npm_install: {
				cmd: function() {
					grunt.log.ok( 'Running npm install...' );
					return 'npm install';
				}
			},
			upload: {
				cmd: function( filename ) {

					var path     = grunt.config.process( '<%= dirs.deploy %>' ),
						filepath = path + '/' + filename;

					return "(echo put " + filepath + ".zip && echo chmod 644 " + filename + ".zip) | sftp -q support@somewherewarm.gr:/home/support/uploads";
				}
			},
			zip: {
				cmd: function( filename, title ) {
					var path = grunt.config.process( '<%= dirs.deploy %>' );
					grunt.log.ok( 'Compressing files...' );

					var excludes = [
						'-x "*/node_modules/*"',
						'-x "*/assets/sass/*"',
						'-x "*/assets/css/*.map"',
						'-x "*/assets/js/frontend/src/*"',
						'-x "*/assets/js/admin/src/*"',
						'-x "*/tests/*"',
						'-x "*/deploy/*"',
						'-x "*/.sass-cache/*"',
						'-x "*/.git/*"',
						'-x "*/Gruntfile.js"',
						'-x "*/phpunit.xml"',
						'-x "*/package.json"',
						'-x "*/package-lock.json"',
						'-x "*/README.md"',
						'-x "*/codeception.yml"',
						'-x "*/.DS_Store"',
						'-x "*/.gitignore"',
						'-x "*/.travis.yml"',
						'-x "*/._*"'
					];

					var cmd = 'cd .. && zip -qFSr ' + title + '/' + path + '/' + filename + '.zip ' + title + ' ' + excludes.join( ' ' );
					grunt.log.ok( 'Running: ' + cmd );
					return cmd;
				}
			}
		},

		// Manage npm dependencies.
		copy: {
			assets: {
				files: [
					{
						expand: true,
						src: [ '<%= dirs.js %>/admin/src/*' ],
						dest: '<%= dirs.js %>/admin',
						flatten: true,
						filter: 'isFile'
					}
				]
			}
		}
	} );

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-exec' );

	/**
	 * Custom Tasks.
	 */
	grunt.registerTask( 'dev', [
		'checktextdomain',
		'uglify',
		'sass:dev',
		'postcss',
		'rtlcss'
	] );

	grunt.registerTask( 'default', [
		'dev',
		'makepot'
	] );

	grunt.registerTask( 'build', [
		'copy',
		'checktextdomain',
		'uglify',
		'sass:dist',
		'postcss',
		'rtlcss',
		'makepot'
	] );

	/**
	 * Function to get the release download key from the server.
	 */
	grunt.registerTask( 'get_download_key', function( filename ) {

		var done    = this.async(),
			options = {
				url: 'https://somewherewarm.com/download.php?get=key&file=' + filename + '.zip',
				headers: {
					'Content-Type': 'text/plain'
				}
			};

		grunt.log.ok( 'Request download key from somewherewarm.com...' );
		request( options, function( error, response, body ) {

			var key_regex = /<body>(.*?)<\/body>/g,
				key       = key_regex.exec( body )[ 1 ],
				url       = 'https://somewherewarm.com/download.php?get=' + key;

			grunt.log.writeln( 'Download link:'['green'].bold );

			grunt.log.writeln( url['yellow'].bold );

			done();
		} );

	} );

	/**
	 * Build and Upload a new Release.
	 *
	 * @deprecated Replaced by Travis CS Process
	 */
	grunt.registerTask( 'deploy', function() {

		var done    = this.async(),
			title   = grunt.config.process( '<%= pkg.name %>' );

		// Sluggify title.
		title = title.toLowerCase().replace( /\s+/g, '-' );

		grunt.task.run( [
			'exec:npm_install',
			'build',
			// 'exec:test',
			'exec:zip:' + title + ':' + title,
			'exec:upload:' + title,
			'get_download_key:' + title
		] );

		done();
	} );

	/**
	 * Build and Upload a new PRE-Release.
	 */
	grunt.registerTask( 'prerelease', function() {

		var done    = this.async(),
			title   = grunt.config.process( '<%= pkg.name %>' ),
			version = grunt.config.process( '<%= pkg.version %>' );

		// Sluggify title.
		title = title.toLowerCase().replace( /\s+/g, '-' );

		var zip_name     = title + '-' + version,
			zip_name_dev = zip_name + '-dev';

		grunt.task.run( [
			'exec:npm_install',
			'build',
			'exec:zip:' + zip_name_dev + ':' + title,
			'exec:upload:' + zip_name_dev,
			'get_download_key:' + zip_name_dev
		] );

		done();
	} );
};
