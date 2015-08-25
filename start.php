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
                <input type="text" v-model="newPackCarrier" placeholder="Ingresa otro pack">
                <i class="search icon" v-class="green:newPackCarrier.length == 9"></i>
              </form>
            </div>
            <div class="header item">Carriers:</div>
            <a class="item" v-repeat="carrier in carriers"
                v-on="click:selectCarrier($index)"
                v-class="active:$index === selectedpack, blue:$index === selectedpack">
              {{carrier.carrier}}:{{carrier.gen}}
              <div class="ui label" v-class="blue: $index === selectedpack">
                {{carrier.contents.length}}
              </div>
            </a>
          </div>


        </div>
        <div class="twelve wide column">
      
          <table class="ui compact small celled striped table">
            <thead>
              <tr>
                <th colspan="7">Piezas en el pack {{carriers[selectedpack].status}}</th>
              </tr>
              <tr v-if="showHeader">
                <th>#</th>
                <th>Serial</th>
                <th>Estado</th>
                <th>Codigo</th>
                <th>Locaci&oacute;n</th>
                <th>Antiguedad</th>
                <th>Recibido</th>
              </tr>
            </thead><tbody>
              <tr v-repeat="device in carriers[selectedpack].contents">
                <td class="collapsing">{{device[0]}}</td>
                <td>{{device[1]}}</td>
                <td class="">{{device[2]}}</td>
                <td class="">{{device[3]}}</td>
                <td class="">{{device[4]}}</td>
                <td class="">{{device[5]}}</td>
                <td class="right aligned collapsing">{{device[6]}}</td>
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

          <div class="ui fuid text menu">
            <!-- <a href="#" class="item"><i class="red flag icon"></i> Report</a>
            <a href="#" class="item"><i class="refresh icon"></i> Reload from database</a>
            <a href="#" class="item"><i class="cloud download icon"></i> Update from OSFM</a> -->
            <!-- <a href="#" class="item"><i class="warning icon"></i> Warning</a> -->
          </div>


        </div>
      </div>



    </template>
  </div>
  <script src="../jsLib/underscore/1.8.3/underscore.js"></script>
  <script src="../jsLib/vue/0.12.9/vue.js"></script>
  <script src="../jsLib/vue-resource/0.1.14/vue-resource.js"></script>
  <script type="text/javascript" src="js/index.js"></script>
</body>
</html>