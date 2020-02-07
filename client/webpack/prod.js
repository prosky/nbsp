const resolve = require('path').resolve;
const merge = require('webpack-merge');
const {common, OUTPUT_DIR}= require('./common.js');
const DIST_DIR = resolve(OUTPUT_DIR, 'prod');

module.exports = merge(common, {
	mode: 'production',
	devtool: 'source-map',
	output: {
		path: DIST_DIR,
		publicPath: '/build/prod/',
		filename: '[name].[contenthash:8].js',
		chunkFilename: '[name].[contenthash:8].js'
	}
});
