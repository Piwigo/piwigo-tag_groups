{combine_script id='tg_script_config' load='footer' path="{$TG_PATH}admin/js/tg_config.js" require='jquery'}
{combine_css path="{$TG_PATH}admin/css/admin.css" order=0}
{footer_script}
const PWG_TOKEN = "{$PWG_TOKEN}";
let TG_CONFIG = {$TG_CONFIG|@json_encode:JSON_HEX_TAG};
{/footer_script}
<div class="titlePage">
  <h2>Tag groups</h2>
</div>

<section class="tg-container {if $themeconf['colorscheme'] == 'dark'}dark{/if}">
  <div class="tg-config">
    <div class="tg-config-container">
      <p class="tg-icon-header">
        <span class="tg-icon icon-cog-alt icon-green"></span>
        <span class="tg-icon-text">{'General'|translate}</span>
      </p>

      <div class="tg-config-method">
        <div class="tg-method">
          <label class="switch">
            <input type="checkbox" name="index_filters" id="index_filters"{if $TG_CONFIG.index_filters} checked{/if}>
            <span class="slider round"></span>
          </label>
          <label for="index_filters">{'Show group filters on the tags page'|translate}</label>
        </div>

        <div class="tg-method">
          <label class="switch">
            <input type="checkbox" name="show_as_field" id="show_as_field"{if $TG_CONFIG.show_as_field} checked{/if}>
            <span class="slider round"></span>
          </label>
          <label for="show_as_field">{'Show tag groups as separate fields'|translate}</label>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="tg-save {if $themeconf['colorscheme'] == 'dark'}dark{/if}">
  <div class="badge-container" id="tg_error_changes">
    <div class="badge-error">
      <i class="icon-cancel"></i>
      {'an error happened'|translate}
    </div>
  </div>

  <div class="badge-container" id="tg_unsaved_changes">
    <div class="badge-unsaved">
      <i class="icon-attention"></i>
      {'You have unsaved changes'|translate}
    </div>
  </div>

  <div class="badge-container" id="tg_saving_changes">
    <div class="badge-succes">
      <i class="icon-ok"></i>
      {'Changes saved'|translate}
    </div>
  </div>

  <button class="buttonLike" id="tg_save_settings">{'Save Settings'|translate}</button>
</section>
