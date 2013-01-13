More/Minify
===========

Additional Minify module for AGL.

## Installation

Add the following package to the `require` section of your application's `composer.json` file:

	"agl/more-minify": "*"

## Configuration

Create a folder `public/minify/` with write permissions.

Add the following event to your `app/etc/config/core/events.json` file:

	"agl_view_render_buffer_before": {
		"more/minify/observer": [
			"minify"
		]
	}

And set `AGL_CACHE_ENABLED` to `true` in your `app/php/run.php` file.

All your CSS and JS files will automatically be minified and concatenated.

Minified files are stored in `public/minify/`. Delete files in this folder to regenerate the cache.
