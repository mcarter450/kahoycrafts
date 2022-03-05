var gulp = require('gulp'),
    sass = require('gulp-sass')(require('sass')),
    concat = require('gulp-concat'),
    autoprefixer = require('gulp-autoprefixer'),
    cleanCSS = require('gulp-clean-css'),
    rename = require('gulp-rename'),
    //purgecss = require('gulp-purgecss'),
    browserSync = require('browser-sync').create();


function css() {
  return gulp.src([
        './assets/src/scss/kahoycrafts.scss',
        './assets/js/OwlCarousel2-2.3.4/src/scss/owl.carousel.scss',
        './assets/js/OwlCarousel2-2.3.4/src/scss/owl.theme.default.scss',
        './assets/src/scss/owl-overrides.scss'
    ])
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS({compatibility: 'ie8'}))
    // .pipe(rename(function(path) {
    //   path.extname = ".min.css";
    // }))
    // .pipe(
    //   purgecss({
    //     content: ['public/**/*.html']
    //   })
    // )
    .pipe(concat('all.min.css'))
    .pipe(gulp.dest('./assets/css/'))      
    .pipe(browserSync.stream());
};

function serve() {
  browserSync.init({
    injectChanges: true,
    files: [
        './assets/css/**/*.css'
    ],
    proxy: {
        target: "http://localhost/"
    }
  });
};

gulp.watch('./assets/src/scss/**/*.scss', css);
//gulp.watch('./src/*.html', html).on('change', browserSync.reload);

exports.default = gulp.parallel(css, serve);