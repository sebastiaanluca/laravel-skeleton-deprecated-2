const Helpers = require('./helpers')
const path = require('path')
const _ = require('lodash')

const webpack = require('webpack')
const autoprefixer = require('autoprefixer')
const ManifestPlugin = require('webpack-manifest-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')

const isProduction = process.env.APP_ENV === 'production'
const defaultFilename = isProduction ? '[name]-[hash]' : '[name]'
const target = 'public/assets'

// Integration for Laravel modules resources
const Modules = require('./modules')

const styleParser = new ExtractTextPlugin(`styles/${defaultFilename}.css`)

/* Configuration */

const config = {
    
    // Sourcemaps, etc.
    devtool: isProduction ? false : 'cheap-module-eval-source-map',
    
    entry: {
        // Dynamically concatenated from different sources (modules and vendors)
    },
    
    // Our entry points, with a unique name as key and a relative path
    // (starting from `context`) as value
    output: {
        // An absolute path to the desired output directory
        path: path.resolve(process.cwd(), target),
        
        // A filename pattern for the output files. This would create
        // `global.js` and `portfolio.js`
        filename: `scripts/${defaultFilename}.js`,
        
        // Used to define the root path of the publicly accessible assets
        publicPath: '/assets/',
    },
    
    module: {
        loaders: [
            {
                // Build browser-safe JavaScript files from ES2015 modules
                test: /\.jsx?$/i,
                // Don't (re)compile vendor JS files
                exclude: /(node_modules|bower_components)/,
                // 'babel-loader' is also a legal name to reference
                loader: 'babel',
                // Instead of queries, this uses the .babelrc file
            },
            
            {
                // Handle .vue components
                test: /\.vue$/,
                loader: 'vue',
                // Uses the .babelrc file to transpile ES2015 code in .vue files
            },
            
            {
                // Compile SASS to CSS with sourcemaps enabled
                test: /\.s?css$/i,
                loader: styleParser.extract(['css?-autoprefixer?sourceMap!postcss!sass?-autoprefixer?sourceMap']),
            },
            
            {
                // Disable compiling of vendor images (i.e. Vis)
                test: /\.(jpe?g|png|gif|svg)$/i,
                loaders: [
                    'null',
                ],
                include: /(node_modules|bower_components)/,
            },
            
            {
                // Optimize images
                test: /\.(jpe?g|png|gif|svg)$/i,
                loaders: [
                    'file?name=[path][name].[ext]&manipulateImageContext',
                    'image-webpack'
                ],
            },
            
            {
                // Extract vendor fonts and copy to public assets directory
                test: /\.(ttf|eot|woff2?|svg)(\?v=[a-z0-9=.]+)?$/i,
                loader: 'file?name=fonts/[name]/' + (isProduction ? '[hash]' : '[name]') + '.[ext]',
            },
        ],
    },
    
    plugins: [
        // Set our environment variables
        new webpack.DefinePlugin({
            'process.env': {
                'APP_ENV': JSON.stringify(process.env.APP_ENV),
                'NODE_ENV': JSON.stringify(process.env.APP_ENV),
            }
        }),
        
        // Log start of compiling
        function () {
            this.plugin('watch-run', function (watching, callback) {
                console.log('Beginning compile at ' + new Date())
                callback()
            })
        },
        
        new ManifestPlugin({
            fileName: 'rev-manifest.json'
        }),
        
        // Provide global support for vendor libraries
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            
            _: 'lodash',
            
            tether: 'tether',
            Tether: 'tether',
            'window.Tether': 'tether',
            
            Chartist: 'chartist',
            'window.Chartist': 'chartist',
        }),
        
        // This plugin looks for similar chunks and files and only
        // includes them once (and provides copies when used)
        new webpack.optimize.DedupePlugin(),
        
        // Split vendor resources from application code.
        // Only takes in account elements (JS, CSS, images) included in JS files,
        // not SASS files for instance (separate loader, no support yet).
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendor',
            minChunks: (module) => Helpers.isExternalModule(module)
        }),
        
        // Extract styles from scripts
        styleParser,
    ],
    
    // An array of extensions webpack should try to resolve in `require`, `import`, etc. statements
    resolve: {
        // Resolve modules from these directories. Allows to use
        // vendor/module and `custommodule` instead of referencing relatively (../../../)
        // See https://github.com/webpack/webpack/issues/472
        
        // An absolute path to a directory containing modules
        root: [path.resolve(process.cwd(), 'modules')],
        
        // A relative path to a tree of directories containing modules
        modulesDirectories: ['node_modules', 'modules'],
        
        extensions: ['', '.js', '.vue', '.css', '.scss'],
        
        alias: _.merge({
                // Force all modules to use the same jquery version
                // See https://github.com/Eonasdan/bootstrap-datetimepicker/issues/1319#issuecomment-208339466
                'jquery': path.resolve(process.cwd(), 'node_modules/jquery/src/jquery'),
                
                // Use runtime Vue version
                'vue$': path.resolve(process.cwd(), 'node_modules/vue/dist/vue.js'),
            },
            // Project-specific script resolves to be able to
            // write `import CurrentTime from CurrentTime`
            //  'theme': 'Theme/resources/scripts',
            _.mapValues(Modules.getAll(), function (value, key) {
                return (value + '/resources/scripts').replace('./modules/', '')
            })
        )
    },
    
    sassLoader: {
        // An array of paths that LibSass can look in to attempt to resolve your @import declarations
        includePaths: [
            path.resolve(process.cwd(), 'modules'),
            path.resolve(process.cwd(), 'node_modules'),
        ],
    },
    
    // Parses file loaders (only file-loader?) name string and enables you
    // to replace elements within it. Used here to provide a context to
    // image-webpack-loader and prevent images being placed in
    // e.g. public/assets/modules/theme/resources/images
    customInterpolateName: function (url, name, options) {
        if (this.query.indexOf('manipulateImageContext') !== - 1) {
            url = url.substring(url.indexOf('images'))
        }
        
        return url
    },
    
    // Image optimization settings
    imageWebpackLoader: {
        mozjpeg: {
            quality: 82
        },
        pngquant: {
            quality: "65-90",
            speed: 4
        },
        svgo: {
            plugins: [
                {removeEmptyAttrs: true},
                {cleanupAttrs: true},
                {removeComments: true},
                {removeMetadata: true},
                {removeTitle: true},
                {removeDesc: true},
                {removeEditorsNSData: true},
                {convertStyleToAttrs: true},
                {removeUselessDefs: true},
                {removeUnknownsAndDefaults: true},
                {removeUselessStrokeAndFill: true},
                {convertPathData: true},
                {removeDimensions: true},
            ]
        }
    },
    
    postcss() {
        return [autoprefixer]
    },
    
    // https://github.com/kentcdodds/webpack-dev-server-issue/tree/master/node_modules/webpack-dev-server/node_modules/http-proxy
    devServer: {
        port: process.env.SERVE_PORT || 8080,
        contentBase: target,
        // Where to serve the bundled assets from
        publicPath: process.env.SERVE_PROXY_TARGET + '/assets',
        // Enable web page updates without reloading
        hot: true,
        https: true,
        
        proxy: {
            '*': {
                target: process.env.SERVE_PROXY_TARGET,
                changeOrigin: true,
                autoRewrite: true,
                xfwd: true,
                secure: false,
            },
        },
        
        watchOptions: {
            aggregateTimeout: 20,
            poll: 1000
        },
    }
}

// Merge app-specific source modules into our source files
config.entry = Object.assign(config.entry, Modules.getModules())

// Optimize order and uglify JS in production
if (process.env.APP_ENV === 'production') {
    // Add additional plugins
    config.plugins = config.plugins.concat([
        // This plugins optimizes chunks and modules by
        // how much they are used in your app
        new webpack.optimize.OccurenceOrderPlugin(),
        
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                // Suppress uglification warnings
                warnings: false,
            },
            mangle: true,
            screw_ie8: true,
        }),
    ])
}

module.exports = config
