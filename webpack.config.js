var path = require('path');

var config = {
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-react'],
                        plugins: ['@babel/plugin-proposal-object-rest-spread']
                    }
                }
            },

            {
                test: /\.s[ac]ss$/i,
                use: [
                    // Creates `style` nodes from JS strings
                    'style-loader',
                    // Translates CSS into CommonJS
                    'css-loader',
                    // Compiles Sass to CSS
                    'sass-loader',
                ],
            },
        ]
    },
};

var frontendConfig = Object.assign({}, config, {
    name: "frontend",
    entry:{
        main: './app/frontend/resources/js/main.js',
    },

    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'app/frontend/public/js/')
    }
});
var backendConfig = Object.assign({}, config,{
    name: "backend",
    entry:{
        main: './app/backend/resources/js/main.js',
    },

    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'app/backend/public/js/')
    }
});

module.exports = [
    frontendConfig, backendConfig,
];