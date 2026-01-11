<?php
$page_title = 'About';
require_once 'includes/header.php';
?>

<style>
.about-hero {
    background: transparent;
    color: var(--heritage-indigo);
    padding: 6rem 0 4rem;
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
                            <a href="#" class="btn btn-primary btn-sm rounded-pill px-5 fw-bold shadow-sm">View Portfolio</a>
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
