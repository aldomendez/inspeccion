var gulp = require('gulp');
var coffee = require('gulp-coffee');
var gutil = require('gulp-util');
var livereload = require('gulp-livereload');
var wait = require('gulp-wait');
var sourcemaps = require("gulp-sourcemaps");
var babel = require("gulp-babel");
var concat = require("gulp-concat");

var DEST = '\\\\wmatvmlr401\\htdocs\\lr4\\check_osas';
var BASE = __dirname;

function serverPath (path) {
  // Obtiene la direccion a la que se enviaran los datos
  var rgx = /[a-zA-Z-_~\. ]*$/;
  path = path.replace(rgx, '');
  return path.replace(BASE,'');
}

copyAndReload = function copyAndReload (event) {
  console.log('Sended to: ',serverPath(event.path));
  gulp.src(event.path)
       .pipe(gulp.dest(DEST + serverPath(event.path)))
       .pipe(wait(1200))
       .pipe(livereload());
}

babelAndReload = function babelAndReload (event){
  var srvPath = serverPath(event.path)
  console.log("babel: ", srvPath);
  gulp.src(event.path)
    .pipe(sourcemaps.init())
    .pipe(babel()).on('error',gutil.log)
    .pipe(concat("all.js"))
    .pipe(sourcemaps.write("."))
    .pipe(gulp.dest("js"))
}

copy = function copy (event) {
  console.log('Sended to: ',serverPath(event.path));
  gulp.src(event.path)
       .pipe(gulp.dest(DEST + serverPath(event.path)))
       // .pipe(livereload(0));
}

compileAndPush = function compileAndPush (event) {
  path = event.path.replace(BASE,'');
  path = '.' + path.replace('\\','/');
  console.log(path);
  gulp.src(path)
    .pipe(coffee()).on('error',gutil.log)
    .pipe(gulp.dest('./js'))
    .pipe(gulp.dest(DEST + serverPath(event.path)))
    .pipe(wait(1200))
    .pipe(livereload());
}

gulp.task('default', function () {
  livereload.listen();
  /*
    La siguiente es la lista de todo lo que esta observando gulp, 
    me gustaria que pudiera decirle cueles son las carpetas que 
    tiene que omitir.
  */
  gulp.watch(["es6/**.js"]).on('change', function (event) {
    babelAndReload(event);
  });
  gulp.watch(['coffee/**.coffee'], function (event) {
  }).on('change', function (event) {
     compileAndPush(event);
  });  
  gulp.watch(['template.php'], function (event) {
  }).on('change', function (event) {
     copyAndReload(event);
  });  
  gulp.watch(['index.php','start.php'], function (event) {
  }).on('change', function (event) {
     copy(event);
  });  
  gulp.watch(['js/**.map'], function (event) {
  }).on('change', function (event) {
     copy(event);
  });  
  gulp.watch(['sql/**.sql'], function (event) {
  }).on('change', function (event) {
     copyAndReload(event);
  });  
  gulp.watch(['*.json'], function (event) {
  }).on('change', function (event) {
     copyAndReload(event);
  });  
  gulp.watch(['js/**.js','gulpfile.js'], function (event) {
  }).on('change', function (event) {
     copyAndReload(event);
  });  
  gulp.watch(['*.html'], function (event) {
  }).on('change', function (event) {
     copyAndReload(event);
  });
});

// gulp.task('default', ['watch']);
console.log("Listening on folder: " + BASE);
