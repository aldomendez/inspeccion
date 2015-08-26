Vue.filter 'passQty', (value)-> 
  a = _.filter value, (el)-> /PASS/.test(el.status)
  a.length
Vue.filter 'gen', (devices)->
  _.compact(_.uniq( _.pluck( devices, 'item'))).map((el)->util.generations[el].gen).join(',')

util = {}
util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php'
util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack')
util.osfmData = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/osfm/:serials')
util.reportPack = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/flag/:carrier')
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
  ' === Reported Pack ==='
]

class Pack
  constructor: (@carrier) ->
    @contents = []
    @serials = []
    @status = 0
    @gen = ''
    @check = false
    @fetchCarrierContent()
  fetchCarrierContent:()->
    @status = 0
    util.packId.get {pack:@carrier}, (data)=>
      console.log data
      if data[0]?.ITEM? then @gen = util.generations[data[0].ITEM].gen || '--'
      for i in data
        if !i.ACTUAL_STATUS? or i.ACTUAL_STATUS is 'noOsfmData' then @serials.push i.SERIAL_NUM
        if i.ACTUAL_STATUS is 'problem_detected' then check = true else check = false
        # if i.ACTUAL_STATUS is 'problem_detected' then @check = true
        @contents.push {
          'carrier_site':i.CARRIER_SITE
          'serial_num':i.SERIAL_NUM
          'status':i.STATUS
          'item':i.ITEM
          'osfm_location':i.OSFM_LOCATION
          'aged_days':i.AGED_DAYS
          'date_received':i.DATE_RECEIVED
          'actual_status':i.ACTUAL_STATUS
          'check': check
        }
      @fetchDataFromOSFM()
    # .error (data, status, request)->
    #   console.log data
    #   console.log status
  fetchDataFromOSFM:()->
    @status = 1
    if @serials.length isnt 0
      util.osfmData.get {serials:"'#{@serials.join "','"}'"}, (data)=>
        console.log data
        @gen = util.generations[data[0].ITEM].gen || '--'
        data.forEach (osfmEl, i)=>
          # utilizo `some` por que quiero que retorne tan pronto como encuentre 
          # un resultado, en vez de recorrer todos los registros
          @contents.some (contEl, i)=>
            if osfmEl.JOB is contEl.serial_num
              contEl.item = osfmEl.ITEM
              contEl.osfm_location = osfmEl.SUBINVENTORY_CODE
              contEl.aged_days = osfmEl.AGED_DAYS
              contEl.date_received = osfmEl.DATE_RECEIVED
              # console.log osfmEl
              return true
        @status = 2
      .error (data, status, request)->
        console.log data
        console.log status


window.vm = new Vue {
  el:'#template'
  data:
    statusCodes:util.statusCodes
    showHeader:true
    carriers:[]
    selectedpack:0
    newPackCarrier:''
  methods:{
    reportPack:(e)->
      e.preventDefault()
      if @carriers[@selectedpack]?
        util.reportPack.get {carrier:@carriers[@selectedpack].carrier}, (data)=>
          @carriers[@selectedpack].status = 5
       
      else
        console.log 'No carrier selected'
    markAsOk:()->
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

Mousetrap.bind 'j', (e)->
  if vm.carriers.length > 0 and vm.selectedpack < vm.carriers.length-1
    vm.selectedpack++
Mousetrap.bind 'k', (e)->
  if vm.carriers.length isnt 0 and vm.selectedpack >= 1
    vm.selectedpack--
