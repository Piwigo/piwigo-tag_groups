$(function() {
  const $tag_area = $('[data-selectize="tags"]').first().closest('p');

  const groups = TG.groups.map((label, i) => ({ label: label, id: 'tg-' + i }));

  let html = '';
  groups.forEach(({ label, id }) => {
    html += `
    <p class="tg-container">
      <strong>${label}</strong>
      <br>
      <select id="${id}" name="tags[]" data-value='${JSON.stringify(TG.tg_selection[label]) ?? null}'
        multiple placeholder="${tg_search_str}" style="width:calc(100% + 2px);"></select>
    </p>`;
  });
  $tag_area.after(html);

  const tgTagsCache = new TagsCache({
    serverKey: TG.cacheKeyTags,
    serverId: TG.cacheHash,
    rootUrl: TG.rootUrl,
  });

  groups.forEach(function({ label, id }) {
    tgTagsCache.get(function(data) {
      var options = tg_group_filter(label)(data);

      $('#' + id).selectize({
        valueField: 'id',
        labelField: 'name',
        sortField: 'name',
        searchField: ['name'],
        plugins: ['remove_button'],
        options: options,
        items: (TG.tg_selection[label] ?? []).map(t => t.id ?? t),
        create: function(input, callback) {
          callback({ id: label + ':' + input, name: input });
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
