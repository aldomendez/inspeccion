(function() {
  var Pack, util;

  Vue.component('main-menu', {});

  util = {};

  util.rootAddr = 'http://wmatvmlr401/lr4/check_osas/index.php';

  util.packId = Vue.resource('http://wmatvmlr401/lr4/check_osas/index.php/getCarrierSerials/:pack');

  Pack = (function() {
    function Pack(carrier1) {
      this.carrier = carrier1;
      this.contents = [];
      this.status = 'Buscando los datos del Pack';
      this.fetchCarrierContent();
    }

    Pack.prototype.fetchCarrierContent = function() {
      return util.packId.get({
        pack: this.carrier
      }, (function(_this) {
        return function(data) {
          var i, j, len, results;
          results = [];
          for (j = 0, len = data.length; j < len; j++) {
            i = data[j];
            results.push(_this.contents.push([i.CARRIER_SITE, i.SERIAL_NUM, i.STATUS]));
          }
          return results;
        };
      })(this));
    };

    return Pack;

  })();

  window.vm = new Vue({
    el: '#template',
    data: {
      carriers: [
        {
          carrier: '156181394',
          contents: [['1', '159866954', 'PASS/POST-PURGE', '159866954', 'MSPP-PIC', '5067-5071', '0'], ['2', '159866954', 'PASS/POST-PURGE', '159866954', 'MSPP-PIC', '5067-5071', '4'], ['3', '159866954', 'PASS/POST-PURGE', '159866954', 'MSPP-PIC', '5067-5071', '3'], ['4', '159866954', 'PASS/POST-PURGE', '159866954', 'MSPP-PIC', '5067-5071', '0']]
        }, {
          carrier: '156181394',
          contents: [['1', '156179044', 'PASS/POST-PURGE', '156179044', 'MSPP-PIC', '5067-5071', '0'], ['2', '156179044', 'PASS/POST-PURGE', '156179044', 'MSPP-PIC', '5067-5071', '4'], ['3', '156179044', 'PASS/POST-PURGE', '156179044', 'MSPP-PIC', '5067-5071', '3'], ['4', '156179044', 'PASS/POST-PURGE', '156179044', 'MSPP-PIC', '5067-5071', '0']]
        }
      ],
      selectedpack: 0,
      newPackCarrier: '156269132'
    },
    methods: {
      selectCarrier: function(pack) {
        console.log(pack);
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
