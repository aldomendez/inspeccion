require("babel/polyfill")

Vue.filter('passQty', (value)=>{
  a = _.filter(value, (el)=>{
    /PASS/.test(el.status)
  });
  return a.length
})

Vue.filter('gen',(devices)=>{
  return _.compact(_.uniq(_.pluck( devices, 'item'))).map((el)=>{util.generations[el].gen}).join(',')
})

util = {}
util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php';
util.packId = Vue.resource(util.rootAddr + '/getCarrierSerials/:pack');
util.osfmData = Vue.resource(util.rootAddr + '/osfm/:serials');
util.reportPack = Vue.resource(util.rootAddr + '/flag/:carrier');
