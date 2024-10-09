const mix = require('laravel-mix');

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

/**
 * Custom webpack config.
 */
mix.webpackConfig(require('./webpack.config'));

const tailwindcss = require('tailwindcss');

mix.js('resources/js/element/app.js', 'public/js')
    .js('resources/js/tailwind/app.js', 'public/js/app-tailwind.js')
    .js('resources/js/analytics/app.js', 'public/js/stream-analytics.js')
    .sass('resources/style/element/app.scss', 'public/css')
    .sass('resources/style/tailwind/app.scss', 'public/css/app-tailwind.css')
    .copy('node_modules/element-ui/lib/theme-chalk/fonts/element-icons.woff', 'public/fonts/element-icons.woff')
    .copy('node_modules/element-ui/lib/theme-chalk/fonts/element-icons.ttf', 'public/fonts/element-icons.ttf')
    .options({
        processCssUrls: false,
        postCss: [tailwindcss('./tailwind.config.js')],
    });

mix.options({
    globalVueStyles: `resources/style/element/_variables.scss`,
    processCssUrls: false
});
