<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

/**
 * `Tag Groups` : add new pwg method
 */
function tg_add_methods($arr)
{
  $service = &$arr[0];

  $service->addMethod(
    'taggroups.setConfig',
    'tg_set_config',
    array(
      'index_filters' => array(
        'type' => WS_TYPE_BOOL,
        'info' => 'Show group filters on the tags page',
      ),
      'show_as_field' => array(
        'type' => WS_TYPE_BOOL,
        'info' => 'Show tag groups as separate fields',
      ),
      'pwg_token' => array(),
    ),
    'Save the Tag Groups configuration',
    null,
    array(
      'hidden' => false,
      'post_only' => true,
      'admin_only' => true,
    )
  );
}

/**
 * `Tag Groups` : save the plugin configuration
 */
function tg_set_config($params)
{
  global $conf;

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if (!is_admin())
  {
    return new PwgError(401, 'Access Denied');
  }

  $validated_conf = array(
    'index_filters' => $params['index_filters'],
    'show_as_field' => $params['show_as_field'],
  );

  conf_update_param('tag_groups', array_merge($conf['tag_groups'], $validated_conf), true);

  return array(
    'message' => 'The configuration has been successfully saved.',
    'configuration' => $conf['tag_groups'],
  );
}
