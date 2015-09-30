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