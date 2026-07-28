$(function() {
  const $card = $('#card-tags');
  const $tags = $('#Tags');
  const $row = $tags.closest('tr');

  let layout, $anchor;
  if ($card.length)      { layout = 'cards'; $anchor = $card; }
  else if ($row.length)  { layout = 'tabs';  $anchor = $row; }
  else if ($tags.length) { layout = 'list';  $anchor = $tags; }
  else return;
  const link = g => layout === 'cards'
    ? `<a class="btn btn-primary btn-raised mr-1" href="${g.URL}">${g.tag_name}</a>`
    : `<a href="${g.URL}">${g.tag_name}</a>`;

  let html = '';
  for (const label of TG.groups) {
    const group = TG.related_tg_groups[label];
    if (!group) continue;

    if (layout === 'cards') {
      html += `
        <h5 class="card-title">${label}</h5>
        <div id="Tg${label}" class="imageInfo">
        ${group.map(link).join(' ')}
        </div>
      `;
    } else if (layout === 'tabs') {
      html += `
        <tr class="tg-row">
          <th scope="row">${label}</th>
          <td><div id="Tg${label}" class="imageInfo">${group.map(link).join(', ')}</div></td>
        </tr>
      `;
    } else {
      html += `
        <dt>${label}</dt>
        <dd>${group.map(link).join(', ')}</dd>
      `;
    }
  }

  if (!html) return;

  if (layout === 'cards') {
    html = `<div id="TgTags" class="card mb-2"><div class="card-body">${html}</div></div>`;
  } else if (layout === 'list') {
    html = `<div id="TgTags" class="imageInfo">${html}</div>`;
  }

  $anchor.after(html);

  if (TG.count_tags === 0) $anchor.hide();
});
