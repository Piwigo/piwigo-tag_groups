<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function tg_clean_tag_name($tag_name)
{
  return preg_replace('/^[^:]*:/', '', $tag_name);
}

function tg_groups_display_prefilter($content)
{
  global $user;

  if('bootstrap_darkroom' != $user['theme'])
  {
    $template_content = file_get_contents(TG_PATH.'/template/tags.tpl');
  }
  else
  {
    $template_content = file_get_contents(TG_PATH.'/template/tags_bdrkm.tpl');
  }
  
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

function tg_get_tag_groups_fields()
{
  $query = '
SELECT
    id,
    name
  FROM '.TAGS_TABLE.'
  WHERE name LIKE \'%:%\'
  ORDER BY name
;';
  $result = pwg_query($query);

  $tg_groups = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $prefix = explode(':', $row['name'], 2)[0];
    $tg_groups[$prefix] = true;
  }
  $tg_groups = array_keys($tg_groups);
  sort($tg_groups);

  return $tg_groups;
}

function tg_get_tag_groups_selection(array $tag)
{
  if (strpos($tag['name'], ':') === false)
  {
    return null; // this tag is not a tag_groups
  }

  $prefix = explode(':', $tag['name'], 2)[0]; // country:France => country
  return [
    'prefix' => $prefix,
    'id' => $tag['id'],
    'name' => $tag['name'],
  ]; 
}
