const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const autoprefixer = require("autoprefixer");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");
const getWPUrl = require("./webpack/js/scripts/lib/get-wp-url");

const PROD = process.env.NODE_ENV === "production";
const siteURL = getWPUrl();

module.exports = {
    mode: PROD ? "production" : "development",
    context: path.resolve(__dirname, "."),

    entry: {
        app: "./webpack/js/app.js",
        inline: "./webpack/css/sass/inline.scss",
        main: "./webpack/css/sass/main.scss",
    },

    output: {
        path: path.resolve(__dirname, "public"),
        filename: "js/[name].min.js", // força sempre minificado
    },

    resolve: {
        modules: [path.resolve(__dirname, "./src"), "node_modules"],
        fullySpecified: false,
        extensions: [".js", ".jsx", ".scss", ".css"],
        alias: {
            "simple-parallax-js$": path.resolve(
                __dirname,
                "node_modules/simple-parallax-js/index.js"
            ),
        },
    },

    // Sempre usa source-map, mas desativa inline maps para performance
    devtool: PROD ? false : "source-map",

    module: {
        rules: [
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {
                            sourceMap: !PROD,
                            url: false,
                        },
                    },
                    {
                        loader: "postcss-loader",
                        options: {
                            sourceMap: !PROD,
                            postcssOptions: {
                                plugins: [
                                    autoprefixer({
                                        overrideBrowserslist: [
                                            "> 1%",
                                            "last 2 versions",
                                            "ie >= 11",
                                        ],
                                    }),
                                ],
                            },
                        },
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            sourceMap: !PROD,
                            sassOptions: {
                                outputStyle: "compressed", // força compactação mesmo em dev
                            },
                        },
                    },
                ],
            },
            {
                test: /\.(ttf|otf|eot|svg|woff2?)$/i,
                type: "asset/resource",
                generator: {
                    filename: "fonts/[name][ext]",
                },
            },
            {
                test: /\.(png|jpe?g|gif|webp)$/i,
                type: "asset/resource",
                generator: {
                    filename: "image/[name][ext]",
                },
            },
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-env", "@babel/preset-react"],
                    },
                },
            },
        ],
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: "css/[name].min.css", // sempre minificado
        }),
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: [
                path.resolve(__dirname, "public/js/*"),
                path.resolve(__dirname, "public/css/*"),
            ],
        }),
        !PROD &&
            new BrowserSyncPlugin(
                {
                    proxy: siteURL,
                    files: [
                        "public/css/*.css",
                        "public/js/*.js",
                        "**/*.php",
                        "template-parts/**/*.php",
                    ],
                    injectCss: true,
                    open: false,
                    notify: false,
                },
                { reload: true }
            ),
    ].filter(Boolean),

    optimization: {
        minimize: true, // ✅ ativa minificação mesmo em dev
        minimizer: [
            new TerserPlugin({
                parallel: true,
                extractComments: false,
                terserOptions: {
                    compress: true,
                    format: {
                        comments: false,
                    },
                },
            }),
            new CssMinimizerPlugin(),
        ],
    },

    watchOptions: {
        poll: true,
        ignored: /node_modules/,
    },
};
