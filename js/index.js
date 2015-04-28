(function() {
  var development, epoxy, loadFetchedIntoR, log, r;

  r = new Ractive({
    el: 'container',
    template: '#template',
    data: {
      components: ["Selecciona un componente", "PLC", "ALPS", "GlassRail", "OSA", "Ceramico", "PDArray"],
      failMode: ['Selecciona un modo de falla', "Desprendido", "Dañado", "Contaminado", "Fuera de posicion", "Exceso de epoxy"],
      step: 0,
      userNumber: '',
      carrier: '155772978',
      carrierContents: [
        {
          CARRIER_SITE: 1,
          STATUS: true
        }, {
          CARRIER_SITE: 2,
          STATUS: true
        }, {
          CARRIER_SITE: 3,
          STATUS: true
        }, {
          CARRIER_SITE: 4,
          STATUS: true
        }, {
          CARRIER_SITE: 5,
          STATUS: true
        }, {
          CARRIER_SITE: 6,
          STATUS: true
        }, {
          CARRIER_SITE: 7,
          STATUS: true
        }, {
          CARRIER_SITE: 8,
          STATUS: true
        }, {
          CARRIER_SITE: 9,
          STATUS: true
        }, {
          CARRIER_SITE: 10,
          STATUS: true
        }
      ]
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
      fetchAll: function(carrier) {
        var addr, promise;
        r.set('carrierContents', [
          {
            CARRIER_SITE: 1,
            STATUS: true
          }, {
            CARRIER_SITE: 2,
            STATUS: true
          }, {
            CARRIER_SITE: 3,
            STATUS: true
          }, {
            CARRIER_SITE: 4,
            STATUS: true
          }, {
            CARRIER_SITE: 5,
            STATUS: true
          }, {
            CARRIER_SITE: 6,
            STATUS: true
          }, {
            CARRIER_SITE: 7,
            STATUS: true
          }, {
            CARRIER_SITE: 8,
            STATUS: true
          }, {
            CARRIER_SITE: 9,
            STATUS: true
          }, {
            CARRIER_SITE: 10,
            STATUS: true
          }
        ]);
        addr = "http://cymautocert/osaapp/inspeccion/index.php/carrier/" + carrier;
        promise = $.getJSON(addr);
        promise.done(function(data) {
          return console.log(data);
        });
        return promise;
      },
      validate: function() {
        var carrier, cmpt, components, e, user;
        user = r.get('userNumber');
        carrier = r.get('carrier');
        components = r.get('carrierContents');
        try {
          if (user === '') {
            throw {
              message: 'Ingresa un NUMERO DE USUARIO',
              path: 'user'
            };
          }
          if (user.length > 10) {
            throw {
              message: 'Numero de usuario demasiado largo',
              path: 'user'
            };
          }
          if (carrier.length !== 9) {
            throw {
              message: 'Numero de carrier cambio, Ingresa el carrier de nuevo',
              path: 'carrier'
            };
          }
          cmpt = components.map(function(el, i) {
            if (el.STATUS === false) {
              return el;
            }
          });
          console.log(cmpt);
          return r.set('error', void 0);
        } catch (_error) {
          e = _error;
          console.log(e);
          r.set('error', e);
          return {
            error: true
          };
        }
      }
    };
  })();

  loadFetchedIntoR = function(data) {
    return data.map(function(el, i, array) {
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".CARRIER_SERIAL_NUM", el.CARRIER_SERIAL_NUM);
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".STATUS", /PASS/.test(el.STATUS) ? true : false);
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".SERIAL_NUM", el.SERIAL_NUM);
      return r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".CARRIER_SITE", el.CARRIER_SITE);
    });
  };

  r.on('cargarCarrier', function(e) {
    e.original.preventDefault();
    return epoxy.fetchAll(r.get('carrier')).done(function(data) {
      return loadFetchedIntoR(data);
    });
  });

  r.observe('carrier', function(nVal, oVal) {
    if (nVal.length === 9) {
      return epoxy.fetchAll(r.get('carrier')).done(function(data) {
        return loadFetchedIntoR(data);
      });
    }
  });

  window.epoxy = epoxy;

  window.r = r;

}).call(this);
