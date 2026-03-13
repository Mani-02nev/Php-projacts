/**
 * High-Performance 3D Card Tilt
 * DISABLED - Card animations removed for flat design
 */

class CardTiltMachine {
    constructor() {
        // Disabled - no 3D tilt animation
    }

    init() {
        // Disabled - no initialization
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


class TiltCard {
    // Disabled - no 3D tilt animation
}

// Auto Init - DISABLED
document.addEventListener('DOMContentLoaded', () => {
    // Card tilt animation disabled
});
