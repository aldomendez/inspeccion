<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Check Osas</title>
  <link rel="stylesheet" type="text/css"  href="../jsLib/SemanticUi/2.0.7/semantic.css">
</head>
<body>
  <div class="ui four column grid container" id="container">
    <template id="template">
      <div class="one column row">
        <div class="column">
          <div class="ui menu">
            <a class="active item" href="../">
              Avago
            </a>
            <a class="item">
              <i class="database icon"></i> Revision de OSAS
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="four wide column">
          

          <div class="ui fluid vertical text menu">
            <div class="item">
              <form v-on="submit:newPack($event)" class="ui transparent icon input">
                <input type="text" v-model="newPackCarrier" placeholder="Ingresa un pack">
                <i class="search icon" v-class="green:newPackCarrier.length == 9"></i>
              </form>
            </div>
            <div class="header item">Carriers:</div>
            <a class="item" v-repeat="carrier in carriers"
                v-on="click:selectCarrier($index)"
                v-class="active:$index === selectedpack, blue:$index === selectedpack">
              {{carrier.carrier}}:{{carrier.contents | gen}}
              <div class="ui label" v-class="blue: $index === selectedpack">
                {{carrier.contents | passQty}}
              </div>
            </a>
          </div>


        </div>
        <div class="twelve wide column">

          <div class="ui fuid menu">
            <a href="#" class="item" v-on="click:markAsOk"><i class="green checkmark icon"></i> Esta bien</a>
            <a href="#" class="item" v-on="click:reportPack"><i class="red flag icon"></i> Reportar</a>
            <a href="#" class="item"><i class="archive icon"></i> Marcar como consumida</a>
            <!-- <a href="#" class="item"><i class="cloud download icon"></i> Update from OSFM</a> -->
            <!-- <a href="#" class="item"><i class="warning icon"></i> Warning</a> -->
          </div>
      
          <table class="ui compact small celled striped table">
            <thead>
              <tr>
                <th colspan="8">Piezas en el pack <span v-if='carriers[selectedpack].check'><i class="green checkmark icon"></i> ya lo revise y se ve bien!</span></th>
              </tr>
              <tr v-if="showHeader">
                <th></th>
                <th>#</th>
                <th>Serial</th>
                <th>Estado</th>
                <th>Codigo</th>
                <th>Locaci&oacute;n</th>
                <th>Antiguedad</th>
                <th>Recibido</th>
              </tr>
            </thead><tbody>
              <tr v-repeat="device in carriers[selectedpack].contents" >
                <td class="collapsing">
                  <div class="ui fitted checkbox">
                    <input type="checkbox" v-model="device.check">
                    <label for=""></label>
                  </div>
                </td>
                <td class="collapsing">{{device.carrier_site}}</td>
                <td>{{device.serial_num}}</td>
                <td class="">{{device.status}}</td>
                <td class="">{{device.item}}</td>
                <td class="">{{device.osfm_location}}</td>
                <td class="">{{device.aged_days}}</td>
                <td class="right aligned collapsing">{{device.date_received}}</td>
              </tr>
              <tr v-if="carriers[selectedpack].contents.length == 0">
                <td colspan="7" class="center aligned">
                  <i class="massive notched circle loading icon"></i>
                </td>
              </tr>
            </tbody>

            <!-- <tfoot class="full-width"><tr>
              <th colspan="7">
                <div class="ui small icon button">Gen2</div>
                <div class="ui small icon button">Gen3</div>                
              </th>
            </tr></tfoot> -->
          </table>



        </div>
      </div>



    </template>
  </div>
  <script src="../jsLib/underscore/1.8.3/underscore.js"></script>
  <script src="../jsLib/vue/0.12.9/vue.js"></script>
  <script src="../jsLib/mousetrap/1.5.3/mousetrap.min.js"></script>
  <script src="../jsLib/vue-resource/0.1.14/vue-resource.js"></script>
  <script type="text/javascript" src="js/index.js"></script>
</body>
</html>