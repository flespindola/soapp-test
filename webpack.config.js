const path = require("path");
//Alias definidos aqui para phpstorm interpretar correctameente
module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js')
        }
    }
}
/*
https://inertiajs.com/code-splitting
Code splitting

Code splitting breaks apart the various pages of your application into smaller bundles, which are then loaded on demand
when visiting new pages. This can significantly reduce the size of the initial JavaScript bundle loaded by the browser,
improving the time to first render.

While code splitting is helpful for very large projects, it does require extra requests when visiting new pages.
Generally speaking, if you're able to use a single bundle, your app is going to feel snappier.

app.js
resolve: name => import(`./Pages/${name}`),

webpack.config.js
output: {
  chunkFilename: 'js/[name].js?id=[chunkhash]',
}

* */
