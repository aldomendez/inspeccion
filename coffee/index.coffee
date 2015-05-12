
r = new Ractive
  el: 'container'
  template:'#template'
  data:{
    components: ["Selecciona un componente","PLC","ALPS","GlassRail","OSA","Ceramico","PDArray","Laser","Pin"]
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
    carrier:''
    saved:false
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
        {CARRIER_SITE:1,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:2,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:3,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:4,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:5,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:6,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:7,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:8,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:9,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
        },{CARRIER_SITE:10,STATUS:true,COMPONENT: "Selecciona un componente",FAILMODE: "Selecciona un modo de falla",COMMENTS:''
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
          if el.STATUS is false or el.COMMENTS isnt ''
            if el.COMMENTS isnt ''
              el.COMPONENT = ''
              el.FAILMODE = ''
            return el
          else
            return null
        components = _.filter components, (el)-> el?
        components.map (el, i)->
          if el isnt null and el.COMMENTS is ''
            if el.COMPONENT is "Selecciona un componente" then throw {message:'Debes de seleccionar el componente', path:'component', position:el.CARRIER_SITE }
            if el.FAILMODE is "Selecciona un modo de falla" then throw {message:'Debes de seleccionar un modo de falla', path:'failMode', position:el.CARRIER_SITE }
            
        r.set 'error', undefined
        console.log components
        return components
      catch e
        console.log e
        r.set 'error', e
        return {error:true}
    saveFailData:(validatedComponents)->
      user = r.get 'userNumber'
      addr = "http://cymautocert/osaapp/inspeccion/index.php/saveFailData"
      data = {
        user: user
        components: validatedComponents
      }
      console.log data
      promise = $.post addr, data
      promise.done (data)->
        console.log data
      return promise


  }


loadFetchedIntoR = (data)->
  r.set 'saved', false
  data.map (el, i, array)->
    if /^P/.test(el.STATUS) 
      if el.SAVEDSTATUS?
        if el.SAVEDSTATUS is 'P'
          pass_fail = true
        else
          pass_fail = false
      else
        pass_fail = true
    else
      pass_fail = false
    exists = if el.SAVEDSTATUS? then true else false
    r.set "carrierContents.#{el.CARRIER_SITE-1}.CARRIER_SERIAL_NUM",  el.CARRIER_SERIAL_NUM
    r.set "carrierContents.#{el.CARRIER_SITE-1}.STATUS",              pass_fail
    r.set "carrierContents.#{el.CARRIER_SITE-1}.SERIAL_NUM",          el.SERIAL_NUM
    r.set "carrierContents.#{el.CARRIER_SITE-1}.CARRIER_SITE",        el.CARRIER_SITE
    r.set "carrierContents.#{el.CARRIER_SITE-1}.COMMENTS",            el.COMMENTS || ''
    r.set "carrierContents.#{el.CARRIER_SITE-1}.COMPONENT",           el.COMPONENT || 'Selecciona un componente'
    r.set "carrierContents.#{el.CARRIER_SITE-1}.FAILMODE",            el.FAILMODE || 'Selecciona un modo de falla'
    r.set "carrierContents.#{el.CARRIER_SITE-1}.exists",              exists

r.on 'cargarCarrier', (e)->
  e.original.preventDefault()
  epoxy.fetchAll(r.get 'carrier').done (data)->
    loadFetchedIntoR(data)

r.observe 'carrierContents.*', ()->
  r.set 'saved', false

r.observe 'carrier', (nVal, oVal)->
  if nVal.length is 9
    epoxy.fetchAll(r.get 'carrier').done (data)->
      loadFetchedIntoR(data)
      console.log r.get 'carrierContents'
    
r.on 'validateAndSave', (e)->
  if !r.get 'saved'
    validatedComponents = epoxy.validate()
    console.log validatedComponents
    if !validatedComponents?.error?
      epoxy.saveFailData(validatedComponents)
      r.set 'saved', true

  
window.epoxy = epoxy
window.r = r