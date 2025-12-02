const canvas = document.getElementById("canvas3d");
const engine = new BABYLON.Engine(canvas, true);

const createScene = function() {
    const scene = new BABYLON.Scene(engine);

    const camera = new BABYLON.ArcRotateCamera("cam",
        Math.PI / 2, Math.PI / 2.5, 3,
        BABYLON.Vector3.Zero(), scene);
    camera.attachControl(canvas, true);

    const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(0, 2, 0), scene);

    BABYLON.SceneLoader.Append("./models/", "sofa.glb", scene, function() {
        trocarBabylon("tecido_bege");
    });

    return scene;
};

const scene = createScene();

function trocarBabylon(nome) {
    const mat = new BABYLON.PBRMaterial(nome, scene);
    mat.albedoTexture = new BABYLON.Texture(`./textures/${nome}/albedo.jpg`, scene);
    mat.bumpTexture = new BABYLON.Texture(`./textures/${nome}/normal.jpg`, scene);
    mat.metallicTexture = new BABYLON.Texture(`./textures/${nome}/roughness.jpg`, scene);

    scene.meshes.forEach(mesh => {
        if (mesh.material) mesh.material = mat;
    });
}

engine.runRenderLoop(function() {
    scene.render();
});
