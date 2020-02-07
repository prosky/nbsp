const {resolve} = require('path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const DEV_SERVER = Boolean(process.env.WEBPACK_DEV_SERVER);
const CLIENT_DIR = resolve(__dirname, '..');
const ROOT_DIR = resolve(CLIENT_DIR, '..');
const WWW_DIR = resolve(ROOT_DIR, 'www');
const NODE_DIR = resolve(ROOT_DIR, 'node_modules');
const OUTPUT_DIR = resolve(WWW_DIR, 'build');

module.exports = {
    DEV_SERVER, CLIENT_DIR, ROOT_DIR, NODE_DIR,  WWW_DIR, OUTPUT_DIR,
    common: {
        entry: {
            front: [
                resolve(CLIENT_DIR, 'front.js'),
                resolve(CLIENT_DIR, 'front.scss')
            ],
        },
        resolve: {
            modules: [
                'node_modules'
            ],
            extensions: ['.js', '.jsx','.scss' ,'.css'],
            alias: {
                client: CLIENT_DIR,
            },
        },
        module: {
            rules: [
                {
                    test: /\.jsx?$/,
                    loader: 'babel-loader',
                    include: [
                        CLIENT_DIR
                    ],
                    options: {
                        plugins: [
                            '@babel/plugin-proposal-nullish-coalescing-operator',
                            '@babel/plugin-proposal-optional-chaining',
                            "@babel/plugin-proposal-private-methods",
                            "@babel/plugin-proposal-class-properties",
                            "@babel/plugin-syntax-dynamic-import"
                        ]
                    },
                },
                {
                    test: /\.s?css$/,
                    use: [
                        'style-loader',
                        //DEV_SERVER ? 'css-hot-loader' : false,
                        //MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: { sourceMap: true },
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: true
                            },
                        },
                    ].filter(Boolean),
                }
            ],
        },
        plugins: [
            !DEV_SERVER ? new CleanWebpackPlugin() : false,
            new ManifestPlugin(),
        ].filter(Boolean),
    }
};
