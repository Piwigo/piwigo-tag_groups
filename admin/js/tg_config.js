$(function () {
  const indexFilters = $('#index_filters');
  const showAsField = $('#show_as_field');
  const btnSave = $('#tg_save_settings');
  const unsavedChanges = $('#tg_unsaved_changes');
  const savedChanges = $('#tg_saving_changes');
  const errorChanges = $('#tg_error_changes');

  let loadingSaveSettings = false;

  function getConfig() {
    return {
      index_filters: indexFilters.prop('checked'),
      show_as_field: showAsField.prop('checked'),
    };
  }

  function hasChanges() {
    const config = getConfig();
    return config.index_filters !== TG_CONFIG.index_filters
      || config.show_as_field !== TG_CONFIG.show_as_field;
  }

  function toggleChanges() {
    if (hasChanges()) {
      savedChanges.hide();
      errorChanges.hide();
      unsavedChanges.fadeIn();
    } else {
      unsavedChanges.fadeOut();
    }
  }

  function sendConfig(config) {
    $.ajax({
      url: 'ws.php?format=json&method=taggroups.setConfig',
      type: 'POST',
      dataType: 'json',
      data: $.extend({ pwg_token: PWG_TOKEN }, config),
      success: function (res) {
        unsavedChanges.hide();
        errorChanges.hide();
        if (res.stat === 'ok') {
          TG_CONFIG = res.result.configuration;
          savedChanges.fadeIn();
          return;
        }
        errorChanges.fadeIn();
      },
      error: function () {
        unsavedChanges.hide();
        savedChanges.hide();
        errorChanges.fadeIn();
      },
      complete: function () {
        loadingSaveSettings = false;
      },
    });
  }

  indexFilters.add(showAsField).off('change').on('change', toggleChanges);

  btnSave.off('click').on('click', function () {
    if (loadingSaveSettings || !hasChanges()) return;
    loadingSaveSettings = true;
    sendConfig(getConfig());
  });
});
