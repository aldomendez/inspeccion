(function() {
  var development, epoxy, log, r;

  r = new Ractive({
    el: 'container',
    template: '#template',
    data: {
      allowedEpoxyTypes: ["3410-XTP", "353ND", "2030SC", "3408"],
      epoxys: {},
      creatingNew: false,
      step: 0,
      askDisposeComment: false,
      newSyringe: {
        type: '',
        lot: '',
        operator: '',
        expiration: '',
        disposeComment: ''
      }
    }
  });

  development = true;

  log = function(message) {
    if (development) {
      return console.log(message);
    }
  };

  epoxy = (function() {
    return epoxy = {
      lotRegex: /(.*)\/(.*)\/(.*)/,
      parseDate: function(d) {
        return new Date(d.substring(0, 4), d.substring(4, 6) - 1, d.substring(6, 8), d.substring(8, 10), d.substring(10, 12), d.substring(12, 14));
      },
      parseyyyymmdd: function(d) {
        return new Date(d.substring(0, 4), d.substring(5, 7) - 1, d.substring(8, 10));
      },
      fetchAll: function() {
        var addr, promise;
        r.get('allowedEpoxyTypes').forEach(function(el) {
          return r.set("epoxys." + el, null);
        });
        addr = 'http://cymautocert/osaapp/epoxy/index.php/epoxy/all';
        promise = $.getJSON(addr);
        promise.done(function(data) {
          return data.forEach(function(el, i, array) {
            var type;
            type = epoxy.lotRegex.exec(el.LOT_NUMBER)[1];
            if (type) {
              el.type = type;
              el.expiration = epoxy.parseDate(el.EXPIRE_DATE);
              return r.set("epoxys." + type, el);
            }
          });
        });
        return promise;
      },
      dispose: function(lot, comment) {
        var addr, promise;
        if (comment == null) {
          comment = '';
        }
        addr = 'http://cymautocert/osaapp/epoxy/index.php/epoxy/dispose';
        promise = $.post(addr, {
          lot: lot,
          comment: comment
        });
        promise.done(function(data) {
          return console.log(data);
        });
        return promise;
      },
      validate: function(original) {
        var actualDate, allowedEpoxyTypes, e, k, v;
        for (k in original) {
          v = original[k];
          original[k] = original[k].trim().toUpperCase();
        }
        try {
          actualDate = epoxy.parseyyyymmdd(original.expiration);
          allowedEpoxyTypes = r.get('allowedEpoxyTypes');
          if (allowedEpoxyTypes.indexOf(original.type) === -1) {
            throw {
              message: 'No es un epoxy permitido',
              path: 'type'
            };
          }
          if (original.lot === '') {
            throw {
              message: 'Ingresa un numero de lote',
              path: 'lot'
            };
          }
          if (original.operator === '') {
            throw {
              message: 'Ingresa un numero de operador',
              path: 'operator'
            };
          }
          if (original.expiration === '') {
            throw {
              message: 'Ingresa un fecha de expiracion',
              path: 'expiration'
            };
          }
          if (actualDate - new Date() < 0) {
            throw {
              message: 'El epoxy esta caducado o la fecha es incorrecta',
              path: 'expiration'
            };
          }
          if (original.disposeComment != null) {
            delete original.disposeComment;
          }
          r.set('error', void 0);
          return original;
        } catch (_error) {
          e = _error;
          console.log(e);
          r.set('error', e);
          return {
            error: true
          };
        }
      },
      register: function(values) {
        var addr;
        addr = 'http://cymautocert/osaapp/epoxy/index.php/epoxy/register';
        return $.post(addr, values);
      }
    };
  })();

  epoxy.fetchAll();

  r.on('refreshLive', function(e) {
    e.original.preventDefault();
    return epoxy.fetchAll();
  });

  r.on('registerNewSyringe', function(e) {
    var validated;
    e.original.preventDefault();
    validated = epoxy.validate(_.clone(r.get('newSyringe')));
    console.log(validated);
    if (validated.error == null) {
      return epoxy.register(validated).done(function(data) {
        r.set({
          'newSyringe.lot': '',
          'newSyringe.operator': '',
          'newSyringe.expiration': '',
          'newSyringe.type': '',
          step: 0,
          askDisposeComment: false
        });
        return epoxy.fetchAll();
      });
    }
  });

  r.on('createNewEpoxy', function(e) {
    e.original.preventDefault();
    r.set('creatingNew', true);
    return epoxy.fetchAll().done(function(data) {
      var step, type;
      type = e.context;
      if (r.get("epoxys." + type)) {
        step = 1;
      } else {
        step = 2;
      }
      return r.set({
        step: step,
        'newSyringe.type': type,
        askDisposeComment: false
      });
    });
  });

  r.on('askForComment', function(e) {
    e.original.preventDefault();
    return r.set({
      askDisposeComment: true,
      'newSyringe.disposeComment': ''
    });
  });

  r.on('returnToStart', function(e) {
    e.original.preventDefault();
    return r.set({
      step: 0,
      'newSyringe.type': '',
      askDisposeComment: false
    });
  });

  r.on('validateAndDisposeSyringe', function(e) {
    var comment, commentLength, lot, promise, type;
    e.original.preventDefault();
    comment = r.get('newSyringe.disposeComment');
    type = r.get('newSyringe.type');
    lot = r.get("epoxys." + type + ".LOT_NUMBER");
    commentLength = comment.length;
    if (commentLength > 450) {
      throw new Error('No se puede capturar un comentario tan largo');
    }
    if (comment === '') {
      throw new Error('Escribe un comentario');
    }
    promise = epoxy.dispose(lot, comment);
    return promise.done(function(data) {
      epoxy.fetchAll();
      return r.set({
        step: 2,
        askDisposeComment: false
      });
    });
  });

  r.observe('newSyringe.type', function() {
    var lot, type;
    type = r.get('newSyringe.type');
    lot = r.get("epoxys." + type + ".LOT_NUMBER");
    if (lot !== void 0) {
      return r.set('step', 1);
    }
  });

  window.r = r;

}).call(this);
