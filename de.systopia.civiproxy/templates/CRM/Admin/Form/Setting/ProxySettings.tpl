<div class="crm-block crm-form-block">
  <div>
    <h3>{ts}Basic Settings{/ts}</h3>
    <div>
      <div>
          <table id="core_settings" class="no-border">
            <tr>
              <td class="label"><label for="proxy_enabled">{ts}CiviProxy enabled{/ts}&nbsp;<a onclick='CRM.help("{ts}CiviProxy enabled{/ts}", {literal}{"id":"id-proxy-enabled","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></label></td>
              <td><input value="1" type="checkbox" id="proxy_enabled" name="proxy_enabled" {if $proxy_enabled}checked="checked"{/if} class="form-checkbox"/></td>
            </tr>
            <tr>
              <td class="label">{$form.proxy_url.label}&nbsp;<a onclick='CRM.help("{ts}Proxy URL{/ts}", {literal}{"id":"id-proxy-url","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></td>
              <td>{$form.proxy_url.html}</td>
            </tr>
            <tr>
              <td class="label">{$form.proxy_version.label}&nbsp;<a onclick='CRM.help("{ts}Proxy Version{/ts}", {literal}{"id":"id-proxy-version","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></td>
              <td>{$form.proxy_version.html}</td>
            </tr>
          </table>
        </div>
    </div>
  </div>
  {* ==== advanced settings disabled ========
  <div>
    <h3>{ts}Advanced Settings{/ts}</h3>
    <div>
      <div>
          <table id="component_settings" class="no-border">
            <tr>
              <td class="label">{$form.civimail_external_optout.label} <a onclick='CRM.help("{ts}CiviMail: External opt-out page{/ts}", {literal}{"id":"id-extoptout-url","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></td>
              <td>{$form.civimail_external_optout.html}</td>
            </tr>
          </table>
        </div>
    </div>
  </div>
  ==== advanced settings disabled ======== *}
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

{literal}
<script type="text/javascript">
  function enableInput() {
    cj('#proxy_url').attr('disabled',!this.checked);
    cj('#civimail_external_optout').attr('disabled', !this.checked);
  }

  function enableInputGlobal() {
    var is_enabled = cj('#proxy_enabled').attr('checked') == 'checked';
    cj('#proxy_url').attr('disabled', !is_enabled);
    cj('#civimail_external_optout').attr('disabled', !is_enabled);
  }

  (function(cj) {
    cj('#proxy_enabled').click(enableInput);
    enableInputGlobal();

  })(cj);
</script>

<style type="text/css">
  #proxy_url, #civimail_external_optout {
    width: 350px;
  }

  .no-border,
  .no-border tr,
  .no-border tbody td,
  .no-border thead th,
  .no-border tfoot th {
    border: none;
  }

  .label {
    min-width: 200px;
  }
</style>
{/literal}
