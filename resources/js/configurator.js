import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';

// Configuration Data (配置数据：包含配件价格和颜色映射表)
const BASE_PRICE = 150000;
const ACCESSORY_PRICES = {
    rims: {
        rim7: { name: 'Sport Rims (Default)', price: 0 },
        rim1: { name: 'Vossen CV3 Style', price: 1200 },
        rim2: { name: 'BBS Super RS Style', price: 1800 },
        rim3: { name: 'Rotiform LAS-R Style', price: 1500 },
        rim4: { name: 'HRE P101 Style', price: 2200 },
        rim5: { name: 'Advan Racing GT Style', price: 2000 },
        rim6: { name: 'TE37 Black Edition', price: 2500 },
    },
    spoilers: {
        wing4: { name: 'Integrated Lip (Default)', price: 0 },
        wing1: { name: 'Carbon Fiber High Wing', price: 1200 },
        wing2: { name: 'GT Performance Wing', price: 1500 },
        wing3: { name: 'Sleek Ducktail Wing', price: 600 },
    },
    bumpers: {
        bumperF3: { name: 'Standard Sport (Default)', price: 0 },
        bumperF1: { name: 'Aggressive Aero Bumper', price: 1800 },
        bumperF2: { name: 'Widebody Spec Bumper', price: 2200 },
    }
};

const COLOR_MAP = {
    red: { hex: 0xc8413d, name: 'Ember Red' },
    yellow: { hex: 0xfacc15, name: 'Racing Yellow' },
    blue: { hex: 0x2563eb, name: 'Apex Blue' },
    grey: { hex: 0x4b5563, name: 'Asphalt Grey' },
    black: { hex: 0x0f172a, name: 'Carbon Black' },
    silver: { hex: 0xcbd5e1, name: 'Liquid Silver' },
    white: { hex: 0xf8fafc, name: 'Chalk White' }
};

const RIM_COLOR_MAP = {
    default: { hex: null, name: 'Default' },
    black: { hex: 0x111111, name: 'Matte Black' },
    white: { hex: 0xf8fafc, name: 'Chalk White' },
    silver: { hex: 0xcbd5e1, name: 'Liquid Silver' },
    bronze: { hex: 0xa87c43, name: 'Saturn Bronze' },
    darkgold: { hex: 0x8a7345, name: 'Imperial Gold' }
};

const BRAKE_COLOR_MAP = {
    red: { hex: 0xc8413d, name: 'Ember Red' },
    blue: { hex: 0x2563eb, name: 'Apex Blue' },
    yellow: { hex: 0xfacc15, name: 'Racing Yellow' },
    white: { hex: 0xf8fafc, name: 'Chalk White' },
    black: { hex: 0x0c0c0e, name: 'Carbon Black' }
};

const TINT_MAP = {
    '100': { transmission: 1.0, opacity: 1.0, color: 0xffffff },
    '70': { transmission: 1.0, opacity: 1.0, color: 0xcccccc },
    '50': { transmission: 1.0, opacity: 1.0, color: 0x999999 },
    '35': { transmission: 1.0, opacity: 1.0, color: 0x666666 },
    '15': { transmission: 1.0, opacity: 1.0, color: 0x333333 },
    '5': { transmission: 1.0, opacity: 1.0, color: 0x111111 },
};

// Application State (应用状态：记录当前选择的配置项、颜色、视角等)
const state = {
    color: 'white',
    rims: 'rim7',
    spoilers: 'wing4',
    bumpers: 'bumperF3',
    rimColor: 'default',
    brakeColor: 'red',
    windowTint: '100',
    doorsOpen: false,
    viewMode: 'exterior', // 'exterior' | 'interior' (视角模式：'外部' | '内部')
    interiorPosMode: 'driver', // 'driver' | 'center' (内部视角位置：'主驾' | '中控')
    transitioning: false,
};

// Three.js Globals (Three.js全局变量：场景、相机、渲染器、控制器等)
let scene, camera, renderer, controls, carModel;
let isInitialized = false;
let animationFrameId = null;
let mixer;
const doorActions = [];
const clock = new THREE.Clock();

const cameraAnimation = {
    active: false,
    startTime: 0,
    duration: 1200,
    startPos: new THREE.Vector3(),
    endPos: new THREE.Vector3(),
    startTarget: new THREE.Vector3(),
    endTarget: new THREE.Vector3(),
    onComplete: null
};

function easeInOutCubic(x) {
    return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2;
}

// References to Car Meshes (汽车网格模型引用：存储需要动态替换或变色的3D部件)
const carParts = {
    rims: {},     // rim1 -> Array of meshes (轮毂1 -> 网格数组)
    spoilers: {}, // wing1 -> Array of meshes (尾翼1 -> 网格数组)
    bumpers: {},  // bumperF1 -> Array of meshes (前保险杠1 -> 网格数组)
    body: [],     // Array of meshes for car_body (车身网格数组)
    glass: []     // Array of meshes for windows (车窗网格数组)
};

// Materials (材质库：存储车漆、轮毂、刹车卡钳、玻璃等材质对象)
let carBodyMaterial;
let carRimMaterial;
let carBrakeMaterial;
let glassMaterial;

/**
 * Helper to check if a mesh is part of a swappable accessory based on name/ancestors using Regex
 * 辅助函数：通过正则表达式判断当前3D部件是否属于可替换的配件（如轮毂、尾翼、保险杠）
 */
function getPartInfo(child) {
    let current = child;
    while (current && current.parent) {
        const name = current.name || '';

        // Rims Regex match (1 to 7) (使用正则匹配轮毂：从1到7)
        const rimMatch = name.match(/rim[_\s-]*0?([1-7])/i);
        if (rimMatch) {
            return { category: 'rims', key: `rim${rimMatch[1]}` };
        }

        // Wings/Spoilers Regex match (1 to 4) (使用正则匹配尾翼：从1到4)
        const wingMatch = name.match(/(wing|spoiler)[_\s-]*0?([1-4])/i);
        if (wingMatch) {
            return { category: 'spoilers', key: `wing${wingMatch[2]}` };
        }

        // Front Bumpers Regex match (1 to 3) (使用正则匹配前保险杠：从1到3)
        const bumperFMatch = name.match(/bumper[_\s-]*f[_\s-]*0?([1-3])/i);
        if (bumperFMatch) {
            return { category: 'bumpers', key: `bumperF${bumperFMatch[1]}` };
        }

        // Rear Bumper Regex match (bumperB1) (使用正则匹配后保险杠：仅bumperB1)
        const bumperBMatch = name.match(/bumper[_\s-]*b[_\s-]*0?1/i);
        if (bumperBMatch) {
            return { category: 'bumperB1', key: 'bumperB1' };
        }

        current = current.parent;
    }
    return null;
}

/**
 * Helper to determine recursively if a mesh belongs to the body paint target list,
 * while respecting negative exclusions.
 * 辅助函数：递归判断部件是否属于需要更改车漆颜色的目标列表（排除玻璃、轮胎、内饰等）
 */
function isMeshBodyPaint(child, partInfo) {
    const bodyPaintNames = [
        'AM-Body', 'AM-Bonet', 'AM-B-BUMPER', 'AM-Hood', 'AM-Lower-Cover',
        'AM-Side-Mirrors', 'AM-Side-Mirrors.001', 'AM-Handle', 'AM-Cover', 'AM-Plane.003',
        'AM-Door1', 'AM-Side-Mirrors1', 'AM-Handle1', 'AM-Door2', 'AM-Side-Mirrors2', 'AM-Handle2'
    ];

    const bodyPaintExclusions = [
        'AM-Window', 'AM-Glass-Supp', 'AM-Tire', 'AM-ForWheels', 'AM-Headlight',
        'AM-Back-Light', 'AM-Back-Small-Light', 'AM-Blinks', 'AM-Disk part 1',
        'AM-Gears', 'AM-Dashboard', 'AM-Dash', 'AM-Dash.001', 'AM-Dash.002',
        'AM-Dash.003', 'AM-Digi', 'AM-Circle', 'AM-Brake', 'AM-Seat', 'AM-Seats', 'AM-Interior', 'AM-Steering'
    ];

    // Whichever front bumper is selected gets colored (无论选择哪个前保险杠，都会被染色)
    if (partInfo && partInfo.category === 'bumpers') {
        return true;
    }

    if (partInfo && (partInfo.category === 'rims' || partInfo.category === 'spoilers')) {
        return false;
    }

    let current = child;
    let isTarget = false;
    let isExcluded = false;

    // Climb the parent tree recursively to see if child or any ancestor matches target list (递归向上查找父节点，判断当前节点或其祖先是否在目标列表中)
    while (current && current.parent) {
        const name = current.name || '';
        const baseName = name.split('.')[0];

        // 1. Check exclusions (1. 检查排除项，确保不被错误染色)
        if (bodyPaintExclusions.includes(name) || bodyPaintExclusions.includes(baseName)) {
            isExcluded = true;
        }

        // Direct prefix checks to exclude indices variations (e.g. AM-Dash.001) (使用前缀检查来排除带有序号的变体，如 AM-Dash.001)
        if (name.startsWith('AM-Dash') ||
            name.startsWith('AM-Window') ||
            name.startsWith('AM-Glass') ||
            name.startsWith('AM-Tire') ||
            name.startsWith('AM-Headlight') ||
            name.startsWith('AM-Back-Light') ||
            name.startsWith('AM-Back-Small-Light') ||
            name.startsWith('AM-Seat') ||
            name.startsWith('AM-Interior')) {
            isExcluded = true;
        }

        // 2. Check paint targets (2. 检查染色目标列表)
        if (bodyPaintNames.includes(name) || bodyPaintNames.includes(baseName)) {
            isTarget = true;
        }

        current = current.parent;
    }

    return isTarget && !isExcluded;
}

/**
 * Initialize Event Delegation on document to ensure persistence against Livewire re-renders
 * 初始化事件代理：确保Livewire重新渲染后，点击事件依然生效（处理配置面板的点击）
 */
document.addEventListener('DOMContentLoaded', () => {
    // Open Configurator (打开配置器)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#open-configurator-btn')) {
            openConfigurator();
        }
    });

    // Close Configurator (关闭配置器)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#close-configurator-btn')) {
            closeConfigurator();
        }
    });

    // Reset Camera (重置摄像机视角)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#camera-reset-btn')) {
            resetCamera();
        }
    });

    // Toggle Doors (开关车门)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#toggle-doors-btn')) {
            if (state.transitioning) return;
            toggleDoors(!state.doorsOpen);
        }
    });

    // Toggle View Mode (Interior/Exterior) (切换视角模式：内饰/外部)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#toggle-view-btn')) {
            toggleView();
        }
    });

    // Toggle Interior Position (Driver/Center) (切换内饰座位视角：主驾/副驾)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#toggle-interior-pos-btn')) {
            toggleInteriorPos();
        }
    });

    // WhatsApp Enquiry Export (导出并跳转到WhatsApp询价)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#enquire-config-btn')) {
            sendWhatsAppEnquiry();
        }
    });

    // Tabs Switcher (配置面板的标签页切换)
    document.addEventListener('click', (e) => {
        const tab = e.target.closest('.tab-btn');
        if (tab) {
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const sectionId = `section-${tab.dataset.tab}`;
            const sections = document.querySelectorAll('.sidebar-section');
            sections.forEach(sec => {
                if (sec.id === sectionId) {
                    sec.classList.add('active');
                } else {
                    sec.classList.remove('active');
                }
            });
        }
    });

    // Color Swatch Selection (Body Color) (选择车漆颜色)
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('.color-swatch[data-color]');
        if (swatch) {
            document.querySelectorAll('.color-swatch[data-color]').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            const colorKey = swatch.dataset.color;
            state.color = colorKey;

            // Update 3D body material color (更新3D车身材质颜色)
            if (carBodyMaterial && COLOR_MAP[colorKey]) {
                carBodyMaterial.color.setHex(COLOR_MAP[colorKey].hex);
            }

            // Update UI label (更新UI界面上的颜色标签)
            const colorValEl = document.getElementById('summary-color-name');
            if (colorValEl) colorValEl.textContent = COLOR_MAP[colorKey].name;
        }
    });

    // Rim Color Swatch Selection (选择轮毂颜色)
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('[data-rim-color]');
        if (swatch) {
            document.querySelectorAll('[data-rim-color]').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            const colorKey = swatch.dataset.rimColor;
            state.rimColor = colorKey;

            updateRimMaterials();

            const label = document.getElementById('summary-rim-color');
            if (label) label.textContent = RIM_COLOR_MAP[colorKey].name;
        }
    });

    // Brake Color Swatch Selection (选择刹车卡钳颜色)
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('[data-brake-color]');
        if (swatch) {
            document.querySelectorAll('[data-brake-color]').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            const colorKey = swatch.dataset.brakeColor;
            state.brakeColor = colorKey;

            if (carBrakeMaterial && BRAKE_COLOR_MAP[colorKey]) {
                carBrakeMaterial.color.setHex(BRAKE_COLOR_MAP[colorKey].hex);
            }

            const label = document.getElementById('summary-brake-color');
            if (label) label.textContent = BRAKE_COLOR_MAP[colorKey].name;
        }
    });

    // Accessory Option Card Selection (选择配件卡片)
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.option-card');
        if (card && card.dataset.category) {
            const category = card.dataset.category;
            const itemKey = card.dataset.item;

            // Visual toggle in category grid (在分类网格中切换选中状态样式)
            const categoryCards = document.querySelectorAll(`.option-card[data-category="${category}"]`);
            categoryCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            // Hide old variant, show new variant in 3D (在3D视图中隐藏旧款，显示新款配件)
            const oldItemKey = state[category];
            state[category] = itemKey;

            togglePartVisibility(category, oldItemKey, itemKey);

            // Update Price Summary (更新价格汇总)
            updateSummaryUI();
        }
    });

    // Window Tint Selection (选择车窗贴膜透光率)
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.option-card[data-tint]');
        if (card) {
            document.querySelectorAll('.option-card[data-tint]').forEach(s => s.classList.remove('active'));
            card.classList.add('active');

            const tintKey = card.dataset.tint;
            state.windowTint = tintKey;

            if (glassMaterial && TINT_MAP[tintKey]) {
                const config = TINT_MAP[tintKey];
                glassMaterial.color.setHex(config.color);
                glassMaterial.transmission = config.transmission;
                glassMaterial.opacity = config.opacity;
            }
        }
    });
});

/**
 * Open the configurator popup modal and load/run Three.js
 * 打开3D看车模态框并加载运行Three.js
 */
function openConfigurator() {
    const modal = document.getElementById('configurator-modal');
    if (!modal) return;
    modal.classList.add('active');
    document.body.classList.add('overflow-hidden');

    if (!isInitialized) {
        initThree();
    } else {
        // Resume rendering (恢复渲染循环)
        animate();
        onWindowResize();
    }
}

/**
 * Close the configurator popup modal
 * 关闭3D看车模态框：隐藏视图并停止渲染循环（节省性能）
 */
function closeConfigurator() {
    const modal = document.getElementById('configurator-modal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.classList.remove('overflow-hidden');

    // Pause animation render loop (暂停动画渲染循环)
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
        animationFrameId = null;
    }
}

/**
 * Show/Hide meshes of a category in the 3D model
 * 显示/隐藏指定类别的3D部件：用于在切换不同轮毂、尾翼时，隐藏旧的并显示新的
 */
function togglePartVisibility(category, oldKey, newKey) {
    // Hide old meshes (隐藏旧的网格模型)
    if (carParts[category] && carParts[category][oldKey]) {
        carParts[category][oldKey].forEach(mesh => {
            mesh.visible = false;
        });
    }

    // Show new meshes (显示新的网格模型)
    if (carParts[category] && carParts[category][newKey]) {
        carParts[category][newKey].forEach(mesh => {
            mesh.visible = true;
        });
    }

    // If swapping rims, ensure the newly visible rim gets the correct material styling (如果是切换轮毂，确保新显示的轮毂应用正确的材质样式)
    if (category === 'rims') {
        updateRimMaterials();
    }
}

/**
 * Refresh prices and selected specs in the UI summary panel
 * 更新价格统计：根据选中的配件计算总价，并更新底部汇总面板的显示
 */
function updateSummaryUI() {
    const rimsValEl = document.getElementById('summary-rims-price');
    const spoilerValEl = document.getElementById('summary-spoiler-price');
    const bumperValEl = document.getElementById('summary-bumper-price');
    const totalValEl = document.getElementById('summary-total-price');

    const rimSpec = ACCESSORY_PRICES.rims[state.rims];
    const spoilerSpec = ACCESSORY_PRICES.spoilers[state.spoilers];
    const bumperSpec = ACCESSORY_PRICES.bumpers[state.bumpers];

    // Update prices on labels (更新标签上的价格显示)
    if (rimsValEl) rimsValEl.textContent = rimSpec.price === 0 ? 'Included' : `+ RM ${rimSpec.price.toLocaleString()}`;
    if (spoilerValEl) spoilerValEl.textContent = spoilerSpec.price === 0 ? 'Included' : `+ RM ${spoilerSpec.price.toLocaleString()}`;
    if (bumperValEl) bumperValEl.textContent = bumperSpec.price === 0 ? 'Included' : `+ RM ${bumperSpec.price.toLocaleString()}`;

    // Calculate Grand Total (计算总价)
    const total = BASE_PRICE + rimSpec.price + spoilerSpec.price + bumperSpec.price;
    if (totalValEl) totalValEl.textContent = `RM ${total.toLocaleString()}`;
}

/**
 * Core Three.js Setup
 * 核心 Three.js 初始化设置
 */
function initThree() {
    const canvasContainer = document.getElementById('configurator-viewport');
    const canvas = document.getElementById('configurator-canvas');
    const modal = document.getElementById('configurator-modal');
    if (!canvasContainer || !canvas || !modal) return;

    // 1. 创建场景 (Scene)：3D世界的容器，所有物体、光照都在这里
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x222226);
    scene.fog = new THREE.FogExp2(0x222226, 0.04);

    // 2. 摄像机设置 (Camera)：相当于人的眼睛，决定了我们从哪个角度看车
    camera = new THREE.PerspectiveCamera(40, canvasContainer.clientWidth / canvasContainer.clientHeight, 0.1, 100);
    camera.position.set(5.5, 2, 5.5);

    // 3. 渲染器 (Renderer)：引擎的核心，负责把 3D 画面计算并渲染到网页的画布 (Canvas) 上
    renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        antialias: true,
        powerPreference: 'high-performance'
    });
    renderer.setSize(canvasContainer.clientWidth, canvasContainer.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;

    const pmremGenerator = new THREE.PMREMGenerator(renderer);
    scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;

    // 4. 轨道控制器 (OrbitControls)：允许用户用鼠标拖动来旋转、缩放、平移视角
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.maxPolarAngle = Math.PI / 2 - 0.03; // Limit looking underneath the floor (限制视角，防止看到地板下面)
    controls.minDistance = 3.5;
    controls.maxDistance = 8.5;
    controls.target.set(0, 0.4, 0);

    // 5. 光照设置 (Lighting)：打光让车身有立体感和材质反射（包含环境光、主光源、补光等）
    const hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x2d2d35, 1.0);
    scene.add(hemisphereLight);

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.6);
    keyLight.position.set(5, 6, 5);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.width = 2048;
    keyLight.shadow.mapSize.height = 2048;
    keyLight.shadow.bias = -0.0005;
    keyLight.shadow.camera.near = 0.5;
    keyLight.shadow.camera.far = 15;
    keyLight.shadow.camera.left = -3;
    keyLight.shadow.camera.right = 3;
    keyLight.shadow.camera.top = 3;
    keyLight.shadow.camera.bottom = -3;
    scene.add(keyLight);

    const rimLight = new THREE.DirectionalLight(0xffffff, 2.0);
    rimLight.position.set(-5, 4, -5);
    scene.add(rimLight);

    const fillLight = new THREE.DirectionalLight(0xddeeff, 1.2);
    fillLight.position.set(-6, 3, 5);
    scene.add(fillLight);

    // 6. 影棚地板和网格 (Floor & Grid)：给车子一个落脚点，并且用来接收底部阴影
    const floorGeo = new THREE.PlaneGeometry(30, 30);
    const floorMat = new THREE.MeshStandardMaterial({
        color: 0x222226,
        roughness: 0.8,
        metalness: 0.1
    });
    const floorMesh = new THREE.Mesh(floorGeo, floorMat);
    floorMesh.rotation.x = -Math.PI / 2;
    floorMesh.receiveShadow = true;
    scene.add(floorMesh);

    const gridHelper = new THREE.GridHelper(24, 24, 0x4a4a52, 0x33333c);
    gridHelper.position.y = 0.005;
    scene.add(gridHelper);

    // 7. 加载 3D 模型 (GLTF Loader)：通过加载器把服务器上的 .glb 汽车模型文件读取进来
    const modelUrl = modal.dataset.modelUrl || '/models/3d/car.glb';
    const loader = new GLTFLoader();

    loader.load(
        modelUrl,
        // On Loaded Success (加载成功后的回调)
        (gltf) => {
            const car = gltf.scene;
            carModel = car;

            scene.add(car);

            // Pre-traverse to hide non-default accessories so they do not corrupt the ground level bounding box calculation (预遍历以隐藏非默认配件，避免它们影响底部包围盒的计算)
            car.traverse((child) => {
                if (child.isMesh) {
                    const partInfo = getPartInfo(child);
                    if (partInfo) {
                        const { category, key } = partInfo;
                        if (category === 'rims' || category === 'spoilers' || category === 'bumpers') {
                            child.visible = (key === state[category]);
                        } else if (category === 'bumperB1') {
                            child.visible = false;
                        }
                    }
                }
            });

            // Step 1: Center X and Z first (步骤1：先在X和Z轴上居中)
            const box1 = new THREE.Box3().setFromObject(car);
            const center = box1.getCenter(new THREE.Vector3());
            car.position.x = -center.x;
            car.position.z = -center.z;

            // Step 2: Recalculate bounding box after centering, then fix Y (步骤2：居中后重新计算包围盒，然后修正Y轴以贴合地面)
            car.updateMatrixWorld(true);
            const box2 = new THREE.Box3().setFromObject(car);
            car.position.y = -box2.min.y;

            // Setup Animation Mixer (设置动画混合器，用于播放开关门动画)
            if (gltf.animations && gltf.animations.length > 0) {
                mixer = new THREE.AnimationMixer(car);

                const clip1 = gltf.animations.find(clip => clip.name === 'AM-Door动作');
                const clip2 = gltf.animations.find(clip => clip.name === 'AM-Door2动作');

                if (clip1) {
                    const action1 = mixer.clipAction(clip1);
                    action1.setLoop(THREE.LoopOnce, 1);
                    action1.clampWhenFinished = true;
                    doorActions.push(action1);
                }
                if (clip2) {
                    const action2 = mixer.clipAction(clip2);
                    action2.setLoop(THREE.LoopOnce, 1);
                    action2.clampWhenFinished = true;
                    doorActions.push(action2);
                }
            }
            // Initialize materials (初始化车漆、轮毂等材质)
            carBodyMaterial = new THREE.MeshPhysicalMaterial({
                color: COLOR_MAP[state.color].hex,
                metalness: 0.9,
                roughness: 0.12,
                clearcoat: 1.0,
                clearcoatRoughness: 0.05
            });

            carRimMaterial = new THREE.MeshStandardMaterial({
                color: RIM_COLOR_MAP[state.rimColor].hex,
                metalness: 0.8,
                roughness: 0.25
            });

            carBrakeMaterial = new THREE.MeshStandardMaterial({
                color: BRAKE_COLOR_MAP[state.brakeColor].hex,
                metalness: 0.6,
                roughness: 0.4
            });

            glassMaterial = new THREE.MeshPhysicalMaterial({
                color: 0xffffff,
                transparent: true,
                opacity: 1.0,
                transmission: 1.0,
                roughness: 0.05,
                ior: 1.5,
                thickness: 0.05
            });

            // Map and identify car meshes (遍历并分类标记汽车所有的网格模型)
            car.traverse((child) => {
                if (child.isMesh) {
                    child.castShadow = true;
                    child.receiveShadow = true;

                    const name = child.name;
                    const nameLower = name.toLowerCase();

                    // Check if it belongs to one of the custom accessories (检查当前网格是否属于自定义配件)
                    const partInfo = getPartInfo(child);
                    if (partInfo) {
                        const { category, key } = partInfo;

                        if (category === 'rims' || category === 'spoilers' || category === 'bumpers') {
                            if (!carParts[category][key]) {
                                carParts[category][key] = [];
                            }
                            carParts[category][key].push(child);

                            // Hide non-default on load (在加载时隐藏非默认配件)
                            child.visible = (key === state[category]);

                            // Store original material reference (保存原始材质的引用，以便重置时使用)
                            child.userData.originalMaterial = child.material;

                            // Apply Rim material to rims category if not default (如果不是默认颜色，则应用自定义轮毂材质)
                            if (category === 'rims') {
                                if (state.rimColor !== 'default') {
                                    const meshName = (child.name || '').toLowerCase();
                                    const isTire = meshName.includes('tire') || meshName.includes('rubber') || meshName.includes('disk');
                                    if (!isTire) {
                                        child.material = carRimMaterial;
                                    }
                                }
                            }
                        } else if (category === 'bumperB1') {
                            child.visible = false; // Hide loose misplaced bumper mesh (隐藏多余错位的保险杠网格)
                        }
                    }

                    // Apply Brake material to brake parts (给刹车部件应用刹车卡钳材质)
                    const isBrake = name.startsWith('AM-Brake') || name.split('.')[0] === 'AM-Brake';
                    if (isBrake) {
                        child.material = carBrakeMaterial;
                    }

                    // Check if this is one of the explicitly excluded fake windows (检查是否是那些被显式排除的假车窗网格)
                    let isExcludedWindow = false;
                    let tempObj = child;
                    while (tempObj && tempObj.parent) {
                        const tempName = (tempObj.name || '').toLowerCase();
                        if (tempName.match(/window[._\s]*00[12]/)) {
                            isExcludedWindow = true;
                            break;
                        }
                        tempObj = tempObj.parent;
                    }

                    // Apply Glass material if it matches glass/window name and is NOT excluded (如果是车窗/玻璃且未被排除，则应用玻璃材质)
                    let isGlass = false;
                    if (!isExcludedWindow) {
                        let currentObj = child;
                        while (currentObj && currentObj.parent) {
                            const curName = (currentObj.name || '').toLowerCase();
                            if (curName.includes('glass') || curName.includes('window') || curName.includes('windshield') || curName.includes('windscreen')) {
                                isGlass = true;
                                break;
                            }
                            currentObj = currentObj.parent;
                        }
                    }

                    if (isGlass) {
                        carParts.glass.push(child);
                        child.material = glassMaterial;
                    } else if (!isExcludedWindow) {
                        // Apply Body paint color strictly to matching targets (and nested body meshes) AND active front bumpers (严格为目标部件、嵌套的车身网格以及当前激活的前保险杠应用车漆颜色)
                        if (isMeshBodyPaint(child, partInfo)) {
                            carParts.body.push(child);
                            child.material = carBodyMaterial;
                        }
                    }
                }
            });

            console.log('Mapped Car Parts:', carParts);

            // Hide Loading Overlay (隐藏加载动画遮罩)
            setTimeout(() => {
                const progressContainer = document.getElementById('configurator-loader');
                if (progressContainer) {
                    progressContainer.style.opacity = '0';
                    setTimeout(() => {
                        progressContainer.style.display = 'none';
                    }, 500);
                }
            }, 300);

            isInitialized = true;
            animate();
        },

        // On Download Progress (加载进度回调)
        (xhr) => {
            if (xhr.lengthComputable) {
                const percent = Math.round((xhr.loaded / xhr.total) * 100);
                const progressBar = document.getElementById('loader-progress-bar');
                const progressPercentage = document.getElementById('loader-percentage');
                if (progressBar) progressBar.style.width = `${percent}%`;
                if (progressPercentage) progressPercentage.textContent = `${percent}%`;
            }
        },

        // On Loading Error (加载失败回调)
        (error) => {
            console.error('Error loading car.glb:', error);
            const progressPercentage = document.getElementById('loader-percentage');
            if (progressPercentage) {
                progressPercentage.textContent = 'Failed to load model';
                progressPercentage.style.color = '#ef4444';
            }
        }
    );

    // Setup Window Resize hooks (设置窗口缩放监听钩子)
    window.addEventListener('resize', onWindowResize);
}

/**
 * Orbit controls and rendering loop
 * 轨道控制器与渲染循环（每一帧的更新）
 */
function animate() {
    animationFrameId = requestAnimationFrame(animate);

    const delta = clock.getDelta();
    if (mixer) {
        mixer.update(delta);
    }

    if (cameraAnimation.active) {
        const elapsed = performance.now() - cameraAnimation.startTime;
        const progress = Math.min(elapsed / cameraAnimation.duration, 1);
        const t = easeInOutCubic(progress);

        camera.position.lerpVectors(cameraAnimation.startPos, cameraAnimation.endPos, t);

        const currentTarget = new THREE.Vector3();
        currentTarget.lerpVectors(cameraAnimation.startTarget, cameraAnimation.endTarget, t);
        camera.lookAt(currentTarget);

        if (progress >= 1) {
            cameraAnimation.active = false;
            if (cameraAnimation.onComplete) {
                cameraAnimation.onComplete();
            }
        }
    } else {
        if (controls && controls.enabled) {
            controls.update();
        }
    }

    if (renderer && scene && camera) {
        renderer.render(scene, camera);
    }
}

/**
 * Handle screen size changes for canvas responsive scaling
 * 响应式处理：当屏幕或画布尺寸改变时更新摄像机比例和渲染器尺寸
 */
function onWindowResize() {
    const canvasContainer = document.getElementById('configurator-viewport');
    if (!canvasContainer || !camera || !renderer) return;

    camera.aspect = canvasContainer.clientWidth / canvasContainer.clientHeight;
    camera.updateProjectionMatrix();

    renderer.setSize(canvasContainer.clientWidth, canvasContainer.clientHeight);
}

/**
 * Reset OrbitControls back to default viewing angle
 * 重置轨道控制器到默认外部视角
 */
function resetCamera() {
    if (camera && controls) {
        if (state.viewMode === 'interior') {
            exitInteriorView();
        } else {
            controls.reset();
            camera.position.set(5.5, 2, 5.5);
            controls.target.set(0, 0.4, 0);
            controls.update();
        }
    }
}

/**
 * Toggle the car doors open/closed by running the mixer animations
 * 开关车门控制：播放GLTF模型中自带的开门/关门动画
 */
function toggleDoors(open, onComplete) {
    if (doorActions.length === 0) {
        if (onComplete) onComplete();
        return;
    }

    state.doorsOpen = open;
    const timeScale = open ? 1 : -1;
    let maxDuration = 0;

    doorActions.forEach(action => {
        action.paused = false;
        action.timeScale = timeScale;

        const clipDuration = action.getClip().duration;
        maxDuration = Math.max(maxDuration, clipDuration);

        if (open) {
            if (action.time === clipDuration) {
                action.time = 0;
            }
        } else {
            if (action.time === 0) {
                action.time = clipDuration;
            }
        }
        action.play();
    });

    // Update doors toggle button UI state (更新开关车门按钮的UI状态)
    const doorBtn = document.getElementById('toggle-doors-btn');
    if (doorBtn) {
        const textSpan = doorBtn.querySelector('span');
        if (textSpan) textSpan.textContent = open ? 'Close Doors' : 'Open Doors';
        if (open) {
            doorBtn.classList.add('active');
        } else {
            doorBtn.classList.remove('active');
        }
    }

    if (onComplete) {
        setTimeout(onComplete, maxDuration * 1000);
    }
}

/**
 * Handle screen fade transitions using CSS overlay
 * 屏幕黑屏淡入淡出：用于在视角切换时做一个过渡效果
 */
function fadeScreen(fade, callback) {
    const overlay = document.getElementById('configurator-fade-overlay');
    if (!overlay) {
        if (callback) callback();
        return;
    }

    if (fade) {
        overlay.classList.add('active');
    } else {
        overlay.classList.remove('active');
    }

    // CSS fade transition is 400ms, wait 450ms to ensure completion (CSS过渡动画为400毫秒，等待450毫秒确保执行完毕)
    setTimeout(() => {
        if (callback) callback();
    }, 450);
}

/**
 * Get interior and door camera/target coordinates relative to the car's current position
 * 获取相对于汽车当前位置的内饰、车门摄像机和目标焦点坐标
 */
function getInteriorCoords() {
    const carPos = (carModel && carModel.position) ? carModel.position.clone() : new THREE.Vector3();

    // Driver's eye/camera position (seated inside LHD driver seat) (主驾视角摄像机位置：坐在驾驶位)
    // Steering wheel is at local: x = -0.508, y = 0.745, z = 0.40 (方向盘相对坐标)
    // Driver seat cushion is at local: x = 0.15, y = 0.45, z = 0.40 (驾驶座垫相对坐标)
    // Driver eye level: x = 0.15, y = 0.95, z = 0.40 (驾驶员视线高度坐标)
    const driverPos = new THREE.Vector3(
        carPos.x + 0.15,
        carPos.y + 0.95,
        carPos.z + 0.40
    );

    // Target inside looking forward: x = -0.60, y = 0.85, z = 0.40 (主驾向前方看的焦点坐标)
    const driverTarget = new THREE.Vector3(
        carPos.x - 0.60,
        carPos.y + 0.85,
        carPos.z + 0.40
    );

    // Center eye level (between seats) (副驾视线高度：位于座位之间)
    const centerPos = new THREE.Vector3(
        carPos.x + 0.15,
        carPos.y + 0.95,
        carPos.z - 0.45
    );

    // Target from center looking forward (从副驾向前方看的焦点坐标)
    const centerTarget = new THREE.Vector3(
        carPos.x - 0.60,
        carPos.y + 0.85,
        carPos.z - 0.45
    );

    // Door exterior check/pan position (outside open driver-side door at z = 1.60) (车门外部检查视角：在打开的主驾门外侧)
    const doorPos = new THREE.Vector3(
        carPos.x - 0.40,
        carPos.y + 1.10,
        carPos.z + 1.60
    );

    // Looking at steering wheel/dashboard area (看向方向盘和仪表盘区域)
    const doorTarget = new THREE.Vector3(
        carPos.x - 0.50,
        carPos.y + 0.85,
        carPos.z + 0.40
    );

    return {
        driverPos,
        driverTarget,
        centerPos,
        centerTarget,
        doorPos,
        doorTarget
    };
}

/**
 * Helper to get active interior camera position vector based on current state
 * 辅助函数：根据当前状态获取激活的内饰摄像机位置向量
 */
function getActiveInteriorPos() {
    const coords = getInteriorCoords();
    if (state.interiorPosMode === 'center') {
        return coords.centerPos;
    }
    return coords.driverPos;
}

/**
 * Helper to get active interior camera target vector based on current state
 * 辅助函数：根据当前状态获取激活的内饰摄像机焦点向量
 */
function getActiveInteriorTarget() {
    const coords = getInteriorCoords();
    if (state.interiorPosMode === 'center') {
        return coords.centerTarget;
    }
    return coords.driverTarget;
}

/**
 * Smoothly transition view between Driver position and Center position in the cabin
 * 切换内饰座位视角：在主驾位置和副驾位置之间平滑过渡切换
 */
function toggleInteriorPos() {
    if (state.transitioning || state.viewMode !== 'interior') return;
    state.transitioning = true;

    // Fade screen to black (使屏幕渐变到黑屏)
    fadeScreen(true, () => {
        // Toggle state (切换当前状态：主驾/副驾)
        state.interiorPosMode = (state.interiorPosMode === 'driver') ? 'center' : 'driver';

        const coords = getInteriorCoords();
        const newPos = (state.interiorPosMode === 'center') ? coords.centerPos : coords.driverPos;
        const newTarget = (state.interiorPosMode === 'center') ? coords.centerTarget : coords.driverTarget;

        // Reset controls target (pivot point is the eye) (重置控制器焦点：以眼睛为轴心点)
        controls.target.copy(newPos);

        // Position the camera slightly behind the pivot so it looks forward (把摄像机放在焦点稍微靠后的位置，使其面向前方)
        const direction = new THREE.Vector3().subVectors(newTarget, newPos).normalize();
        camera.position.copy(newPos).sub(direction.multiplyScalar(0.01));

        controls.update();

        // Update button label and active state (更新按钮的文本标签和高亮状态)
        const interiorPosBtn = document.getElementById('toggle-interior-pos-btn');
        if (interiorPosBtn) {
            const textSpan = interiorPosBtn.querySelector('span');
            if (textSpan) {
                textSpan.textContent = (state.interiorPosMode === 'center') ? 'Driver View' : 'Passenger View';
            }
            if (state.interiorPosMode === 'center') {
                interiorPosBtn.classList.add('active');
            } else {
                interiorPosBtn.classList.remove('active');
            }
        }

        // Fade screen back in (使屏幕黑屏渐渐褪去，恢复亮屏)
        fadeScreen(false, () => {
            state.transitioning = false;
        });
    });
}

/**
 * Animate the camera smoothly towards the open door area
 * 镜头移动动画：将镜头平滑移动到车门附近，引导用户观察开门动作
 */
function animateCameraToDoorSide(callback) {
    if (!camera || !controls) {
        if (callback) callback();
        return;
    }

    controls.enabled = false; // Disable controls during active tween interpolation (在补间动画插值执行期间，禁用用户控制)
    cameraAnimation.active = true;
    cameraAnimation.startTime = performance.now();
    cameraAnimation.duration = 1200;
    cameraAnimation.startPos.copy(camera.position);

    const coords = getInteriorCoords();

    cameraAnimation.endPos.copy(coords.doorPos);
    cameraAnimation.startTarget.copy(controls.target);
    cameraAnimation.endTarget.copy(coords.doorTarget);

    cameraAnimation.onComplete = callback;
}

/**
 * Handle transition to interior cabin view
 * 处理过渡到车内内饰视角的逻辑
 */
function enterInteriorView() {
    if (state.transitioning) return;
    state.transitioning = true;

    // 1. Play door open animation first (第一步：先播放打开车门的动画)
    toggleDoors(true, () => {
        // 2. Camera moves slowly towards the car door area (第二步：摄像机缓慢移动向车门区域)
        animateCameraToDoorSide(() => {
            // 3. Screen fades to black (第三步：屏幕渐变到黑屏)
            fadeScreen(true, () => {
                // Close doors silently while screen is black (趁屏幕黑屏时，偷偷把车门关上以避免内饰穿模)
                toggleDoors(false, () => {
                    // 4. Camera jumps inside the car (第四步：摄像机瞬间跳进车内)
                    state.viewMode = 'interior';
                    const activePos = getActiveInteriorPos();
                    const activeTarget = getActiveInteriorTarget();

                    controls.enabled = true;
                    controls.enableZoom = false;
                    controls.enablePan = false;
                    controls.minDistance = 0.01;
                    controls.maxDistance = 0.01;
                    controls.maxPolarAngle = Math.PI - 0.1; // Allow looking down at floor/console (放宽轨道控制器的垂直角度限制，允许往下看)

                    // Pivot is the eye position (旋转中心/枢轴点 就是眼睛的位置)
                    controls.target.copy(activePos);

                    // Camera is slightly offset backwards so it looks forward towards the target (相机微微向后偏移，这样就能面向前方)
                    const direction = new THREE.Vector3().subVectors(activeTarget, activePos).normalize();
                    camera.position.copy(activePos).sub(direction.multiplyScalar(0.01));

                    controls.update();

                    // Show the interior position toggle button (显示主驾/副驾视角切换按钮)
                    const interiorPosBtn = document.getElementById('toggle-interior-pos-btn');
                    if (interiorPosBtn) {
                        interiorPosBtn.style.display = 'inline-flex';
                        const textSpan = interiorPosBtn.querySelector('span');
                        if (textSpan) {
                            textSpan.textContent = (state.interiorPosMode === 'center') ? 'Driver View' : 'Passenger View';
                        }
                        if (state.interiorPosMode === 'center') {
                            interiorPosBtn.classList.add('active');
                        } else {
                            interiorPosBtn.classList.remove('active');
                        }
                    }

                    // Update View Toggle button UI (更新内/外视角切换按钮的UI样式)
                    const viewBtn = document.getElementById('toggle-view-btn');
                    if (viewBtn) {
                        const textSpan = viewBtn.querySelector('span');
                        if (textSpan) textSpan.textContent = 'Exterior View';
                        viewBtn.classList.add('active');
                    }

                    // 5. Screen fades back in (第五步：屏幕黑屏渐渐褪去)
                    fadeScreen(false, () => {
                        state.transitioning = false;
                    });
                });
            });
        });
    });
}

/**
 * Handle transition back to exterior showroom view
 * 处理退回到外部展厅视角的逻辑
 */
function exitInteriorView() {
    if (state.transitioning) return;
    state.transitioning = true;

    // 1. Screen fades to black
    fadeScreen(true, () => {
        // Hide the interior position toggle button (隐藏主驾/副驾视角切换按钮)
        const interiorPosBtn = document.getElementById('toggle-interior-pos-btn');
        if (interiorPosBtn) {
            interiorPosBtn.style.display = 'none';
        }

        // 2. Camera jumps back to exterior position (x=5, y=2, z=8) (第二步：摄像机瞬间跳回外部预设位置)
        camera.position.set(5, 2, 8);
        controls.target.set(0, 0.4, 0);

        controls.enabled = true;
        controls.enableZoom = true;
        controls.enablePan = true;
        controls.minDistance = 3.5;
        controls.maxDistance = 8.5;
        controls.maxPolarAngle = Math.PI / 2 - 0.03; // Limit looking underneath the floor (限制视角，防止看到地板下面)
        controls.update();

        state.viewMode = 'exterior';

        // Update View Toggle button UI (更新内/外视角切换按钮的UI样式)
        const viewBtn = document.getElementById('toggle-view-btn');
        if (viewBtn) {
            const textSpan = viewBtn.querySelector('span');
            if (textSpan) textSpan.textContent = 'Interior View';
            viewBtn.classList.remove('active');
        }

        // 3. Screen fades in (第三步：屏幕黑屏渐渐褪去)
        fadeScreen(false, () => {
            // 4. Play door close animation (第四步：播放关门的动画)
            toggleDoors(false, () => {
                state.transitioning = false;
            });
        });
    });
}

/**
 * Toggle between interior and exterior views
 * 在外部视图和内部视图之间切换
 */
function toggleView() {
    if (state.viewMode === 'exterior') {
        enterInteriorView();
    } else {
        exitInteriorView();
    }
}

/**
 * Update rim materials based on selected rim color state.
 * If 'default', restores original GLB materials; otherwise applies colored carRimMaterial.
 * 更新轮毂材质：如果是 'default'，则恢复原始的 GLTF 材质；否则应用已着色的自定义金属材质。
 */
function updateRimMaterials() {
    const isDefault = state.rimColor === 'default';
    for (const rimKey in carParts.rims) {
        carParts.rims[rimKey].forEach(mesh => {
            if (isDefault) {
                if (mesh.userData.originalMaterial) {
                    mesh.material = mesh.userData.originalMaterial;
                }
            } else {
                const name = (mesh.name || '').toLowerCase();
                const isTire = name.includes('tire') || name.includes('rubber') || name.includes('disk');
                if (!isTire && carRimMaterial && RIM_COLOR_MAP[state.rimColor]) {
                    carRimMaterial.color.setHex(RIM_COLOR_MAP[state.rimColor].hex || 0xffffff);
                    mesh.material = carRimMaterial;
                } else if (isTire && mesh.userData.originalMaterial) {
                    mesh.material = mesh.userData.originalMaterial;
                }
            }
        });
    }
}

/**
 * Grab chosen options and compile a WhatsApp link
 * 获取选中的配件配置，并拼接成 WhatsApp 发送询价的链接
 */
function sendWhatsAppEnquiry() {
    const enquireBtn = document.getElementById('enquire-config-btn');
    if (!enquireBtn) return;

    const colorSpec = COLOR_MAP[state.color].name;
    const rimColorSpec = RIM_COLOR_MAP[state.rimColor].name;
    const brakeColorSpec = BRAKE_COLOR_MAP[state.brakeColor].name;
    const rimSpec = ACCESSORY_PRICES.rims[state.rims].name;
    const rimPrice = ACCESSORY_PRICES.rims[state.rims].price === 0 ? 'Included' : `+RM ${ACCESSORY_PRICES.rims[state.rims].price.toLocaleString()}`;
    const spoilerSpec = ACCESSORY_PRICES.spoilers[state.spoilers].name;
    const spoilerPrice = ACCESSORY_PRICES.spoilers[state.spoilers].price === 0 ? 'Included' : `+RM ${ACCESSORY_PRICES.spoilers[state.spoilers].price.toLocaleString()}`;
    const bumperSpec = ACCESSORY_PRICES.bumpers[state.bumpers].name;
    const bumperPrice = ACCESSORY_PRICES.bumpers[state.bumpers].price === 0 ? 'Included' : `+RM ${ACCESSORY_PRICES.bumpers[state.bumpers].price.toLocaleString()}`;
    const windowTintSpec = state.windowTint + '%';

    const total = BASE_PRICE + ACCESSORY_PRICES.rims[state.rims].price + ACCESSORY_PRICES.spoilers[state.spoilers].price + ACCESSORY_PRICES.bumpers[state.bumpers].price;

    const storePhoneRaw = enquireBtn.dataset.phone || '60123456789';

    const textMessage = `Hello Win Win Car Studio! 🚗\n\nI have customized a car on your website using the 3D Car Configurator. Here is my custom configuration details:\n\n` +
        `• Paint Color: ${colorSpec}\n` +
        `• Rim Style: ${rimSpec} (Color: ${rimColorSpec}) (${rimPrice})\n` +
        `• Brake Caliper Color: ${brakeColorSpec}\n` +
        `• Spoiler Style: ${spoilerSpec} (${spoilerPrice})\n` +
        `• Front Bumper: ${bumperSpec} (${bumperPrice})\n` +
        `• Window Tint: ${windowTintSpec}\n\n` +
        `----------------------------\n` +
        `• Base Price: RM ${BASE_PRICE.toLocaleString()}\n` +
        `• Estimated Total: RM ${total.toLocaleString()}\n\n` +
        `Please check availability and guide me on ordering these accessories! Thank you.`;

    const whatsAppUrl = `https://wa.me/${storePhoneRaw}?text=${encodeURIComponent(textMessage)}`;
    window.open(whatsAppUrl, '_blank');
}
