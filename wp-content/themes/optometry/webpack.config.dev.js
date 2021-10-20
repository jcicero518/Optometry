"use strict";

const merge = require('webpack-merge');
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const CleanWebpackPlugin = require( 'clean-webpack-plugin' );

const common = require('./webpack.common.js');
const config = require('./assets/config.json');

module.exports = merge( common, {
    devtool: 'eval-source-map'
    /*entry: {
        app: './assets/scripts/custom/app.js'
    },*/
    //plugins: [
        //new BrowserSyncPlugin({
            //proxy: config.proxyUrl,
            //proxy: config.devUrl,
            //files: [
                //'**/*.php'
            //],
            //reloadDelay: 0
        //})
    //]
});