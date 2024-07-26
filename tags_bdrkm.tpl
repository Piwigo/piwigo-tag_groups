{if $display_mode == 'groups'}
  {foreach from=$tag_groups item=tag_group}
    <div class="card w-100 mb-3 tagGroup">
      <div class="card-header tagLetter">{$tag_group.TITLE}</div>
      <div class="list-group list-group-flush">
    {foreach from=$tag_group.tags item=tag}
        <a href="{$tag.URL}" class="list-group-item list-group-item-action" title="{$tag.name}">{$tag.name}<span class="badge badge-secondary ml-2">{$tag.counter} photo</span></a>
    {/foreach}
      </div>
    </div>
  {/foreach}
{/if}
