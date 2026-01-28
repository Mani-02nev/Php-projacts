<?php
$page_title = 'About Us';
require_once 'includes/header.php';

// Dynamic Team Data
$team = [
    [
        'name' => 'Karuppasamy M',
        'role' => 'Chief Architect',
        'initials' => 'KM',
        'icon' => 'bi-lightning-charge-fill',
        'bio' => 'Driving the technical vision with scalable PHP architecture and secure systems.',
        'skills' => ['PHP', 'DevOps', 'Security'],
        'color' => '#5203dcff'
    ],
    [
        'name' => 'Nathakumar',
        'role' => 'Backend Developer',
        'initials' => 'NK',
        'icon' => 'bi-server',
        'bio' => 'Specializing in server-side logic and database optimization protocols.',
        'skills' => ['Logic', 'MySQL', 'API'],
        'color' => '#10B981'
    ],
    [
        'name' => 'Bharathvaj',
        'role' => 'UI/UX Designer',
        'initials' => 'BV',
        'icon' => 'bi-palette-fill',
        'bio' => 'Crafting intuitive user interfaces and seamless digital experiences.',
        'skills' => ['Design', 'Figma', 'CSS'],
        'color' => '#F59E0B'
    ],
    [
        'name' => 'Tamil Arasan',
        'role' => 'Frontend Engineer',
        'initials' => 'TA',
        'icon' => 'bi-code-slash',
        'bio' => 'Translating creative designs into responsive and interactive web code.',
        'skills' => ['HTML5', 'JS', 'React'],
        'color' => '#3B82F6'
    ],
    [
        'name' => 'Tamil Selvan',
        'role' => 'QA Auditor',
        'initials' => 'TS',
        'icon' => 'bi-shield-check',
        'bio' => 'Ensuring system integrity, security compliance, and zero-bug releases.',
        'skills' => ['Testing', 'Security', 'QA'],
        'color' => '#EF4444'
    ],
    [
        'name' => 'Kishor Kumar',
        'role' => 'Data Architect',
        'initials' => 'KK',
        'icon' => 'bi-database-fill',
        'bio' => 'Managing data flow efficiency and structural integrity of information.',
        'skills' => ['Data', 'Analytics', 'Flow'],
        'color' => '#8B5CF6'
    ]
];
?>

<style>
/* ═══════════════════════════════════════════════════════════
   ABOUT PAGE - PREMIUM DARK THEME
   ═══════════════════════════════════════════════════════════ */

.about-page-wrapper {
    background-color: #0B0B0E;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}

/* Hero Background Gradient */
.about-hero-bg {
    background: radial-gradient(circle at top right, rgba(124, 58, 237, 0.15), transparent 40%),
                radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05), transparent 40%);
}

/* ═══════════════════════════════════════════════════════════
   CORE VALUES CARDS
   ═══════════════════════════════════════════════════════════ */
.feature-card {
    background: #14161A;
    border: 1px solid #2D2D35;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: #7C3AED;
    box-shadow: 0 20px 40px -10px rgba(124, 58, 237, 0.15);
}

.feature-icon-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

/* ═══════════════════════════════════════════════════════════
   3D FLIP CARD SYSTEM - VISIONARIES
   ═══════════════════════════════════════════════════════════ */
.flip-card {
    background-color: transparent;
    height: 320px;
    perspective: 1000px;
    cursor: pointer;
}

.flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    transform-style: preserve-3d;
}

.flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
}

.flip-card-front, 
.flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid #2D2D35;
}

/* Front Side */
.flip-card-front {
    background-color: #14161A;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

/* Back Side */
.flip-card-back {
    background-color: #0B0B0E;
    color: #E5E7EB;
    transform: rotateY(180deg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    border-color: #7C3AED;
    background-image: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(20, 22, 26, 0) 100%);
}

/* Avatar Glow Effect */
.avatar-glow {
    box-shadow: 0 0 30px rgba(124, 58, 237, 0.2);
    transition: all 0.3s ease;
}

.flip-card:hover .avatar-glow {
    box-shadow: 0 0 50px rgba(124, 58, 237, 0.4);
    transform: scale(1.1);
}

/* ═══════════════════════════════════════════════════════════
   PROJECT INFO SECTION
   ═══════════════════════════════════════════════════════════ */
.project-info-card {
    background: #14161A;
    border: 1px solid #2D2D35;
    border-radius: 24px;
    overflow: hidden;
}

.project-info-divider {
    border-color: #2D2D35 !important;
}

.tech-badge {
    background: #0B0B0E;
    border: 1px solid #2D2D35;
    color: #9CA3AF;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE DESIGN
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
    .flip-card {
        height: 280px;
    }
    
    .feature-icon-wrapper {
        width: 60px;
        height: 60px;
    }
}

/* ═══════════════════════════════════════════════════════════
   ACCESSIBILITY & CONTRAST
   ═══════════════════════════════════════════════════════════ */
.text-primary-light {
    color: #E5E7EB;
}

.text-secondary-light {
    color: #9CA3AF;
}

.text-tertiary-light {
    color: #6B7280;
}
</style>

<div class="about-page-wrapper">
    
    <!-- ═══════════════════════════════════════════════════════════
         1. HERO SECTION
         ═══════════════════════════════════════════════════════════ -->
    <div class="about-hero-bg">
        <div class="container py-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8 mx-auto py-5">
                    <span class="badge rounded-pill px-3 py-2 mb-4" style="background: rgba(124, 58, 237, 0.1); color: #A78BFA; border: 1px solid rgba(124, 58, 237, 0.2);">EST. 2026</span>
                    <h1 class="display-3 fw-bold mb-4" style="color: #E5E7EB; letter-spacing: -1px;">
                        We are <span style="color: #7C3AED;">UNIVAULT</span>
                    </h1>
                    <p class="lead mb-0" style="color: #9CA3AF; line-height: 1.8; max-width: 600px; margin: 0 auto;">
                        A collective of digital craftsmen building the next generation of e-commerce experiences. 
                        Driven by innovation, secured by logic, and designed with passion.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         2. CORE VALUES (3 CARDS ONLY)
         ═══════════════════════════════════════════════════════════ -->
    <div class="container mb-5 pb-4">
        <div class="row g-4">
            <!-- Modern Stack -->
            <div class="col-md-4">
                <div class="feature-card p-5 rounded-5 text-center">
                    <div class="feature-icon-wrapper" style="background: rgba(124, 58, 237, 0.1); color: #7C3AED;">
                        <i class="bi bi-layers-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Modern Stack</h4>
                    <p class="mb-0" style="color: #9CA3AF; line-height: 1.6;">
                        Built on a robust PHP framework with CSV-based data architecture for lightning-fast performance.
                    </p>
                </div>
            </div>

            <!-- Bank-Grade Security -->
            <div class="col-md-4">
                <div class="feature-card p-5 rounded-5 text-center">
                    <div class="feature-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                        <i class="bi bi-shield-lock-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Bank-Grade Security</h4>
                    <p class="mb-0" style="color: #9CA3AF; line-height: 1.6;">
                        Engineered with enterprise security protocols to ensure every transaction is safe and encrypted.
                    </p>
                </div>
            </div>

            <!-- Scalable Design -->
            <div class="col-md-4">
                <div class="feature-card p-5 rounded-5 text-center">
                    <div class="feature-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                        <i class="bi bi-infinity fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Scalable Design</h4>
                    <p class="mb-0" style="color: #9CA3AF; line-height: 1.6;">
                        Modular architecture allowing infinite scalability and seamless feature integration.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         3. MEET OUR VISIONARIES
         ═══════════════════════════════════════════════════════════ -->
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3" style="color: #E5E7EB;">Meet Our Visionaries</h2>
            <p class="mb-5" style="color: #9CA3AF;">The brilliant minds behind the code.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($team as $member): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            
                            <!-- Front Side -->
                            <div class="flip-card-front">
                                <div class="avatar-glow rounded-circle d-flex align-items-center justify-content-center mb-4" 
                                     style="width: 100px; height: 100px; background: #0B0B0E; border: 2px solid <?php echo $member['color']; ?>; color: <?php echo $member['color']; ?>; font-size: 2.5rem;">
                                    <i class="bi <?php echo $member['icon']; ?>"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #E5E7EB;"><?php echo $member['name']; ?></h4>
                                <p class="small text-uppercase fw-bold mb-4" style="color: <?php echo $member['color']; ?>; letter-spacing: 1px;">
                                    <?php echo $member['role']; ?>
                                </p>
                                
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    <?php foreach ($member['skills'] as $skill): ?>
                                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.05); color: #9CA3AF; border: 1px solid #2D2D35; font-weight: 500;">
                                            <?php echo $skill; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="flip-card-back">
                                <div class="mb-3 d-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); color: <?php echo $member['color']; ?>;">
                                    <span class="fw-bold fs-4"><?php echo $member['initials']; ?></span>
                                </div>
                                <h5 class="fw-bold mb-3" style="color: #E5E7EB;">About</h5>
                                <p class="text-center mb-4 small px-3" style="line-height: 1.6; color: #9CA3AF;">
                                    <?php echo $member['bio']; ?>
                                </p>
                                <button class="btn btn-sm rounded-pill px-4 fw-bold" style="background: <?php echo $member['color']; ?>; color: white; border: none;">
                                    View Profile
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         4. PROJECT INFORMATION
         ═══════════════════════════════════════════════════════════ -->
    <div class="container py-5">
        <div class="project-info-card">
            <div class="row g-0">
                <!-- Left Column: Info Table -->
                <div class="col-lg-6 p-5 d-flex flex-column justify-content-center" style="border-right: 1px solid #2D2D35;">
                    <h3 class="fw-bold mb-4" style="color: #E5E7EB;">Project Information</h3>
                    <div class="vstack gap-3">
                        <div class="d-flex justify-content-between border-bottom pb-3 project-info-divider">
                            <span style="color: #9CA3AF;">Project Name</span>
                            <span class="fw-bold" style="color: #E5E7EB;">Univault E-Commerce</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 project-info-divider">
                            <span style="color: #9CA3AF;">Institution</span>
                            <span class="fw-bold" style="color: #E5E7EB;">Computer Science Dept.</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 project-info-divider">
                            <span style="color: #9CA3AF;">Academic Year</span>
                            <span class="fw-bold" style="color: #E5E7EB;">2nd Year Diploma</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1">
                            <span style="color: #9CA3AF;">Tech Stack</span>
                            <div class="d-flex gap-2 flex-wrap justify-content-end">
                                <span class="tech-badge">PHP 8.2</span>
                                <span class="tech-badge">HTML5</span>
                                <span class="tech-badge">CSV</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: CTA -->
                <div class="col-lg-6 p-5 d-flex align-items-center text-center justify-content-center" 
                     style="background: radial-gradient(circle, rgba(124, 58, 237, 0.1), transparent);">
                    <div>
                        <i class="bi bi-code-square display-1 mb-4 d-block" style="color: #7C3AED;"></i>
                        <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Open Source Learning</h4>
                        <p class="mb-4 px-4" style="color: #9CA3AF; line-height: 1.6;">
                            This project represents a complete dedication to learning full-stack development, 
                            from server-side logic to frontend interactivity.
                        </p>
                        <a href="products.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg" 
                           style="background-color: #7C3AED; border-color: #7C3AED;">
                            Explore the Platform
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         5. ABOUT THIS PROJECT
         ═══════════════════════════════════════════════════════════ -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-4 text-center" style="color: #E5E7EB;">
                    <i class="bi bi-lightbulb me-2" style="color: #7C3AED;"></i>
                    About This Project
                </h2>
                <div style="color: #9CA3AF; line-height: 1.8; font-size: 1.05rem;">
                    <p>
                        UNIVAULT is an e-commerce platform developed as part of our 2nd year diploma project for PHP web development. 
                        This project demonstrates our understanding of full-stack web development, including user authentication, 
                        product management, shopping cart functionality, and admin panel features.
                    </p>
                    <p>
                        Built with modern web technologies and following best practices, UNIVAULT showcases a clean, responsive design 
                        with a focus on user experience. The platform uses CSV files for data storage, making it lightweight and easy 
                        to deploy without requiring a traditional database server.
                    </p>
                    <p class="mb-0">
                        This project has been a collaborative effort, with each team member contributing their unique skills and expertise 
                        to create a functional and visually appealing e-commerce solution.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         6. CLOSING SECTION
         ═══════════════════════════════════════════════════════════ -->
    <div class="container text-center py-5 mb-5">
        <p class="h6 text-uppercase fw-bold mb-3" style="color: #9CA3AF; letter-spacing: 2px;">Thank You</p>
        <h2 class="display-6 fw-bold mb-0" style="color: #E5E7EB;">
            Created with <i class="bi bi-heart-fill mx-2" style="color: #EF4444;"></i> by the Students
        </h2>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
