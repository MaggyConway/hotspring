const gulp = require('gulp'),
	sass = require('gulp-sass')(require('sass')),
	postcss = require('gulp-postcss'),
	sourcemaps = require('gulp-sourcemaps'),
	autoprefixer = require('autoprefixer'),
	cssnano = require('cssnano');

const del = require('del');
const plugins = [
	autoprefixer(),
	//cssnano(),
];

const compiler = require('webpack'),
	webpackStream = require('webpack-stream');


/**
 * Define all source paths
 */

var paths = {
	styles: {
		src: './src/scss/*.scss',
		dest: './assets/css'
	},
	scripts: {
		src: './src/js/*.js',
		dest: './assets/js'
	}
};

/* Not all tasks need to use streams, a gulpfile is just another node program
 * and you can use all packages available on npm, but it must return either a
 * Promise, a Stream or take a callback and call it
 */
function clean() {
	// You can use multiple globbing patterns as you would with `gulp.src`,
	// for example if you are using del 2.0 or above, return its promise
	return del(['assets']);
}

/*
 * Define our tasks using plain functions
 */
function styles() {
	return gulp.src(paths.styles.src)
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(postcss(plugins))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest(paths.styles.dest));
}

function scripts() {
	return gulp.src(paths.scripts.src)
		.pipe(webpackStream({
			config: require('./webpack.config.js')
		}, compiler))
		.pipe(gulp.dest(paths.scripts.dest));
}

function runTask(taskName) {
	return function(evt, filepath) {
	  if (evt === "unlink") {
		remember.forget(taskName, path.resolve(filepath));
		delete cached.caches[taskName][path.resolve(filepath)];
	  }
	  gulp.series(taskName)();
	};
}

function watch() {
	//gulp.parallel(gulp.watch(paths.styles.src, styles),gulp.watch(paths.scripts.src, scripts))
	// gulp.watch(paths.styles.src, styles);
	// gulp.watch(paths.scripts.src, scripts);
	// gulp.watch(paths.styles.src).on("all", runTask("styles"));
  	// gulp.watch(paths.scripts.src).on("all", runTask("scripts"));
	  gulp.watch([paths.styles.src, paths.scripts.src],  gulp.parallel(styles, scripts));
}

/*
 * Specify if tasks run in series or parallel using `gulp.series` and `gulp.parallel`
 */
var build = gulp.series(clean, gulp.parallel(styles, scripts));

/*
 * You can use CommonJS `exports` module notation to declare tasks
 */
exports.clean = clean;
exports.styles = styles;
exports.scripts = scripts;
exports.watch = watch;
exports.build = build;
/*
 * Define default task that can be called by just running `gulp` from cli
 */
exports.default = build;