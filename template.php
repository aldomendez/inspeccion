<div class="row">
  <div class="column">
    <div class="ui menu">
      <a class="active item" href="../">
        Avago
      </a>
      <a class="item">
        <i class="book icon"></i> Control de epoxys
      </a>
      <a class="item" on-click="refreshLive"><i class="refresh icon"></i>Refresh</a>
      <!-- <div class="right menu">
        <div class="item">
          <span class="ui label red">not finished</span>
        </div>
      </div> -->
    </div>
  </div>
</div>
<div class="row">
  <div class="four wide column">
      
    <div class="ui vertical menu">
      <div class="header item">Epoxys activos</div>
      {{#each allowedEpoxyTypes}}
      <a class="item" on-click="createNewEpoxy">
        {{this}}
        {{#if epoxys[this]}}
        <div class="ui teal label">{{epoxys[this].COMMENTS}}</div>
        {{/if}}
      </a>
      {{/each}}
    </div>

  </div>
  <div class="twelve wide column">

    {{#if step==0}}
      {{>start}}
    {{/if}}
    {{#if step==1}}
      {{>disposeSyringe}}
    {{/if}}
    {{#if step==2}}
      {{>captureForm}}
    {{/if}}

  </div>
</div>

{{#partial start}}
<h2 class="ui header">
  <div class="content">Control de epoxys</div>
  <div class="sub header"></div>
</h2>
<p>Este control de epoxys te muestra en panel derecho los numeros de las geringas que estan activas (si hay). Justo al lado del tipo de geringa a la que pertenece</p>
<p>En caso de que quieras registar una nueva geringa, solo tienes que hacer <code>click</code> en el tipo de geringa que quieras
dar de alta y llenar los campos correspondientes</p>
{{/partial}}

{{#partial disposeSyringe}}
{{^askDisposeComment}}
<h2 class="ui header">
  <div class="content">Geringa activa</div>
  <!-- <div class="sub header">Solo puede haber una geringa activa</div> -->
</h2>
<p class="ui segment">Solamente puede haber una geringa activa, puedes borrar la geringa 
<b>{{epoxys[newSyringe.type].LOT_NUMBER}}</b> y registrar una nueva</p>
<div class="positive ui button" on-click="returnToStart"><b>Regresar</b> y dejar la jeringa que esta activa</div>
<div class="negative ui button" on-click="askForComment"><b>Tirar</b> la geringa activa y registrar una nueva</div>
{{/askDisposeComment}}
{{#askDisposeComment}}
<form action="" class="ui form">
  <div class="field required">
    <label for="comment">Por que vas a tirar la geringa (caracteres restantes:{{450 - newSyringe.disposeComment.length}})</label>
    <textarea name="comment" id="" cols="30" rows="10" value="{{newSyringe.disposeComment}}"></textarea>
  </div>
</form>
<div class="ui divider hidden"></div>
<div class="positive ui button" on-click="returnToStart"><b>Regresar</b> y dejar la jeringa que esta activa</div>
<div class="negative ui button" on-click="validateAndDisposeSyringe"><b>Tirar</b> la geringa activa y registrar una nueva</div>
{{/askDisposeComment}}

{{/partial}}

{{#partial captureForm}}
<form class="ui form {{#error}}error{{/error}}">
  <h2 class="ui header">
  <div class="content">Registro de epoxys</div>
  <div class="sub header"></div>
</h2>
  <div class="two fields">
    <div class="field">
      <label>Tipo de epoxy</label>
      <select class="ui search dropdown" value='{{newSyringe.type}}'>
        {{#each allowedEpoxyTypes}}
        <option>{{this}}</option>
        {{/each}}
      </select>
    </div>
    <div class="field {{#if error.path == 'lot'}}error{{/if}}">
      <label>Lote</label>
      <div class="field">
        <input type="text" name="lote" placeholder="" value="{{newSyringe.lot}}">
      </div>
    </div>
  </div>
  <div class="two fields {{#if error.path == 'expiration'}}error{{/if}}">
    <div class="field">
      <label for="">Fecha de expiracion</label>
      <input type="date" name="expDate" value="{{newSyringe.expiration}}">
    </div>
    <div class="field {{#if error.path == 'operator'}}error{{/if}}">
      <label for="">Numero de operador</label>
      <input type="text" name="operator" value="{{newSyringe.operator}}">
    </div>
  </div>

  <div class="ui error message">
    <div class="header">{{error.message}}</div>
  </div>

  <div class="ui divider hidden"></div>
  <div class="ui submit button" on-click="returnToStart">Cancelar</div>
  <div class="ui submit button primary" on-click="registerNewSyringe">Registrar</div>
</form>
{{/partial}}