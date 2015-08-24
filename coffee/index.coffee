Vue.component 'main-menu', {}

util = {}
util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php'
util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack')
class Pack
  constructor: (@carrier) ->
    @contents = []
    @status = 'Buscando los datos del Pack'
    @fetchCarrierContent()
  fetchCarrierContent:()->
    util.packId.get {pack:@carrier}, (data)=>
      for i in data
        @contents.push [i.CARRIER_SITE, i.SERIAL_NUM, i.STATUS]
      
  


window.vm = new Vue {
  el:'#template'
  data:
    carriers:[
      {
        carrier:'156181394'
        contents:[
          ['1','159866954','PASS/POST-PURGE','159866954','MSPP-PIC','5067-5071','0']
          ['2','159866954','PASS/POST-PURGE','159866954','MSPP-PIC','5067-5071','4']
          ['3','159866954','PASS/POST-PURGE','159866954','MSPP-PIC','5067-5071','3']
          ['4','159866954','PASS/POST-PURGE','159866954','MSPP-PIC','5067-5071','0']
        ]
      }
      {
        carrier:'156181394'
        contents:[
          ['1','156179044','PASS/POST-PURGE','156179044','MSPP-PIC','5067-5071','0']
          ['2','156179044','PASS/POST-PURGE','156179044','MSPP-PIC','5067-5071','4']
          ['3','156179044','PASS/POST-PURGE','156179044','MSPP-PIC','5067-5071','3']
          ['4','156179044','PASS/POST-PURGE','156179044','MSPP-PIC','5067-5071','0']
        ]
      }
    ]
    selectedpack:0
    newPackCarrier:'156269132'
  methods:{
    selectCarrier:(pack)->
      console.log pack
      vm.selectedpack = pack
    isActive:(selected)->
      return (vm.selectedpack is selected)?true:false
    newPack:(e,carrier)->
      e.preventDefault()
      if vm.newPackCarrier.length is 9
        vm.carriers.unshift new Pack(vm.newPackCarrier)
        vm.newPackCarrier = ''
        vm.selectedpack = 0
  }
}