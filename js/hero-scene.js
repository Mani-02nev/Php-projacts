/**
 * Univaluat Global E-Commerce Hero
 * ================================
 * A cinematic, Sellix-inspired particle globe representing 
 * the global reach of AI-powered commerce.
 * 
 * FEATURES:
 * - Particle-dot surface globe
 * - Smooth, continuous rotation
 * - Purple glow atmosphere
 * - Soft star field background
 */

class GlobalCommerceHero {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.width = this.container.clientWidth;
        this.height = this.container.clientHeight;

        this.time = 0;
        this.init();
        this.createGlobe();
        this.createAtmosphere();
        this.createStarField();
        this.addEventListeners();
        this.animate();

        window.heroScene = this;
    }

    init() {
        this.scene = new THREE.Scene();

        this.camera = new THREE.PerspectiveCamera(45, this.width / this.height, 0.1, 1000);
        this.camera.position.z = 15;

        this.renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: "high-performance"
        });
        this.renderer.setSize(this.width, this.height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.container.appendChild(this.renderer.domElement);
    }

    createGlobe() {
        // Particle Globe
        const radius = 5;
        const count = 8000;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(count * 3);
        const colors = new Float32Array(count * 3);

        const color = new THREE.Color(0xA78BFA); // Purple base

        for (let i = 0; i < count; i++) {
            const phi = Math.acos(-1 + (2 * i) / count);
            const theta = Math.sqrt(count * Math.PI) * phi;

            const x = radius * Math.cos(theta) * Math.sin(phi);
            const y = radius * Math.sin(theta) * Math.sin(phi);
            const z = radius * Math.cos(phi);

            positions[i * 3] = x;
            positions[i * 3 + 1] = y;
            positions[i * 3 + 2] = z;

            colors[i * 3] = color.r;
            colors[i * 3 + 1] = color.g;
            colors[i * 3 + 2] = color.b;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        const material = new THREE.PointsMaterial({
            size: 0.03,
            vertexColors: true,
            transparent: true,
            opacity: 0.8,
            blending: THREE.AdditiveBlending
        });

        this.globe = new THREE.Points(geometry, material);
        this.scene.add(this.globe);
    }

    createAtmosphere() {
        // Outer Glow Ring
        const geometry = new THREE.TorusGeometry(5.2, 0.02, 16, 100);
        const material = new THREE.MeshBasicMaterial({
            color: 0x7C3AED,
            transparent: true,
            opacity: 0.3,
            blending: THREE.AdditiveBlending
        });
        this.ring = new THREE.Mesh(geometry, material);
        this.scene.add(this.ring);

        // Core Glow
        const spriteMaterial = new THREE.SpriteMaterial({
            map: this.generateGlowTexture(),
            color: 0x7C3AED,
            transparent: true,
            opacity: 0.4,
            blending: THREE.AdditiveBlending
        });
        const sprite = new THREE.Sprite(spriteMaterial);
        sprite.scale.set(15, 15, 1);
        this.scene.add(sprite);
    }

    generateGlowTexture() {
        const canvas = document.createElement('canvas');
        canvas.width = 64;
        canvas.height = 64;
        const context = canvas.getContext('2d');
        const gradient = context.createRadialGradient(32, 32, 0, 32, 32, 32);
        gradient.addColorStop(0, 'rgba(255,255,255,1)');
        gradient.addColorStop(0.2, 'rgba(255,255,255,0.8)');
        gradient.addColorStop(0.5, 'rgba(124,58,237,0.2)');
        gradient.addColorStop(1, 'rgba(0,0,0,0)');
        context.fillStyle = gradient;
        context.fillRect(0, 0, 64, 64);
        return new THREE.CanvasTexture(canvas);
    }

    createStarField() {
        const count = 1500;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(count * 3);

        for (let i = 0; i < count; i++) {
            positions[i * 3] = (Math.random() - 0.5) * 100;
            positions[i * 3 + 1] = (Math.random() - 0.5) * 100;
            positions[i * 3 + 2] = -Math.random() * 50;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        const material = new THREE.PointsMaterial({ color: 0xffffff, size: 0.05, transparent: true, opacity: 0.5 });
        this.stars = new THREE.Points(geometry, material);
        this.scene.add(this.stars);
    }

    triggerShopHover(isHovering) {
        this.isHoveringCTA = isHovering;
    }

    addEventListeners() {
        window.addEventListener('resize', () => {
            this.width = this.container.clientWidth;
            this.height = this.container.clientHeight;
            this.camera.aspect = this.width / this.height;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(this.width, this.height);
        });
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        this.time += 0.005;
        this.globe.rotation.y += 0.002;
        this.globe.rotation.x += 0.0005;

        // Subtle pulse for ring
        const pulse = 1 + Math.sin(this.time) * 0.05;
        this.ring.scale.set(pulse, pulse, 1);
        this.ring.lookAt(this.camera.position);

        this.renderer.render(this.scene, this.camera);
    }
}

// Global initialization
window.addEventListener('load', () => {
    new GlobalCommerceHero('hero-canvas-container');
});
