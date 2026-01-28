<?php
$page_title = "AI Shopping Assistant";
require_once 'includes/header.php';
?>

<!-- Three.js specific for this page -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<!-- AI Chat Specific CSS -->
<link rel="stylesheet" href="css/ai-chat.css">

<div class="container-fluid p-0">
    <div class="ai-page-container">
        <div class="ai-main-grid">
            
            <!-- LEFT: Chat Interface (65%) -->
            <div class="chat-section">
                <!-- Messages Area -->
                <div class="chat-messages" id="chatMessages">
                    <div class="message bot">
                        <div class="message-content">
                            Hello! I'm your Univault AI Assistant. 🧠<br>
                            I can analyze products to find exactly what you need.<br>
                            <i>Try asking: "Best gaming laptop under 100k"</i>
                        </div>
                    </div>
                    <!-- Typing Indicator -->
                    <div class="typing-indicator" id="typingIndicator">
                        <span>AI Analyzing</span>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>

                <!-- Sticky Input Area -->
                <div class="chat-input-area">
                    <div class="input-wrapper">
                        <input type="text" id="chatInput" placeholder="Describe what you're looking for..." autocomplete="off">
                        <button class="send-btn" id="sendBtn">
                            <i class="bi bi-arrow-up-short"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Intelligence & Recommendations (35%) -->
            <div class="intelligence-panel">
                
                <!-- 3D Core Container -->
                <div id="ai-core-canvas" class="ai-core-canvas"></div>
                
                <!-- Product Recommendations Overlay -->
                <div class="recommendations-overlay">
                    <div class="panel-header">
                        <small>AI RECOMMENDATIONS</small>
                    </div>
                    
                    <div id="productList">
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="bi bi-cpu"></i>
                            <p>Waiting for data...</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- AI Chat Logic -->
<script src="js/ai-chat.js"></script>

<?php require_once 'includes/footer.php'; ?>
