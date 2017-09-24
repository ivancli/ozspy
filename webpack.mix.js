let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
/*js files*/
    .js('resources/assets/js/app/app.js', 'public/js')
    /*auth js*/
    .js('resources/assets/js/app/auth/login.js', 'public/js/auth')
    .js('resources/assets/js/app/auth/register.js', 'public/js/auth')
    .js('resources/assets/js/app/auth/forgot.js', 'public/js/auth')
    .js('resources/assets/js/app/auth/reset.js', 'public/js/auth')

    .sass('resources/assets/sass/app.scss', 'public/css')
    .copyDirectory('resources/assets/images', 'public/images')
    .sourceMaps();
