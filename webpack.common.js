const webpack = require('webpack');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const path = require('path');

const babelOptions = {
    presets: [
        [
            "@babel/env",
            {
                targets: {
                    edge: "17",
                    firefox: "60",
                    chrome: "70",
                    safari: "11.1",
                },
                useBuiltIns: "usage",
            },
        ],
    ],
    plugins: [
        "@babel/syntax-dynamic-import"
    ]
};

module.exports = {
    entry: {
        app: "./frontend/js/app.js"
    },
    output: {
        path: path.resolve(__dirname, "public_html"),
        filename: "js/[name].js"
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules|public|production/,
                loader: "babel-loader",
                options: babelOptions
            },
            {
                test: /\.vue$/,
                loader: "vue-loader",
                options: {
                    loaders: {
                        js: 'babel-loader'
                    }
                }
            },
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            },
            {
                test: /\.scss/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.(jpe?g|gif|png|svg|eot|woff|woff2|ttf|wav|mp3|mp4|pdf)$/,
                use: [{loader: 'file-loader', options: {name: "static/[hash].[ext]"}}]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new webpack.ProvidePlugin({
            _:      'lodash/lodash.js',
            moment: 'moment',
            Vue: ['vue/dist/vue.esm.js', 'default'],
            mapState: ['vuex/dist/vuex.esm.js', 'mapState'],
            mapGetters: ['vuex/dist/vuex.esm.js', 'mapGetters'],
            mapActions: ['vuex/dist/vuex.esm.js', 'mapActions'],
        }),
    ]
};