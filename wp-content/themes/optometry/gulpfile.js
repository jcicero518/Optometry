"use strict";

let gulp        = require('gulp');

let browserify  = require('browserify');
let babelify    = require('babelify');
let watchify    = require('watchify');
let source      = require('vinyl-source-stream');
let buffer      = require('vinyl-buffer');
let uglify      = require('gulp-uglify');
let sourcemaps  = require('gulp-sourcemaps');
let livereload  = require('gulp-livereload');

let plumber     = require('gulp-plumber');
let gutil       = require('gulp-util');
let assign      = require('lodash.assign');

let sass = require('gulp-sass');
let browserSync = require('browser-sync');
let autoprefixer = require('autoprefixer');
let postcss = require('gulp-postcss');
let cssnano = require('cssnano');
let lost = require('lost');

gulp.task('browser-sync', function() {
    "use strict";
    var files = [
        '**/*.php',
        '**/*.html',
        '**/*.js',
        '**/*.css',
        '**/*.{png,jpg,gif}'
    ];

    browserSync.init(files, {
        proxy: 'sunyoptometry.dev', // local VM domain name
        injectChanges: true
    });
});

let onError = function(err) {
    console.log( 'An error has occured: ', err.message );
    this.emit( 'end' );
};

let enabled = {
    isProd: false,
    cssnano: false,
    aprefixer: true,
    jshint: false,
    sourcemaps: true,
    stripdebug: false
};

let browserifyOpts = {
    entries: './js/custom/app.js',
    debug: true
};

let babelOpts = {
    presets: ["es2015"]
};

let opts = assign({}, watchify.args, browserifyOpts);
let b = watchify(browserify(opts)).transform("babelify", babelOpts);
gulp.task('buildBundle', bundle);
b.on('update', bundle);
b.on('log', gutil.log);

function bundle() {
    return b.bundle()
        .on('error', function(err) {
            gutil.log.bind(gutil, 'Browserify error: ' + err);
            console.log('Error: ' + err.message);
            // end this stream
            // this prevents browserify to crash on compilation error
            this.emit('end');
        })
        .pipe(source('app.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(uglify())
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./dist/js'))
        .pipe(browserSync.stream({ once: true }));
}



gulp.task('scripts', function () {
    // app.js is your main JS file with all your module inclusions
    return browserify({entries: './js/custom/app.js', debug: true})
        .transform("babelify", { presets: ["es2015"] })
        .bundle()
        .on('error', function(err) {
            console.log('Error: ' + err.message);
            // end this stream
            // this prevents browserify to crash on compilation error
            this.emit('end');
        })
        .pipe(source('app.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./dist/js'))
        .pipe(browserSync.stream({ once: true }));
        //.pipe(livereload());
});

gulp.task('styles', function() {
    gulp.src('./sass/**/*.scss')
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(sourcemaps.init())
        .pipe(sass({
            errLogToConsole: true,
            outputStyle: 'nested'
        }))
        .pipe(postcss([ lost(), autoprefixer() ]))
        //.pipe(autoprefixer('last 2 versions', '> 1%', 'ie 8'))
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./build/styles'))
        .pipe(browserSync.stream({ once: true }));
});

gulp.task('watch', ['browser-sync'], function () {
    //livereload.listen();
    gulp.watch('./sass/**/*.scss', ['styles']);
    //gulp.watch('./js/custom/*.js', ['scripts']);
    gulp.watch('./js/custom/*.js', ['buildBundle']);
});

gulp.task('default', ['watch']);