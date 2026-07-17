import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { DRACOLoader } from 'three/examples/jsm/loaders/DRACOLoader.js';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';

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
        bumperF2: { name: 'Widebody Spec Bumper', price: 2200 },
    },
    dashcams: {
        dashcam0: { name: 'None (Default)', price: 0 },
        dashcam1: { name: 'Mohawk', price: 0 },
        dashcam2: { name: '70mai', price: 0 },
        dashcam3: { name: 'DDPAI', price: 0 },
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

// Application State
const state = {
    color: 'white',
    rims: 'rim7',
    spoilers: 'wing4',
    bumpers: 'bumperF3',
    dashcams: 'dashcam0',
    rimColor: 'default',
    brakeColor: 'red',
    windowTint: '100',
    doorsOpen: false,
    viewMode: 'exterior', // 'exterior' | 'interior'
    interiorPosMode: 'driver', // 'driver' | 'center'
    transitioning: false,
};

// Three.js Globals
let scene, camera, renderer, controls, carModel;
let isInitialized = false;
let animationFrameId = null;
let renderUntil = 0;
let mixer;
const doorActions = [];
const clock = new THREE.Timer();

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

// References to Car Meshes
const carParts = {
    rims: {},     // rim1 -> Array of meshes
    spoilers: {}, // wing1 -> Array of meshes
    bumpers: {},  // bumperF1 -> Array of meshes
    dashcams: {}, // dashcam1 -> Array of meshes
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
 * Helper: use regex to tell whether a 3D part is a swappable accessory (rim, spoiler, bumper).
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

        // Dashcams Regex match (1 to 3)
        const dashcamMatch = name.match(/dashcam[_\s-]*0?([1-3])/i);
        if (dashcamMatch) {
            return { category: 'dashcams', key: `dashcam${dashcamMatch[1]}` };
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
 * Helper: recursively decide whether a part should take the body paint colour (excludes glass, tyres, interior).
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
        'AM-Dash.003', 'AM-Digi', 'AM-Circle', 'AM-Brake', 'AM-Seat', 'AM-Seats', 'AM-Interior', 'AM-Steering',
        'AM-DoorL', 'AM-DoorR'
    ];

    // Whichever front bumper is selected gets colored
    if (partInfo && partInfo.category === 'bumpers') {
        return true;
    }

    if (partInfo && (partInfo.category === 'rims' || partInfo.category === 'spoilers' || partInfo.category === 'dashcams')) {
        return false;
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
            name.startsWith('AM-Back-Small-Light') ||
            name.startsWith('AM-Seat') ||
            name.startsWith('AM-Interior') ||
            name.startsWith('AM-DoorL') ||
            name.startsWith('AM-DoorR')) {
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
 * Set up delegated events so clicks keep working after Livewire re-renders the configurator panel.
 */
function wireConfiguratorEvents() {
    // Open Configurator — the hero trigger, plus any promo CTA
    // tagged with .js-open-configurator (e.g. the Products-page banner).
    document.addEventListener('click', (e) => {
        if (e.target.closest('#open-configurator-btn, .js-open-configurator')) {
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

    // Toggle Doors
    document.addEventListener('click', (e) => {
        if (e.target.closest('#toggle-doors-btn')) {
            if (state.transitioning) return;
            toggleDoors(!state.doorsOpen);
        }
    });

    // Toggle View Mode (Interior/Exterior)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#toggle-view-btn')) {
            toggleView();
        }
    });

    // Toggle Interior Position (Driver/Center)
    document.addEventListener('click', (e) => {
        if (e.target.closest('#toggle-interior-pos-btn')) {
            toggleInteriorPos();
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

            updateRimMaterials();
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
        }
    });

    // Accessory Option Card Selection
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.option-card');
        if (card && card.dataset.category) {
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
        }
    });

    // Window Tint Selection
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.option-card[data-tint]');
        if (card) {
            document.querySelectorAll('.option-card[data-tint]').forEach(s => s.classList.remove('active'));
            card.classList.add('active');

            const tintKey = card.dataset.tint;
            state.windowTint = tintKey;

            if (glassMaterial && TINT_MAP[tintKey]) {
                const config = TINT_MAP[tintKey];
                if (glassMaterial.isMeshPhysicalMaterial) {
                    glassMaterial.color.setHex(config.color);
                    glassMaterial.transmission = config.transmission;
                    glassMaterial.opacity = config.opacity;
                } else {
                    const pct = parseInt(tintKey, 10);
                    glassMaterial.color.setHex(0x000000); // pure black
                    
                    // opacity calculation: 100% (Fully Transparent) -> 0.0 opacity, 5% (Darkest) -> 0.95 opacity
                    // We set a minimum of 0.1 so the glass doesn't completely disappear at 100%
                    let desiredOpacity = 1.0 - (pct / 100.0);
                    if (desiredOpacity < 0.15) desiredOpacity = 0.15; // default clear glass has some reflection
                    
                    glassMaterial.opacity = desiredOpacity;
                }
            }

            requestRender();
        }
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('#configurator-modal')) {
            requestRender();
        }
    });

    // Free GPU memory if the user leaves the page (wire:navigate) or closes the
    // tab without closing the configurator first. Uses 'livewire:navigating'
    // (fires BEFORE the page is swapped) so it tears down on the way out and never
    // races with the loader's auto-open, which runs on 'livewire:navigated'.
    const teardownOnLeave = () => { if (isInitialized) disposeConfigurator(); };
    document.addEventListener('livewire:navigating', teardownOnLeave);
    window.addEventListener('pagehide', teardownOnLeave);
}

// This module is dynamic-imported on demand, usually long after
// DOMContentLoaded has fired — wire immediately in that case.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wireConfiguratorEvents);
} else {
    wireConfiguratorEvents();
}

// Let the loader shim open the modal right after the module finishes loading.
export { openConfigurator };

// ── Modal accessibility state ─────────────────────────────────────────────
// The element that opened the modal (focus returns to it on close) and the
// keydown handler that provides Esc-to-close plus a Tab focus trap. Without
// these, keyboard and screen-reader users could Tab straight out into the
// page behind the dialog and had no way to dismiss it.
let configuratorOpener = null;
let configuratorKeydownHandler = null;

function trapConfiguratorKeydown(e) {
    const modal = document.getElementById('configurator-modal');
    if (!modal || !modal.classList.contains('active')) return;

    if (e.key === 'Escape') {
        e.preventDefault();
        closeConfigurator();
        return;
    }

    if (e.key !== 'Tab') return;

    const focusables = [...modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    )].filter((el) => el.offsetParent !== null || el === document.activeElement);
    if (focusables.length === 0) return;

    const first = focusables[0];
    const last = focusables[focusables.length - 1];

    if (e.shiftKey && (document.activeElement === first || !modal.contains(document.activeElement))) {
        e.preventDefault();
        last.focus();
    } else if (!e.shiftKey && (document.activeElement === last || !modal.contains(document.activeElement))) {
        e.preventDefault();
        first.focus();
    }
}

/**
 * Open the configurator popup modal and load/run Three.js
 * Open the 3D viewer modal and boot Three.js.
 */
function openConfigurator() {
    const modal = document.getElementById('configurator-modal');
    if (!modal) return;
    modal.classList.add('active');
    document.body.classList.add('overflow-hidden');

    // Dialog keyboard contract: initial focus on the close button, Esc closes,
    // Tab cycles inside the modal, and focus returns to the opener on close.
    configuratorOpener = document.activeElement instanceof HTMLElement ? document.activeElement : null;
    configuratorKeydownHandler = trapConfiguratorKeydown;
    document.addEventListener('keydown', configuratorKeydownHandler, true);
    document.getElementById('close-configurator-btn')?.focus();

    // Mark the configurator as open in the URL so a refresh re-opens it here
    // instead of dropping the user back to the plain products page.
    if (window.location.hash !== '#car-configurator') {
        history.replaceState(null, '', window.location.pathname + window.location.search + '#car-configurator');
    }

    // Friendly message for old devices / disabled WebGL instead of a black screen.
    if (!isInitialized && !isWebGLAvailable()) {
        const loader = document.getElementById('configurator-loader');
        if (loader) {
            loader.innerHTML = '<div style="text-align:center;padding:2rem;color:#E8E0D8;max-width:22rem;margin:0 auto;">'
                + '<div style="font-size:2.5rem;margin-bottom:0.75rem;">🚗</div>'
                + '<p style="font-weight:700;margin-bottom:0.5rem;">3D viewer not supported on this device</p>'
                + '<p style="font-size:0.85rem;opacity:0.7;">Your browser or device does not support WebGL. '
                + 'Visit our showroom or WhatsApp us to explore the accessories instead.</p></div>';
        }
        return;
    }

    if (!isInitialized) {
        initThree();
    } else {
        requestRender(700);
    }
}

function isConfiguratorOpen() {
    return !!document.getElementById('configurator-modal')?.classList.contains('active');
}

function requestRender(duration = 350) {
    if (!renderer || !scene || !camera || !isConfiguratorOpen()) return;

    renderUntil = Math.max(renderUntil, performance.now() + duration);

    if (!animationFrameId) {
        clock.update();
        animationFrameId = requestAnimationFrame(animate);
    }
}

/**
 * WebGL availability check
 */
function isWebGLAvailable() {
    try {
        const canvas = document.createElement('canvas');
        return !!(window.WebGLRenderingContext
            && (canvas.getContext('webgl2') || canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
    } catch {
        return false;
    }
}

/**
 * Close the configurator popup modal
 * Close the 3D viewer modal: hide it and stop the render loop to save resources.
 */
function closeConfigurator() {
    const modal = document.getElementById('configurator-modal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.classList.remove('overflow-hidden');

    // Tear down the dialog keyboard contract and hand focus back to whatever
    // opened the modal.
    if (configuratorKeydownHandler) {
        document.removeEventListener('keydown', configuratorKeydownHandler, true);
        configuratorKeydownHandler = null;
    }
    if (configuratorOpener && document.contains(configuratorOpener)) {
        configuratorOpener.focus();
    }
    configuratorOpener = null;

    // Drop the #car-configurator marker so a later refresh stays on the page.
    if (window.location.hash === '#car-configurator') {
        history.replaceState(null, '', window.location.pathname + window.location.search);
    }

    // Release ALL GPU memory immediately. Three.js never frees WebGL resources on
    // its own, so without this each open stacked another ~1GB that never cleared.
    // Reopening re-runs initThree() (the GLB is HTTP-cached, the loader animation
    // covers the brief re-upload), so only one renderer ever lives at a time.
    disposeConfigurator();
}

/**
 * Tear down the Three.js scene and free every GPU resource. Idempotent — safe to
 * call when nothing is initialized. The garbage collector cannot reclaim WebGL
 * memory, so geometries, materials, textures, render targets and the renderer
 * itself must each be disposed by hand.
 */
function disposeConfigurator() {
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
        animationFrameId = null;
    }
    renderUntil = 0;

    window.removeEventListener('resize', onWindowResize);

    if (mixer) {
        mixer.stopAllAction();
        try { mixer.uncacheRoot(mixer.getRoot()); } catch { /* root already gone */ }
        mixer = null;
    }
    doorActions.length = 0;

    if (controls) {
        controls.dispose();   // also detaches OrbitControls' DOM + 'change' listeners
        controls = null;
    }

    const disposeMaterial = (material) => {
        if (!material) return;
        // Dispose any texture maps referenced by the material (map, normalMap, …).
        for (const value of Object.values(material)) {
            if (value && value.isTexture) value.dispose();
        }
        material.dispose();
    };

    if (scene) {
        scene.traverse((obj) => {
            if (!obj.isMesh) return;
            obj.geometry?.dispose();
            if (Array.isArray(obj.material)) obj.material.forEach(disposeMaterial);
            else disposeMaterial(obj.material);
        });
        scene.environment?.dispose();   // PMREM render-target texture
        scene.clear();
        scene = null;
    }

    // The shared body/rim/brake/glass materials may be detached from any mesh
    // (hidden parts), so dispose them explicitly too.
    disposeMaterial(carBodyMaterial);
    disposeMaterial(carRimMaterial);
    disposeMaterial(carBrakeMaterial);
    disposeMaterial(glassMaterial);
    carBodyMaterial = carRimMaterial = carBrakeMaterial = glassMaterial = undefined;

    if (renderer) {
        renderer.dispose();          // frees programs + render targets (incl. shadow maps)
        renderer.forceContextLoss(); // releases the WebGL context / GPU buffers
        // A force-lost canvas can never produce a working context again, so swap
        // in a fresh <canvas> (cloneNode keeps the same id/attributes) for the
        // next initThree() to bind a clean context to — otherwise reopen is blank.
        const oldCanvas = renderer.domElement;
        if (oldCanvas?.parentNode) {
            oldCanvas.parentNode.replaceChild(oldCanvas.cloneNode(false), oldCanvas);
        }
        renderer = null;
    }

    // Reset cached mesh references so a fresh load never appends to stale arrays.
    carParts.rims = {};
    carParts.spoilers = {};
    carParts.bumpers = {};
    carParts.dashcams = {};
    carParts.body = [];
    carParts.glass = [];

    camera = null;
    carModel = null;
    isInitialized = false;
}

/**
 * Show/Hide meshes of a category in the 3D model
 * Show/hide the 3D parts of a category, swapping the old variant for the newly selected one.
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

    // If swapping rims, ensure the newly visible rim gets the correct material styling
    if (category === 'rims') {
        updateRimMaterials();
    }

    if (renderer) renderer.shadowMap.needsUpdate = true;
    requestRender(700);
}

/**
 * Core Three.js Setup
 * Core Three.js initialisation.
 */
function initThree() {
    // If a previous session somehow survived (e.g. re-init after a Livewire DOM
    // morph), tear it down first so we never stack two WebGL contexts.
    if (renderer) disposeConfigurator();

    isInitialized = true; // Set immediately to prevent race conditions from double-clicks or auto-open

    const canvasContainer = document.getElementById('configurator-viewport');
    const canvas = document.getElementById('configurator-canvas');
    const modal = document.getElementById('configurator-modal');
    if (!canvasContainer || !canvas || !modal) {
        isInitialized = false;
        return;
    }

    // 1. Scene: the container for the 3D world — all objects and lights live here.
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x222226);
    scene.fog = new THREE.FogExp2(0x222226, 0.04);

    // 2. Camera: the viewer's eye — decides the angle the car is seen from.
    camera = new THREE.PerspectiveCamera(40, canvasContainer.clientWidth / canvasContainer.clientHeight, 0.1, 100);
    camera.position.set(5.5, 2, 5.5);

    // 3. Renderer: draws the computed 3D scene onto the page canvas.
    renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        antialias: true,
        powerPreference: 'high-performance'
    });
    renderer.setSize(canvasContainer.clientWidth, canvasContainer.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.autoUpdate = false;
    renderer.shadowMap.needsUpdate = true;
    renderer.shadowMap.type = THREE.PCFShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;

    const pmremGenerator = new THREE.PMREMGenerator(renderer);
    scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
    pmremGenerator.dispose();

    // 4. OrbitControls: let the user rotate, zoom and pan the view by dragging.
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.maxPolarAngle = Math.PI / 2 - 0.03; // Limit looking underneath the floor
    controls.minDistance = 3.5;
    controls.maxDistance = 8.5;
    controls.target.set(0, 0.4, 0);
    controls.addEventListener('change', () => requestRender());

    // 5. Lighting: ambient + key + fill lights give the body depth and material reflections.
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

    // 6. Floor & grid: a ground plane for the car that also receives its shadow.
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

    // 7. GLTF loader: read the .glb car model from the server.
    // The model is Draco-compressed, so DRACOLoader is needed to decode the geometry.
    const modelUrl = modal.dataset.modelUrl || '/models/3d/car-draco.glb';
    const dracoLoader = new DRACOLoader();
    dracoLoader.setDecoderPath('/draco/');
    const loader = new GLTFLoader();
    loader.setDRACOLoader(dracoLoader);

    const setProgress = (percent) => {
        const bar = document.getElementById('loader-progress-bar');
        const pct = document.getElementById('loader-percentage');
        if (bar) bar.style.width = `${percent}%`;
        if (pct) pct.textContent = `${Math.round(percent)}%`;
    };

    // On Loaded Success
    const onModelLoaded = (gltf) => {
        const car = gltf.scene;
        carModel = car;

        scene.add(car);

        // Pre-traverse to hide non-default accessories so they do not corrupt the ground level bounding box calculation
        car.traverse((child) => {
            if (child.isMesh) {
                const partInfo = getPartInfo(child);
                if (partInfo) {
                    const { category, key } = partInfo;
                    if (category === 'rims' || category === 'spoilers' || category === 'bumpers' || category === 'dashcams') {
                        child.visible = (key === state[category]);
                    } else if (category === 'bumperB1') {
                        child.visible = false;
                    }
                }
            }
        });

        // Step 1: Center X and Z first
        const box1 = new THREE.Box3().setFromObject(car);
        const center = box1.getCenter(new THREE.Vector3());
        car.position.x = -center.x;
        car.position.z = -center.z;

        // Step 2: Recalculate bounding box after centering, then fix Y
        car.updateMatrixWorld(true);
        const box2 = new THREE.Box3().setFromObject(car);
        car.position.y = -box2.min.y;

        // Setup Animation Mixer
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

        glassMaterial = new THREE.MeshStandardMaterial({
            color: 0x000000,
            metalness: 0.1,
            roughness: 0.05,
            transparent: true,
            opacity: 0.15, // 100% Transparent default
        });

        const dashcamMaterial = new THREE.MeshStandardMaterial({
            color: 0x303030,
            metalness: 0.2,
            roughness: 0.8,
            side: THREE.DoubleSide
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

                    if (category === 'rims' || category === 'spoilers' || category === 'bumpers' || category === 'dashcams') {
                        if (!carParts[category][key]) {
                            carParts[category][key] = [];
                        }
                        carParts[category][key].push(child);

                        // Hide non-default on load
                        child.visible = (key === state[category]);

                        // Store original material reference
                        child.userData.originalMaterial = child.material;

                        if (category === 'dashcams') {
                            child.material = dashcamMaterial;
                        }

                        // Apply Rim material to rims category if not default
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
                        child.visible = false; // Hide loose misplaced bumper mesh
                    }
                }

                // Fix: the model was exported with duplicate overlapping doors — hide the redundant .001 door.
                if (name.includes('door') || name.includes('Door')) {
                    if (name.includes('.001')) {
                        child.visible = false;
                    }
                }

                // Apply Brake material to brake parts
                const isBrake = name.startsWith('AM-Brake') || name.split('.')[0] === 'AM-Brake';
                if (isBrake) {
                    child.material = carBrakeMaterial;
                }

                // Check if this is one of the explicitly excluded fake windows
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

                // Apply Glass material if it matches glass/window name and is NOT excluded
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
                    // Apply Body paint color strictly to matching targets (and nested body meshes) AND active front bumpers
                    if (isMeshBodyPaint(child, partInfo)) {
                        carParts.body.push(child);
                        child.material = carBodyMaterial;
                    }
                }
            }
        });


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

        if (renderer) renderer.shadowMap.needsUpdate = true;
        requestRender(1200);
    };

    const onModelError = (error) => {
        console.error('Error loading 3D model:', error);
        isInitialized = false; // Reset so user can try again
        const pct = document.getElementById('loader-percentage');
        const sub = document.querySelector('.loader-subtitle');
        if (pct) { pct.textContent = 'Failed to load — check your connection'; pct.style.color = '#ef4444'; }
        if (sub) sub.textContent = '';
    };

    // Stream the .glb so the bar reflects real downloaded bytes even when the
    // server omits Content-Length (gzip/chunked). Download fills 0–90%; the
    // last 10% covers Draco decode + scene setup so the bar never sits frozen
    // at 100% while a slow device is still decoding 3M vertices.
    const KNOWN_SIZE = 25_700_000; // ~ car-draco.glb, used only as a fallback total

    streamGlb(modelUrl, KNOWN_SIZE, (frac) => setProgress(frac * 90))
        .then((buffer) => {
            setProgress(92);
            const sub = document.querySelector('.loader-subtitle');
            if (sub) sub.textContent = 'Preparing your car…';
            // Give the browser a frame to paint 92% before the (blocking) decode.
            requestAnimationFrame(() => {
                loader.parse(buffer, '', (gltf) => {
                    setProgress(100);
                    onModelLoaded(gltf);
                    buffer = null;
                }, onModelError);
            });
        })
        .catch(onModelError);

    // Setup Window Resize hooks
    window.addEventListener('resize', onWindowResize);
}

/**
 * Fetch a .glb as a stream and report real download progress (0–1).
 * Falls back to a known total when the server doesn't send Content-Length.
 */
async function streamGlb(url, knownSize, onProgress) {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const lenHeader = response.headers.get('content-length');
    const total = lenHeader ? parseInt(lenHeader, 10) : knownSize;

    const reader = response.body.getReader();
    const chunks = [];
    let received = 0;
    for (; ;) {
        const { done, value } = await reader.read();
        if (done) break;
        chunks.push(value);
        received += value.length;
        // Clamp below 1 so we never show 100% before parse() finishes.
        onProgress(Math.min(received / total, 0.99));
    }

    const out = new Uint8Array(received);
    let offset = 0;
    for (const chunk of chunks) { out.set(chunk, offset); offset += chunk.length; }
    return out.buffer;
}

/**
 * Orbit controls and rendering loop
 * OrbitControls update + the per-frame render loop.
 */
function animate() {
    const now = performance.now();
    clock.update();
    const delta = clock.getDelta();

    let keepRendering = now < renderUntil;

    if (mixer) {
        const doorAnimationActive = doorActions.some(action => action.isRunning());
        if (doorAnimationActive) {
            mixer.update(delta);
            if (renderer) renderer.shadowMap.needsUpdate = true;
            keepRendering = true;
        }
    }

    if (cameraAnimation.active) {
        const elapsed = performance.now() - cameraAnimation.startTime;
        const progress = Math.min(elapsed / cameraAnimation.duration, 1);
        const t = easeInOutCubic(progress);

        camera.position.lerpVectors(cameraAnimation.startPos, cameraAnimation.endPos, t);

        const currentTarget = new THREE.Vector3();
        currentTarget.lerpVectors(cameraAnimation.startTarget, cameraAnimation.endTarget, t);
        camera.lookAt(currentTarget);

        keepRendering = true;

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

    if (keepRendering) {
        animationFrameId = requestAnimationFrame(animate);
    } else {
        animationFrameId = null;
    }
}

/**
 * Handle screen size changes for canvas responsive scaling
 * Responsive: update camera aspect and renderer size when the canvas resizes.
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
 * Reset OrbitControls to the default exterior view.
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
 * Door toggle: play the open/close door animations bundled in the GLTF model.
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

    // Update doors toggle button UI state
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
 * Fade the screen to/from black as a transition when switching views.
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

    // CSS fade transition is 400ms, wait 450ms to ensure completion
    setTimeout(() => {
        if (callback) callback();
    }, 450);
}

/**
 * Get interior and door camera/target coordinates relative to the car's current position
 * Compute interior/door camera and target positions relative to the car's current position.
 */
function getInteriorCoords() {
    const carPos = (carModel && carModel.position) ? carModel.position.clone() : new THREE.Vector3();

    // Driver's eye/camera position (seated inside LHD driver seat)
    // Steering wheel is at local: x = -0.508, y = 0.745, z = 0.40
    // Driver seat cushion is at local: x = 0.15, y = 0.45, z = 0.40
    // Driver eye level: x = 0.15, y = 0.95, z = 0.40
    const driverPos = new THREE.Vector3(
        carPos.x + 0.15,
        carPos.y + 0.95,
        carPos.z + 0.40
    );

    // Target inside looking forward: x = -0.60, y = 0.85, z = 0.40
    const driverTarget = new THREE.Vector3(
        carPos.x - 0.60,
        carPos.y + 0.85,
        carPos.z + 0.40
    );

    // Center eye level (between seats)
    const centerPos = new THREE.Vector3(
        carPos.x + 0.15,
        carPos.y + 0.95,
        carPos.z - 0.45
    );

    // Target from center looking forward
    const centerTarget = new THREE.Vector3(
        carPos.x - 0.60,
        carPos.y + 0.85,
        carPos.z - 0.45
    );

    // Door exterior check/pan position (outside open driver-side door at z = 1.60)
    const doorPos = new THREE.Vector3(
        carPos.x - 0.40,
        carPos.y + 1.10,
        carPos.z + 1.60
    );

    // Looking at steering wheel/dashboard area
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
 * Helper: the active interior camera position vector for the current state.
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
 * Helper: the active interior camera target vector for the current state.
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
 * Switch the interior seat view, smoothly transitioning between driver and centre positions.
 */
function toggleInteriorPos() {
    if (state.transitioning || state.viewMode !== 'interior') return;
    state.transitioning = true;

    // Fade screen to black
    fadeScreen(true, () => {
        // Toggle state
        state.interiorPosMode = (state.interiorPosMode === 'driver') ? 'center' : 'driver';

        const coords = getInteriorCoords();
        const newPos = (state.interiorPosMode === 'center') ? coords.centerPos : coords.driverPos;
        const newTarget = (state.interiorPosMode === 'center') ? coords.centerTarget : coords.driverTarget;

        // Reset controls target (pivot point is the eye)
        controls.target.copy(newPos);

        // Position the camera slightly behind the pivot so it looks forward
        const direction = new THREE.Vector3().subVectors(newTarget, newPos).normalize();
        camera.position.copy(newPos).sub(direction.multiplyScalar(0.01));

        controls.update();

        // Update button label and active state
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

        // Fade screen back in
        fadeScreen(false, () => {
            state.transitioning = false;
        });
    });
}

/**
 * Animate the camera smoothly towards the open door area
 * Camera move animation: glide toward the door to draw attention to it opening.
 */
function animateCameraToDoorSide(callback) {
    if (!camera || !controls) {
        if (callback) callback();
        return;
    }

    controls.enabled = false; // Disable controls during active tween interpolation
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
 * Handle the transition into the interior view.
 */
function enterInteriorView() {
    if (state.transitioning) return;
    state.transitioning = true;

    // 1. Play door open animation first
    toggleDoors(true, () => {
        // 2. Camera moves slowly towards the car door area
        animateCameraToDoorSide(() => {
            // 3. Screen fades to black
            fadeScreen(true, () => {
                // Close doors silently while screen is black
                toggleDoors(false, () => {
                    // 4. Camera jumps inside the car
                    state.viewMode = 'interior';
                    const activePos = getActiveInteriorPos();
                    const activeTarget = getActiveInteriorTarget();

                    controls.enabled = true;
                    controls.enableZoom = false;
                    controls.enablePan = false;
                    controls.minDistance = 0.01;
                    controls.maxDistance = 0.01;
                    controls.maxPolarAngle = Math.PI - 0.1; // Allow looking down at floor/console

                    // Pivot is the eye position
                    controls.target.copy(activePos);

                    // Camera is slightly offset backwards so it looks forward towards the target
                    const direction = new THREE.Vector3().subVectors(activeTarget, activePos).normalize();
                    camera.position.copy(activePos).sub(direction.multiplyScalar(0.01));

                    controls.update();

                    // Show the interior position toggle button
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

                    // Update View Toggle button UI
                    const viewBtn = document.getElementById('toggle-view-btn');
                    if (viewBtn) {
                        const textSpan = viewBtn.querySelector('span');
                        if (textSpan) textSpan.textContent = 'Exterior View';
                        viewBtn.classList.add('active');
                    }

                    // 5. Screen fades back in
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
 * Handle the transition back to the exterior showroom view.
 */
function exitInteriorView() {
    if (state.transitioning) return;
    state.transitioning = true;

    // 1. Screen fades to black
    fadeScreen(true, () => {
        // Hide the interior position toggle button
        const interiorPosBtn = document.getElementById('toggle-interior-pos-btn');
        if (interiorPosBtn) {
            interiorPosBtn.style.display = 'none';
        }

        // 2. Camera jumps back to exterior position (x=5, y=2, z=8)
        camera.position.set(5, 2, 8);
        controls.target.set(0, 0.4, 0);

        controls.enabled = true;
        controls.enableZoom = true;
        controls.enablePan = true;
        controls.minDistance = 3.5;
        controls.maxDistance = 8.5;
        controls.maxPolarAngle = Math.PI / 2 - 0.03; // Limit looking underneath the floor
        controls.update();

        state.viewMode = 'exterior';

        // Update View Toggle button UI
        const viewBtn = document.getElementById('toggle-view-btn');
        if (viewBtn) {
            const textSpan = viewBtn.querySelector('span');
            if (textSpan) textSpan.textContent = 'Interior View';
            viewBtn.classList.remove('active');
        }

        // 3. Screen fades in
        fadeScreen(false, () => {
            // 4. Play door close animation
            toggleDoors(false, () => {
                state.transitioning = false;
            });
        });
    });
}

/**
 * Toggle between interior and exterior views
 * Toggle between the exterior and interior views.
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
 * Update rim material: 'default' restores the original GLTF material, otherwise apply the tinted custom metal material.
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
 * Collect the selected configuration and build the WhatsApp enquiry link.
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
    const dashcamSpec = ACCESSORY_PRICES.dashcams[state.dashcams];

    const total = BASE_PRICE + ACCESSORY_PRICES.rims[state.rims].price + ACCESSORY_PRICES.spoilers[state.spoilers].price + ACCESSORY_PRICES.bumpers[state.bumpers].price + dashcamSpec.price;

    const storePhoneRaw = enquireBtn.dataset.phone || '60123456789';

    const textMessage = `Hello Win Win Car Studio! 🚗\n\nI have customized a car on your website using the 3D Car Configurator. Here is my custom configuration details:\n\n` +
        `• Paint Color: ${colorSpec}\n` +
        `• Rim Style: ${rimSpec} (Color: ${rimColorSpec}) (${rimPrice})\n` +
        `• Brake Caliper Color: ${brakeColorSpec}\n` +
        `• Spoiler Style: ${spoilerSpec} (${spoilerPrice})\n` +
        `• Front Bumper: ${bumperSpec} (${bumperPrice})\n` +
        `• Window Tint: ${windowTintSpec}\n` +
        `• Dash Camera: ${dashcamSpec.name}\n\n` +
        `----------------------------\n` +
        `• Base Price: RM ${BASE_PRICE.toLocaleString()}\n` +
        `• Estimated Total: RM ${total.toLocaleString()}\n\n` +
        `Please check availability and guide me on ordering these accessories! Thank you.`;

    const whatsAppUrl = `https://wa.me/${storePhoneRaw}?text=${encodeURIComponent(textMessage)}`;
    window.open(whatsAppUrl, '_blank');
}
