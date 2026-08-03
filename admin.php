<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $page, $conf, $template;

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

$page['tab'] = isset($_GET['tab']) ? $_GET['tab'] : $page['tab'] = 'config';

// Create tabsheet
include_once(PHPWG_ROOT_PATH . 'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('tag_groups');
$tabsheet->add('config', '<span class="icon-cog"></span>'.l10n('Configuration'), TG_ADMIN . '-config');
$tabsheet->select($page['tab']);
$tabsheet->assign();

$template->assign(array(
  'TG_PATH'=> TG_PATH,
  'TG_CONFIG' => $conf['tag_groups'],
  'PWG_TOKEN' => get_pwg_token(),
));
$template->set_filename('tg_admin_content', TG_REALPATH . '/admin/template/configuration.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'tg_admin_content');