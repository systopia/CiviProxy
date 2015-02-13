<div class="crm-block crm-form-block">
  <div>
    <h3>{ts}Core Settings{/ts}</h3>
    <div>
      <div>
          <table id="core_settings">
            <tr>
              <td class="label"><label for="proxy_enabled"> {ts}Enable proxy{/ts} <a onclick='CRM.help("{ts}Enable Proxy{/ts}", {literal}{"id":"id-proxy-enabled","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></label></td>
              <td><input value="1" type="checkbox" id="proxy_enabled" name="proxy_enabled" {if $proxy_enabled}checked="checked"{/if} class="form-checkbox"/></td>
            </tr>
            <tr>
              <td class="label">{$form.proxy_url.label} <a onclick='CRM.help("{ts}Proxy URL{/ts}", {literal}{"id":"id-proxy-url","file":"CRM\/Admin\/Form\/Setting\/ProxySettings"}{/literal}); return false;' href="#" title="{ts}Help{/ts}" class="helpicon">&nbsp;</a></td>
              <td>{$form.proxy_url.html}</td>
            </tr>
          </table>
        </div>
    </div>
  </div>
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

{literal}
<script type="text/javascript">
  (function(cj) {

  })(cj);
</script>

<style type="text/css">
  #proxy_url {
    width: 300px;
  }
</style>
{/literal}
