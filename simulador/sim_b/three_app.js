let scene, camera, renderer, sofa;
let autoRotate = true;

init();
loadModel();
animate();

function init() {
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf2f2f2);

    camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 100);
    camera.position.set(2.5, 1.5, 3);

    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth - 260, window.innerHeight - 120);
    document.getElementById("canvas3d").appendChild(renderer.domElement);

    // LUZES
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
    scene.add(hemiLight);

    const dirLight = new THREE.DirectionalLight(0xffffff, 1);
    dirLight.position.set(4, 10, 4);
    scene.add(dirLight);

    window.addEventListener("resize", onWindowResize);
}

function onWindowResize() {
    camera.aspect = (window.innerWidth - 260) / (window.innerHeight - 120);
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth - 260, window.innerHeight - 120);
}

function loadModel() {
    const loader = new THREE.GLTFLoader();

    loader.load("./models/sofa.glb", gltf => {
        sofa = gltf.scene;
        sofa.scale.set(1, 1, 1);
        scene.add(sofa);

        trocarTecido('tecido_bege');
    });
}

function criarMaterial(nome) {
    const loader = new THREE.TextureLoader();

    return new THREE.MeshStandardMaterial({
        map: loader.load(`./textures/${nome}/albedo.jpg`),
        normalMap: loader.load(`./textures/${nome}/normal.jpg`),
        roughnessMap: loader.load(`./textures/${nome}/roughness.jpg`),
    });
}

function aplicarMaterial(obj, material) {
    obj.traverse(child => {
        if (child.isMesh) child.material = material;
    });
}

function trocarTecido(nome) {
    const mat = criarMaterial(nome);
    aplicarMaterial(sofa, mat);
}

function toggleAutoRotate() {
    autoRotate = !autoRotate;
}

function zoomIn() {
    camera.position.z -= 0.2;
}

function zoomOut() {
    camera.position.z += 0.2;
}

function animate() {
    requestAnimationFrame(animate);

    if (sofa && autoRotate) sofa.rotation.y += 0.005;

    renderer.render(scene, camera);
}
