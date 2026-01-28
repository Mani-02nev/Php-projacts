/**
 * Add-to-Cart 3D Flying Animation
 * Uses Three.js to animate a product image flying into the cart icon.
 */

class AddToCartAnimation {
    constructor() {
        this.canvas = null;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.isPlaying = false;

        // Configuration
        this.zIndex = 9999; // Top of everything
    }

    init() {
        if (this.renderer) return; // Already inited

        // Create Container
        this.container = document.createElement('div');
        this.container.id = 'cart-anim-container';
        this.container.style.position = 'fixed';
        this.container.style.top = '0';
        this.container.style.left = '0';
        this.container.style.width = '100%';
        this.container.style.height = '100%';
        this.container.style.pointerEvents = 'none'; // Click-through
        this.container.style.zIndex = this.zIndex;
        document.body.appendChild(this.container);

        // Scene
        this.scene = new THREE.Scene();

        // Camera - Orthographic is easier for UI-to-UI mapping
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
        this.container.appendChild(this.renderer.domElement);

        // Handle Resize
        window.addEventListener('resize', () => {
            if (!this.renderer) return;
            this.width = window.innerWidth;
            this.height = window.innerHeight;
            this.camera.left = this.width / -2;
            this.camera.right = this.width / 2;
            this.camera.top = this.height / 2;
            this.camera.bottom = this.height / -2;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(this.width, this.height);
        });

        // Loop
        this.animate = this.animate.bind(this);
        requestAnimationFrame(this.animate);
    }

    // Convert Screen (Pixels) to Three.js Orthographic Coords
    toWorld(x, y) {
        return {
            x: x - this.width / 2,
            y: -(y - this.height / 2)
        };
    }

    fly(imageElement, cartIconSelector, onComplete) {
        if (!imageElement) return;
        this.init(); // Ensure initialized

        const cartIcon = document.querySelector(cartIconSelector);
        if (!cartIcon) {
            console.warn('Cart icon not found:', cartIconSelector);
            if (onComplete) onComplete();
            return;
        }

        // Get Coordinates
        const startRect = imageElement.getBoundingClientRect();
        const endRect = cartIcon.getBoundingClientRect();

        const startPos = this.toWorld(
            startRect.left + startRect.width / 2,
            startRect.top + startRect.height / 2
        );
        const endPos = this.toWorld(
            endRect.left + endRect.width / 2,
            endRect.top + endRect.height / 2
        );

        // Create Flying Object (Plane with Image Texture)
        const geometry = new THREE.PlaneGeometry(startRect.width, startRect.height);

        // Load Texture
        const texture = new THREE.TextureLoader().load(imageElement.src);
        const material = new THREE.MeshBasicMaterial({
            map: texture,
            transparent: true,
            opacity: 1,
            side: THREE.DoubleSide
        });

        const flyer = new THREE.Mesh(geometry, material);
        flyer.position.set(startPos.x, startPos.y, 0);
        this.scene.add(flyer);

        // Animation Data
        const startTime = performance.now();
        const duration = 800; // ms

        // Add to active animations list
        this.activeAnimations = this.activeAnimations || [];
        this.activeAnimations.push({
            mesh: flyer,
            start: startPos,
            end: endPos,
            startTime: startTime,
            duration: duration,
            onComplete: onComplete,
            targetIcon: cartIcon
        });

        this.isPlaying = true;
    }

    animate(time) {
        requestAnimationFrame(this.animate);

        if (!this.isPlaying || !this.activeAnimations) return;

        this.activeAnimations.forEach((anim, index) => {
            const elapsed = time - anim.startTime;
            let progress = elapsed / anim.duration;

            if (progress > 1) progress = 1;

            // Ease InOut Quad
            const ease = progress < 0.5 ? 2 * progress * progress : -1 + (4 - 2 * progress) * progress;

            // Curved Path (Quadratic Bezier)
            // Control point: mid-x, but higher y (arc up)
            const midX = (anim.start.x + anim.end.x) / 2;
            const midY = Math.max(anim.start.y, anim.end.y) + 200; // Arc Height

            // Bezier Calculation
            const p0 = anim.start;
            const p1 = { x: midX, y: midY };
            const p2 = anim.end;

            // New Pos
            const x = (1 - ease) * (1 - ease) * p0.x + 2 * (1 - ease) * ease * p1.x + ease * ease * p2.x;
            const y = (1 - ease) * (1 - ease) * p0.y + 2 * (1 - ease) * ease * p1.y + ease * ease * p2.y;

            anim.mesh.position.set(x, y, 0);

            // Rotation & Scale
            anim.mesh.rotation.z = ease * Math.PI * 2; // Spin 360
            anim.mesh.scale.set(1 - ease * 0.9, 1 - ease * 0.9, 1); // Shrink to 10%
            anim.mesh.material.opacity = 1 - ease * 0.5; // Fade slightly

            // Completion
            if (progress === 1) {
                // Pulse Cart Icon
                if (anim.targetIcon) {
                    anim.targetIcon.animate([
                        { transform: 'scale(1)' },
                        { transform: 'scale(1.5)' },
                        { transform: 'scale(1)' }
                    ], { duration: 300 });
                }

                // Cleanup
                this.scene.remove(anim.mesh);
                anim.mesh.geometry.dispose();
                anim.mesh.material.dispose();

                // Do callback (e.g., submit form)
                if (anim.onComplete) anim.onComplete();

                // Remove from list
                this.activeAnimations.splice(index, 1);
            }
        });

        if (this.scene) {
            this.renderer.render(this.scene, this.camera);
        }
    }
}

// Global Instance
window.addToCartAnim = new AddToCartAnimation();

// Auto-Hook Helper
window.setupAddToCartAnimation = function (buttonSelector, imageSelector, cartSelector) {
    const buttons = document.querySelectorAll(buttonSelector);

    buttons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            // Find related image
            // Assuming button is inside a container that also holds the image
            // This logic might need custom tuning per page structure
            let parent = btn.closest('.card') || btn.closest('.product-detail-container') || document.body;
            let img = parent.querySelector(imageSelector);

            if (!img) {
                // Fallback: try finding main product image if we are on detail page
                img = document.querySelector('.product-image-container img');
            }

            if (img) {
                window.addToCartAnim.fly(img, cartSelector, () => {
                    // Resume action
                    if (btn.type === 'submit') {
                        // Create a hidden input to signal this button was clicked (since JS submit bypasses it)
                        const form = btn.closest('form');
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = btn.name;
                        hiddenInput.value = btn.value;
                        form.appendChild(hiddenInput);
                        form.submit();
                    } else if (btn.href) {
                        window.location.href = btn.href;
                    }
                });
            } else {
                // If no image found, just proceed
                if (btn.type === 'submit') {
                    btn.closest('form').submit();
                } else if (btn.href) {
                    window.location.href = btn.href;
                }
            }
        });
    });
};
