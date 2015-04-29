
r = new Ractive
  el: 'container'
  template:'#template'
  data:{
    components: ["Selecciona un componente","PLC","ALPS","GlassRail","OSA","Ceramico","PDArray"]
    failMode: [
      'Selecciona un modo de falla'
      "Desprendido"
      "Dañado"
      "Contaminado"
      "Fuera de posicion"
      "Exceso de epoxy"
      "Fracturado"
      "Falta epoxy"
      ]
    step:0
    userNumber:''
    carrier:'155772978'
    carrierContents:[
      {CARRIER_SITE:1,STATUS:true
      },{CARRIER_SITE:2,STATUS:true
      },{CARRIER_SITE:3,STATUS:true
      },{CARRIER_SITE:4,STATUS:true
      },{CARRIER_SITE:5,STATUS:true
      },{CARRIER_SITE:6,STATUS:true
      },{CARRIER_SITE:7,STATUS:true
      },{CARRIER_SITE:8,STATUS:true
      },{CARRIER_SITE:9,STATUS:true
      },{CARRIER_SITE:10,STATUS:true
      }]
  }
development = true
log = (message)->
  if development
    console.log message

epoxy = do()->
  epoxy = {
    lotRegex:/(.*)\/(.*)\/(.*)/
    parseDate : (d) ->
      # yyyymmddhhmiss
      new Date(d.substring(0,4),
        d.substring(4, 6) - 1,
        d.substring(6, 8),
        d.substring(8, 10),
        d.substring(10, 12),
        d.substring(12,14))

    fetchAll:(carrier)->
      r.set 'carrierContents', [
        {CARRIER_SITE:1,STATUS:true
        },{CARRIER_SITE:2,STATUS:true
        },{CARRIER_SITE:3,STATUS:true
        },{CARRIER_SITE:4,STATUS:true
        },{CARRIER_SITE:5,STATUS:true
        },{CARRIER_SITE:6,STATUS:true
        },{CARRIER_SITE:7,STATUS:true
        },{CARRIER_SITE:8,STATUS:true
        },{CARRIER_SITE:9,STATUS:true
        },{CARRIER_SITE:10,STATUS:true
        }]

      addr = "http://cymautocert/osaapp/inspeccion/index.php/carrier/#{carrier}"
      promise = $.getJSON addr
      promise.done (data)->
        console.log data
      return promise

    validate:()->
      user = r.get 'userNumber'
      carrier = r.get 'carrier'
      components = r.get 'carrierContents'

      try
        if user is '' then throw {message:'Ingresa un NUMERO DE USUARIO', path:'user'}
        if user.length > 10 then throw {message:'Numero de usuario demasiado largo', path:'user'}
        if carrier.length isnt 9 then throw {message:'Numero de carrier cambio, Ingresa el carrier de nuevo', path:'carrier'}
        components = components.map (el, i)->
          if el.STATUS is false
            return el
          else
            return null
        console.log components
        components = components.map (el, i)->
          if el isnt null
            if !el.COMPONENT? then throw {message:'Debes de seleccionar el componente', path:'component', position:el.CARRIER_SITE }
            if !el.FAILMODE? then throw {message:'Debes de seleccionar un modo de falla', path:'failMode', position:el.CARRIER_SITE }
            
        r.set 'error', undefined
        return components
      catch e
        console.log e
        r.set 'error', e
        return {error:true}
  }


loadFetchedIntoR = (data)->
  data.map (el, i, array)->
    r.set "carrierContents.#{el.CARRIER_SITE-1}.CARRIER_SERIAL_NUM",  el.CARRIER_SERIAL_NUM
    r.set "carrierContents.#{el.CARRIER_SITE-1}.STATUS",              if /PASS/.test(el.STATUS) then true else false
    r.set "carrierContents.#{el.CARRIER_SITE-1}.SERIAL_NUM",          el.SERIAL_NUM
    r.set "carrierContents.#{el.CARRIER_SITE-1}.CARRIER_SITE",        el.CARRIER_SITE

r.on 'cargarCarrier', (e)->
  e.original.preventDefault()
  epoxy.fetchAll(r.get 'carrier').done (data)->
    loadFetchedIntoR(data)

r.observe 'carrier', (nVal, oVal)->
  if nVal.length is 9
    epoxy.fetchAll(r.get 'carrier').done (data)->
      loadFetchedIntoR(data)
    
r.on 'validateAndSave', (e)->
  epoxy.validate()
  
window.epoxy = epoxy
window.r = r