var gulp = require('gulp'),
    sass = require('gulp-sass')(require('sass')),
    concat = require('gulp-concat'),
    autoprefixer = require('gulp-autoprefixer'),
    cleanCSS = require('gulp-clean-css'),
    rename = require('gulp-rename'),
    //purgecss = require('gulp-purgecss'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    browserSync = require('browser-sync').create();

function js() {
  return gulp.src('./assets/src/js/**/*.js')
    .pipe(uglify())
    .pipe(sourcemaps.init())
    .pipe(sourcemaps.write('./maps', {
        sourceRoot: '../src/js'
    }))
    .pipe(rename(function(path) {
        if (! path.extname.endsWith('.map') ) {
            path.extname = ".min.js";
        }
    }))
    //.pipe(concat('all.min.js'))
    .pipe(gulp.dest('./assets/js/'))
    .pipe(browserSync.stream());
};

function css() {
  return gulp.src([
        './assets/src/scss/style.scss',
        './assets/src/scss/purge-style.scss'
    ])
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS({compatibility: '*'}))
    //.pipe(concat('all.min.css'))
    .pipe(sourcemaps.write('.', {
        includeContent: false,
        sourceRoot: '../src/scss'
    }))
    .pipe(rename(function(path) {
        if (! path.extname.endsWith('.map') ) {
            path.extname = ".min.css";
        }
    }))
    .pipe(gulp.dest('./assets/css/'))      
    .pipe(browserSync.stream());
};

function serve() {
  browserSync.init({
    injectChanges: true,
    files: [
        './assets/js/**/*.js',
        './assets/css/**/*.css'
    ],
    proxy: {
        target: "http://localhost/"
    }
  });
};

gulp.watch('./assets/src/scss/**/*.scss', css);
gulp.watch('./assets/src/js/**/*.js', js);

exports.watch = gulp.parallel(js, css);

exports.default = gulp.parallel(js, css, serve);
