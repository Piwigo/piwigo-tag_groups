{footer_script}
jQuery(document).ready(function() {
  jQuery(".tag_group select").change(function() {
    var url_tags = jQuery(this).val();
    window.location.href = url_tags;
  })
});
{/footer_script}

<div class="tag_groups_selection" style="margin:20px;">
{foreach from=$tag_groups key=tag_group_name item=tags}
  <div class="tag_group" id="tag_group-{substr(md5($tag_group_name), 0, 5)}" style="display: inline-block; margin-right:20px;">
    <span class="tag_group_name">
      {$tag_group_name}
      <select>
  {foreach from=$tags key=tag_id item=tag}
        <option value="{$tag.value}" {$tag.selected|default:null} {$tag.disabled|default:null}>{$tag.name}</option>
  {/foreach}
      </select>
    </span>
  </div>
{/foreach}
</div>