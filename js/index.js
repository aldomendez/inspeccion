(function() {
  var development, epoxy, loadFetchedIntoR, log, r;

  r = new Ractive({
    el: 'container',
    template: '#template',
    data: {
      components: ["Selecciona un componente", "PLC", "ALPS", "GlassRail", "OSA", "Ceramico", "PDArray", "Laser", "Pin"],
      failMode: ['Selecciona un modo de falla', "Desprendido", "Dañado", "Contaminado", "Fuera de posicion", "Exceso de epoxy", "Fracturado", "Falta epoxy"],
      step: 0,
      userNumber: '',
      carrier: '',
      saved: false,
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
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 2,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 3,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 4,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 5,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 6,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 7,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 8,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 9,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
          }, {
            CARRIER_SITE: 10,
            STATUS: true,
            COMPONENT: "Selecciona un componente",
            FAILMODE: "Selecciona un modo de falla",
            COMMENTS: ''
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
        var carrier, components, e, user;
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
          components = components.map(function(el, i) {
            if (el.STATUS === false || el.COMMENTS !== '') {
              if (el.COMMENTS !== '') {
                el.COMPONENT = '';
                el.FAILMODE = '';
              }
              return el;
            } else {
              return null;
            }
          });
          components = _.filter(components, function(el) {
            return el != null;
          });
          components.map(function(el, i) {
            if (el !== null && el.COMMENTS === '') {
              if (el.COMPONENT === "Selecciona un componente") {
                throw {
                  message: 'Debes de seleccionar el componente',
                  path: 'component',
                  position: el.CARRIER_SITE
                };
              }
              if (el.FAILMODE === "Selecciona un modo de falla") {
                throw {
                  message: 'Debes de seleccionar un modo de falla',
                  path: 'failMode',
                  position: el.CARRIER_SITE
                };
              }
            }
          });
          r.set('error', void 0);
          console.log(components);
          return components;
        } catch (_error) {
          e = _error;
          console.log(e);
          r.set('error', e);
          return {
            error: true
          };
        }
      },
      saveFailData: function(validatedComponents) {
        var addr, data, promise, user;
        user = r.get('userNumber');
        addr = "http://cymautocert/osaapp/inspeccion/index.php/saveFailData";
        data = {
          user: user,
          components: validatedComponents
        };
        console.log(data);
        promise = $.post(addr, data);
        promise.done(function(data) {
          return console.log(data);
        });
        return promise;
      }
    };
  })();

  loadFetchedIntoR = function(data) {
    r.set('saved', false);
    return data.map(function(el, i, array) {
      var exists, pass_fail;
      if (/^P/.test(el.STATUS)) {
        if (el.SAVEDSTATUS != null) {
          if (el.SAVEDSTATUS === 'P') {
            pass_fail = true;
          } else {
            pass_fail = false;
          }
        } else {
          pass_fail = true;
        }
      } else {
        pass_fail = false;
      }
      exists = el.SAVEDSTATUS != null ? true : false;
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".CARRIER_SERIAL_NUM", el.CARRIER_SERIAL_NUM);
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".STATUS", pass_fail);
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".SERIAL_NUM", el.SERIAL_NUM);
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".CARRIER_SITE", el.CARRIER_SITE);
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".COMMENTS", el.COMMENTS || '');
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".COMPONENT", el.COMPONENT || 'Selecciona un componente');
      r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".FAILMODE", el.FAILMODE || 'Selecciona un modo de falla');
      return r.set("carrierContents." + (el.CARRIER_SITE - 1) + ".exists", exists);
    });
  };

  r.on('cargarCarrier', function(e) {
    e.original.preventDefault();
    return epoxy.fetchAll(r.get('carrier')).done(function(data) {
      return loadFetchedIntoR(data);
    });
  });

  r.observe('carrierContents.*', function() {
    return r.set('saved', false);
  });

  r.observe('carrier', function(nVal, oVal) {
    if (nVal.length === 9) {
      return epoxy.fetchAll(r.get('carrier')).done(function(data) {
        loadFetchedIntoR(data);
        return console.log(r.get('carrierContents'));
      });
    }
  });

  r.on('validateAndSave', function(e) {
    var validatedComponents;
    if (!r.get('saved')) {
      validatedComponents = epoxy.validate();
      console.log(validatedComponents);
      if ((validatedComponents != null ? validatedComponents.error : void 0) == null) {
        epoxy.saveFailData(validatedComponents);
        return r.set('saved', true);
      }
    }
  });

  window.epoxy = epoxy;

  window.r = r;

}).call(this);
