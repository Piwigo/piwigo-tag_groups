$(function() {
  const $tag_area = $('[data-selectize="tags"]').first().closest('p');

  let html = '';
  TG.groups.forEach((label) => {
    const id = label.toLowerCase();
    html += `
    <p class="tg-container">
      <strong>${label}</strong>
      <br>
      <select id="tg-${id}" name="tags[]" data-value='${JSON.stringify(TG.tg_selection[label]) ?? null}'
        multiple placeholder="${tg_search_str}" style="width:calc(100% + 2px);"></select>
    </p>`;
  });
  $tag_area.after(html);

  const tgTagsCache = new TagsCache({
    serverKey: TG.cacheKeyTags,
    serverId: TG.cacheHash,
    rootUrl: TG.rootUrl,
  });

  TG.groups.forEach(function(group) {
    tgTagsCache.get(function(data) {
      var options = tg_group_filter(group)(data);

      $('#tg-' + group).selectize({
        valueField: 'id',
        labelField: 'name',
        sortField: 'name',
        searchField: ['name'],
        plugins: ['remove_button'],
        options: options,
        items: (TG.tg_selection[group] ?? []).map(t => t.id ?? t),
        create: function(input, callback) {
          callback({ id: group + ':' + input, name: input });
        },
      });
    });
  });


});

function tg_group_filter(prefix) {
  return function(data) {
    return data
      .filter(t => String(t.name).indexOf(prefix + ':') === 0)
      .map(t => $.extend({}, t, { name: t.name.slice(prefix.length + 1) }));
  };
}

window.tg_main_filter = function(data) {
  return data.filter(function(t) {
    return String(t.name).indexOf(':') === -1;
  });
}
