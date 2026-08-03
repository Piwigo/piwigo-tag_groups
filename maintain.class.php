<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class tag_groups_maintain extends PluginMaintain
{
  function __construct($plugin_id)
  {
    parent::__construct($plugin_id);
  }

  /**
   * Plugin install
   */
  function install($plugin_version, &$errors = array())
  {
    global $conf;
    if (empty($conf['tag_groups']))
    {
      // cast with the older $conf (in local/config/config.inc.php)
      $default_conf = array(
        'index_filters' => $conf['tag_groups_index_filters'] ?? false,
        'show_as_field' => $conf['tag_groups_show_as_field'] ?? false,
      );

      conf_update_param('tag_groups', $default_conf, true);
    }
  }

  /**
   * Plugin activate
   */
  function activate($plugin_version, &$errors = array())
  {
  }

  /**
   * Plugin deactivate
   */
  function deactivate()
  {
  }

  /**
   * Plugin update
   */
  function update($old_version, $new_version, &$errors = array())
  {
    $this->install($new_version, $errors);
  }

  /**
   * Plugin uninstallation
   */
  function uninstall()
  {
    conf_delete_param('tag_groups');
  }

}
