const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');

module.exports = {
    configureWebpack: {
        plugins: [
            new MonacoWebpackPlugin({
                languages: ['javascript', 'css', 'html', 'typescript', 'json', 'php', 'python', 'markdown', 'rust'],
            }),
        ],
    },
    devServer: {
        host: '127.0.0.1',
        port: 8080,
        https: false,
        hotOnly: false,
    },
    pwa: {
        name: 'IntraCloud',
        themeColor: '#4DBA87',
        msTileColor: '#000000',
        appleMobileWebAppCapable: 'yes',
        appleMobileWebAppStatusBarStyle: 'black',
    },
};
