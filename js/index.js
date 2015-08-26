(function() {
  var Pack, util;

  Vue.filter('passQty', function(value) {
    var a;
    a = _.filter(value, function(el) {
      return /PASS/.test(el.status);
    });
    return a.length;
  });

  Vue.filter('gen', function(devices) {
    return _.compact(_.uniq(_.pluck(devices, 'item'))).map(function(el) {
      return util.generations[el].gen;
    }).join(',');
  });

  util = {};

  util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php';

  util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack');

  util.osfmData = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/osfm/:serials');

  util.reportPack = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/flag/:carrier');

  util.generations = {
    'LR4GEN2OSA': {
      gen: 'Gen2'
    },
    '5067-5071': {
      gen: 'Gen3'
    }
  };

  util.statusCodes = ['Cargando desde Base de datos ...', 'Cargando desde OSFM ...', 'Completada la carga', 'Carga fallida', ' === Reported Pack ==='];

  Pack = (function() {
    function Pack(carrier1) {
      this.carrier = carrier1;
      this.contents = [];
      this.serials = [];
      this.status = 0;
      this.gen = '';
      this.check = false;
      this.fetchCarrierContent();
    }

    Pack.prototype.fetchCarrierContent = function() {
      this.status = 0;
      return util.packId.get({
        pack: this.carrier
      }, (function(_this) {
        return function(data) {
          var check, i, j, len, ref;
          console.log(data);
          if (((ref = data[0]) != null ? ref.ITEM : void 0) != null) {
            _this.gen = util.generations[data[0].ITEM].gen || '--';
          }
          for (j = 0, len = data.length; j < len; j++) {
            i = data[j];
            if ((i.ACTUAL_STATUS == null) || i.ACTUAL_STATUS === 'noOsfmData') {
              _this.serials.push(i.SERIAL_NUM);
            }
            if (i.ACTUAL_STATUS === 'problem_detected') {
              check = true;
            } else {
              check = false;
            }
            _this.contents.push({
              'carrier_site': i.CARRIER_SITE,
              'serial_num': i.SERIAL_NUM,
              'status': i.STATUS,
              'item': i.ITEM,
              'osfm_location': i.OSFM_LOCATION,
              'aged_days': i.AGED_DAYS,
              'date_received': i.DATE_RECEIVED,
              'actual_status': i.ACTUAL_STATUS,
              'check': check
            });
          }
          return _this.fetchDataFromOSFM();
        };
      })(this));
    };

    Pack.prototype.fetchDataFromOSFM = function() {
      this.status = 1;
      if (this.serials.length !== 0) {
        return util.osfmData.get({
          serials: "'" + (this.serials.join("','")) + "'"
        }, (function(_this) {
          return function(data) {
            console.log(data);
            _this.gen = util.generations[data[0].ITEM].gen || '--';
            data.forEach(function(osfmEl, i) {
              return _this.contents.some(function(contEl, i) {
                if (osfmEl.JOB === contEl.serial_num) {
                  contEl.item = osfmEl.ITEM;
                  contEl.osfm_location = osfmEl.SUBINVENTORY_CODE;
                  contEl.aged_days = osfmEl.AGED_DAYS;
                  contEl.date_received = osfmEl.DATE_RECEIVED;
                  return true;
                }
              });
            });
            return _this.status = 2;
          };
        })(this)).error(function(data, status, request) {
          console.log(data);
          return console.log(status);
        });
      }
    };

    return Pack;

  })();

  window.vm = new Vue({
    el: '#template',
    data: {
      statusCodes: util.statusCodes,
      showHeader: true,
      carriers: [],
      selectedpack: 0,
      newPackCarrier: ''
    },
    methods: {
      reportPack: function(e) {
        e.preventDefault();
        if (this.carriers[this.selectedpack] != null) {
          return util.reportPack.get({
            carrier: this.carriers[this.selectedpack].carrier
          }, (function(_this) {
            return function(data) {
              return _this.carriers[_this.selectedpack].status = 5;
            };
          })(this));
        } else {
          return console.log('No carrier selected');
        }
      },
      markAsOk: function() {},
      selectCarrier: function(pack) {
        return vm.selectedpack = pack;
      },
      isActive: function(selected) {
        var base;
        return typeof (base = vm.selectedpack === selected) === "function" ? base({
          "true": false
        }) : void 0;
      },
      newPack: function(e, carrier) {
        e.preventDefault();
        if (vm.newPackCarrier.length === 9) {
          vm.carriers.unshift(new Pack(vm.newPackCarrier));
          vm.newPackCarrier = '';
          return vm.selectedpack = 0;
        }
      }
    }
  });

  Mousetrap.bind('j', function(e) {
    if (vm.carriers.length > 0 && vm.selectedpack < vm.carriers.length - 1) {
      return vm.selectedpack++;
    }
  });

  Mousetrap.bind('k', function(e) {
    if (vm.carriers.length !== 0 && vm.selectedpack >= 1) {
      return vm.selectedpack--;
    }
  });

}).call(this);
