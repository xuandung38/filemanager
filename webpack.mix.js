const mix = require('laravel-mix');

mix.js('src/Resources/js/app.js', 'src/assets/js/file-manager.min.js')
    .js('src/Resources/js/client.js', 'src/assets/js/client.min.js')
    .sass('src/Resources/css/app.scss', 'src/assets/css/file-manager.min.css')
    .copy('node_modules/element-ui/lib/theme-chalk/index.css', 'src/assets/css/element-ui.min.css')
    .copy('node_modules/element-ui/lib/theme-chalk/fonts', 'src/assets/css/fonts');
