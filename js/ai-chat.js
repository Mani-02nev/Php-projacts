document.addEventListener('DOMContentLoaded', () => {
    // --- 1. UI ELEMENTS ---
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatMessages = document.getElementById('chatMessages');
    const productList = document.getElementById('productList');
    const typingIndicator = document.getElementById('typingIndicator');
    const resetBtn = document.getElementById('resetChatBtnDesktop');
    const emptyStatePanel = document.getElementById('emptyStatePanel');

    // Chat Session Management
    let conversationState = {
        step: 'init',
        category: null,
        budget: null,
        usage: null
    };

    // Auto reset timer block
    let autoResetTimer = null;

    // --- 2. THEME AND LAYOUT INITIALIZATION ---
    // Make sure we snap to top immediately upon open
    chatMessages.scrollTop = 0;

    // --- 3. EVENT LISTENERS ---
    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', resetConversation);
    }

    // --- 4. CORE CHAT LOGIC ---
    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        // User Action
        addUserMessage(text);
        chatInput.value = '';

        // AI Reaction
        showTyping(true);

        try {
            // API Call (Fallback compatible)
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

                // If Product recommendations returned
                if (data.products && data.products.length > 0) {
                    // Maximum of 2 products per requirement
                    updateProductPanel(data.products.slice(0, 2));
                }

                // Check conversation end
                if (conversationState.step === 'init') {
                    handleConversationEnd();
                }

            }, 600); // Smooth delay
        } catch (error) {
            console.error('Error:', error);
            showTyping(false);
            addBotMessage("I'm having trouble analyzing right now. Let's start over.");
            setTimeout(resetConversation, 3000);
        }
    }

    // --- 5. AUTOMATED FLOWS ---
    function handleConversationEnd() {
        // Conversation complete event
        chatInput.disabled = true;
        sendBtn.disabled = true;
        chatInput.placeholder = "Session complete...";

        // Show reset button in header
        if (resetBtn) resetBtn.style.display = 'inline-block';

        // Auto restart after 5 seconds if idle
        autoResetTimer = setTimeout(() => {
            resetConversation();
        }, 5000);
    }

    function resetConversation() {
        if (autoResetTimer) clearTimeout(autoResetTimer);

        // 1. Fade old messages
        chatMessages.style.opacity = '0';

        setTimeout(() => {
            // 2. Clear Chat Container
            chatMessages.innerHTML = `
                <div class="message bot">
                    <div class="message-content">
                        Hello again! I'm ready for a new search. ✨<br><br>
                        What are you looking to buy this time?
                    </div>
                </div>
            `;

            // 3. Reset State & Panel
            conversationState = {
                step: 'init',
                category: null,
                budget: null,
                usage: null
            };

            productList.innerHTML = `
                <div class="empty-state text-center" id="emptyStatePanel">
                    <div class="empty-icon-ring mb-3 mx-auto">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Awaiting Parameters</h6>
                    <p class="text-secondary small">Recommendations will appear here once I understand your needs.</p>
                </div>
            `;

            // 4. Re-enable Input
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.value = '';
            chatInput.placeholder = "Describe what you're looking for...";

            if (resetBtn) resetBtn.style.display = 'none';

            // 5. Scroll to Top Smoothly
            chatMessages.scrollTo({
                top: 0,
                behavior: "smooth"
            });
            chatMessages.style.opacity = '1';

            // Focus smoothly
            setTimeout(() => { chatInput.focus() }, 500);

        }, 400); // Wait for CSS fade out
    }

    // --- 6. UTILITY UI FUNCTIONS ---

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'message user';
        // XSS safe rendering
        div.innerHTML = `<div class="message-content">${escapeHtml(text)}</div>`;
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function addBotMessage(htmlContent) {
        const div = document.createElement('div');
        div.className = 'message bot';
        div.innerHTML = `<div class="message-content">${htmlContent}</div>`;
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function showTyping(show) {
        if (show) {
            typingIndicator.style.display = 'flex';
        } else {
            typingIndicator.style.display = 'none';
        }
        // Force scroll when indicator appears
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTo({
            top: chatMessages.scrollHeight,
            behavior: "smooth"
        });
    }

    function updateProductPanel(products) {
        // Clear empty state
        productList.innerHTML = '';

        products.forEach((product, index) => {
            const isBest = index === 0;
            const tagLabel = isBest ? 'Best Match' : 'Alternative';
            const tagClass = isBest ? 'tag-best' : 'tag-alt';
            const cardClass = isBest ? 'best-choice' : '';

            // Example static reasoning (would normally come from API)
            const reasonIcon = isBest ? '<i class="bi bi-star-fill text-warning me-1"></i>' : '<i class="bi bi-check-circle-fill text-success me-1"></i>';
            const productReason = product.reason || (isBest ? 'Perfectly fits your budget and primary use case.' : 'A solid secondary option with slightly different features.');

            const card = document.createElement('div');
            card.className = `ai-product-card ${cardClass}`;
            card.style.animationDelay = `${index * 0.15}s`;

            card.innerHTML = `
                <img src="${product.image}" alt="${escapeHtml(product.name)}" class="card-img" onerror="this.src='assets/images/placeholder_product.png'">
                <div class="card-info">
                    <span class="card-tag ${tagClass}">${tagLabel}</span>
                    <div class="card-title">${escapeHtml(product.name)}</div>
                    <div class="card-price">₹${parseInt(product.price).toLocaleString()}</div>
                    <div class="card-reason mt-1">${reasonIcon} <span>${escapeHtml(productReason)}</span></div>
                    <a href="product-detail.php?id=${product.id}" class="btn btn-sm ${isBest ? 'btn-primary shadow-sm' : 'btn-outline-secondary'} rounded-pill mt-3 px-4 fw-bold align-self-start" target="_blank">
                        View Details
                    </a>
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
