/**
 * Wishlist Heart Scale & Burst Animation
 * Uses Three.js to render a 3D Heart pop effect on click.
 */

class WishlistAnimation {
    constructor() {
        this.canvas = null;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.activeAnimations = [];
        this.containerId = 'interaction-anim-container';
    }

    init() {
        if (document.getElementById(this.containerId)) return;

        // Container
        const container = document.createElement('div');
        container.id = this.containerId;
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100vw';
        container.style.height = '100vh';
        container.style.pointerEvents = 'none';
        container.style.zIndex = 10000;
        document.body.appendChild(container);

        // Scene
        this.scene = new THREE.Scene();

        // Camera (Orthographic for screen coords)
        this.width = window.innerWidth;
        this.height = window.innerHeight;
        this.camera = new THREE.OrthographicCamera(
            this.width / -2, this.width / 2,
            this.height / 2, this.height / -2,
            1, 1000
        );
        this.camera.position.z = 100;

        // Renderer
        this.renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        this.renderer.setSize(this.width, this.height);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(this.renderer.domElement);

        // Lighting
        const ambient = new THREE.AmbientLight(0xffffff, 0.8);
        this.scene.add(ambient);
        const point = new THREE.PointLight(0xffffff, 0.5);
        point.position.set(50, 50, 100);
        this.scene.add(point);

        // Resize
        window.addEventListener('resize', () => {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
            this.camera.left = this.width / -2;
            this.camera.right = this.width / 2;
            this.camera.top = this.height / 2;
            this.camera.bottom = this.height / -2;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(this.width, this.height);
        });

        this.animate = this.animate.bind(this);
        requestAnimationFrame(this.animate);
    }

    createHeartGeometry() {
        const x = 0, y = 0;
        const heartShape = new THREE.Shape();
        heartShape.moveTo(x + 5, y + 5);
        heartShape.bezierCurveTo(x + 5, y + 5, x + 4, y, x, y);
        heartShape.bezierCurveTo(x - 6, y, x - 6, y + 7, x - 6, y + 7);
        heartShape.bezierCurveTo(x - 6, y + 11, x - 3, y + 15.4, x + 5, y + 19);
        heartShape.bezierCurveTo(x + 12, y + 15.4, x + 16, y + 11, x + 16, y + 7);
        heartShape.bezierCurveTo(x + 16, y + 7, x + 16, y, x + 10, y);
        heartShape.bezierCurveTo(x + 7, y, x + 5, y + 5, x + 5, y + 5);

        const extrudeSettings = {
            depth: 2,
            bevelEnabled: true,
            bevelSegments: 2,
            steps: 2,
            bevelSize: 1,
            bevelThickness: 1
        };

        return new THREE.ExtrudeGeometry(heartShape, extrudeSettings);
    }

    toWorld(x, y) {
        return {
            x: x - this.width / 2,
            y: -(y - this.height / 2)
        };
    }

    play(element, onComplete) {
        this.init();

        const rect = element.getBoundingClientRect();
        const startPos = this.toWorld(
            rect.left + rect.width / 2,
            rect.top + rect.height / 2
        );

        // 1. Core Heart
        const geo = this.createHeartGeometry();
        // Center the geometry
        geo.center();

        const mat = new THREE.MeshPhongMaterial({
            color: 0xFF0055, // Neon Pink
            emissive: 0x550022,
            shininess: 100,
            specular: 0xffffff
        });

        const heart = new THREE.Mesh(geo, mat);
        heart.position.set(startPos.x, startPos.y, 0);
        // Single rotation to orient heart correctly (point down)
        heart.rotation.z = 0; // No rotation needed - heart naturally points up
        heart.scale.set(0.1, 0.1, 0.1);
        this.scene.add(heart);

        // 2. Particles
        const particleCount = 12;
        const particles = [];
        const pGeo = new THREE.CircleGeometry(2, 8);
        const pMat = new THREE.MeshBasicMaterial({ color: 0xFFD700 }); // Gold sparks

        for (let i = 0; i < particleCount; i++) {
            const p = new THREE.Mesh(pGeo, pMat);
            p.position.set(startPos.x, startPos.y, -1);
            // Random direction
            const angle = Math.random() * Math.PI * 2;
            const speed = 2 + Math.random() * 3;
            p.userData = {
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed
            };
            this.scene.add(p);
            particles.push(p);
        }

        this.activeAnimations.push({
            type: 'heart',
            mesh: heart,
            particles: particles,
            startTime: performance.now(),
            duration: 800,
            onComplete: onComplete
        });
    }

    animate(time) {
        requestAnimationFrame(this.animate);

        if (this.activeAnimations.length === 0) return;

        // Cleanup finished
        for (let i = this.activeAnimations.length - 1; i >= 0; i--) {
            const anim = this.activeAnimations[i];
            const elapsed = time - anim.startTime;
            let progress = elapsed / anim.duration;

            if (progress >= 1) {
                // Done
                this.scene.remove(anim.mesh);
                anim.mesh.geometry.dispose();
                anim.mesh.material.dispose();

                anim.particles.forEach(p => {
                    this.scene.remove(p);
                    p.geometry.dispose();
                });

                if (anim.onComplete) anim.onComplete();
                this.activeAnimations.splice(i, 1);
                continue;
            }

            // Animate Heart
            // Pop up scale
            // Elastic Out
            const p = progress;
            const scale = 1.5 * (Math.sin(-13 * (p + 1) * Math.PI / 2) * Math.pow(2, -10 * p) + 1);
            anim.mesh.scale.set(scale, scale, scale);

            // Fade out near end
            if (progress > 0.7) {
                anim.mesh.material.opacity = 1 - ((progress - 0.7) / 0.3);
                anim.mesh.material.transparent = true;
            }

            // Move up slightly
            anim.mesh.position.y += 0.5;

            // Animate Particles
            const explosionProgress = progress; // Linear
            anim.particles.forEach(p => {
                p.position.x += p.userData.vx;
                p.position.y += p.userData.vy;
                p.scale.setScalar(1 - explosionProgress); // Shrink
            });
        }

        this.renderer.render(this.scene, this.camera);
    }
}

// Global Instance
window.wishlistAnim = new WishlistAnimation();

// Auto Hook
window.setupWishlistAnimation = function (selector) {
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest(selector);
        if (btn) {
            e.preventDefault(); // Stop immediate redirect

            // Play Animation
            window.wishlistAnim.play(btn, () => {
                // Resume link
                if (btn.tagName === 'A') {
                    window.location.href = btn.href;
                } else if (btn.onclick) {
                    // Primitive handling for onclick attributes just in case
                    // But usually we just follow href
                }
            });
        }
    }, true); // Capture phase to intervene early
};
