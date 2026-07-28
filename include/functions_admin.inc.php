<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function tg_tags_filter_prefilter($content)
{
  return str_replace(
    "tagsCache.selectize(jQuery('[data-selectize=tags]'), { lang: {",
    "tagsCache.selectize(jQuery('[data-selectize=tags]'), { filter: window.tg_main_filter, lang: {",
    $content
  );
}