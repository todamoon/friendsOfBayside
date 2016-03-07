'use strict';

var gulp = require('gulp');
var shell = require('shelljs');
var unzip = require('unzip');
var download = require('gulp-download');
var rename = require('gulp-rename');
var clean = require('gulp-clean');
var fs = require('fs');
var db = require('./database');
var plumber = require('gulp-plumber');
var autoprefixer = require('gulp-autoprefixer');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var imagemin = require('gulp-imagemin');
var cache = require('gulp-cache');
var minifycss = require('gulp-minify-css');
var sass = require('gulp-sass');
var browserSync = require('browser-sync');
var usemin = require('gulp-usemin');
var uglify = require('gulp-uglify');
var minifyCss = require('gulp-minify-css');
var rev = require('gulp-rev');
var inject = require('gulp-inject');

//downloads and installs craft
gulp.task('craft', function() {

  gulp.src('craft/app', {read: false})
    .pipe(clean());

  download('http://buildwithcraft.com/latest.zip?accept_license=yes')
    .pipe(rename('craft.zip'))
    .pipe(gulp.dest('.tmp'))
    .on('end', function() {
      fs.createReadStream('.tmp/craft.zip')
      .pipe(unzip.Extract({ path: '.tmp' }))
      setTimeout(function() {
        shell.exec('mv .tmp/craft/app craft/app');
      }, 5000);
    });
});

//pulls db from live server or creates a new local db
gulp.task('backup', function() {
  //ssh into server and download database
  var output = shell.exec(
    'ssh forge@' + db.stage.host + ' "mysqldump ' + db.stage.dbName + ' --quote-names --opt --hex-blob --add-drop-database -u' + db.stage.user + ' -p' + db.stage.pass + '"'
  ).output;

  fs.writeFile('db.sql', output);

  //backup existing local db, if exists
  shell.exec('mysqldump -h localhost -u' + db.local.user + ' -p' + db.local.pass + ' ' + db.local.dbName + ' > ./backups/' + Date.now() + '.sql');

  //update local db with downloaded db
  // var shellError = shell.exec('mysql -h localhost -u' + db.local.user + ' -p' + db.local.pass + ' ' + db.local.dbName + ' < db.sql').output;
  // if (shellError) {
  //   console.log('creating new db..');
  //   shell.exec('echo "CREATE DATABASE ' + db.local.dbName + '" | mysql -u' + db.local.user + ' -p' + db.local.pass);
  //   shell.exec('mysql -h localhost -u' + db.local.user + ' -p' + db.local.pass + ' ' + db.local.dbName + ' < db.sql')
  // } else {
  //   console.log('overwrote local db');
  // }
});

gulp.task('import', function() {
  setTimeout(function() {
    console.log('start');
    var shellError = shell.exec('mysql -h localhost -u' + db.local.user + ' -p' + db.local.pass + ' ' + db.local.dbName + ' < db.sql').output;
    console.log(shellError)
    if (shellError) {
      console.log('creating new db..');
      shell.exec('echo "CREATE DATABASE ' + db.local.dbName + '" | mysql -u' + db.local.user + ' -p' + db.local.pass);
      shell.exec('mysql -h localhost -u' + db.local.user + ' -p' + db.local.pass + ' ' + db.local.dbName + ' < db.sql')
    } else {
      console.log('overwrote local db');
    }
   }, 2000);

});


gulp.task('db',['backup', 'import']);



gulp.task('bs-reload', function () {
  browserSync.reload();
});

gulp.task('images', function(){
  gulp.src('public/assets/**/*')
    .pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
    .pipe(gulp.dest('dist/assets/'));
});

gulp.task('styles', function(){
  gulp.src(['scss/**/*.scss'])
    .pipe(plumber({
      errorHandler: function (error) {
        console.log(error.message);
        this.emit('end');
    }}))
    .pipe(sass())
    .pipe(autoprefixer('last 2 versions'))
    .pipe(gulp.dest('public/styles/'))
    // .pipe(rename({suffix: '.min'}))
    // .pipe(minifycss())
    .pipe(gulp.dest('public/styles/'))
    .pipe(browserSync.stream());
});


gulp.task('scripts', function(){
  return gulp.src('public/scripts/**/*.js')
    .pipe(plumber({
      errorHandler: function (error) {
        console.log(error.message);
        this.emit('end');
    }}))
    // .pipe(concat('concat.js'))
    .pipe(babel())
    .pipe(gulp.dest('public/js/'))
    // .pipe(rename({suffix: '.min'}))
    // .pipe(uglify())
    .pipe(gulp.dest('public/js/'))
    .pipe(browserSync.reload({stream:true}))
});

gulp.task('usemin', function () {
  return gulp.src('craft/templates/_layout.html')
    .pipe(usemin({
      css: [minifyCss(), 'concat'],
      js: []
    }))
    .pipe(gulp.dest('public/'));
});

gulp.task('build', ['usemin']);

gulp.task('browser-sync', function() {
  browserSync.init({
    files : 'public/styles/*.css',
    watchTask: true,
    port: 3000
  });
});

gulp.task('install', ['craft', 'db', 'styles', 'scripts']);

gulp.task('default', ['browser-sync'], function(){
  gulp.watch("scss/**/*.scss", ['styles']);
  gulp.watch("public/scripts/**/*.js", ['scripts']);
  gulp.watch("*.html", ['bs-reload']);
});
