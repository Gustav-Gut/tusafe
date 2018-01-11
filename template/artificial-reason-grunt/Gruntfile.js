module.exports = function(grunt) {
    "use strict";

    // Force use of Unix newlines
    grunt.util.linefeed = '\n';

    // Project configuration.
    grunt.initConfig({

        // Metadata.
        pkg: grunt.file.readJSON('package.json'),

        distPath: 'dist',
        assetsPath: '<%= distPath %>/assets',
        relativeAssetsPath: '',

        clean: {
            dist: '<%= distPath %>',
            assets: '<%= assetsPath %>',
            color: 'less/build.less'
        },

        colors: {
            aqua: {},
            blue: {},
            blue2: {},
            blue3: {},
            blue4: {},
            blue5: {},
            fuchsia: {},
            gray: {},
            green: {},
            green2: {},
            green3: {},
            green4: {},
            green5: {},
            orange: {},
            orange2: {},
            orange3: {},
            pink: {},
            red: {},
            red2: {},
            red3: {},
            violet: {},
            violet2: {},
            violet3: {},
            yellow: {},
            yellow2: {}
        },
        copy: {
            vendor: {
                files: [{
                    expand: true,
                    flatten: true,
                    src: [
                        'bower_components/bxslider-4/dist/jquery.bxslider.css',
                        'bower_components/bootstrap/dist/css/bootstrap.min.css',
                        'bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                        'bower_components/animate-css/animate.min.css',
                        'bower_components/font-awesome/css/*.*.*',
                        'bower_components/lightbox/dist/css/lightbox.css',
                        'bower_components/slidebars/dist/slidebars.min.css',
                        'bower_components/Buttons/css/buttons.css',
                        'bower_components/owl-carousel2/dist/assets/owl.carousel.min.css',
                        'bower_components/owl-carousel2/dist/assets/owl.theme.default.min.css',
                    ],
                    dest: '<%= assetsPath %>/css/'
                }, {
                    expand: true,
                    flatten: true,
                    src: 'bower_components/syntaxhighlighter/styles/*.css',
                    dest: '<%= assetsPath %>/css/syntaxhighlighter/'
                }, {
                    expand: true,
                    flatten: true,
                    src: [
                        'bower_components/jquery/dist/*.min.js',
                        'bower_components/bxslider-4/dist/jquery.bxslider.min.js',
                        'bower_components/jquery-cookie/jquery.cookie.js',
                        'bower_components/jquery.countdown/dist/jquery.countdown.min.js',
                        'bower_components/jquery-mousewheel/jquery.mousewheel.min.js',
                        'bower_components/bootstrap/dist/js/bootstrap.min.js',
                        'bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                        'bower_components/circles/circles.min.js',
                        'bower_components/holderjs/holder.js',
                        'bower_components/mixitup/build/jquery.mixitup.min.js',
                        'bower_components/lightbox/js/lightbox.min.js',
                        'bower_components/slidebars/dist/slidebars.min.js',
                        'bower_components/Buttons/js/buttons.js',
                        'bower_components/wowjs/dist/wow.min.js',
                        'bower_components/masonry/dist/masonry.pkgd.min.js',
                        'bower_components/imagesloaded/imagesloaded.pkgd.min.js',
                        'bower_components/matchHeight/jquery.matchHeight-min.js',
                        'bower_components/owl-carousel2/dist/*.min.js',
                        'bower_components/html5shiv/dist/html5shiv.min.js',
                        'bower_components/respond/dest/respond.min.js',
                        'bower_components/jquery/dist/*.min.map',
                        'bower_components/lightbox/js/lightbox.min.map',
                    ],
                    dest: '<%= assetsPath %>/js/'
                }, {
                    expand: true,
                    flatten: true,
                    src: 'bower_components/syntaxhighlighter/scripts/*.js',
                    dest: '<%= assetsPath %>/js/syntaxhighlighter/'
                }, {
                    expand: true,
                    flatten: true,
                    src: [
                        'bower_components/bxslider-4/dist/images/*',
                        'bower_components/lightbox/dist/images/*',
                    ],
                    dest: '<%= assetsPath %>/img/'
                }, {
                    expand: true,
                    flatten: true,
                    src: [
                        'bower_components/bootstrap/dist/fonts/*',
                        'bower_components/font-awesome/fonts/*',
                    ],
                    dest: '<%= assetsPath %>/fonts/'
                }]
            },
            assets: {
                expand: true,
                src: ["css/**", "js/**", "img/**", "fonts/**"],
                dest: '<%= assetsPath %>/'
            }
        },

        processhtml: {
            options: {
                process: true,
                recursive: true,
                strip: true,
            },
            header_dark_dark: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        header_class: 'header-full-dark',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-header-full navbar-dark',
                        navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
                        navbar_nav_class: 'nav navbar-nav'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/header-dark-dark'
                }]
            },
            header_dark_inverse: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        header_class: 'header-full-dark',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-header-full navbar-inverse',
                        navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
                        navbar_nav_class: 'nav navbar-nav'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/header-dark-inverse'
                }]
            },
            header_dark_light: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        header_class: 'header-full-dark',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-header-full',
                        navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
                        navbar_nav_class: 'nav navbar-nav'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/header-dark-light'
                }]
            },
            header_light_dark: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        header_class: 'header-full',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-header-full navbar-dark',
                        navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
                        navbar_nav_class: 'nav navbar-nav'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/header-light-dark'
                }]
            },
            header_light_inverse: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        header_class: 'header-full',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-header-full navbar-inverse',
                        navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
                        navbar_nav_class: 'nav navbar-nav'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/header-light-inverse'
                }]
            },
            header_light_light: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        header_class: 'header-full',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-header-full',
                        navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
                        navbar_nav_class: 'nav navbar-nav'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/header-light-light'
                }]
            },
            navbar_dark: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-dark',
                        navbar_brand_class: 'navbar-brand',
                        navbar_nav_class: 'nav navbar-nav navbar-right'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/navbar-dark'
                }]
            },
            navbar_inverse: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        navbar_class: 'navbar navbar-static-top navbar-default navbar-inverse',
                        navbar_brand_class: 'navbar-brand',
                        navbar_nav_class: 'nav navbar-nav navbar-right'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/navbar-inverse'
                }]
            },
            navbar_light: {
                options: {
                    data: {
                        assets: '<%= relativeAssetsPath %>',
                        navbar_class: 'navbar navbar-static-top navbar-default',
                        navbar_brand_class: 'navbar-brand',
                        navbar_nav_class: 'nav navbar-nav navbar-right'
                    },
                },
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['html/*.html'],
                    dest: '<%= distPath %>/navbar-light'
                }]
            }
        },

        dom_munger: {
            options: {
                callback: function($, file) {
                    $('a[href="' + file.split('/').pop() + '"]').addClass('active');
                }
            },
            dist: {
                src: '<%= distPath %>/**/*.html',
            }
        },

        jsbeautifier: {
            files: ["dist/**/*.html"],
        },

        concat: {
            color: {}
        },
        less: {
            color: {}
        },

        watch: {
            options: {
                livereload: true
            },
            less: {
                files: 'less/*.less',
                tasks: ['less']
            },
            html: {
                files: 'html/**/*.html',
                tasks: ['html']
            }
        },

        connect: {
            server: {
                options: {
                    livereload: true,
                    port: 4000,
                    base: 'dist',
                    hostname: '*',
                }
            }
        }
    });

    require('load-grunt-tasks')(grunt);

    grunt.registerMultiTask('colors', 'Compile Colors files to CSS', function() {
        var color = this.target;

        grunt.config('concat.color', {
            src: [
                'less/colors/' + color + '.less',
                'less/reason.less'
            ],
            dest: 'less/build.less'
        });

        grunt.config('less.color', {
            src: 'less/build.less',
            dest: '<%= assetsPath %>/css/style-' + color + '.css'
        });

        grunt.task.run(['concat:color', 'less:color', 'clean:color']);
    });

    grunt.registerTask('assets', ['clean:assets', 'colors', 'copy']);

    grunt.registerTask('html', function() {
        var tasks = ['processhtml', 'dom_munger'];

        if (grunt.option('beauty')) {
            tasks.push('jsbeautifier');
        }

        grunt.task.run(tasks);
    });

    grunt.registerTask('dist', ['clean', 'assets', 'html']);

    grunt.registerTask('dev', function() {
        grunt.config('relativeAssetsPath', '../assets/');

        grunt.task.run(['clean', 'assets', 'html']);
    });

    grunt.registerTask('default', ['connect', 'watch']);
};
