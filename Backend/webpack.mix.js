const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

mix.scripts([
    'resources/app/assets/plugins/jquery/jquery-2.1.1.min.js',
    'resources/app/assets/plugins/moment/min/moment.min.js',
    //'resources/app/assets/plugins/dropzone/dist/dropzone.js',
    'resources/app/assets/plugins/chart.js/dist/Chart.min.js',
    //'resources/app/assets/plugins/fullcalendar/dist/fullcalendar.js',
    //'resources/app/assets/plugins/bootstrap-validator/dist/validator.min.js',
    //'resources/app/assets/plugins/select2/dist/js/select2.full.min.js',
    //'resources/app/assets/plugins/ckeditor/ckeditor.js',
    //'resources/app/assets/plugins/datatable/media/js/jquery.dataTables.min.js',
//	'resources/app/assets/plugins/datatable/media/js/dataTables.bootstrap4.min.js',
    'resources/app/assets/plugins/exort/uploader.min.js',
    'resources/app/assets/plugins/tether/dist/js/tether.min.js',
    'resources/app/assets/plugins/bootstrap/dist/js/bootstrap.min.js',
    //'resources/app/assets/plugins/owl-carousel/dist/owl.carousel.min.js',
    'resources/app/assets/plugins/slimscroll/jquery.slimscroll.min.js',
    'resources/app/assets/plugins/nanobar/nanobar.min.js',
    //'resources/app/assets/plugins/jquery-ui/jquery-ui.min.js',
    //'resources/app/assets/plugins/bootstrap-daterangepicker/daterangepicker.js',
    'resources/app/assets/plugins/tilt/tilt.jquery.min.js',
    'resources/app/assets/plugins/alertify/alertify.js',
//	'resources/app/assets/plugins/tilt/tilt.jquery.min.js'
], 'public/app/js/vendor.js');
