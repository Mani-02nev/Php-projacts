/**
 * High-Performance 3D Card Tilt
 * Uses RequestAnimationFrame + CSS & JS Physics to create a premium 3D effect.
 * Works on any element with class ".global-product-card" or ".tilt-card".
 */

class CardTiltMachine {
    constructor() {
        this.cards = [];
        this.init();
    }

    init() {
        // Find all cards
        const elements = document.querySelectorAll('.global-product-card, .tilt-card');

        elements.forEach(el => {
            // Check if already inited
            if (el.dataset.tiltInited) return;

            this.cards.push(new TiltCard(el));
            el.dataset.tiltInited = "true";
        });

        // Add Glow Element to all
        elements.forEach(el => {
            if (!el.querySelector('.card-glow')) {
                const glow = document.createElement('div');
                glow.classList.add('card-glow');
                el.appendChild(glow);
            }
        });
    }
}

class TiltCard {
    constructor(element) {
        this.element = element;
        this.glow = element.querySelector('.card-glow'); // Might be added later by script above or already there

        // Configuration
        this.settings = {
            maxTilt: 15, // Degrees
            perspective: 1000,
            scale: 1.05,
            speed: 400, // ms transition
            easing: "cubic-bezier(.03,.98,.52,.99)"
        };

        // State
        this.width = 0;
        this.height = 0;
        this.left = 0;
        this.top = 0;
        this.centerX = 0;
        this.centerY = 0;
        this.mouseX = 0;
        this.mouseY = 0;
        this.isHovering = false;

        // Bindings
        this.handleMouseEnter = this.handleMouseEnter.bind(this);
        this.handleMouseMove = this.handleMouseMove.bind(this);
        this.handleMouseLeave = this.handleMouseLeave.bind(this);
        this.update = this.update.bind(this);

        this.addEventListeners();
    }

    addEventListeners() {
        this.element.addEventListener('mouseenter', this.handleMouseEnter);
        this.element.addEventListener('mousemove', this.handleMouseMove);
        this.element.addEventListener('mouseleave', this.handleMouseLeave);
    }

    handleMouseEnter() {
        this.isHovering = true;
        this.updateRect();
        // Remove standard transition for instant JS control, add back for exit
        this.element.style.transition = 'none';

        // Start loop
        this.reqId = requestAnimationFrame(this.update);
    }

    handleMouseMove(e) {
        this.mouseX = e.clientX;
        this.mouseY = e.clientY;
    }

    handleMouseLeave() {
        this.isHovering = false;
        // Restore transition for smooth reset
        this.element.style.transition = `transform ${this.settings.speed}ms ${this.settings.easing}`;
        this.element.style.transform = `perspective(${this.settings.perspective}px) rotateX(0deg) rotateY(0deg) scale3d(1,1,1)`;

        if (this.glow) {
            this.glow.style.transform = `translateZ(1px)`; // Reset glow pos
            this.glow.style.background = `radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 60%)`;
        }

        cancelAnimationFrame(this.reqId);
    }

    updateRect() {
        const rect = this.element.getBoundingClientRect();
        this.width = rect.width;
        this.height = rect.height;
        this.left = rect.left;
        this.top = rect.top;
        this.centerX = this.left + this.width / 2;
        this.centerY = this.top + this.height / 2;
    }

    update() {
        if (!this.isHovering) return;

        // Calculate Tilt
        // X mouse -> Y rotation (left/right)
        // Y mouse -> X rotation (up/down) - Reversed
        const xPct = (this.mouseX - this.left) / this.width;
        const yPct = (this.mouseY - this.top) / this.height;

        const x = (xPct - 0.5) * 2; // -1 to 1
        const y = (yPct - 0.5) * 2; // -1 to 1

        const tiltX = -(y * this.settings.maxTilt);
        const tiltY = x * this.settings.maxTilt;

        // Apply Transform
        // We use string interpolation for max performance
        const transform = `perspective(${this.settings.perspective}px) ` +
            `rotateX(${tiltX.toFixed(2)}deg) ` +
            `rotateY(${tiltY.toFixed(2)}deg) ` +
            `scale3d(${this.settings.scale}, ${this.settings.scale}, 1)`;

        this.element.style.transform = transform;

        // Update Glow (Move gradient opposite to mouse or with mouse)
        // "Shine" usually moves with mouse
        if (this.glow) {
            // Reposition gradient center
            const glowX = xPct * 100;
            const glowY = yPct * 100;
            this.glow.style.background = `radial-gradient(circle at ${glowX}% ${glowY}%, rgba(255,255,255,0.2) 0%, transparent 60%)`;
        }

        this.reqId = requestAnimationFrame(this.update);
    }
}

// Auto Init
document.addEventListener('DOMContentLoaded', () => {
    window.cardTilt = new CardTiltMachine();
});
