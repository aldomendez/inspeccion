'use strict';

require("babel/polyfill");

Vue.filter('passQty', function (value) {
  a = _.filter(value, function (el) {
    /PASS/.test(el.status);
  });
  return a.length;
});

Vue.filter('gen', function (devices) {
  return _.compact(_.uniq(_.pluck(devices, 'item'))).map(function (el) {
    util.generations[el].gen;
  }).join(',');
});
//# sourceMappingURL=all.js.map
