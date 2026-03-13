document.addEventListener('DOMContentLoaded', () => {

    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dots .dot');
    let currentSlide = 0;
    const totalSlides = slides.length;
    let autoPlayInterval;

    // --- 1. THREE.JS SCENE SETUP ---
    class HeroScene {
        constructor() {
            this.container = document.getElementById('hero-3d-canvas');
            if (!this.container) return;

            // Scene setup
            this.scene = new THREE.Scene();

            // Camera setup
            this.camera = new THREE.PerspectiveCamera(45, this.container.clientWidth / this.container.clientHeight, 0.1, 1000);
            this.camera.position.z = 10;

            // Renderer setup
            this.renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: "high-performance" });
            this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
            this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            this.container.appendChild(this.renderer.domElement);

            // Lighting setup for realistic feel
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            this.scene.add(ambientLight);

            const dl = new THREE.DirectionalLight(0xffffff, 0.8);
            dl.position.set(5, 5, 5);
            this.scene.add(dl);

            const hl = new THREE.HemisphereLight(0xffffff, 0x444444, 0.4);
            hl.position.set(0, 10, 0);
            this.scene.add(hl);

            // Assets Loader
            this.manager = new THREE.LoadingManager();
            this.textureLoader = new THREE.TextureLoader(this.manager);

            this.meshes = [];
            this.activeMeshIndex = 0;

            // Initializing objects
            this.initObjects();

            // Start render loop
            this.animate();

            // Resize handler
            this.handleResize();
        }

        initObjects() {
            // Image assets passed from slides conceptually or hardcoded based on prompt
            const assets = [
                'assets/images/hero_electronics.png',
                'assets/images/hero_shoes.png',
                'assets/images/hero_groceries.png',
                'assets/images/hero_vegetables.png'
            ];

            const geometry = new THREE.PlaneGeometry(8, 8); // Adjust size relative to camera

            assets.forEach((src, idx) => {
                this.textureLoader.load(src, (texture) => {
                    texture.minFilter = THREE.LinearFilter;

                    const material = new THREE.MeshPhysicalMaterial({
                        map: texture,
                        transparent: true,
                        alphaTest: 0.1,
                        opacity: idx === 0 ? 1 : 0, // Only first visible initially
                        roughness: 0.2,
                        metalness: 0.1,
                        clearcoat: 0.5,
                        clearcoatRoughness: 0.1
                    });

                    const mesh = new THREE.Mesh(geometry, material);

                    // Initial positions to add some slight natural rotation
                    mesh.rotation.y = -0.1;
                    mesh.position.y = 0;

                    // Setup initial random float parameters
                    mesh.userData = {
                        targetOpacity: idx === 0 ? 1 : 0,
                        floatSpeed: 0.001 + Math.random() * 0.001,
                        baseY: 0,
                        time: Math.random() * 100
                    };

                    this.scene.add(mesh);
                    this.meshes[idx] = mesh;
                }, undefined, (err) => {
                    console.log("Error loading texture:", src, err);
                });
            });
        }

        transitionTo(index) {
            this.activeMeshIndex = index;
            if (this.meshes.length > 0) {
                this.meshes.forEach((mesh, idx) => {
                    if (mesh) {
                        mesh.userData.targetOpacity = (idx === index) ? 1 : 0;
                        // Reset position slightly for soft entry
                        if (idx === index) {
                            mesh.position.x = 2; // Slide in from right slightly
                        }
                    }
                });
            }
        }

        animate() {
            requestAnimationFrame(this.animate.bind(this));

            // Skip processing if no meshes
            if (this.meshes.length === 0) {
                this.renderer.render(this.scene, this.camera);
                return;
            }

            this.meshes.forEach((mesh, idx) => {
                if (!mesh) return;

                // Smooth Opacity Transitions
                mesh.material.opacity = THREE.MathUtils.lerp(mesh.material.opacity, mesh.userData.targetOpacity, 0.05);

                // Hide completely to save rendering if 0
                mesh.visible = mesh.material.opacity > 0.01;

                if (mesh.visible) {
                    mesh.userData.time += mesh.userData.floatSpeed;

                    // Soft Floating Motion
                    mesh.position.y = Math.sin(mesh.userData.time * 5) * 0.2;
                    mesh.rotation.x = Math.sin(mesh.userData.time * 2) * 0.05;
                    mesh.rotation.z = Math.cos(mesh.userData.time * 2) * 0.02;

                    // Smooth Entry Slide-In
                    mesh.position.x = THREE.MathUtils.lerp(mesh.position.x, 0, 0.05);

                    // Parallax Mouse Interaction
                    mesh.rotation.y = THREE.MathUtils.lerp(mesh.rotation.y, (mouseX * 0.1) - 0.1, 0.05);
                }
            });

            this.renderer.render(this.scene, this.camera);
        }

        handleResize() {
            window.addEventListener('resize', () => {
                if (!this.container) return;
                const aspect = this.container.clientWidth / this.container.clientHeight;
                this.camera.aspect = aspect;
                this.camera.updateProjectionMatrix();
                this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);

                // Pause 3D on mobile strictly or hide the canvas layer
                if (window.innerWidth < 991) {
                    this.container.style.opacity = '0.3';
                } else {
                    this.container.style.opacity = '1';
                }
            });
            // trigger once
            window.dispatchEvent(new Event('resize'));
        }
    }

    let mouseX = 0;
    document.addEventListener('mousemove', (e) => {
        mouseX = (e.clientX / window.innerWidth) * 2 - 1;
    });

    // Initialize 3D Hero Scene
    const hero3D = new HeroScene();


    // --- 2. HTML SLIDER LOGIC ---
    function updateSlide(newIndex) {
        // Prevent out of bounds
        if (newIndex >= totalSlides) newIndex = 0;
        if (newIndex < 0) newIndex = totalSlides - 1;

        // Reset current active classes
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');

        // Update to new
        currentSlide = newIndex;
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');

        // Update 3D Scene
        if (hero3D) hero3D.transitionTo(currentSlide);
    }

    // Expose click handlers to global window for onclick=""
    window.nextSlide = function () {
        startTimer(); // Reset auto timer
        updateSlide(currentSlide + 1);
    }

    window.prevSlide = function () {
        startTimer();
        updateSlide(currentSlide - 1);
    }

    window.gotoSlide = function (pageNumber) {
        startTimer();
        // Zero-index correction
        updateSlide(pageNumber - 1);
    }

    function startTimer() {
        if (autoPlayInterval) clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(() => {
            updateSlide(currentSlide + 1);
        }, 6000); // 6 Seconds auto-transition
    }

    // Initialize the slider auto-play
    startTimer();
});
