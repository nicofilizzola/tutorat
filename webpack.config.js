const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('app_home', './assets/routes/app_home.js')
    .addEntry('app_register', './assets/routes/app_register.js')
    .addEntry('app_register_after', './assets/routes/app_register_after.js')
    .addEntry('app_login', './assets/routes/app_login.js')
    .addEntry('app_sessions', './assets/routes/app_sessions.js')
    .addEntry('app_ownSessions', './assets/routes/app_ownSessions.js')
    .addEntry('app_sessions_view', './assets/routes/app_sessions_view.js')
    .addEntry('app_sessions_create', './assets/routes/app_sessions_create.js')
    .addEntry('app_users', './assets/routes/app_users.js')
    .addEntry('app_become-tutor', './assets/routes/app_become-tutor.js')
    .addEntry('app_subject', './assets/routes/app_subject.js')
    .addEntry('app_sessions_pending', './assets/routes/app_sessions_pending.js')
    .addEntry('app_sessions_log', './assets/routes/app_sessions_log.js')
    .addEntry('app_sessions_participants', './assets/routes/app_sessions_participants.js')
    .addEntry('app_classroom', './assets/routes/app_classroom.js')
    .addEntry('app_semester', './assets/routes/app_semester.js')
    .addEntry('app_contact', './assets/routes/app_contact.js')
    .addEntry('app_superadmin', './assets/routes/app_superadmin.js')
    .addEntry('app_start', './assets/routes/app_start.js')
    .addEntry('app_forgot_password_request', './assets/routes/app_forgot_password_request.js')
    .addEntry('app_check_email', './assets/routes/app_check_email.js')
    .addEntry('app_reset_password', './assets/routes/app_reset_password.js')
    

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    .copyFiles({
        from: './assets/img',

        // optional target path, relative to the output dir
        to: 'images/[name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg)$/
    });

module.exports = Encore.getWebpackConfig();
