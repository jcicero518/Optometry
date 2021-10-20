'use strict';

export default {

  browserPort: 3000,
  UIPort: 3001,

  sourceDir: './',
  buildDir: './build/',
  prodDir: './dist/',

  styles: {
	src: 'sass/**/*.scss',
	dest: 'build/css',
	prodDest: 'dist/css',
	prodSourcemap: false,
	sassIncludePaths: []
  },
  components: {
	src: 'app/components/**/*.html'
  },
  scripts: {
	src: 'js/custom/**/*.js',
  baseUrl: './js/custom',
	dest: 'build/js',
	prodDest: 'dist/js',
  entryFile: './js/custom/app.js',
  outputFile: 'app.js'
  },

  images: {
	src: '../../wp-content/uploads/**/*',
	dest: 'build/images',
	prodDest: 'dist/images'
  },

  media: {
	src: 'app/media/**/*',
	dest: 'build/media',
	prodDest: 'dist/media'
  },

  fonts: {
	src: ['fonts/**/*'],
	dest: 'build/fonts',
	prodDest: 'dist/fonts'
  },

  views: {
	index: 'app/**/*.html',
	src: 'app/views/**/*.html',
	dest: 'build/',
	prodDest: 'dist/'
  },

  init: function() {
	this.views.watch = [
	  this.views.index,
	  this.views.src
	];

	return this;
  }

}.init();
