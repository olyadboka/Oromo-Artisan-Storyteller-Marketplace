// 3D Viewer for product demo using Three.js
window.show3DViewer = function(modelUrl) {
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
  // Load Three.js if not loaded
  if (!window.THREE) {
    let s = document.createElement('script');
    s.src = 'assets/three.min.js';
    s.onload = ()=>initViewer(modelUrl);
    document.body.appendChild(s);
  } else {
    initViewer(modelUrl);
  }
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
    // Add a bright directional light
    const dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
    dirLight.position.set(5, 10, 7.5);
    scene.add(dirLight);
    camera.position.set(0,1,3);
    // Orbit controls
    let controls;
    if (THREE.OrbitControls) {
      controls = new THREE.OrbitControls(camera, renderer.domElement);
    }
    // Load model (GLTF/GLB)
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
};
