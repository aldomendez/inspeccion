(function() {
  var Pack, util;

  Vue.component('main-menu', {});

  util = {};

  util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php';

  util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack');

  util.osfmData = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/osfm/:serials');

  util.generations = {
    'LR4GEN2OSA': {
      gen: 'Gen2'
    },
    '5067-5071': {
      gen: 'Gen3'
    }
  };

  util.statusCodes = ['Cargando desde Base de datos ...', 'Cargando desde OSFM ...', 'Completada la carga', 'Carga fallida'];

  Pack = (function() {
    function Pack(carrier1) {
      this.carrier = carrier1;
      this.contents = [];
      this.serials = [];
      this.status = 0;
      this.gen = '';
      this.fetchCarrierContent();
    }

    Pack.prototype.fetchCarrierContent = function() {
      this.status = 0;
      return util.packId.get({
        pack: this.carrier
      }, (function(_this) {
        return function(data) {
          var i, j, len;
          for (j = 0, len = data.length; j < len; j++) {
            i = data[j];
            _this.serials.push(i.SERIAL_NUM);
            _this.contents.push([i.CARRIER_SITE, i.SERIAL_NUM, i.STATUS]);
          }
          return _this.fetchDataFromOSFM();
        };
      })(this)).error(function(data, status, request) {
        console.log(data);
        return console.log(status);
      });
    };

    Pack.prototype.fetchDataFromOSFM = function() {
      this.status = 1;
      return util.osfmData.get({
        serials: "'" + (this.serials.join("','")) + "'"
      }, (function(_this) {
        return function(data) {
          _this.gen = util.generations[data[0].ITEM].gen || '--';
          data.forEach(function(osfmEl, i) {
            return _this.contents.some(function(contEl, i) {
              if (osfmEl.JOB === contEl[1]) {
                contEl.push(osfmEl.ITEM);
                contEl.push(osfmEl.SUBINVENTORY_CODE);
                contEl.push(osfmEl.AGED_DAYS);
                contEl.push(osfmEl.DATE_RECEIVED);
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
    };

    return Pack;

  })();

  window.vm = new Vue({
    el: '#template',
    data: {
      showHeader: true,
      carriers: [],
      selectedpack: 0,
      newPackCarrier: '156269132'
    },
    methods: {
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

}).call(this);
