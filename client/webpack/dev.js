const {resolve} = require('path');
const fs = require('fs');
const webpack = require('webpack');
const merge = require('webpack-merge');
const {common, ROOT_DIR, DEV_SERVER, WWW_DIR, OUTPUT_DIR} = require('./common.js');
const chokidar = require('chokidar');
const ip = require('ip');
const DIST_DIR = resolve(OUTPUT_DIR, 'dev');
const DEV_HOST = ip.address();
const DEV_PORT = 8099;
let PUBLIC_PATH = '/build/dev/';

if (DEV_SERVER) PUBLIC_PATH = `http://${DEV_HOST}:${DEV_PORT}${PUBLIC_PATH}`;
fs.unlink(resolve(ROOT_DIR, 'temp', 'cache', '_assets'), () => null);
console.log(PUBLIC_PATH);
module.exports = merge(common, {
    mode: 'development',
    devtool: 'source-map',
    devServer: {
        contentBase: WWW_DIR,
        publicPath: PUBLIC_PATH,
        disableHostCheck: true,
        headers: {
            'Access-Control-Allow-Origin': '*',
        },
        stats: {
            colors: true
        },
        host: DEV_HOST,
        port: DEV_PORT,
        hot: true,
        before(app, server) {
            const files = [ROOT_DIR + "/app/**/*.latte"];
            chokidar.watch(files, {
                alwaysStat: true,
                atomic: false,
                followSymlinks: false,
                ignoreInitial: true,
                ignorePermissionErrors: true,
                persistent: true,
                usePolling: true
            }).on('all', () => {
                server.sockWrite(server.sockets, "content-changed");
            });
        },
    },
    output: {
        path: DIST_DIR,
        publicPath: PUBLIC_PATH,
        filename: DEV_SERVER ? '[name].js' : '[name].[hash:8].js',
        chunkFilename: DEV_SERVER ? '[name].js' : '[name].[hash:8].js',
    },
    stats: {colors: true},
    plugins: [
        DEV_SERVER ?new webpack.HotModuleReplacementPlugin():false
    ].filter(Boolean),
});