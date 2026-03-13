<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U| Modern Digital Product UI</title>
    <meta name="description" content="Experience the next generation of digital product interfaces with Aura Premium.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Design System -->
    <link rel="stylesheet" href="css/premium-design.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Extra custom styles for the showcase */
        .glass-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: var(--space-2) 0;
        }

        @media (prefers-color-scheme: dark) {
            .glass-nav {
                background: rgba(15, 23, 42, 0.8);
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }
            .nav-links a:hover {
                color: var(--accent);
            }
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-box {
            display: flex;
            align-items: center;
            gap: var(--space-1);
            font-weight: 800;
            font-size: 24px;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            gap: var(--space-4);
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 15px;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .section-tag {
            display: inline-block;
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: var(--space-2);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--secondary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--primary);
            margin-bottom: var(--space-2);
        }

        .footer-premium {
            background: var(--primary);
            color: var(--text-body);
            padding: var(--space-8) 0;
            margin-top: var(--space-10);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: var(--space-6);
        }

        .footer-links h4 {
            color: var(--text-body);
            margin-bottom: var(--space-3);
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: var(--space-2);
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        /* Micro-animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade {
            animation: fadeIn 0.8s ease forwards;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>

    <nav class="glass-nav">
        <div class="container-premium nav-content">
            <a href="#" class="logo-box">
                <img src="assets/aura-logo.png" alt="Aura Logo" style="height: 40px; border-radius: 8px;">
                Aura
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <a href="#contact">Contact</a>
            </div>
            <a href="#" class="btn-premium btn-primary" style="padding: 10px 20px; font-size: 14px;">Get Started</a>
        </div>
    </nav>

    <section class="hero-premium">
        <div class="container-premium">
            <span class="section-tag animate-fade">Production Ready</span>
            <h1 class="hero-title animate-fade delay-1">The Premium Standard for Digital Products</h1>
            <p class="hero-subtitle animate-fade delay-2">A clean, high-conversion interface built with an 8px grid system, professional color palettes, and accessibility at its core.</p>
            <div class="animate-fade delay-3">
                <a href="#" class="btn-premium btn-accent">Explore Aura Premium</a>
                <a href="#" class="btn-premium btn-outline" style="margin-left: 12px;">View Documentation</a>
            </div>
        </div>
    </section>

    <section id="features" class="container-premium" style="padding: var(--space-10) 0;">
        <div style="text-align: center; margin-bottom: var(--space-8);">
            <h2 style="font-size: 32px; margin-bottom: var(--space-2);">Designed for Excellence</h2>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto;">Everything you need to build a professional, scalable web application.</p>
        </div>
        <div class="grid-premium">
            <div class="card-premium animate-fade">
                <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Enterprise Security</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Built with Trustworthy Navy tones and secure design principles that inspire confidence in your users.</p>
            </div>
            <div class="card-premium animate-fade delay-1">
                <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                <h3>High Conversion</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Clear CTAs with visual priority and emerald accents guide users towards meaningful actions.</p>
            </div>
            <div class="card-premium animate-fade delay-2">
                <div class="feature-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                <h3>Mobile-First</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Responsive by design, ensuring a premium experience across desktops, tablets, and phones.</p>
            </div>
        </div>
    </section>

    <section style="background-color: var(--secondary-dark); padding: var(--space-10) 0;">
        <div class="container-premium">
            <div class="grid-premium" style="align-items: center;">
                <div class="animate-fade">
                    <span class="section-tag">Interactive UI</span>
                    <h2 style="font-size: 40px; margin-bottom: var(--space-3);">Beautifully Crafted Forms</h2>
                    <p style="color: var(--text-muted); margin-bottom: var(--space-4);">Clean inputs, visible focus states, and soft shadows provide a premium user experience that feels intuitive and alive.</p>
                    <ul style="list-style: none; margin-bottom: var(--space-4);">
                        <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-check" style="color: var(--accent);"></i>
                            <span>Visible focus states for accessibility</span>
                        </li>
                        <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-check" style="color: var(--accent);"></i>
                            <span>Soft elevtaion with no harsh borders</span>
                        </li>
                    </ul>
                </div>
                <div class="card-premium animate-fade delay-1" style="max-width: 450px; justify-self: center;">
                    <form onsubmit="event.preventDefault(); alert('Submission simulated!');">
                        <div class="form-group">
                            <label class="label-premium">Full Name</label>
                            <input type="text" class="input-premium" placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label class="label-premium">Email Address</label>
                            <input type="email" class="input-premium" placeholder="john@example.com">
                        </div>
                        <div class="form-group">
                            <label class="label-premium">Message</label>
                            <textarea class="input-premium" rows="4" placeholder="How can we help?"></textarea>
                        </div>
                        <button type="submit" class="btn-premium btn-primary" style="width: 100%;">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section style="padding: var(--space-10) 0;">
        <div class="container-premium">
            <div style="text-align: center; margin-bottom: var(--space-6);">
                <span class="section-tag">Design System</span>
                <h2>The Aura Foundation</h2>
            </div>
            <div class="grid-premium">
                <div class="card-premium">
                    <h4 style="margin-bottom: var(--space-2);">Color Palette</h4>
                    <div style="display: flex; gap: 8px;">
                        <div style="width: 40px; height: 40px; background: #0F172A; border-radius: 4px;" title="Primary"></div>
                        <div style="width: 40px; height: 40px; background: #1E293B; border-radius: 4px;" title="Primary Light"></div>
                        <div style="width: 40px; height: 40px; background: #F1F5F9; border-radius: 4px; border: 1px solid #ddd;" title="Secondary"></div>
                        <div style="width: 40px; height: 40px; background: #10B981; border-radius: 4px;" title="Accent"></div>
                    </div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 12px;">Enterprise-level tones: Navy, Soft Neutral, and Emerald.</p>
                </div>
                <div class="card-premium">
                    <h4 style="margin-bottom: var(--space-2);">Typography</h4>
                    <p style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">Inter Sans-Serif</p>
                    <p style="font-size: 14px; color: var(--text-muted);">Highly readable, modern, and professional for enterprise interfaces.</p>
                </div>
                <div class="card-premium">
                    <h4 style="margin-bottom: var(--space-2);">8px Grid System</h4>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                        <div style="height: 20px; background: #F1F5F9; border-radius: 2px;"></div>
                        <div style="height: 20px; background: #F1F5F9; border-radius: 2px;"></div>
                        <div style="height: 20px; background: #F1F5F9; border-radius: 2px;"></div>
                        <div style="height: 20px; background: #F1F5F9; border-radius: 2px;"></div>
                    </div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 12px;">Consistent spacing and alignment based on an 8px base unit.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-premium">
        <div class="container-premium">
            <div class="footer-grid">
                <div>
                    <a href="#" class="logo-box" style="color: var(--text-body); margin-bottom: var(--space-3);">
                        <img src="assets/aura-logo.png" alt="Aura Logo" style="height: 32px; border-radius: 6px; filter: brightness(0) invert(1);">
                        Aura
                    </a>
                    <p style="color: var(--text-muted); font-size: 14px; max-width: 300px;">
                        The premium digital product interface for modern commerce and enterprise-level startups.
                    </p>
                </div>
                <div class="footer-links">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Solutions</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Updates</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div style="margin-top: var(--space-8); padding-top: var(--space-4); border-top: 1px solid rgba(0, 0, 0, 0.05); text-align: center; font-size: 14px; color: var(--text-muted);">
                &copy; 2026 Aura Premium. All rights reserved. Built with precision and trust.
            </div>
        </div>
    </footer>

</body>
</html>
