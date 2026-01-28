document.addEventListener('DOMContentLoaded', () => {
    // --- 1. UI ELEMENTS ---
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatMessages = document.getElementById('chatMessages');
    const productList = document.getElementById('productList');
    const typingIndicator = document.getElementById('typingIndicator');

    let conversationState = {
        step: 'init',
        category: null,
        budget: null,
        usage: null
    };

    // --- 2. THREE.JS AI CORE ENGINE ---
    class AICore {
        constructor() {
            this.container = document.getElementById('ai-core-canvas');
            if (!this.container) return;

            this.scene = new THREE.Scene();
            // Fog for depth
            this.scene.fog = new THREE.FogExp2(0x000000, 0.05);

            this.camera = new THREE.PerspectiveCamera(75, this.container.clientWidth / this.container.clientHeight, 0.1, 1000);
            this.camera.position.z = 5;

            this.renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
            this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            this.container.appendChild(this.renderer.domElement);

            this.initObjects();
            this.animate();
            this.handleResize();
        }

        initObjects() {
            // A. The Core (Icosahedron)
            const geometry = new THREE.IcosahedronGeometry(1.5, 1); // Radius 1.5, Detail 1
            const material = new THREE.MeshBasicMaterial({
                color: 0x7C3AED,
                wireframe: true,
                transparent: true,
                opacity: 0.3
            });
            this.core = new THREE.Mesh(geometry, material);
            this.scene.add(this.core);

            // B. The Inner Light (PointLight)
            this.light = new THREE.PointLight(0x7C3AED, 1, 100);
            this.light.position.set(0, 0, 0);
            this.scene.add(this.light);

            // C. Orbiting Particles (Points)
            const particlesGeom = new THREE.BufferGeometry();
            const particleCount = 200;
            const positions = new Float32Array(particleCount * 3);
            for (let i = 0; i < particleCount; i++) {
                const r = 2.5 + Math.random() * 2; // Radius 2.5 to 4.5
                const theta = Math.random() * Math.PI * 2;
                const phi = Math.acos(2 * Math.random() - 1);

                positions[i * 3] = r * Math.sin(phi) * Math.cos(theta);
                positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
                positions[i * 3 + 2] = r * Math.cos(phi);
            }
            particlesGeom.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            const pMaterial = new THREE.PointsMaterial({
                color: 0x06B6D4,
                size: 0.05,
                transparent: true
            });
            this.particles = new THREE.Points(particlesGeom, pMaterial);
            this.scene.add(this.particles);

            // State variables
            this.targetScale = 1;
            this.targetSpeed = 0.002;
        }

        animate() {
            requestAnimationFrame(this.animate.bind(this));

            const time = Date.now() * 0.001;

            // Rotation
            this.core.rotation.x += this.targetSpeed;
            this.core.rotation.y += this.targetSpeed;
            this.particles.rotation.y -= this.targetSpeed * 0.5;

            // Breathing / Pulsing
            const scale = THREE.MathUtils.lerp(this.core.scale.x, this.targetScale, 0.1);
            this.core.scale.set(scale, scale, scale);

            // Idle "Breathing" when not active
            if (this.targetScale === 1) {
                this.core.scale.setScalar(1 + Math.sin(time * 2) * 0.05);
            }

            this.renderer.render(this.scene, this.camera);
        }

        // Actions
        pulse() {
            this.targetScale = 1.4;
            this.targetSpeed = 0.02;
            this.core.material.color.setHex(0xA78BFA); // Lighter purple
            this.core.material.opacity = 0.8;
            setTimeout(() => this.relax(), 300);
        }

        think() {
            this.targetSpeed = 0.05; // Fast spin
            this.core.material.color.setHex(0x06B6D4); // Cyan for thinking
        }

        relax() {
            this.targetScale = 1;
            this.targetSpeed = 0.002;
            this.core.material.color.setHex(0x7C3AED); // Back to purple
            this.core.material.opacity = 0.3;
        }

        handleResize() {
            window.addEventListener('resize', () => {
                const w = this.container.clientWidth;
                const h = this.container.clientHeight;
                this.camera.aspect = w / h;
                this.camera.updateProjectionMatrix();
                // Ensure renderer is always transparent style
                this.renderer.setSize(w, h);
            });
        }
    }

    // Initialize 3D Core
    const aiCore = new AICore();

    // --- 3. CHAT LOGIC ---

    // Input monitoring for "Pulse" effect
    chatInput.addEventListener('input', () => {
        if (aiCore) {
            aiCore.core.rotation.y += 0.1; // Small twitch
        }
    });

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        // User Action
        addUserMessage(text);
        chatInput.value = '';

        // AI Reaction
        showTyping(true);
        if (aiCore) aiCore.think(); // 3D Effect

        try {
            // API Call
            const response = await fetch('api/chat-endpoint.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: text,
                    state: conversationState
                })
            });

            const data = await response.json();

            // Update Logic
            if (data.state) conversationState = data.state;

            // Delayed response for effect
            setTimeout(() => {
                showTyping(false);
                addBotMessage(data.message);

                if (data.products && data.products.length > 0) {
                    updateProductPanel(data.products);
                }

                // CHECK FOR RESET
                if (conversationState.step === 'init') {
                    handleConversationEnd();
                }

                if (aiCore) aiCore.relax(); // 3D Relax
            }, 800); // Artificial delay for "Thinking" feel

        } catch (error) {
            console.error('Error:', error);
            showTyping(false);
            if (aiCore) aiCore.relax();
            addBotMessage("I'm having trouble connecting to my brain right now. Please try again.");
        }
    }

    function handleConversationEnd() {
        // Disable input temporarily
        chatInput.disabled = true;
        sendBtn.disabled = true;
        chatInput.placeholder = "Conversation complete.";

        // Append 'Start New Search' Button
        const btnDiv = document.createElement('div');
        btnDiv.className = 'message bot system-msg';
        btnDiv.innerHTML = `
            <div class="message-content text-center" style="background: transparent; border: none; box-shadow: none;">
                <button id="restartBtn" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="bi bi-arrow-repeat me-2"></i> Start New Search
                </button>
            </div>
        `;
        chatMessages.insertBefore(btnDiv, typingIndicator);
        scrollToBottom();

        document.getElementById('restartBtn').addEventListener('click', resetConversation);
    }

    function resetConversation() {
        // 1. Fade out messages
        chatMessages.style.opacity = '0';

        setTimeout(() => {
            // 2. Clear Chat (keep typing indicator)
            // Remove all children except typing indicator
            while (chatMessages.firstChild && chatMessages.firstChild !== typingIndicator) {
                chatMessages.removeChild(chatMessages.firstChild);
            }

            // 3. Restore Greeting
            const greeting = document.createElement('div');
            greeting.className = 'message bot';
            greeting.innerHTML = `
                <div class="message-content">
                    Hello again! I'm ready for a new task. 🧠<br>
                    What are you looking for now?
                </div>
            `;
            chatMessages.insertBefore(greeting, typingIndicator);

            // 4. Reset Product Panel
            productList.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-cpu"></i>
                    <p>Waiting for data...</p>
                </div>
            `;

            // 5. Re-enable Input
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.value = '';
            chatInput.placeholder = "Describe what you're looking for...";
            chatInput.focus();

            // 6. Reset Scroll & Opacity
            chatMessages.scrollTop = 0;
            chatMessages.style.opacity = '1';

        }, 300);
    }

    function addUserMessage(text) {
        if (aiCore) aiCore.pulse();
        const div = document.createElement('div');
        div.className = 'message user';
        div.innerHTML = `<div class="message-content">${escapeHtml(text)}</div>`;
        chatMessages.insertBefore(div, typingIndicator);
        scrollToBottom();
    }

    function addBotMessage(htmlContent) {
        if (aiCore) aiCore.pulse();
        const div = document.createElement('div');
        div.className = 'message bot';
        div.innerHTML = `
            <div class="message-content">${htmlContent}</div>
        `;
        chatMessages.insertBefore(div, typingIndicator);
        scrollToBottom();
    }

    function showTyping(show) {
        typingIndicator.style.display = show ? 'flex' : 'none';
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function updateProductPanel(products) {
        productList.innerHTML = '';

        products.forEach((product, index) => {
            const isBest = index === 0;
            const tagLabel = isBest ? 'Best Match' : 'Alternative';
            const tagClass = isBest ? 'tag-best' : 'tag-alt';
            const cardClass = isBest ? 'best-choice' : '';

            const card = document.createElement('div');
            card.className = `ai-product-card ${cardClass}`;
            card.style.animationDelay = `${index * 0.2}s`;

            card.innerHTML = `
                <img src="${product.image}" alt="${escapeHtml(product.name)}" class="card-img" onerror="this.src='assets/images/placeholder_product.png'">
                <div class="card-info">
                    <span class="card-tag ${tagClass}">${tagLabel}</span>
                    <div class="card-title">${escapeHtml(product.name)}</div>
                    <div class="card-price">₹${parseInt(product.price).toLocaleString()}</div>
                    <div class="card-reason"><i class="bi bi-stars"></i> ${escapeHtml(product.reason)}</div>
                </div>
                <div class="align-self-center">
                    <a href="product-detail.php?id=${product.id}" class="btn btn-sm btn-outline-light rounded-circle" target="_blank"><i class="bi bi-chevron-right"></i></a>
                </div>
            `;
            productList.appendChild(card);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
