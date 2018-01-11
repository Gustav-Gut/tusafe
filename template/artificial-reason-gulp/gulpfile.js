var gulp =          require('gulp'),
    less =          require('gulp-less'),
    connect =       require('gulp-connect'),
    processhtml =   require('gulp-processhtml'),
    rename =        require("gulp-rename"),
    concat =        require('gulp-concat'),
    gutil =         require('gulp-util'),
    del =           require('del'),
    path =          require('path'),
    jshint =        require('gulp-jshint'),
    changed =       require('gulp-changed'),
    runSequence =   require('run-sequence'),
    livereload =    require('gulp-livereload'),
    postcss =       require('gulp-postcss'),
    sourcemaps =    require('gulp-sourcemaps'),
    imageop =       require('gulp-image-optimization');
    autoprefixer =  require('autoprefixer');

var data = {
    paths : {
        dist: 'dist',
        html: '.',
        assets: 'assets',
        shop_assets: '../assets'
    },

    vendors : {
        concat_js : [
            'bower_components/jquery/dist/*.min.js',
            'bower_components/bxslider-4/dist/jquery.bxslider.min.js',
            'bower_components/jquery-cookie/jquery.cookie.js',
            'bower_components/jquery.countdown/dist/jquery.countdown.min.js',
            'bower_components/jquery-mousewheel/jquery.mousewheel.min.js',
            'bower_components/bootstrap/dist/js/bootstrap.min.js',
            'bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js',
            'bower_components/circles/circles.min.js',
            'bower_components/mixitup/build/jquery.mixitup.min.js',
            'bower_components/lightbox/js/lightbox.min.js',
            'bower_components/slidebars/dist/slidebars.min.js',
            'bower_components/Buttons/js/buttons.js',
            'bower_components/wowjs/dist/wow.min.js',
            'bower_components/masonry/dist/masonry.pkgd.min.js',
            'bower_components/imagesloaded/imagesloaded.pkgd.min.js',
            'bower_components/matchHeight/jquery.matchHeight-min.js',
            'bower_components/owl-carousel2/dist/*.min.js',
        ],

        js : [
            'bower_components/html5shiv/dist/html5shiv.min.js',
            'bower_components/respond/dest/respond.min.js',
            'bower_components/jquery/dist/*.min.map',
            'bower_components/lightbox/js/lightbox.min.map',
            'bower_components/holderjs/holder.js',
        ],

        css : [
            'bower_components/bxslider-4/dist/jquery.bxslider.css',
            'bower_components/bootstrap/dist/css/bootstrap.min.css',
            'bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
            'bower_components/animate-css/animate.min.css',
            'bower_components/font-awesome/css/*.*.*',
            'bower_components/lightbox/css/lightbox.css',
            'bower_components/slidebars/dist/slidebars.min.css',
            'bower_components/Buttons/css/buttons.css',
            'bower_components/owl-carousel2/dist/assets/owl.carousel.min.css',
            'bower_components/owl-carousel2/dist/assets/owl.theme.default.min.css',
        ],

        img : [
            'bower_components/bxslider-4/dist/images/*',
            'bower_components/lightbox/img/*',
        ],

        fonts : [
            'bower_components/bootstrap/dist/fonts/*',
            'bower_components/font-awesome/fonts/*',
            'src/fonts/**/*',
        ]
    },

    colors : [
        'aqua', 'blue', 'blue2', 'blue3', 'blue4', 'blue5', 'fuchsia', 'gray', 'green', 'green2', 'green3', 'green4', 'green5', 'orange', 'orange2', 'orange3', 'pink', 'red', 'red2', 'red3', 'violet', 'violet2', 'violet3', 'yellow', 'yellow2'
    ],
};

var targets = {
    header_light_light : {
        environment: 'header_light_light',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full',
            navbar_class: 'navbar navbar-default navbar-header-full',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
    header_light_dark : {
        environment: 'header_light_dark',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full',
            navbar_class: 'navbar navbar-default navbar-header-full navbar-dark',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
    header_light_inverse : {
        environment: 'header_light_inverse',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full',
            navbar_class: 'navbar navbar-default navbar-header-full navbar-inverse',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
    header_dark_light : {
        environment: 'header_dark_light',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full-dark',
            navbar_class: 'navbar navbar-default navbar-header-full',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
    header_dark_dark : {
        environment: 'header_dark_dark',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full-dark',
            navbar_class: 'navbar navbar-default navbar-header-full navbar-dark',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm navbar-dark',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
    header_dark_inverse : {
        environment: 'header_dark_inverse',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full-dark',
            navbar_class: 'navbar navbar-default navbar-header-full navbar-inverse',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm navbar-inverse',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
    navbar_light : {
        environment: 'navbar_light',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            navbar_class: 'navbar navbar-default',
            navbar_brand_class: 'navbar-brand',
            navbar_nav_class: 'nav navbar-nav navbar-right'
        },
    },
    navbar_dark : {
        environment: 'navbar_dark',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            navbar_class: 'navbar navbar-default navbar-dark',
            navbar_brand_class: 'navbar-brand',
            navbar_nav_class: 'nav navbar-nav navbar-right'
        },
    },
    navbar_inverse : {
        environment: 'navbar_inverse',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            navbar_class: 'navbar navbar-default navbar-inverse',
            navbar_brand_class: 'navbar-brand',
            navbar_nav_class: 'nav navbar-nav navbar-right'
        },
    },
    dev : {
        environment: 'dev',
        data: {
            assets: data.paths.assets + '/',
            shop_assets: data.paths.shop_assets + '/',
            header_class: 'header-full',
            navbar_class: 'navbar navbar-default navbar-header-full navbar-dark',
            navbar_brand_class: 'navbar-brand hidden-lg hidden-md hidden-sm',
            navbar_nav_class: 'nav navbar-nav'
        },
    },
};

var path_js = path.join(data.paths.dist, data.paths.assets, 'js'),
    path_css = path.join(data.paths.dist, data.paths.assets, 'css'),
    path_img = path.join(data.paths.dist, data.paths.assets, 'img'),
    path_fonts = path.join(data.paths.dist, data.paths.assets, 'fonts'),
    path_html = path.join(data.paths.dist, data.paths.html);

gulp.task('vendor', function() {
    gulp.src(data.vendors.concat_js)
        .pipe(concat("vendors.js"))
        .pipe(gulp.dest(path_js));

    gulp.src(data.vendors.js)
        .pipe(gulp.dest(path_js));

    gulp.src(data.vendors.css)
        .pipe(concat("vendors.css"))
        .pipe(gulp.dest(path_css));

    gulp.src(data.vendors.img)
        .pipe(gulp.dest(path_img));

    gulp.src(data.vendors.fonts)
        .pipe(gulp.dest(path_fonts));

    gulp.src('bower_components/syntaxhighlighter/scripts/*')
        .pipe(gulp.dest(path.join(path_js, 'syntaxhighlighter')));

    gulp.src('bower_components/syntaxhighlighter/styles/*')
        .pipe(gulp.dest(path.join(path_css, 'syntaxhighlighter')));
});

gulp.task('less', function() {
    for (var color in data.colors) {
        gulp.src(['src/less/colors/' + data.colors[color] + '.less', 'src/less/reason.less'])
        .pipe(concat('style-' + data.colors[color] + '.less'))
        .pipe(less({
            paths: [path.join(__dirname, 'src', 'less')],
        }))
        .pipe(gulp.dest(path_css));
    }
});

gulp.task('less:dev', function() {
    gulp.src(['src/less/colors/blue.less', 'src/less/reason.less'])
        .pipe(changed(path.join(path_css)))
        .pipe(concat('style-blue.less'))
        .pipe(less({
            paths: [path.join(__dirname, 'src', 'less')],
            plugins: []

        }))
        .pipe(gulp.dest(path_css))
        .pipe(connect.reload());
});

gulp.task('autoprefixer', function () {
    return gulp.src(['dist/assets/css/**/*.css', '!dist/assets/css/vendors.css'])
        .pipe(sourcemaps.init())
        .pipe(postcss([ autoprefixer({ browsers: ['last 2 versions'] }) ]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('dist/assets/css/'));
});

gulp.task('js', function() {
    return gulp.src('src/js/**/*.js')
        .pipe(changed(path.join(path_js)))
        .pipe(jshint())
        .pipe(jshint.reporter('default'))
        .pipe(gulp.dest(path_js));
});

gulp.task('html', function() {
    for (var target in targets) {
        if (target == 'dev')
            continue;
        gulp.src('src/html/*.html')
            .pipe(changed(path.join(path_html, targets[target].environment)))
            .pipe(processhtml({
                recursive: true,
                process: true,
                strip: true,
                environment: targets[target].environment,
                data: targets[target].data,
            }))
            .pipe(gulp.dest(path.join(path_html, targets[target].environment)));
    }
});

gulp.task('html:dev', function() {
    gulp.src(['src/html/**/*.html', '!src/html/layout/**/*'])
        .pipe(processhtml({
            recursive: true,
            process: true,
            strip: true,
            environment: targets.dev.environment,
            data: targets.dev.data,
        }))
        .pipe(gulp.dest(path.join(path_html)))
        .pipe(connect.reload());
});

gulp.task('img', function() {
    gulp.src('src/img/**/*')
        .pipe(imageop({
            optimizationLevel: 5,
            progressive: true,
            interlaced: true
        }))
        .pipe(gulp.dest(path_img))
        .pipe(connect.reload());
});

gulp.task('img:dev', function() {
    gulp.src('src/img/**/*')
        .pipe(gulp.dest(path_img))
        .pipe(connect.reload());
});

gulp.task('css', function() {
    gulp.src('src/css/**/*')
        .pipe(gulp.dest(path_css));
});

gulp.task('clean', function() {
    del.sync([
        path.join('.', data.paths.dist),
        path.join('.', 'tmp'),
    ]);
});

gulp.task('watch', function() {
    gulp.watch(['src/less/**/*.less'], ['less:dev']);
    gulp.watch(['src/js/**/*.js'], ['js']);
    gulp.watch(['src/html/**/*.html'], ['html:dev']);
    gulp.watch(['src/css/*.css'], ['css']);
    gulp.watch(['src/img/**/*'], ['img:dev']);
});

gulp.task('connect', function() {
    connect.server({
        root: 'dist',
        port: '8080',
        livereload: true,
    });
});



gulp.task('work', function() {
    runSequence(
        'dev',
        ['connect', 'watch']
    );
});

gulp.task('dev', function() {
    runSequence(
        'clean',
        ['vendor', 'less', 'css', 'img:dev', 'js', 'html:dev']
    );
});

gulp.task('default', function() {
    runSequence(
        'clean',
        ['vendor', 'less', 'css', 'img', 'js', 'html'],
        'autoprefixer'
    );
});

gulp.task('preview', function() {
    runSequence(
        'clean',
        ['vendor', 'less', 'css', 'img', 'js', 'html:dev'],
        'autoprefixer'
    );
});
