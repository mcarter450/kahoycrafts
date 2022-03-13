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

const fs = require('fs');
const axios = require('axios').default;
const purgecss = require('gulp-purgecss');

const webpages = [
    {
        name: 'home',
        path: '/'
    },
    {
        name: 'shop',
        path: '/shop/'
    },
    {
        name: 'category',
        path: '/product-category/knobs-drawer-pulls/'
    },
    {
        name: 'product_detail',
        path: '/product/decorative-bookends-multicolor-backlit-cityscape-1-pair/'
    },
    {
        name: 'bio',
        path: '/owners-bio/'
    },
    {
        name: 'contact',
        path: '/contact/'
    },
    {
        name: 'wordpress-plugin-donation',
        path: '/wordpress-plugin-donation/'
    },
    {
        name: 'free-shipping-kit',
        path: '/free-shipping-kit/'
    },
     {
        name: 'products-feed-generator',
        path: '/products-feed-generator/'
    },
    {
        name: 'blog',
        path: '/blog/'
    },
    {
        name: 'blog_page',
        path: '/half-moon-oak-cabinet-pulls/'
    },
    {
        name: 'myaccount',
        path: '/my-account/'
    }
];

async function download_webpages(cb) {
    // Make a request for a user with a given ID
    for (let page in webpages) {
        try {
            const resp = await axios.get('https://www.kahoycrafts.com' + webpages[page].path);

            fs.writeFile(`assets/src/downloads/${webpages[page].name}.html`, resp.data, function(err) {
                if (err) {
                    return console.log(err);
                }
            }); 
        } 
        catch (err) {
            console.log(err);
            //cb();
        }
    }
    cb();
}

function purge_theme_styles() {
    return gulp.src([
        '../../plugins/woocommerce/assets/css/twenty-twenty-one.css',
        '../../plugins/jetpack/modules/theme-tools/compat/twentytwentyone.css',
        '../twentytwentyone/style.css'
      ])
      .pipe(purgecss({
          content: ['assets/src/downloads/**/*.html'],
          safelist: {
            standard: ['onsale', 'variations', 'woocommerce-variation-price', 'widget'],
            deep: [/^woocommerce-product-gallery/, /^primary-navigation/]
          },
          rejected: false
      }))
      .pipe(concat('twentytwentyone.css'))
      .pipe(gulp.dest('assets/src/purge-css/'));
}

function purge_block_styles() {
    return gulp.src([
        '../../../wp-includes/css/dist/block-library/style.css',
        '../../plugins/woocommerce/assets/css/woocommerce-layout.css',
        '../../plugins/woocommerce/assets/css/woocommerce-smallscreen.css',
        '../../plugins/woocommerce/packages/woocommerce-blocks/build/wc-blocks-vendors-style.css',
        '../../plugins/woocommerce/packages/woocommerce-blocks/build/wc-blocks-style.css'
      ])
      .pipe(purgecss({
          content: ['assets/src/downloads/**/*.html'],
          safelist: {
            standard: ['onsale']
          },
          rejected: false
      }))
      .pipe(gulp.dest('assets/src/purge-css/'));
}

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
    .pipe(gulp.dest('./assets/js/'))
    .pipe(browserSync.stream());
};

function css() {
  return gulp.src('./assets/src/scss/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS({compatibility: '*'}))
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

function watch() {
    gulp.watch('./assets/src/scss/**/*.scss', css);
    gulp.watch('./assets/src/js/**/*.js', js);
}

exports.download = download_webpages;

exports.purge = gulp.series(purge_theme_styles, purge_block_styles);

exports.build = gulp.parallel(js, css);

exports.watch = gulp.parallel(js, css, watch);

exports.default = gulp.parallel(js, css, watch, serve);
