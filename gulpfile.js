const { dest, series, src } = require('gulp');
const bump = require('gulp-bump');
const checktextdomain = require('gulp-checktextdomain');
const fs = require('fs');
const potomo = require('gulp-potomo');
const pottopo = require('gulp-pottopo');
const sort = require('gulp-sort');
const wpPot = require('gulp-wp-pot');
const YAML = require('yaml');

const config = YAML.parse(fs.readFileSync('./+/config.yml', 'utf8'));

function getBumpType() {
  const args = process.argv.slice(2);
  if (args.includes('--major')) return 'major';
  if (args.includes('--minor')) return 'minor';
  if (args.includes('--prerelease')) return 'prerelease';
  return 'patch'; // default
}

function bumpVersion() {
  return src(config.bump)
    .pipe(bump({ type: getBumpType(), keys: ['stable tag', 'version'] }))
    .pipe(dest('.'));
}

function poToMo() {
  return src(config.language.destination + '*.po')
    .pipe(potomo())
    .pipe(dest(config.language.destination));
}

function pot() {
  return src(config.language.watch)
    .pipe(checktextdomain({
      text_domain: config.language.domain,
      keywords: [
        '__:1,2d', '_e:1,2d', '_x:1,2c,3d',
        'esc_html__:1,2d', 'esc_html_e:1,2d', 'esc_html_x:1,2c,3d',
        'esc_attr__:1,2d', 'esc_attr_e:1,2d', 'esc_attr_x:1,2c,3d',
        '_ex:1,2c,3d', '_n:1,2,4d', '_nx:1,2,4c,5d',
        '_n_noop:1,2,3d', '_nx_noop:1,2,3c,4d'
      ]
    }))
    .pipe(sort())
    .pipe(wpPot({
      domain: config.language.domain,
      includePOTCreationDate: false,
      lastTranslator: config.language.translator,
      team: config.language.team
    }))
    .pipe(dest(`${config.language.destination}${config.language.domain}.pot`));
}

function potToPo() {
  return src(config.language.destination + '*.pot')
    .pipe(pottopo())
    .pipe(dest(config.language.destination));
}

exports.bump = bumpVersion;
exports.pot = pot;
exports['po-to-mo'] = poToMo;
exports['pot-to-po'] = potToPo;

exports.default = series(pot, potToPo, poToMo);
