const mix = require('laravel-mix');

require('laravel-mix-eslint')

const webpackConfig = require('./webpack.config');

require('laravel-vue-i18n/mix')



/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .extract() //Separar código do vendor da app.js
    .vue()
    .eslint({
        fix: true,
        extensions: ['vue', 'js']
      })
    .i18n()
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        //require('tailwindcss'),
    ])
    .sass('resources/js/Layouts/App.scss', 'public/css/app.css')
    .version()

    .webpackConfig(webpackConfig) //Alias config
/*    .alias({
        '@': 'resources/js',
    });*/

if (mix.inProduction()) {
    mix.version();
}
