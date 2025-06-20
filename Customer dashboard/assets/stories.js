// Demo story data and filtering logic
const stories = [
  {id:1, title:'The Lion and the Hare', category:'Folktale', media:'audio', src:'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3'},
  {id:2, title:'The Clever Farmer', category:'Legend', media:'video', src:'https://www.w3schools.com/html/mov_bbb.mp4'}
];
window.onload = function() {
  const list = document.getElementById('storyList');
  const search = document.getElementById('storySearch');
  const catFilter = document.getElementById('storyCategory');
  // Populate filter
  [...new Set(stories.map(s=>s.category))].forEach(cat=>{
    catFilter.innerHTML += `<option value="${cat}">${cat}</option>`;
  });
  function render() {
    let filtered = stories.filter(s =>
      (!search.value || s.title.toLowerCase().includes(search.value.toLowerCase())) &&
      (!catFilter.value || s.category === catFilter.value)
    );
    list.innerHTML = filtered.map(s =>
      `<div class='story-card'>
        <div class="story-title">${s.title}</div>
        <div class="story-meta">${s.category}</div>
        <div class="story-media">`+
        (s.media==='audio' ? `<audio controls src="${s.src}"></audio>` : `<video controls width="100%" src="${s.src}"></video>`) +
        `</div>
      </div>`
    ).join('') || '<em>No stories found.</em>';
  }
  search.oninput = catFilter.onchange = render;
  render();
};
