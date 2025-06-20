// Demo product data and filtering logic
const products = [
  {id:1, name:'Handmade Basket', category:'Basketry', location:'Finfinne', price:200, model:'assets/sample.glb', images:[
    'https://picsum.photos/id/1011/400/300',
    'https://picsum.photos/id/1012/400/300',
    'https://picsum.photos/id/1013/400/300',
    'https://picsum.photos/id/1015/400/300'
  ]},
  {id:2, name:'Beaded Necklace', category:'Jewelry', location:'Jimma', price:150, model:'assets/sample.glb', images:[
    'https://picsum.photos/id/1021/400/300',
    'https://picsum.photos/id/1022/400/300',
    'https://picsum.photos/id/1023/400/300',
    'https://picsum.photos/id/1025/400/300'
  ]},
  {id:3, name:'Woven Scarf', category:'Textiles', location:'Harar', price:300, model:'assets/sample.glb', images:[
    'https://picsum.photos/id/1031/400/300',
    'https://picsum.photos/id/1032/400/300',
    'https://picsum.photos/id/1033/400/300',
    'https://picsum.photos/id/1035/400/300'
  ]}
];
window.onload = function() {
  const list = document.getElementById('productList');
  const search = document.getElementById('search');
  const catFilter = document.getElementById('categoryFilter');
  const locFilter = document.getElementById('locationFilter');
  // Populate filters
  [...new Set(products.map(p=>p.category))].forEach(cat=>{
    catFilter.innerHTML += `<option value="${cat}">${cat}</option>`;
  });
  [...new Set(products.map(p=>p.location))].forEach(loc=>{
    locFilter.innerHTML += `<option value="${loc}">${loc}</option>`;
  });
  function render() {
    let filtered = products.filter(p =>
      (!search.value || p.name.toLowerCase().includes(search.value.toLowerCase())) &&
      (!catFilter.value || p.category === catFilter.value) &&
      (!locFilter.value || p.location === locFilter.value)
    );
    list.innerHTML = filtered.map(p=>
      `<div class='product-card'>
        <img src="${p.images && p.images.length ? p.images[0] : 'https://picsum.photos/seed/' + p.id + '/220/160'}" alt="${p.name}" class="product-img">
        <div class="product-title">${p.name}</div>
        <div class="product-meta">${p.category} &bull; ${p.location}</div>
        <div class="product-price">${p.price} ETB</div>
        <div class="product-actions">
          <button onclick="addToCart(${p.id})">Add to Cart</button>
          <button onclick=\"show3DViewer('${p.model}')\">3D/VR View</button>
          <button onclick='show360Viewer([${p.images.map(i=>`"${i}"`).join(",")}])'>360° View</button>
        </div>
      </div>`
    ).join('') || '<em>No products found.</em>';
  }
  search.oninput = catFilter.onchange = locFilter.onchange = render;
  render();
};
function addToCart(id) {
  let cart = JSON.parse(localStorage.getItem('cart')||'[]');
  cart.push(id);
  localStorage.setItem('cart', JSON.stringify(cart));
  alert('Added to cart!');
}
function loadThreeDependencies(callback) {
  // Load Three.js core
  if (!window.THREE) {
    const script = document.createElement('script');
    script.src = 'assets/three.min.js';
    script.onload = () => loadGLTFLoader(callback);
    document.body.appendChild(script);
  } else {
    loadGLTFLoader(callback);
  }
  function loadGLTFLoader(cb) {
    if (!window.THREE.GLTFLoader) {
      const loaderScript = document.createElement('script');
      loaderScript.src = 'assets/gltfloader.js';
      loaderScript.onload = () => loadOrbitControls(cb);
      document.body.appendChild(loaderScript);
    } else {
      loadOrbitControls(cb);
    }
  }
  function loadOrbitControls(cb) {
    if (!window.THREE.OrbitControls) {
      const controlsScript = document.createElement('script');
      controlsScript.src = 'assets/orbitcontrols.js';
      controlsScript.onload = cb;
      document.body.appendChild(controlsScript);
    } else {
      cb();
    }
  }
}

window.show3DViewer = function(modelUrl) {
  loadThreeDependencies(() => {
    let modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = 0;
    modal.style.left = 0;
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.background = 'rgba(0,0,0,0.8)';
    modal.style.zIndex = 1000;
    modal.innerHTML = '<div id="viewer3d" style="width:80vw;height:80vh;margin:5vh auto;background:#222;"></div><button id="close3d" style="position:absolute;top:2vh;right:2vw;z-index:1001;">Close</button>';
    document.body.appendChild(modal);
    document.getElementById('close3d').onclick = ()=>modal.remove();
    initViewer(modelUrl);
    function initViewer(url) {
      const container = document.getElementById('viewer3d');
      container.innerHTML = '';
      const scene = new THREE.Scene();
      const camera = new THREE.PerspectiveCamera(75, container.offsetWidth/container.offsetHeight, 0.1, 1000);
      const renderer = new THREE.WebGLRenderer({antialias:true});
      renderer.setSize(container.offsetWidth, container.offsetHeight);
      container.appendChild(renderer.domElement);
      const light = new THREE.HemisphereLight(0xffffff, 0x444444, 1.5);
      scene.add(light);
      const dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
      dirLight.position.set(5, 10, 7.5);
      scene.add(dirLight);
      camera.position.set(0,1,3);
      let controls;
      if (THREE.OrbitControls) {
        controls = new THREE.OrbitControls(camera, renderer.domElement);
      }
      const loader = new THREE.GLTFLoader();
      loader.load(url, function(gltf) {
        scene.add(gltf.scene);
        animate();
      }, undefined, function(e){
        container.innerHTML = '<p style="color:white">Failed to load 3D model.</p>';
      });
      function animate() {
        requestAnimationFrame(animate);
        if (controls) controls.update();
        renderer.render(scene, camera);
      }
    }
  });
};
