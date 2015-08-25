Vue.component 'main-menu', {}

util = {}
util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php'
util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack')
util.osfmData = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/osfm/:serials')
util.generations = {
  'LR4GEN2OSA':{
    gen:'Gen2'
  },
  '5067-5071':{
    gen:'Gen3'
  }
}
util.statusCodes = [
  'Cargando desde Base de datos ...'
  'Cargando desde OSFM ...'
  'Completada la carga'
  'Carga fallida'
]

class Pack
  constructor: (@carrier) ->
    @contents = []
    @serials = []
    @status = 0
    @gen = ''
    @fetchCarrierContent()
  fetchCarrierContent:()->
    @status = 0
    util.packId.get {pack:@carrier}, (data)=>
      for i in data
        @serials.push i.SERIAL_NUM
        @contents.push [i.CARRIER_SITE, i.SERIAL_NUM, i.STATUS]
      @fetchDataFromOSFM()
    .error (data, status, request)->
      console.log data
      console.log status
  fetchDataFromOSFM:()->
    @status = 1
    util.osfmData.get {serials:"'#{@serials.join "','"}'"}, (data)=>
      @gen = util.generations[data[0].ITEM].gen || '--'
      data.forEach (osfmEl, i)=>
        @contents.some (contEl, i)=>
          if osfmEl.JOB is contEl[1]
            contEl.push osfmEl.ITEM
            contEl.push osfmEl.SUBINVENTORY_CODE
            contEl.push osfmEl.AGED_DAYS
            contEl.push osfmEl.DATE_RECEIVED
            # console.log osfmEl
            return true
      @status = 2
    .error (data, status, request)->
      console.log data
      console.log status


window.vm = new Vue {
  el:'#template'
  data:
    showHeader:true
    carriers:[]
    selectedpack:0
    newPackCarrier:'156269132'
  methods:{
    selectCarrier:(pack)->
      # console.log pack
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