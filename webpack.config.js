const path = require("path");
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const webpack = require("webpack");
const env = JSON.stringify(process.env.NODE_ENV || "development");


let sassLoader = {}
if (env === 'development') {
	sassLoader = [
		{
			loader: 'style-loader'
		},
		{
			loader: 'css-loader',
			options: {
				modules: true
			}
		},
		{
			loader: 'sass-loader'
		}
	]
}

const config = {
	entry: ["./app/javascripts/index.js", "./app/styles/index.scss"],
	output: {
		path: path.resolve(__dirname, "dist"),
		filename: "bundle.js"
	},
	mode: "development",
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				include: path.join(__dirname, "node_modules", "select2"),
				use: "babel-loader"
			},
			{
				test: /\.css$/,
				use: [
					{
						loader: "style-loader"
					},
					{
						loader: "css-loader"
					}
				]
			},
			{
				test: /\.(gif|jpe?g|png)$/,
				loader: "url-loader",
				query: {
					limit: 10000,
					name: "images/[name].[ext]"
				}
			},
			{
				test: /\.scss$/,
				use:
					env !== "development"
						? ExtractTextPlugin.extract({
								fallback: "style-loader",
								use: [
									{
										loader: "css-loader?url=false"
									},
									{
										loader: "sass-loader"
									}
								]
						  })
						: sassLoader
			}
		]
	},
	plugins: [
		new webpack.DefinePlugin({
			NODE_ENV: env
		}),
		new ExtractTextPlugin({
			filename: "css/index.css"
		})
	]
};

module.exports = config;
