const path = require('path');
const ESLintPlugin = require('eslint-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");

module.exports = {
    resolve: {
        alias: {
            /**
             * An alias for the JS imports.
             *
             * Example of usage:
             * import datepicker from "@/components/datepicker/datepicker.js";
             */
            '@': path.join(__dirname, '', 'resources/js'),
        },
    },

    module: {
        rules: [
            {
                enforce: 'pre',
                test: /\.(js|vue)$/,
                loader: 'eslint-loader',
                exclude: /node_modules/,
            },
        ],
    },

    // optimization: {
    //     minimize: true,
    //     minimizer: [new TerserPlugin({
    //         // include: /\/analytics\/injectSource\.js/,
    //         // terserOptions: {
    //         //
    //         // }
    //     })],
    // },
    // plugins: [new ESLintPlugin({})],
};
