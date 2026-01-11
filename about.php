<?php
$page_title = 'About';
require_once 'includes/header.php';
?>

<style>
.about-hero {
    background: linear-gradient(135deg, var(--black) 0%, var(--gray-800) 100%);
    color: var(--white);
    padding: 4rem 0;
    text-align: center;
    margin-bottom: 3rem;
}

.about-hero h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.about-hero p {
    font-size: 1.2rem;
    color: var(--gray-300);
    max-width: 800px;
    margin: 0 auto;
}

.about-section {
    max-width: 1000px;
    margin: 0 auto 4rem;
    padding: 0 20px;
}

.about-section h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
}

.about-section p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--gray-700);
    margin-bottom: 1rem;
}

.project-info {
    background: var(--gray-100);
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 3rem;
}

.project-info h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--black);
}

.project-info ul {
    list-style: none;
    padding: 0;
}

.project-info li {
    padding: 0.5rem 0;
    font-size: 1.05rem;
    color: var(--gray-700);
}

.project-info li strong {
    color: var(--black);
    font-weight: 600;
}

.team-section {
    margin-top: 3rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.team-member {
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: var(--transition);
}

.team-member:hover {
    border-color: var(--black);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
}

.team-member-icon {
    width: 80px;
    height: 80px;
    background: var(--black);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1rem;
}

.team-member h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--black);
}

.team-member .role {
    color: var(--gray-600);
    font-size: 0.95rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.team-member .badge {
    display: inline-block;
    background: var(--gray-200);
    color: var(--black);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.lead-badge {
    background: var(--black) !important;
    color: var(--white) !important;
}

@media (max-width: 768px) {
    .about-hero h1 {
        font-size: 2rem;
    }
    
    .team-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="about-hero">
    <div class="container">
        <h1><i class="bi bi-info-circle"></i> About UNIVAULT</h1>
        <p>A modern e-commerce platform built with PHP for educational purposes</p>
    </div>
</div>

<div class="about-section">
    <div class="project-info">
        <h3><i class="bi bi-file-code"></i> Project Information</h3>
        <ul>
            <li><strong>Project Name:</strong> UNIVAULT - E-Commerce Platform</li>
            <li><strong>Academic Year:</strong> 2nd Year Diploma Project</li>
            <li><strong>Subject:</strong> PHP Web Development</li>
            <li><strong>Institution:</strong> Diploma in Computer Science</li>
            <li><strong>Technology Stack:</strong> PHP, HTML5, CSS3, JavaScript, CSV Database</li>
            <li><strong>Features:</strong> Product Catalog, Shopping Cart, User Authentication, Admin Panel</li>
        </ul>
    </div>

    <h2><i class="bi bi-people"></i> Our Team</h2>
    <p style="text-align: center; margin-bottom: 2rem;">
        A dedicated team of students working together to create this e-commerce platform
    </p>

    <div class="team-grid">
        <!-- Team Lead & Main Developer -->
        <div class="team-member">
            <div class="team-member-icon">
                <i class="bi bi-person-badge"></i>
            </div>
            <h3>KARUPPASAMY M(KS)</h3>
            <div class="role">Project Lead</div>
            <div class="role">Backend  • DevOps • Architect</div>
            <span class="badge lead-badge">Team Lead</span>
        </div>

        <!-- Backend Support -->
        <div class="team-member">
            <div class="team-member-icon">
                <i class="bi bi-code-slash"></i>
            </div>
            <h3>Nathakumar</h3>
            <!-- <div class="role">Backend Developer</div> -->
            <div class="role">Backend Support & Development</div>
            <span class="badge">Developer</span>
        </div>

        <!-- Frontend/UI Developer -->
        <div class="team-member">
            <div class="team-member-icon">
                <i class="bi bi-palette"></i>
            </div>
            <h3>Bharathvaj</h3>
            <!-- <div class="role">Frontend Developer</div> -->
            <div class="role">UI/UX Design & Implementation</div>
            <span class="badge">Designer</span>
        </div>

        <!-- Logo Designer -->
        <div class="team-member">
            <div class="team-member-icon">
                <i class="bi bi-brush"></i>
            </div>
            <h3>Tamil Arasan</h3>
            <!-- <div class="role">Graphic Designer</div> -->
            <div class="role">Logo & Icon Design</div>
            <span class="badge">Designer</span>
        </div>

        <!-- Tester -->
        <div class="team-member">
            <div class="team-member-icon">
                <i class="bi bi-bug"></i>
            </div>
            <h3>Tamil Selvan</h3>
            <!-- <div class="role">Quality Assurance</div> -->
            <div class="role">Testing & Bug Reporting</div>
            <span class="badge">Tester</span>
        </div>

        <!-- Data Collector -->
        <div class="team-member">
            <div class="team-member-icon">
                <i class="bi bi-database"></i>
            </div>
            <h3>Kishor Kumar</h3>
            <!-- <div class="role">Data Manager</div> -->
            <div class="role">Product Data Collection</div>
            <span class="badge">Data Analyst</span>
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

<div class="about-section" style="text-align: center; padding: 3rem 0;">
    <h2><i class="bi bi-heart"></i> Thank You</h2>
    <p>
        Thank you for visiting UNIVAULT. This project represents our dedication to learning and applying 
        web development concepts in a real-world scenario.
    </p>
    <a href="products.php" class="btn btn-black" style="margin-top: 1rem;">
        <i class="bi bi-grid"></i> Browse Products
    </a>
</div>

<?php include 'includes/footer.php'; ?>
