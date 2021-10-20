"use strict";

const path = require('path');
const webpack = require('webpack');
const merge = require('webpack-merge');
const common = require('./webpack.common.js');
const config = require('./assets/config.json');
const buildFolder = path.resolve( __dirname, 'build' );

const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const CleanWebpackPlugin = require( 'clean-webpack-plugin' );
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const StyleExtHtmlWebpackPlugin = require('style-ext-html-webpack-plugin');

module.exports = merge( common, {
    devtool: 'cheap-module-source-map',
    plugins: [
        new webpack.DefinePlugin({
            'process.env': {
                'NODE_ENV': JSON.stringify('production')
            }
        }),
        new webpack.optimize.UglifyJsPlugin({
            mangle: {
                screw_ie8: true
            },
            compress: {
                screw_ie8: true,
                warnings: false
            },
            output: {
                comments: false
            },
            sourceMap: true
        }),
        new CleanWebpackPlugin( [buildFolder] ),
        new ExtractTextPlugin({
            disable: false,
            filename: '[name].min.css',
            //filename: '[name].[contentHash].css',
            allChunks: true
        })
    ],
    output: {
        path: buildFolder + '/dist'
    }
});