// Simple 360 image viewer
window.show360Viewer = function(imgArray) {
  let modal = document.createElement('div');
  modal.style.position = 'fixed';
  modal.style.top = 0;
  modal.style.left = 0;
  modal.style.width = '100vw';
  modal.style.height = '100vh';
  modal.style.background = 'rgba(0,0,0,0.8)';
  modal.style.zIndex = 1000;
  modal.innerHTML = '<div id="viewer360" style="width:60vw;height:60vh;margin:10vh auto;background:#222;display:flex;align-items:center;justify-content:center;"></div><button id="close360" style="position:absolute;top:2vh;right:2vw;z-index:1001;">Close</button>';
  document.body.appendChild(modal);
  document.getElementById('close360').onclick = ()=>modal.remove();
  const container = document.getElementById('viewer360');
  let idx = 0;
  let img = document.createElement('img');
  img.src = imgArray[0];
  img.style.maxWidth = '100%';
  img.style.maxHeight = '100%';
  container.appendChild(img);
  let startX = null;
  container.onmousedown = e => { startX = e.clientX; };
  container.onmouseup = e => { startX = null; };
  container.onmouseleave = e => { startX = null; };
  container.onmousemove = e => {
    if (startX !== null) {
      let dx = e.clientX - startX;
      if (Math.abs(dx) > 10) {
        idx = (idx + (dx > 0 ? 1 : -1) + imgArray.length) % imgArray.length;
        img.src = imgArray[idx];
        startX = e.clientX;
      }
    }
  };
};
