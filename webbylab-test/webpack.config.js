var path = require('path');

module.exports = {
  mode: 'development',
  entry: './resources/js/app.js',
  output: {
    path: path.resolve(__dirname, 'public/assets/js'),
    filename: 'app.js'
  }
};

