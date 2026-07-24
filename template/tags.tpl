  {if $display_mode == 'groups'}
  <table>
    <tr>
      <td valign="top">
    {foreach from=$tag_groups item=tag_group}
  <fieldset class="tagLetter">
    <legend class="tagLetterLegend">{$tag_group.TITLE}</legend>
    <table class="tagLetterContent">
      {foreach from=$tag_group.tags item=tag}
      <tr class="tagLine">
        <td><a href="{$tag.URL}" title="{$tag.name}">{$tag.name}</a></td>
        <td class="nbEntries">{$tag.counter|@translate_dec:'%d photo':'%d photos'}</td>
      </tr>
      {/foreach}
    </table>
  </fieldset>
      {if isset($tag_group.CHANGE_COLUMN) }
      </td>
      <td valign="top">
      {/if}
    {/foreach}
      </td>
    </tr>
  </table>
  {/if}
