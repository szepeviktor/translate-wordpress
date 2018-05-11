const webpack = require('webpack');
const path = require('path');

const config = {
  entry: './app/javascripts/index.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js'
  },
  mode: 'development',
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          'style-loader',
          'css-loader'
        ]
      },
      {
        test: /\.scss$/,
        use: [
          'style-loader',
          'css-loader',
          'sass-loader'
        ]
	  },
	  {
        test: /\.(js|jsx)$/,
		exclude: /node_modules/,
		include: path.join(__dirname, 'node_modules', 'select2'),
        use: 'babel-loader'
      }
    ]
  },
  resolve: {
    extensions: [
      '.js',
      '.jsx'
    ]
  }
}

module.exports = config;
