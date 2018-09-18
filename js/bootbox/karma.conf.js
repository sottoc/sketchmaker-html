var baseConfig = require('./karma-base.conf');

module.exports = baseConfig({
  vendor: [
    'tests/vendor/jquery-1.11.2.min.js',
    'tests/vendor/popper.min.js',
    'tests/vendor/bootstrap-4.0.0-beta.min.js'
  ],
  src: ['src/bootbox.js', 'src/bootbox.locales.js']
});
