import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

// Configuration Data
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

// Application State
const state = {
    color: 'white',
    rims: 'rim7',
    spoilers: 'wing4',
    bumpers: 'bumperF3',
    rimColor: 'silver',
    brakeColor: 'red',
};

// Three.js Globals
let scene, camera, renderer, controls;
let isInitialized = false;
let animationFrameId = null;

// References to Car Meshes
const carParts = {
    rims: {},     // rim1 -> Array of meshes
    spoilers: {}, // wing1 -> Array of meshes
    bumpers: {},  // bumperF1 -> Array of meshes
    body: [],     // Array of meshes for car_body
    glass: []     // Array of meshes for windows
};

// Materials
let carBodyMaterial;
let carRimMaterial;
let carBrakeMaterial;
let glassMaterial;

/**
 * Helper to check if a mesh is part of a swappable accessory based on name/ancestors using Regex
 */
function getPartInfo(child) {
    let current = child;
    while (current && current.parent) {
        const name = current.name || '';
        
        // Rims Regex match (1 to 7)
        const rimMatch = name.match(/rim[_\s-]*0?([1-7])/i);
        if (rimMatch) {
            return { category: 'rims', key: `rim${rimMatch[1]}` };
        }

        // Wings/Spoilers Regex match (1 to 4)
        const wingMatch = name.match(/(wing|spoiler)[_\s-]*0?([1-4])/i);
        if (wingMatch) {
            return { category: 'spoilers', key: `wing${wingMatch[2]}` };
        }

        // Front Bumpers Regex match (1 to 3)
        const bumperFMatch = name.match(/bumper[_\s-]*f[_\s-]*0?([1-3])/i);
        if (bumperFMatch) {
            return { category: 'bumpers', key: `bumperF${bumperFMatch[1]}` };
        }

        // Rear Bumper Regex match (bumperB1)
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
 */
function isMeshBodyPaint(child, partInfo) {
    const bodyPaintNames = [
        'AM-Body', 'AM-Bonet', 'AM-B-BUMPER', 'AM-Hood', 'AM-Lower-Cover', 
        'AM-Side-Mirrors', 'AM-Side-Mirrors.001', 'AM-Handle', 'AM-Cover', 'AM-Plane.003'
    ];

    const bodyPaintExclusions = [
        'AM-Window', 'AM-Glass-Supp', 'AM-Tire', 'AM-ForWheels', 'AM-Headlight', 
        'AM-Back-Light', 'AM-Back-Small-Light', 'AM-Blinks', 'AM-Disk part 1', 
        'AM-Gears', 'AM-Dashboard', 'AM-Dash', 'AM-Dash.001', 'AM-Dash.002', 
        'AM-Dash.003', 'AM-Digi', 'AM-Circle', 'AM-Brake'
    ];

    // Whichever front bumper is selected gets colored
    if (partInfo && partInfo.category === 'bumpers') {
        return true;
    }

    let current = child;
    let isTarget = false;
    let isExcluded = false;

    // Climb the parent tree recursively to see if child or any ancestor matches target list
    while (current && current.parent) {
        const name = current.name || '';
        const baseName = name.split('.')[0];

        // 1. Check exclusions
        if (bodyPaintExclusions.includes(name) || bodyPaintExclusions.includes(baseName)) {
            isExcluded = true;
        }

        // Direct prefix checks to exclude indices variations (e.g. AM-Dash.001)
        if (name.startsWith('AM-Dash') || 
            name.startsWith('AM-Window') || 
            name.startsWith('AM-Glass') || 
            name.startsWith('AM-Tire') || 
            name.startsWith('AM-Headlight') ||
            name.startsWith('AM-Back-Light') ||
            name.startsWith('AM-Back-Small-Light')) {
            isExcluded = true;
        }

        // 2. Check paint targets
        if (bodyPaintNames.includes(name) || bodyPaintNames.includes(baseName)) {
            isTarget = true;
        }

        current = current.parent;
    }

    return isTarget && !isExcluded;
}

/**
 * Initialize Event Delegation on document to ensure persistence against Livewire re-renders
 */
document.addEventListener('DOMContentLoaded', () => {
    // Open Configurator
    document.addEventListener('click', (e) => {
        if (e.target.closest('#open-configurator-btn')) {
            openConfigurator();
        }
    });

    // Close Configurator
    document.addEventListener('click', (e) => {
        if (e.target.closest('#close-configurator-btn')) {
            closeConfigurator();
        }
    });

    // Reset Camera
    document.addEventListener('click', (e) => {
        if (e.target.closest('#camera-reset-btn')) {
            resetCamera();
        }
    });

    // WhatsApp Enquiry Export
    document.addEventListener('click', (e) => {
        if (e.target.closest('#enquire-config-btn')) {
            sendWhatsAppEnquiry();
        }
    });

    // Tabs Switcher
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

    // Color Swatch Selection (Body Color)
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('.color-swatch[data-color]');
        if (swatch) {
            document.querySelectorAll('.color-swatch[data-color]').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            const colorKey = swatch.dataset.color;
            state.color = colorKey;
            
            // Update 3D body material color
            if (carBodyMaterial && COLOR_MAP[colorKey]) {
                carBodyMaterial.color.setHex(COLOR_MAP[colorKey].hex);
            }

            // Update UI label
            const colorValEl = document.getElementById('summary-color-name');
            if (colorValEl) colorValEl.textContent = COLOR_MAP[colorKey].name;
        }
    });

    // Rim Color Swatch Selection
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('[data-rim-color]');
        if (swatch) {
            document.querySelectorAll('[data-rim-color]').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            const colorKey = swatch.dataset.rimColor;
            state.rimColor = colorKey;
            
            if (carRimMaterial && RIM_COLOR_MAP[colorKey]) {
                carRimMaterial.color.setHex(RIM_COLOR_MAP[colorKey].hex);
            }

            const label = document.getElementById('summary-rim-color');
            if (label) label.textContent = RIM_COLOR_MAP[colorKey].name;
        }
    });

    // Brake Color Swatch Selection
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

    // Accessory Option Card Selection
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.option-card');
        if (card) {
            const category = card.dataset.category;
            const itemKey = card.dataset.item;

            // Visual toggle in category grid
            const categoryCards = document.querySelectorAll(`.option-card[data-category="${category}"]`);
            categoryCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            // Hide old variant, show new variant in 3D
            const oldItemKey = state[category];
            state[category] = itemKey;
            
            togglePartVisibility(category, oldItemKey, itemKey);

            // Update Price Summary
            updateSummaryUI();
        }
    });
});

/**
 * Open the configurator popup modal and load/run Three.js
 */
function openConfigurator() {
    const modal = document.getElementById('configurator-modal');
    if (!modal) return;
    modal.classList.add('active');
    document.body.classList.add('overflow-hidden');

    if (!isInitialized) {
        initThree();
    } else {
        // Resume rendering
        animate();
        onWindowResize();
    }
}

/**
 * Close the configurator popup modal
 */
function closeConfigurator() {
    const modal = document.getElementById('configurator-modal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.classList.remove('overflow-hidden');

    // Pause animation render loop
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
        animationFrameId = null;
    }
}

/**
 * Show/Hide meshes of a category in the 3D model
 */
function togglePartVisibility(category, oldKey, newKey) {
    // Hide old meshes
    if (carParts[category] && carParts[category][oldKey]) {
        carParts[category][oldKey].forEach(mesh => {
            mesh.visible = false;
        });
    }

    // Show new meshes
    if (carParts[category] && carParts[category][newKey]) {
        carParts[category][newKey].forEach(mesh => {
            mesh.visible = true;
        });
    }
}

/**
 * Refresh prices and selected specs in the UI summary panel
 */
function updateSummaryUI() {
    const rimsValEl = document.getElementById('summary-rims-price');
    const spoilerValEl = document.getElementById('summary-spoiler-price');
    const bumperValEl = document.getElementById('summary-bumper-price');
    const totalValEl = document.getElementById('summary-total-price');

    const rimSpec = ACCESSORY_PRICES.rims[state.rims];
    const spoilerSpec = ACCESSORY_PRICES.spoilers[state.spoilers];
    const bumperSpec = ACCESSORY_PRICES.bumpers[state.bumpers];

    // Update prices on labels
    if (rimsValEl) rimsValEl.textContent = rimSpec.price === 0 ? 'Included' : `+ RM ${rimSpec.price.toLocaleString()}`;
    if (spoilerValEl) spoilerValEl.textContent = spoilerSpec.price === 0 ? 'Included' : `+ RM ${spoilerSpec.price.toLocaleString()}`;
    if (bumperValEl) bumperValEl.textContent = bumperSpec.price === 0 ? 'Included' : `+ RM ${bumperSpec.price.toLocaleString()}`;

    // Calculate Grand Total
    const total = BASE_PRICE + rimSpec.price + spoilerSpec.price + bumperSpec.price;
    if (totalValEl) totalValEl.textContent = `RM ${total.toLocaleString()}`;
}

/**
 * Core Three.js Setup
 */
function initThree() {
    const canvasContainer = document.getElementById('configurator-viewport');
    const canvas = document.getElementById('configurator-canvas');
    const modal = document.getElementById('configurator-modal');
    if (!canvasContainer || !canvas || !modal) return;

    // 1. Create Scene
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x222226);
    scene.fog = new THREE.FogExp2(0x222226, 0.04);

    // 2. Camera Setup
    camera = new THREE.PerspectiveCamera(40, canvasContainer.clientWidth / canvasContainer.clientHeight, 0.1, 100);
    camera.position.set(5.5, 2, 5.5);

    // 3. Renderer Setup
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

    // 4. Orbit Controls
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.maxPolarAngle = Math.PI / 2 - 0.03; // Limit looking underneath the floor
    controls.minDistance = 3.5;
    controls.maxDistance = 8.5;
    controls.target.set(0, 0.4, 0);

    // 5. Lighting Setup
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

    // 6. Ground Studio floor grid
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

    // 7. Load GLB Model
    const modelUrl = modal.dataset.modelUrl || '/models/3d/car.glb';
    const loader = new GLTFLoader();

    loader.load(
        modelUrl,
        // On Loaded Success
        (gltf) => {
            const car = gltf.scene;

            // Center car object on ground plane
            const box = new THREE.Box3().setFromObject(car);
            const center = box.getCenter(new THREE.Vector3());
            
            car.position.x = -center.x;
            car.position.z = -center.z;
            car.position.y = -box.min.y;

            scene.add(car);

            // Initialize materials
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
                opacity: 0.45,
                transmission: 1.0,
                roughness: 0.05,
                ior: 1.5,
                thickness: 0.05
            });

            // Map and identify car meshes
            car.traverse((child) => {
                if (child.isMesh) {
                    child.castShadow = true;
                    child.receiveShadow = true;
                    
                    const name = child.name;
                    const nameLower = name.toLowerCase();

                    // Check if it belongs to one of the custom accessories
                    const partInfo = getPartInfo(child);
                    if (partInfo) {
                        const { category, key } = partInfo;
                        
                        if (category === 'rims' || category === 'spoilers' || category === 'bumpers') {
                            if (!carParts[category][key]) {
                                carParts[category][key] = [];
                            }
                            carParts[category][key].push(child);
                            
                            // Hide non-default on load
                            child.visible = (key === state[category]);

                            // Apply Rim material to rims category
                            if (category === 'rims') {
                                child.material = carRimMaterial;
                            }
                        } else if (category === 'bumperB1') {
                            child.visible = true; // Always visible
                        }
                    }

                    // Apply Brake material to brake parts
                    const isBrake = name.startsWith('AM-Brake') || name.split('.')[0] === 'AM-Brake';
                    if (isBrake) {
                        child.material = carBrakeMaterial;
                    }

                    // Apply Glass material if it matches glass/window name (checked first to prevent painting glass)
                    const isGlass = nameLower.includes('glass') || nameLower.includes('window') || nameLower.includes('windshield') || nameLower.includes('windscreen');
                    
                    if (isGlass) {
                        carParts.glass.push(child);
                        child.material = glassMaterial;
                    } else {
                        // Apply Body paint color strictly to matching targets (and nested body meshes) AND active front bumpers
                        if (isMeshBodyPaint(child, partInfo)) {
                            carParts.body.push(child);
                            child.material = carBodyMaterial;
                        }
                    }
                }
            });

            console.log('Mapped Car Parts:', carParts);

            // Hide Loading Overlay
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

        // On Download Progress
        (xhr) => {
            if (xhr.lengthComputable) {
                const percent = Math.round((xhr.loaded / xhr.total) * 100);
                const progressBar = document.getElementById('loader-progress-bar');
                const progressPercentage = document.getElementById('loader-percentage');
                if (progressBar) progressBar.style.width = `${percent}%`;
                if (progressPercentage) progressPercentage.textContent = `${percent}%`;
            }
        },

        // On Loading Error
        (error) => {
            console.error('Error loading car.glb:', error);
            const progressPercentage = document.getElementById('loader-percentage');
            if (progressPercentage) {
                progressPercentage.textContent = 'Failed to load model';
                progressPercentage.style.color = '#ef4444';
            }
        }
    );

    // Setup Window Resize hooks
    window.addEventListener('resize', onWindowResize);
}

/**
 * Orbit controls and rendering loop
 */
function animate() {
    animationFrameId = requestAnimationFrame(animate);

    if (controls) {
        controls.update();
    }

    if (renderer && scene && camera) {
        renderer.render(scene, camera);
    }
}

/**
 * Handle screen size changes for canvas responsive scaling
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
 */
function resetCamera() {
    if (camera && controls) {
        controls.reset();
        camera.position.set(5.5, 2, 5.5);
        controls.target.set(0, 0.4, 0);
        controls.update();
    }
}

/**
 * Grab chosen options and compile a WhatsApp link
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

    const total = BASE_PRICE + ACCESSORY_PRICES.rims[state.rims].price + ACCESSORY_PRICES.spoilers[state.spoilers].price + ACCESSORY_PRICES.bumpers[state.bumpers].price;

    const storePhoneRaw = enquireBtn.dataset.phone || '60123456789';

    const textMessage = `Hello Win Win Car Studio! 🚗\n\nI have customized a car on your website using the 3D Car Configurator. Here is my custom configuration details:\n\n` +
        `• Paint Color: ${colorSpec}\n` +
        `• Rim Style: ${rimSpec} (Color: ${rimColorSpec}) (${rimPrice})\n` +
        `• Brake Caliper Color: ${brakeColorSpec}\n` +
        `• Spoiler Style: ${spoilerSpec} (${spoilerPrice})\n` +
        `• Front Bumper: ${bumperSpec} (${bumperPrice})\n\n` +
        `----------------------------\n` +
        `• Base Price: RM ${BASE_PRICE.toLocaleString()}\n` +
        `• Estimated Total: RM ${total.toLocaleString()}\n\n` +
        `Please check availability and guide me on ordering these accessories! Thank you.`;

    const whatsAppUrl = `https://wa.me/${storePhoneRaw}?text=${encodeURIComponent(textMessage)}`;
    window.open(whatsAppUrl, '_blank');
}
