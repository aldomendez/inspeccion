
r = new Ractive
  el: 'container'
  template:'#template'
  data:{
    allowedEpoxyTypes: ["3410-XTP", "353ND", "2030SC", "3408"]
    epoxys:{}
    creatingNew: false
    step:0
    askDisposeComment:false
    newSyringe:{
      type:''
      lot:''
      operator:''
      expiration:''
      disposeComment:''
    }
  }
development = true
log = (message)->
  if development
    console.log message

epoxy = do()->
  epoxy = {
    lotRegex:/(.*)\/(.*)\/(.*)/
    parseDate : (d) ->
      new Date(d.substring(0,4),
        d.substring(4, 6) - 1,
        d.substring(6, 8),
        d.substring(8, 10),
        d.substring(10, 12),
        d.substring(12,14))
    parseyyyymmdd: (d)->
      new Date(d.substring(0,4),
      d.substring(5,7)-1,
      d.substring(8,10))

    fetchAll:()->
      r.get('allowedEpoxyTypes').forEach (el)->
        r.set("epoxys.#{el}",null);
      addr = 'http://cymautocert/osaapp/epoxy/index.php/epoxy/all'
      promise = $.getJSON addr
      promise.done (data)->
        data.forEach (el, i, array)->
          type = epoxy.lotRegex.exec(el.LOT_NUMBER)[1]
          if type
            el.type = type
            el.expiration = epoxy.parseDate el.EXPIRE_DATE
            r.set "epoxys.#{type}" , el
      return promise

    dispose:(lot,comment='')->
      addr = 'http://cymautocert/osaapp/epoxy/index.php/epoxy/dispose'
      promise = $.post addr, {lot:lot, comment:comment}
      promise.done (data)->
        console.log data
      return promise

    validate:(original)->
      # This is for cleaning
      for k, v of original
        original[k] = original[k].trim().toUpperCase()

      try
        actualDate = epoxy.parseyyyymmdd(original.expiration)
        allowedEpoxyTypes = r.get 'allowedEpoxyTypes'
        if allowedEpoxyTypes.indexOf(original.type) is -1 then throw {message:'No es un epoxy permitido', path:'type'}
        if original.lot is '' then throw {message:'Ingresa un numero de lote', path:'lot'}
        if original.operator is '' then throw {message:'Ingresa un numero de operador', path:'operator'}
        if original.expiration is '' then throw {message:'Ingresa un fecha de expiracion', path:'expiration'}
        if actualDate - new Date() < 0 then throw {message:'El epoxy esta caducado o la fecha es incorrecta', path:'expiration'}
        if original.disposeComment? then delete original.disposeComment
        r.set 'error', undefined
        return original
      catch e
        console.log e
        r.set 'error', e
        return {error:true}
        
      

    register:(values)->
      addr = 'http://cymautocert/osaapp/epoxy/index.php/epoxy/register'
      return $.post addr, values


  }

epoxy.fetchAll()

r.on 'refreshLive', (e)->
  e.original.preventDefault()
  epoxy.fetchAll()


r.on 'registerNewSyringe', (e)->
  e.original.preventDefault()
  # console.log r.get 'newSyringe'

  validated = epoxy.validate(_.clone r.get 'newSyringe')
  console.log validated
  if not validated.error?
    epoxy.register(validated).done (data)->
      r.set {
        'newSyringe.lot':'',
        'newSyringe.operator':'',
        'newSyringe.expiration':'',
        'newSyringe.type':'',
        step:0,
        askDisposeComment:false
      }
      epoxy.fetchAll()


r.on 'createNewEpoxy', (e)->
  e.original.preventDefault()
  # console.log e
  r.set 'creatingNew', true
  epoxy.fetchAll().done (data)->
    type = e.context
    if r.get "epoxys.#{type}" then step = 1 else step = 2
    r.set {
      step:step
      'newSyringe.type':type
      askDisposeComment:false
    }

r.on 'askForComment', (e)->
  e.original.preventDefault()
  r.set {
    askDisposeComment:true
    'newSyringe.disposeComment':''
  }

r.on 'returnToStart',(e)->
  e.original.preventDefault()
  r.set {
    step:0
    'newSyringe.type':''
    askDisposeComment:false
  }

r.on 'validateAndDisposeSyringe',(e)->
  e.original.preventDefault()
  comment = r.get 'newSyringe.disposeComment'
  type = r.get 'newSyringe.type'
  lot = r.get "epoxys.#{type}.LOT_NUMBER"
  commentLength = comment.length
  if commentLength > 450 then throw new Error 'No se puede capturar un comentario tan largo'
  if comment is '' then throw new Error 'Escribe un comentario'
  promise = epoxy.dispose lot, comment
  promise.done (data)->
    epoxy.fetchAll()
    r.set {
      step:2
      askDisposeComment:false
    }

r.observe 'newSyringe.type', ()->
  type = r.get 'newSyringe.type'
  lot = r.get "epoxys.#{type}.LOT_NUMBER"
  # console.log type
  if lot isnt undefined
    # console.log 'lot undefined'
    r.set 'step', 1



window.r = r