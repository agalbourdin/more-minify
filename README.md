AGL Framework - More/Minify
===========================

Additional Minify module for [AGL Framework](https://github.com/agl-php/agl-app).

## Installation

Run the following command in the root of your AGL application:

	composer require agl/more-minify:*

## Configuration

Enable AGL Cache by editing `app/php/run.php`:

	define('AGL_CACHE_ENABLED', true);

All your CSS and JS files will automatically be minified and concatenated.
Cached files will alse be updated if source files are modified.
