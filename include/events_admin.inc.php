<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function tg_loc_end_picture_modify()
{
  global $template, $conf, $tag_selection;

  if (!$conf['tag_groups']['show_as_field'])
  {
    return;
  }

  // get tag groups as field
  $tg_groups = tg_get_tag_groups_fields();

  $tg_selection = [];
  foreach($tag_selection as $tag)
  {
    $tag_group = tg_get_tag_groups_selection($tag);
    if (null === $tag_group) continue;

    $tg_selection[$tag_group['prefix']][] = [
      'id' => $tag['id'],
      'name' => $tag['name'],
    ]; 
  }

  $cache_keys = get_admin_client_cache_keys(array('tags'));
  $template->assign('TG', array(
    'groups' => $tg_groups,
    'tg_selection' => $tg_selection,
    'cacheKeyTags' => $cache_keys['tags'],
    'cacheHash' => $cache_keys['_hash'],
    'rootUrl' => get_root_url(),
  ));

  $template->set_prefilter('picture_modify', 'tg_tags_filter_prefilter');
  $template->set_filename('tg_picture_modify', TG_PATH.'/admin/template/picture_modify.tpl');
  $template->parse('tg_picture_modify');
}