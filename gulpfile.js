var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var less = require('gulp-less');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var env = process.env.GULP_ENV;

gulp.task('back_js', function () {
    return gulp.src(['bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js'])
        .pipe(concat('backend_script.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'));
});

gulp.task('js', function () {
    return gulp.src(['bower_components/jquery/dist/jquery.js',
        'bower_components/what-input/dist/what-input.js',
        'bower_components/foundation-sites/dist/js/foundation.js',
        'bower_components/greensock-js/src/uncompressed/plugins/CSSPlugin.js',
        'bower_components/greensock-js/src/uncompressed/easing/EasePack.js',
        'bower_components/greensock-js/src/uncompressed/TweenLite.js',
        'bower_components/scrollmagic/scrollmagic/uncompressed/ScrollMagic.js',
        'bower_components/scrollmagic/scrollmagic/uncompressed/plugins/animation.gsap.js'])
        .pipe(concat('script.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'));
});

gulp.task('site_js', function () {
    return gulp.src(['app/Resources/public/js/zinne.js'])
        .pipe(concat('zinne.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'));
});

gulp.task('back_style', function () {
    return gulp.src([
        'app/Resources/public/less/admin/styles.less'])
        .pipe(gulpif(/[.]less/, less()))
        .pipe(concat('backend_styles.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'));
});

gulp.task('style', function () {
    return gulp.src([
        'app/Resources/public/sass/front/styles.scss'])
        .pipe(gulpif(/[.]scss/, sass().on('error', sass.logError)))
        .pipe(concat('styles.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'));
});

gulp.task('fonts', function () {
    return gulp.src('bower_components/bootstrap/fonts/*.*')
        .pipe(gulp.dest('web/fonts'));
});


gulp.task('default', ['back_js', 'js', 'back_style', 'style', 'fonts']);