{*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015 SYSTOPIA                            |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+-------------------------------------------------------*}
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
  <br/>
  <div>
    <h3>{ts}Mailing Settings{/ts}</h3>
    <div>
      <div>
          <table id="core_settings" class="no-border">
            <tr>
              <td class="label">{$form.custom_mailing_base.label}&nbsp;<a onclick='CRM.help("{ts}CiviMail: Custom pages{/ts}", {literal}{"id":"id-custom-mailing-base","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></td>
              <td>{$form.custom_mailing_base.html}</td>
            </tr>
          </table>
        </div>
    </div>
  </div>
  <br/>
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

{literal}
<script type="text/javascript">
  function enableInput() {
    cj('#proxy_url').attr('disabled',!this.checked);
    cj('#custom_mailing_base').attr('disabled', !this.checked);
  }

  function enableInputGlobal() {
    var is_enabled = cj('#proxy_enabled').attr('checked') == 'checked';
    cj('#proxy_url').attr('disabled', !is_enabled);
    cj('#custom_mailing_base').attr('disabled', !is_enabled);
  }

  (function(cj) {
    cj('#proxy_enabled').click(enableInput);
    enableInputGlobal();

    // set the default value to {$proxy_url}/mailing
    if (cj("#custom_mailing_base").val().length == 0) {
      cj("#custom_mailing_base").val(cj("#proxy_url").val() + '/mailing');
    } 

  })(cj);
</script>

<style type="text/css">
  #proxy_url, #custom_mailing_base {
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
