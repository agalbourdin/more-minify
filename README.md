AGL Framework - More/Minify
===========================

Additional Minify module for [AGL Framework](https://github.com/agl-php/agl-app).

## Installation

Run the following command in the root of your AGL application:

	php composer.phar require agl/more-minify:*

## Configuration

Create the following directory with write permissions: `public/minify/`.

Enable AGL Cache by editing `app/php/run.php`:

	define('AGL_CACHE_ENABLED', true);

All your CSS and JS files will automatically be minified and concatenated.

Minified files are stored in `public/minify/`. Delete files in this directory to regenerate the cache.
