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

$render_tag_names = true;
if (defined('IN_ADMIN'))
{
  $render_tag_names = false;
}

if (script_basename() == 'tags')
{
  global $page, $conf;
  
  if (isset($_GET['display_mode']))
  {
    if ('groups' == $_GET['display_mode'])
    {
      $render_tag_names = false;
    }
  }
  elseif ('groups' == $conf['tags_default_display_mode'])
  {
    $render_tag_names = false;
  }

  $page['tg_display'] = !$render_tag_names;
}

if ($render_tag_names)
{
  // due to the change of the way tags are loaded in admin (via API and/or stored in LocalStorage),
  // we don't really know when we're in admin, so it's better to not "render" (ie remove group name)
  // add_event_handler('render_tag_name', 'tg_clean_tag_name');
}

function tg_clean_tag_name($tag_name)
{
  return preg_replace('/^[^:]*:/', '', $tag_name);
}

// file_get_contents('tags.tpl')

if (script_basename() == 'tags')
{
  add_event_handler('loc_begin_page_header', 'tg_groups_display');
}

function tg_groups_display()
{
  global $conf, $template, $user, $tags, $page;

  load_language('plugin.lang', PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');
  load_language('lang', PHPWG_ROOT_PATH.PWG_LOCAL_DIR, array('no_fallback'=>true, 'local'=>true) );

  $template->set_prefilter('tags', 'tg_add_display_link_prefilter');

  $template->assign('U_TAG_GROUPS', get_root_url().'tags.php?display_mode=groups');

  if ($page['tg_display'])
  {
    // echo __FILE__.'::'.__LINE__.' display_mode=groups<br>';
    $template->set_prefilter('tags', 'tg_groups_display_prefilter');

    $template->assign('display_mode', 'groups');

    // we want tags diplayed in alphabetic order
    usort($tags, 'tag_alpha_compare');

    $current_tag_group = null;
    $nb_tags = count($tags);
    $current_column = 1;
    $current_tag_idx = 0;
    
    $tag_group = array(
      'tags' => array()
      );
    
    foreach ($tags as $tag)
    {
      // if the tag belongs to no group, we don't show it on the "tag by
      // group" display mode
      if (strpos($tag['name'], ':') === false)
      {
        continue;
      }
      else
      {
        list($tag['group'], $tag['name']) = explode(':', $tag['name'], 2);
        $tag['group'] = preg_replace('/^[^=]*=/', '', $tag['group']);
      }

      if ($current_tag_idx == 0)
      {
        $current_tag_group = $tag['group'];
        $tag_group['TITLE'] = $tag['group'];
      }
      
      // new group?
      if ($tag['group'] !== $current_tag_group)
      {
        if ($current_column < $conf['tag_letters_column_number']
            and $current_tag_idx > $current_column*$nb_tags/$conf['tag_letters_column_number'] )
        {
          $tag_group['CHANGE_COLUMN'] = true;
          $current_column++;
        }
        
        $tag_group['TITLE'] = $current_tag_group;
        
        $template->append(
          'tag_groups',
          $tag_group
          );
        
        $current_tag_group = $tag['group'];
        $tag_group = array(
          'tags' => array()
          );
      }
      
      array_push(
        $tag_group['tags'],
        array_merge(
          $tag,
          array(
            'URL' => make_index_url(
              array(
                'tags' => array($tag),
                )
              ),
            )
          )
        );
      
      $current_tag_idx++;
    }
    
    // flush last group
    if (count($tag_group['tags']) > 0)
    {
      unset($tag_group['CHANGE_COLUMN']);
      $tag_group['TITLE'] = $current_tag_group;
      $template->append(
        'tag_groups',
        $tag_group
        );
    }
  }
}

function tg_groups_display_prefilter($content)
{
  $template_content = file_get_contents(PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/tags.tpl');
  
  $search = '#\{/if\}((\s*</div>\s*)+)<!-- content -->#mi';
  $replace = '{/if}'."\n".$template_content.'\1 <!-- content -->';

  return preg_replace($search, $replace, $content);
}

function tg_add_display_link_prefilter($content)
{
  $search = '#\{if \$display_mode == \'letters\'\}\s*<li>#mi';
  $replace = '{if $display_mode != \'cloud\'}<li>';
  $content = preg_replace($search, $replace, $content);

  $search = '#\{if \$display_mode == \'cloud\'\}\s*<li>#mi';
  $replace = '{if $display_mode != \'letters\'}<li>';
  $content = preg_replace($search, $replace, $content);

  $search = '<ul class="categoryActions">';
  $replace = '<ul class="categoryActions">
{if $display_mode != \'groups\'}<li>
  <li><a href="{$U_TAG_GROUPS}">{\'show tag groups\'|@translate}</a></li>
{/if}
';
  return str_replace($search, $replace, $content);
}

add_event_handler('loc_end_index', 'tg_index_groups_display');
function tg_index_groups_display()
{
  global $template, $page, $conf;

  if (!isset($conf['tag_groups_index_filters']) or !$conf['tag_groups_index_filters'])
  {
    return;
  }

  if ('tags' != $page['section'])
  {
    return;
  }

  $current_tag_groups = array();
  $is_tag_group_selection = true;
  foreach ($page['tags'] as $tag)
  {
    $tag['name'] = trigger_change('render_tag_name', $tag['name'], $tag);
    if (!preg_match('/:/', $tag['name']))
    {
      $is_tag_group_selection = false;
    }
    else
    {
      list($group, $name) = explode(':', $tag['name'], 2);
      $group = preg_replace('/^[^=]*=/', '', $group);
      $current_tag_groups[$group] = $tag['id'];
    }
  }

  if (!$is_tag_group_selection)
  {
    return;
  }

  $template->set_filenames(array('tag_groups_selection' => dirname(__FILE__).'/index.tpl'));

  // related tags
  $related_tags = get_common_tags(
    $page['items'],
    0,
    $page['tag_ids']
  );

  $related_tag_ids = array();
  foreach ($related_tags as $related_tag)
  {
    $related_tag_ids[ $related_tag['id'] ] = 1;
  }

  // we need to filter on available_tags
  $all_tag_ids = array(-1 => 1);
  $all_tags = get_available_tags();
  foreach ($all_tags as $tag)
  {
    $all_tag_ids[ $tag['id'] ] = 1;
  }

  $query = '
SELECT
    *
  FROM '.TAGS_TABLE.'
  WHERE name LIKE \'%:%\'
    AND id IN ('.implode(',', array_keys($all_tag_ids)).')
  ORDER BY name
;';
  $tags = query2array($query, 'id');

  $tag_groups = array();
  foreach ($tags as $id => $tag)
  {
    $tag['name'] = trigger_change('render_tag_name', $tag['name'], $tag);
    list($group, $name) = explode(':', $tag['name'], 2);
    $group = preg_replace('/^[^=]*=/', '', $group);

    if (!isset($tag_groups[$group]))
    {
      $tag_groups_url = $current_tag_groups;
      unset($tag_groups_url[$group]);
      if (empty($tag_groups_url))
      {
        $value = 'tags.php?display_mode=groups';
      }
      else
      {
        $value = tg_make_tag_group_url(array_values($tag_groups_url), $page['tags']);
      }

      $tag_groups[$group] = array(
        0 => array(
          'name' => '--',
          'value' => $value,
        )
      );
    }

    $tag_groups_url = $current_tag_groups;

    // only one tag (or none) for each tag group
    $tag_groups_url[$group] = $id;
    $value = tg_make_tag_group_url(array_values($tag_groups_url), array_merge($page['tags'], array($tag)));

    // we need to know if the tag is selectable.
    $other_group_tags_related_tag_ids = tg_get_other_groups_related_tag_ids($current_tag_groups, $group);

    // is there only current group selected?
    $only_this_group_selected = false;
    if (count($current_tag_groups) == 1 and isset($current_tag_groups[$group]))
    {
      $only_this_group_selected = true;
    }

    $disabled = 'disabled';
    if ($only_this_group_selected)
    {
      $disabled = '';
    }
    elseif (isset($other_group_tags_related_tag_ids[$id]))
    {
      $disabled = '';
    }

    if ('' == $disabled or !conf_get_param('tag_groups_dynamic_filters', false))
    {
      // When dynamic filters enabled, we only want usable tags and skip the disabled
      $tag_groups[$group][$id] = array(
        'name' => $name,
        'value' => $value,
        'selected' => (in_array($id, $page['tag_ids']) ? 'selected' : ''),
        'disabled' => $disabled,
      );
    }
  }

  if (conf_get_param('tag_groups_dynamic_filters', false))
  {
    foreach ($tag_groups as $group_name => $tags)
    {
      if (1 == count($tags))
      {
        unset($tag_groups[$group_name]);
      }
    }
  }

  $template->assign('tag_groups', $tag_groups);

  $template->assign_var_from_handle('PLUGIN_INDEX_CONTENT_BEGIN', 'tag_groups_selection');
}

function tg_make_tag_group_url($selected_tag_ids, $available_tags)
{
  $tags = array();

  foreach ($available_tags as $candidate_tag)
  {
    if (in_array($candidate_tag['id'], $selected_tag_ids))
    {
      $tags[] = $candidate_tag;
    }
  }

  return make_index_url(array('section'=>'tags', 'tags'=>$tags));
}

function tg_get_other_groups_related_tag_ids($current_tag_groups, $group)
{
  global $page;

  // Let's say you have 3 groups {activity, city, orientation} and following tags:
  //
  // activity:sport #1 <= currently selected
  // activity:industry #2
  // city:London #3
  // city:Paris #4 <= currently selected
  // city:Madrid #5
  // orientation:landscape #6
  // orientation:portrait #7
  //
  // The $current_tag_groups is array('activity'=>1, 'city'=>4)
  //
  // There are 2 kind of situations:
  //
  // 1) you ask for a group which is not currently selected (orientation in our
  //    example) and we must simply find all tags related to tags {1,4}
  //
  // 2) you ask for a group which is already selected (activity or city) and we
  //    must exclude the tag of this group before searching for related tags. We
  //    do this because if there are photos tagged "activity:sport" and
  //    "city:London" we want to let the user switch to "city:London"

  $other_group_tags = $current_tag_groups;
  unset($other_group_tags[$group]);
  asort($other_group_tags);
  $cache_key = implode('~', array_values($other_group_tags));

  if (!isset($page[__FUNCTION__.'_cache'][$cache_key]))
  {
    $other_group_tags_items = get_image_ids_for_tags($other_group_tags);
    $other_group_tags_related_tags = get_common_tags($other_group_tags_items, 0, $other_group_tags);
    $other_group_tags_related_tag_ids = array();
    foreach ($other_group_tags_related_tags as $tag)
    {
      $other_group_tags_related_tag_ids[ $tag['id'] ] = 1;
    }

    @$page[__FUNCTION__.'_cache'][$cache_key] = $other_group_tags_related_tag_ids;
  }

  return $page[__FUNCTION__.'_cache'][$cache_key];
}
?>
