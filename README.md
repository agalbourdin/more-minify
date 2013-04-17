More/Minify
===========

Additional Minify module for AGL.

## Installation

Add the following package to the `require` section of your application's `composer.json` file:

	"agl/more-minify": "*"

Then run the following command:

	php composer.phar update

## Configuration

Create the following folder with write permissions: `public/minify/`

Enable AGL Cache by editing `app/php/run.php`:

	define('AGL_CACHE_ENABLED', true);

All your CSS and JS files will automatically be minified and concatenated.

Minified files are stored in `public/minify/`. Delete files in this folder to regenerate the cache.
