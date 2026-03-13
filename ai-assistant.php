<?php
$page_title = "AI Shopping Assistant";
require_once 'includes/header.php';
?>

<!-- AI Chat Specific CSS -->
<link rel="stylesheet" href="css/ai-chat.css">

<div class="container-fluid p-0">
    <div class="ai-page-container">
        <div class="ai-main-grid">
            
            <!-- LEFT: Chat Interface (65%) -->
            <div class="chat-section">
                
                <!-- Header of Chat -->
                <div class="chat-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ai-avatar pulse-glow">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Univault Assistant</h5>
                            <small class="text-secondary"><i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> Online & Ready</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill fw-bold bg-white text-dark shadow-sm px-3" id="resetChatBtnDesktop" style="display: none;">
                        <i class="bi bi-arrow-clockwise me-1"></i> Start New Search
                    </button>
                </div>

                <!-- Messages Area -->
                <div class="chat-messages" id="chatMessages">
                    <div class="message bot">
                        <div class="message-content">
                            Hello! I'm your Univault AI Assistant. ✨<br><br>
                            I can help you find exactly what you need without endless searching.<br>
                            <span class="text-muted mt-2 d-block" style="font-size: 0.9em;">Try saying: "I need a fast laptop for graphic design under 80000"</span>
                        </div>
                    </div>
                </div>

                <!-- Sticky Input Area -->
                <div class="chat-input-area">
                    <!-- Thinking Indicator overlays input -->
                    <div class="thinking-indicator" id="typingIndicator">
                        <div class="d-flex align-items-center gap-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span>AI analyzing your request...</span>
                        </div>
                    </div>

                    <div class="input-glass-wrapper">
                        <input type="text" id="chatInput" placeholder="Describe what you're looking for..." autocomplete="off">
                        <button class="send-btn shadow-sm" id="sendBtn" title="Send message">
                            <i class="bi bi-arrow-up"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: External Intelligence & Recommendations (35%) -->
            <div class="intelligence-panel">
                <div class="panel-header text-center">
                    <span class="badge rounded-pill fw-bold px-3 py-2" style="background: rgba(124, 58, 237, 0.1); color: var(--ai-primary); border: 1px solid rgba(124, 58, 237, 0.2);">
                        AI RECOMMENDATIONS
                    </span>
                </div>
                
                <div id="productList" class="recommendations-container">
                    <!-- Empty State -->
                    <div class="empty-state text-center" id="emptyStatePanel">
                        <div class="empty-icon-ring mb-3 mx-auto">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Awaiting Parameters</h6>
                        <p class="text-secondary small">Recommendations will appear here once I understand your needs.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- AI Chat Logic -->
<script src="js/ai-chat.js"></script>

<?php require_once 'includes/footer.php'; ?>
