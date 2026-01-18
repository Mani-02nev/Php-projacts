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
        'color' => '#7C3AED'
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
/* Page-Specific Animations & Styles */
.about-hero-bg {
    background: radial-gradient(circle at top right, rgba(124, 58, 237, 0.15), transparent 40%),
                radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05), transparent 40%);
}

.feature-card {
    background: #14161A;
    border: 1px solid #2D2D35;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: #7C3AED;
    box-shadow: 0 20px 40px -10px rgba(124, 58, 237, 0.15);
}

/* 3D Flip Card System */
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

.flip-card-front, .flip-card-back {
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

/* Avatar Glow */
.avatar-glow {
    box-shadow: 0 0 30px rgba(124, 58, 237, 0.2);
    transition: all 0.3s ease;
}
.flip-card:hover .avatar-glow {
    box-shadow: 0 0 50px rgba(124, 58, 237, 0.4);
    transform: scale(1.1);
}
</style>

<div class="about-hero-bg" style="background-color: #0B0B0E; min-height: 100vh;">
    
    <!-- Hero Section -->
    <div class="container py-5 text-center">
        <div class="rowjustify-content-center">
            <div class="col-lg-8 mx-auto py-5">
                <span class="badge rounded-pill px-3 py-2 mb-4" style="background: rgba(124, 58, 237, 0.1); color: #A78BFA; border: 1px solid rgba(124, 58, 237, 0.2);">EST. 2026</span>
                <h1 class="display-3 fw-bold mb-4" style="color: #E5E7EB; letter-spacing: -1px;">We are <span style="color: #7C3AED;">UNIVAULT</span></h1>
                <p class="lead mb-0" style="color: #9CA3AF; line-height: 1.8;">
                    A collective of digital craftsmen building the next generation of e-commerce experiences. 
                    Driven by innovation, secured by logic, and designed with passion.
                </p>
            </div>
        </div>
    </div>

    <!-- Feature Grid -->
    <div class="container mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card h-100 p-5 rounded-5 text-center position-relative overflow-hidden">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: rgba(124, 58, 237, 0.1); color: #7C3AED;">
                        <i class="bi bi-layers-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Modern Stack</h4>
                    <p class="mb-0" style="color: #9CA3AF;">Built on a robust PHP framework with CSV-based data architecture for lightning-fast performance.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 p-5 rounded-5 text-center position-relative overflow-hidden">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); color: #10B981;">
                        <i class="bi bi-shield-lock-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Bank-Grade Security</h4>
                    <p class="mb-0" style="color: #9CA3AF;">Engineered with enterprise security protocols to ensure every transaction is safe and encrypted.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 p-5 rounded-5 text-center position-relative overflow-hidden">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                        <i class="bi bi-infinity fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Scalable Design</h4>
                    <p class="mb-0" style="color: #9CA3AF;">Modular architecture allowing infinite scalability and seamless feature integration.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Visionaries Section -->
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
                            
                            <!-- Front -->
                            <div class="flip-card-front">
                                <div class="avatar-glow rounded-circle d-flex align-items-center justify-content-center mb-4" 
                                     style="width: 100px; height: 100px; background: #0B0B0E; border: 2px solid <?php echo $member['color']; ?>; color: <?php echo $member['color']; ?>; font-size: 2.5rem;">
                                    <i class="bi <?php echo $member['icon']; ?>"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #E5E7EB;"><?php echo $member['name']; ?></h4>
                                <p class="small text-uppercase fw-bold mb-4" style="color: <?php echo $member['color']; ?>; letter-spacing: 1px;"><?php echo $member['role']; ?></p>
                                
                                <div class="d-flex gap-2">
                                    <?php foreach ($member['skills'] as $skill): ?>
                                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.05); color: #9CA3AF; border: 1px solid #2D2D35; font-weight: 500;">
                                            <?php echo $skill; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Back -->
                            <div class="flip-card-back">
                                <div class="mb-3 d-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); color: <?php echo $member['color']; ?>;">
                                    <span class="fw-bold fs-4"><?php echo $member['initials']; ?></span>
                                </div>
                                <h5 class="fw-bold mb-3" style="color: #E5E7EB;">About</h5>
                                <p class="text-center text-muted mb-4 small px-3" style="line-height: 1.6;">
                                    <?php echo $member['bio']; ?>
                                </p>
                                <a href="#" class="btn btn-sm rounded-pill px-4 fw-bold" style="background: <?php echo $member['color']; ?>; color: white; border: none;">
                                    View Profile
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Project Info Section -->
    <div class="container py-5">
        <div class="card border-0 rounded-5 overflow-hidden" style="background: #14161A;">
            <div class="row g-0">
                <div class="col-lg-6 p-5 d-flex flex-column justify-content-center" style="border-right: 1px solid #2D2D35;">
                    <h3 class="fw-bold mb-4" style="color: #E5E7EB;">Project Information</h3>
                    <div class="vstack gap-3">
                        <div class="d-flex justify-content-between border-bottom pb-3" style="border-color: #2D2D35 !important;">
                            <span style="color: #9CA3AF;">Project Name</span>
                            <span class="fw-bold" style="color: #E5E7EB;">Univault E-Commerce</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3" style="border-color: #2D2D35 !important;">
                            <span style="color: #9CA3AF;">Institution</span>
                            <span class="fw-bold" style="color: #E5E7EB;">Computer Science Dept.</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3" style="border-color: #2D2D35 !important;">
                            <span style="color: #9CA3AF;">Academic Year</span>
                            <span class="fw-bold" style="color: #E5E7EB;">2nd Year Diploma</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1">
                            <span style="color: #9CA3AF;">Core Technology</span>
                            <div class="d-flex gap-2">
                                <span class="badge bg-dark border border-secondary text-secondary">PHP 8.2</span>
                                <span class="badge bg-dark border border-secondary text-secondary">HTML5 CSV</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 p-5 d-flex align-items-center text-center justify-content-center" style="background: radial-gradient(circle, rgba(124, 58, 237, 0.1), transparent);">
                    <div>
                        <i class="bi bi-code-square display-1 mb-4 d-block" style="color: #7C3AED;"></i>
                        <h4 class="fw-bold mb-3" style="color: #E5E7EB;">Open Source Learning</h4>
                        <p class="text-muted small mb-4 px-4">
                            This project represents a complete dedication to learning full-stack development, 
                            from server-side logic to frontend interactivity.
                        </p>
                        <a href="products.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg" style="background-color: #7C3AED; border-color: #7C3AED;">
                            Explore the Platform
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="container text-center py-5 mb-5">
        <p class="h6 text-uppercase fw-bold mb-3" style="color: #9CA3AF; letter-spacing: 2px;">Thank You</p>
        <h2 class="display-6 fw-bold mb-0" style="color: #E5E7EB;">Created with <i class="bi bi-heart-fill mx-2" style="color: #EF4444;"></i> by the Students</h2>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
    text-align: center;
    position: relative;
    overflow: hidden;
}

.about-hero h1 {
    font-size: 3.5rem;
    font-weight: 800;
    letter-spacing: -2px;
    margin-bottom: 1.5rem;
    color: var(--heritage-indigo);
}

.about-hero p {
    font-size: 1.25rem;
    color: var(--heritage-indigo);
    max-width: 700px;
    margin: 0 auto;
    font-weight: 700;
}

.about-section {
    max-width: 1000px;
    margin: 0 auto 6rem;
    padding: 0 20px;
}

.about-section h2 {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 2.5rem;
    letter-spacing: -1px;
    color: var(--heritage-indigo);
}

.about-section p {
    font-size: 1.15rem;
    line-height: 1.9;
    color: var(--heritage-indigo);
    opacity: 0.85;
}

.project-info {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    padding: 3.5rem;
    border-radius: 2rem;
    box-shadow: var(--card-shadow);
    border: 1px solid var(--border-light);
}

.project-info h3 {
    font-size: 1.75rem;
    margin-bottom: 2rem;
    font-weight: 700;
    color: var(--heritage-indigo);
}

.project-info li {
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(45, 50, 97, 0.05);
    color: var(--heritage-indigo);
}

.project-info li:last-child {
    border-bottom: none;
}

.project-info li strong {
    color: var(--heritage-earth);
    font-weight: 600;
    margin-right: 10px;
}

.team-member {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-light);
    border-radius: 2rem;
    padding: 2.5rem 1.5rem;
    transition: var(--flow-slow);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.team-member:hover {
    transform: translateY(-12px);
    box-shadow: var(--hover-shadow) !important;
}

.team-member h5 {
    font-weight: 700;
    color: var(--heritage-indigo);
    margin-top: 1rem;
}

.team-member .text-primary {
    color: var(--heritage-gold) !important;
    font-weight: 600;
}

@media (max-width: 768px) {
    .about-hero h1 { font-size: 2.5rem; }
    .project-info { padding: 2rem; }
}
</style>

<div class="about-hero p-0 m-0" style="padding: 8rem 0 6rem !important;">
    <div class="container">
        <h1 class="animate__animated animate__fadeInDown"><i class="bi bi-info-circle"></i> About UNIVAULT</h1>
        <p class="animate__animated animate__fadeInUp">A premium e-commerce platform built with PHP for educational excellence</p>
    </div>
</div>

<!-- Namma Service / Features Section -->
<div class="container mb-5" style="position: relative; z-index: 10; margin-top: 2rem;">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="p-5 rounded-4 glass-card border-0 h-100 transition-hover animate__animated animate__zoomIn" style="animation-delay: 0.1s;">
                <div class="text-gold mb-3"><i class="bi bi-flower1 fs-1"></i></div>
                <h5 class="fw-black text-indigo">Heritage Service</h5>
                <p class="text-indigo small mb-0 opacity-75">Local delivery across Tamil Nadu & Kerala. Fast & Secure.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-5 rounded-4 glass-card border-0 h-100 transition-hover animate__animated animate__zoomIn" style="animation-delay: 0.3s;">
                <div class="text-indigo mb-3"><i class="bi bi-shield-lock-fill fs-1"></i></div>
                <h5 class="fw-black text-indigo">Secured for You</h5>
                <p class="text-indigo small mb-0 opacity-75">Every transaction is protected by bank-grade security protocols.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-5 rounded-4 glass-card border-0 h-100 transition-hover animate__animated animate__zoomIn" style="animation-delay: 0.5s;">
                <div class="text-emerald mb-3"><i class="bi bi-headset fs-1"></i></div>
                <h5 class="fw-black text-indigo">24/7 Support</h5>
                <p class="text-indigo small mb-0 opacity-75">Support available in Tamil, Malayalam, and English.</p>
            </div>
        </div>
    </div>
</div>

<div class="about-section">
    <div class="project-info glass-card border-0 rounded-5">
        <h3 class="fw-black text-indigo"><i class="bi bi-file-code"></i> Project Information</h3>
        <ul class="list-unstyled">
            <li><strong>Project Name:</strong> <span class="text-indigo"><?php echo SITE_NAME; ?> - Heritage E-Commerce</span></li>
            <li><strong>Academic Year:</strong> <span class="text-indigo">2nd Year Diploma Excellence</span></li>
            <li><strong>Subject:</strong> <span class="text-indigo">Creative PHP Development</span></li>
            <li><strong>Institution:</strong> <span class="text-indigo">Computer Science Department</span></li>
            <li><strong>Stack:</strong> <span class="badge bg-indigo text-white rounded-0">PHP</span> <span class="badge bg-indigo text-white rounded-0">HTML5</span> <span class="badge bg-indigo text-white rounded-0">CSV</span></li>
        </ul>
    </div>

    <h2 class="text-center fw-bold mb-5"><i class="bi bi-people-fill text-primary"></i> Meet Our Visionaries</h2>

    <!-- Lead Developer Spotlight -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden transition-hover" style="background: #ffffff;">
                <div class="row g-0 align-items-center">
                    <div class="col-md-4 p-5 text-center" style="background: var(--heritage-indigo); position: relative; overflow: hidden;">
                        <div class="bg-kolam position-absolute top-0 left-0 w-100 h-100 opacity-25"></div>
                        <div class="rounded-circle bg-white text-indigo d-inline-flex align-items-center justify-content-center shadow-sm mb-4 position-relative" style="width: 130px; height: 130px; font-size: 3.5rem; opacity: 0.9;">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h4 class="fw-black mb-1 text-white">KS</h4>
                        <span class="badge bg-kolam border border-white text-white rounded-0 px-3 py-2 fw-bold">MASTER DEVELOPER</span>
                    </div>
                    <div class="col-md-8 p-5 bg-kolam">
                        <h3 class="fw-black mb-1 text-indigo">KARUPPASAMY M (KS)</h3>
                        <p class="text-indigo mb-3" style="color: var(--heritage-indigo); font-weight: 800; opacity: 0.7;">Chief Architect & Visionary</p>
                        <p class="text-muted mb-4" style="line-height: 1.8;">
                            Driving the technical vision of Univault with a focus on scalable PHP architecture and secure data systems. 
                            Specializing in DevOps and backend optimization for educational frameworks.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="https://ks-02.vercel.app/" class="btn btn-primary btn-sm rounded-pill px-5 fw-bold shadow-sm" target="_blank">View Portfolio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Senior Developer & Implementation -->
    <div class="row g-4 team-grid mb-5">
        <!-- Backend Support -->
        <div class="col-md-6 col-lg-4">
            <div class="team-member shadow-sm bg-terracotta">
                <div class="text-indigo opacity-75 mb-3"><i class="bi bi-code-slash fs-1"></i></div>
                <h5 class="fw-black mb-1">Nathakumar</h5>
                <p class="text-indigo small fw-bold mb-3">natha backend developer</p>
                <div class="mt-auto d-flex flex-wrap justify-content-center gap-2">
                    <span class="badge bg-indigo text-white rounded-0 px-2">PHP</span>
                    <span class="badge bg-indigo text-white rounded-0 px-2">Logic</span>
                </div>
            </div>
        </div>

        <!-- Frontend/UI Developer 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="team-member shadow-sm bg-kolam">
                <div class="text-indigo opacity-75 mb-3"><i class="bi bi-palette fs-1"></i></div>
                <h5 class="fw-black mb-1">Bharathvaj</h5>
                <p class="text-indigo small fw-bold mb-3">UI/UX Developer</p>
                <div class="mt-auto d-flex flex-wrap justify-content-center gap-2">
                    <span class="badge bg-indigo text-white rounded-0 px-2">Interface</span>
                    <span class="badge bg-indigo text-white rounded-0 px-2">Visuals</span>
                </div>
            </div>
        </div>

        <!-- Frontend/UI Developer 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="team-member shadow-sm bg-kolam">
                <div class="text-indigo opacity-75 mb-3"><i class="bi bi-brush fs-1"></i></div>
                <h5 class="fw-black mb-1">Tamil Arasan</h5>
                <p class="text-indigo small fw-bold mb-3">UI/UX Developer</p>
                <div class="mt-auto d-flex flex-wrap justify-content-center gap-2">
                    <span class="badge bg-indigo text-white rounded-0 px-2">Branding</span>
                    <span class="badge bg-indigo text-white rounded-0 px-2">Motion</span>
                </div>
            </div>
        </div>

        <!-- System Tester -->
        <div class="col-md-6 col-lg-4">
            <div class="team-member shadow-sm">
                <div class="text-indigo opacity-75 mb-3"><i class="bi bi-shield-check fs-1"></i></div>
                <h5 class="fw-black mb-1">Tamil Selvan</h5>
                <p class="text-indigo small fw-bold mb-3">QA Auditor</p>
                <div class="mt-auto d-flex flex-wrap justify-content-center gap-2">
                    <span class="badge bg-indigo text-white rounded-0 px-2">Testing</span>
                    <span class="badge bg-indigo text-white rounded-0 px-2">Security</span>
                </div>
            </div>
        </div>

        <!-- Data Specialist -->
        <div class="col-md-6 col-lg-4">
            <div class="team-member shadow-sm">
                <div class="text-indigo opacity-75 mb-3"><i class="bi bi-database-fill fs-1"></i></div>
                <h5 class="fw-black mb-1">Kishor Kumar</h5>
                <p class="text-indigo small fw-bold mb-3">Data Architect</p>
                <div class="mt-auto d-flex flex-wrap justify-content-center gap-2">
                    <span class="badge bg-indigo text-white rounded-0 px-2">Data</span>
                    <span class="badge bg-indigo text-white rounded-0 px-2">Flow</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="about-section">
    <h2><i class="bi bi-lightbulb"></i> About This Project</h2>
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
    <p>
        This project has been a collaborative effort, with each team member contributing their unique skills and expertise 
        to create a functional and visually appealing e-commerce solution.
    </p>
</div>

<div class="about-section" style="text-align: center; padding: 5rem 0;">
    <h2 class="text-indigo"><i class="bi bi-heart opacity-50"></i> With Heart & Soul</h2>
    <p class="text-muted mb-5">
        A tribute to craftsmanship and the digital fusion of tradition. 
        Thank you for journeying through UNIVAULT.
    </p>
    <a href="products.php" class="btn btn-primary px-5 btn-lg">
        Explore Collections
    </a>
</div>

<?php include 'includes/footer.php'; ?>
