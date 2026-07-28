<?php
/*
Plugin Name: Tag Groups
Version: auto
Description: Create groups of tags
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=
Author: plg
Author URI: http://piwigo.org
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// check root directory
if (basename(dirname(__FILE__)) != 'tag_groups')
{
  add_event_handler('init', 'tg_error');
  function tg_error()
  {
    global $page;
    $page['errors'][] = 'Tag Groups plugin folder name is incorrect, uninstall the plugin and rename it to "tag_groups"';
  }
  return;
}

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

define('TG_ID', basename(dirname(__FILE__)));
define('TG_PATH', PHPWG_PLUGINS_PATH . TG_ID . '/');
define('TG_REALPATH', realpath(TG_PATH));

// +-----------------------------------------------------------------------+
// | Init Piwigo Tag Groups                                                |
// +-----------------------------------------------------------------------+

include_once(TG_PATH . 'include/functions.inc.php');

$events_functions = TG_PATH.'include/events.inc.php';

// if ($render_tag_names)
// {
//   // due to the change of the way tags are loaded in admin (via API and/or stored in LocalStorage),
//   // we don't really know when we're in admin, so it's better to not "render" (ie remove group name)
//   // add_event_handler('render_tag_name', 'tg_clean_tag_name');
// }

add_event_handler('init', 'tg_init', EVENT_HANDLER_PRIORITY_NEUTRAL, $events_functions);
add_event_handler('loc_begin_page_header', 'tg_groups_display', EVENT_HANDLER_PRIORITY_NEUTRAL, $events_functions);
add_event_handler('loc_end_index', 'tg_index_groups_display', EVENT_HANDLER_PRIORITY_NEUTRAL, $events_functions);
add_event_handler('loc_end_picture', 'tg_loc_end_picture', EVENT_HANDLER_PRIORITY_NEUTRAL, $events_functions);

if (defined('IN_ADMIN'))
{
  include_once(TG_PATH . 'include/functions_admin.inc.php');
  $events_admin = TG_PATH.'include/events_admin.inc.php';
  add_event_handler('loc_end_picture_modify', 'tg_loc_end_picture_modify', EVENT_HANDLER_PRIORITY_NEUTRAL, $events_admin);
}
?>
