<div class="row">
  <div class="column">
    <div class="ui menu">
      <a class="active item" href="../">
        Avago
      </a>
      <a class="item">
        <i class="checkmark box icon"></i> Inspecci&oacute;n Visual
      </a>
      <!-- <a class="item" on-click="refreshLive"><i class="refresh icon"></i>Refresh</a> -->
      <!-- <div class="right menu">
        <div class="item">
          <span class="ui label red">not finished</span>
        </div>
      </div> -->
    </div>
  </div>
</div>
<div class="row">
  <div class="sixteen wide column">
       
    {{>sampleForm}}
    
  </div>
</div>

{{#partial sampleForm}}
<form class="ui small form segment {{#if error}}error{{/if}}">
  <div class="ui error message">
    <div class="header">{{error.message}}</div>
  </div>
  <div class="three fields">
    <div class="required field {{#if error.path == 'user'}}error{{/if}}">
      <label>Numero de usuario</label>
      <div class="field">
        <input type="text" value="{{userNumber}}">
      </div>
    </div>

    <div class="required field">
      <label>Numero de Carrier</label>
      <div class="field">
        <input type="text" value="{{carrier}}">
      </div>
    </div>
    
    <div class="field">
      <label>&nbsp;</label>
      <button class="fluid ui button" on-click="cargarCarrier">Cargar carrier</button>
    </div>

  </div>

<table class="ui compact celled definition table">
  <thead class="full-width">
    <tr>
      <th>Numero de Serie</th>
      <th>Posicion</th>
      <th>Pass</th>
      <th>Componente</th>
      <th>Modo de Falla</th>
      <th>Comentarios</th>
    </tr>
  </thead>
  <tbody>
      {{#each carrierContents}}
    <tr class="{{#if this.STATUS}}positive{{/if}}{{#if !this.STATUS}}negative{{/if}}">
      <td>{{this.SERIAL_NUM}}</td>
      <td class="collapsing">{{this.CARRIER_SITE}}</td>
      <td class="collapsing">
        <!-- <div class="ui checkbox"> -->
            <input type="checkbox" checked='{{this.STATUS}}'> <label></label>
        <!-- </div> -->
      </td>
      <td>
        <div class="ui form  {{#if error}}error{{/if}}">
          <div class="field {{#if error.path == 'component'}}{{#if error.position == this.CARRIER_SITE}}error{{/if}}{{/if}}">
            <select value="{{this.COMPONENT}}">
              {{#each components}}
                <option value="{{this}}">{{this}}</option>
              {{/each}}
            </select>
          </div>
        </div>
      </td>
      <td>
        <div class="ui form  {{#if error}}error{{/if}}">
          <div class="field {{#if error.path == 'failMode'}}{{#if error.position == this.CARRIER_SITE}}error{{/if}}{{/if}}">
            <select  value="{{this.FAILMODE}}">
              {{#each failMode}}
                <option value="{{this}}">{{this}}</option>
              {{/each}}
            </select>
          </div>
        </div>
      </td>
      <td>
        <input type="text" value="{{this.COMMENT}}">
      </td>
    </tr>
      {{/each}}
  </tbody>
  <tfoot class="full-width">
    <tr>
      <th></th>
      <th colspan="6">
        <div class="ui right floated small green button" on-click="validateAndSave">
          Guardar
        </div>
      </th>
    </tr>
  </tfoot>
</table>








</form>
{{/partial}}
