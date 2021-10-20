"use strict";

const path = require('path');
const webpack = require('webpack');
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const CleanWebpackPlugin = require( 'clean-webpack-plugin' );
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const StyleExtHtmlWebpackPlugin = require('style-ext-html-webpack-plugin');

const config = require('./assets/config.json');

module.exports = {
    entry: {
        app: [
        	'./js/custom/app.js',
	        './sass/style.scss'
        ],
        vendor: ['lodash']
    },
    devtool: 'inline-source-map',
    plugins: [
	    new webpack.DefinePlugin({
		    'process.env': {
			    'NODE_ENV': JSON.stringify('development')
		    }
	    }),
        new CleanWebpackPlugin( ['dist'] ),
	    new webpack.NamedModulesPlugin(),
        //new webpack.HashedModuleIdsPlugin(), // for production only
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendor'
        }),
	    /*new webpack.optimize.CommonsChunkPlugin({
		    name: 'manifest'
	    }),*/
        /*new webpack.optimize.CommonsChunkPlugin({
            name: 'runtime'
        }),*/
        new ExtractTextPlugin({
	        //disable: process.env.NODE_ENV !== 'production',
            //filename: '[name].[contentHash].css',
            filename: '[name].min.css',
            allChunks: true
        }),

        new webpack.HotModuleReplacementPlugin(),
        new StyleExtHtmlWebpackPlugin({
            minify: true
        }),
	    new BrowserSyncPlugin({
		    //proxy: config.proxyUrl,
		    proxy: config.devUrl,
		    files: config.watch,
		    reloadDelay: config.reloadDelay
            //injectChanges: true
	    }, {
        	reload: false
	    })
    ],
	devServer: {
    	contentBase: './dist'
	},
    output: {
        path: path.resolve(__dirname, 'dist'), // the target directory for all output - files must be an absolute path
        filename: '[name].bundle.js', // the filename template for entry chunks - https://webpack.js.org/configuration/output/#output-filename
        //chunkFilename: '[name].vendor.bundle.js',
        publicPath: '/' // // the url to the output directory resolved relative to the HTML page
    },
    module: {
        rules: [
            {
                enforce: 'pre',
                test: /\.js$/,
                exclude: /node_modules/,
                use: [{
                    loader: 'eslint-loader',
                    options: {
                        formatter: require('eslint/lib/formatters/stylish'),
                        failOnError: false
                    }
                }]
            },
            {
                test: /\.js$/,
                //include: path.resolve(__dirname, 'assets/scripts/custom'),
                exclude: /node_modules/,
                use: [{
                    loader: 'babel-loader',
                    options: {
                        sourceMap: true
                    }
                }]
            },
            {
                test: /\.hbs$/,
                use: [{
                    loader: 'handlebars-loader'
                }]
            },
            {
                test: /\.css$/,
                use: [
                    'style-loader',
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {
                            importLoaders: 1,
                            sourceMap: true,
                            plugins: () => ([
                                require('autoprefixer')({
                                    browsers: ['last 2 versions', 'ie > 8']
                                })
                            ])
                        }
                    }
                ]
            },
            {
                test: /\.(sass|scss)$/,
                // .extract( options - { fallback, use* }
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: [
                        {
                            loader: 'css-loader',
                            options: {
                                minimize: true
                            }
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                sourceMap: true,
                                plugins: () => ([
                                    require('autoprefixer')({
                                        browsers: ['last 2 versions', 'ie > 8']
                                    })
                                ])
                            }
                        },
                        {
                            loader: 'resolve-url-loader',
	                        options: {
                            	debug: true
	                        }
                        },
                        {
                            loader: 'sass-loader',
	                        options: {
                            	sourceMap: true
	                        }
                        }
                    ]
                })
            },
            {
                test: /\.(ttf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
                //include: config.paths.assets,
                loader: 'url-loader',
                options: {
                    limit: 48096,
                    name: `[path][name].[ext]`
                }
            }
        ]
    }
};