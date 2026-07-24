{if $display_mode == 'groups'}
  <div class="card-columns">
  {* <div class="card-deck"> *}
  {foreach from=$tag_groups item=tag_group}
    {assign var="hidden" value=false}
    <div class="tg_{$tag_group.ID}">
      <div class="card mb-3 tagGroup align-self-stretch">
        <div class="card-header tagLetter">
          {$tag_group.TITLE}
        </div>
        <div class="card-body">
        {* <div class=" card-body list-group list-group-flush"> *}
      {if isset($tg_items_to_display)}
        {foreach from=$tag_group.tags item=tag name=counter}
          {if $smarty.foreach.counter.iteration <= $tg_items_to_display}
          <a href="{$tag.URL}" class="list-group-item list-group-item-action visible" title="{$tag.name}">{$tag.name}<span class="badge badge-secondary ml-2">{$tag.counter} photo</span></a>
          {elseif $smarty.foreach.counter.iteration > $tg_items_to_display}
          {$hidden = true}
          <a href="{$tag.URL}" class="list-group-item list-group-item-action hiddenGroups hide" title="{$tag.name}">{$tag.name}<span class="badge badge-secondary ml-2">{$tag.counter} photo</span></a>
          {/if}
        
        {/foreach}
      {else}
        <a href="{$tag.URL}" class="list-group-item list-group-item-action" title="{$tag.name}">{$tag.name}<span class="badge badge-secondary ml-2">{$tag.counter} photo</span></a>
      {/if}
        </div>
      {if $hidden == true}
        <div class="card-footer">
          <span id="tg_see_more" class="show" onclick="tg_toggle_groups('{$tag_group.ID}','show')">{"See more"|translate}</span>
          <span id="tg_hide" class="hide" onclick="tg_toggle_groups('{$tag_group.ID}','hide')">{"Hide"|translate}</span>
        </div>
      {/if}
      </div>
    </div>
  {/foreach}
</div>
{/if}

<style>

.card-footer{
  padding: 15px 35px;
  padding-top:0px;
}

#tg_see_more,
#tg_hide{
  color: var(--secondary);
}

.hide{
  display:none
}

.show{
  display:block;
}

</style>
{if isset($tg_items_to_display)}
<script >
function tg_toggle_groups(tagGroup,toggle)
{
  var hiddenElements = document.querySelectorAll(".tg_"+tagGroup+" .hiddenGroups")
  hiddenElements.forEach(function(e){
    if("show" == toggle)
    {
      e.classList.remove("hide")
      e.classList.add("show");
    }
    else
    {
      e.classList.add("hide")
      e.classList.remove("show");
    }
  });

  var seeMore = document.querySelector(".tg_"+tagGroup+" #tg_see_more")
  var hide = document.querySelector(".tg_"+tagGroup+" #tg_hide")

  if("show" == toggle)
  {
    seeMore.classList.remove("show")
    seeMore.classList.add("hide");
    hide.classList.remove("hide")
    hide.classList.add("show");
  }
  else
  {
    seeMore.classList.remove("hide")
    seeMore.classList.add("show");
    hide.classList.remove("show")
    hide.classList.add("hide");
  }
}

</script>
{/if}

