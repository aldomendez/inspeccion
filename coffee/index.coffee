Vue.component 'main-menu', {}

util = {}
util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php'
util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack')
util.osfmData = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/osfm/:serials')

class Pack
  constructor: (@carrier) ->
    @contents = []
    @serials = []
    @status = ''
    @fetchCarrierContent()
  fetchCarrierContent:()->
    @status = ': Buscando los datos del Pack'
    util.packId.get {pack:@carrier}, (data)=>
      for i in data
        @serials.push i.SERIAL_NUM
        @contents.push [i.CARRIER_SITE, i.SERIAL_NUM, i.STATUS]
      @fetchDataFromOSFM()
  fetchDataFromOSFM:()->
    @status = ': Buscando los datos en OSFM...'
    util.osfmData.get {serials:"'#{@serials.join "','"}'"}, (data)=>
      data.forEach (osfmEl, i)=>
        @contents.some (contEl, i)=>
          if osfmEl.JOB is contEl[1]
            contEl.push osfmEl.ITEM
            contEl.push osfmEl.SUBINVENTORY_CODE
            contEl.push osfmEl.AGED_DAYS
            # console.log osfmEl
            return true
      @status = ''

        


      
  


window.vm = new Vue {
  el:'#template'
  data:
    carriers:[]
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