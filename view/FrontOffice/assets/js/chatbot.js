// Chatbot pour guider les donnateurs
class DonationChatbot {
    constructor() {
        this.currentStep = 0;
        this.donationData = {};
        this.categories = [];
        this.init();
    }

    init() {
        this.createChatbotUI();
        this.loadCategories();
        this.attachEventListeners();
    }

    createChatbotUI() {
        const chatbotHTML = `
            <div class="chatbot-container">
                <button class="chatbot-toggle" id="chatbot-toggle">
                    🤖
                    <span class="chatbot-badge">1</span>
                </button>
                
                <div class="chatbot-window" id="chatbot-window">
                    <div class="chatbot-header">
                        <div class="chatbot-header-content">
                            <div class="chatbot-avatar">
                                🤖
                            </div>
                            <div class="chatbot-header-text">
                                <h3>EcoBot 🌱</h3>
                                <p>Assistant de don intelligent</p>
                            </div>
                        </div>
                        <button class="chatbot-close" id="chatbot-close">×</button>
                    </div>
                    
                    <div class="chatbot-messages" id="chatbot-messages"></div>
                    
                    <div class="chat-input-container" id="chat-input-container" style="display: none;">
                        <div class="chat-input-wrapper">
                            <input type="text" class="chat-input" id="chat-input" placeholder="Tapez votre réponse...">
                            <button class="chat-send-btn" id="chat-send-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
    }

    loadCategories() {
        const typeSelect = document.getElementById('type-don');
        if (typeSelect) {
            const options = typeSelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.value) {
                    this.categories.push({
                        code: option.value,
                        nom: option.textContent,
                        description: option.title || ''
                    });
                }
            });
        }
    }

    attachEventListeners() {
        const toggle = document.getElementById('chatbot-toggle');
        const close = document.getElementById('chatbot-close');
        const window = document.getElementById('chatbot-window');
        const sendBtn = document.getElementById('chat-send-btn');
        const input = document.getElementById('chat-input');

        toggle.addEventListener('click', () => {
            window.classList.toggle('active');
            if (window.classList.contains('active') && this.currentStep === 0) {
                this.startConversation();
            }
            const badge = toggle.querySelector('.chatbot-badge');
            if (badge) badge.style.display = 'none';
        });

        close.addEventListener('click', () => {
            window.classList.remove('active');
        });

        sendBtn.addEventListener('click', () => this.handleUserInput());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.handleUserInput();
        });
    }

    addMessage(text, isBot = true, options = null) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${isBot ? 'bot' : 'user'}`;
        
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.textContent = text;
        
        messageDiv.appendChild(bubble);
        messagesContainer.appendChild(messageDiv);

        if (options && isBot) {
            const optionsDiv = document.createElement('div');
            optionsDiv.className = 'chat-options';
            
            options.forEach(option => {
                const btn = document.createElement('button');
                btn.className = 'chat-option-btn';
                btn.textContent = option.text;
                
                if (option.type) {
                    btn.setAttribute('data-type', option.type);
                }
                
                btn.onclick = () => {
                    option.action();
                    setTimeout(() => this.scrollToBottom(), 100);
                };
                optionsDiv.appendChild(btn);
            });
            
            messageDiv.appendChild(optionsDiv);
        }

        this.scrollToBottom();
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbot-messages');
        setTimeout(() => {
            messagesContainer.scrollTo({
                top: messagesContainer.scrollHeight + 200,
                behavior: 'smooth'
            });
        }, 100);
    }

    showTyping() {
        const messagesContainer = document.getElementById('chatbot-messages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTyping() {
        const typing = document.getElementById('typing-indicator');
        if (typing) typing.remove();
    }

    async delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async startConversation() {
        await this.delay(500);
        this.showTyping();
        await this.delay(1000);
        this.hideTyping();
        
        this.addMessage("Bonjour ! 👋 Je suis votre assistant intelligent pour faire un don à EcoMind.");
        
        await this.delay(800);
        this.showTyping();
        await this.delay(1000);
        this.hideTyping();
        
        this.addMessage("Je vais vous guider étape par étape. Commençons ! ✨");
        
        await this.delay(500);
        this.askDonationType();
    }

    askDonationType() {
        this.currentStep = 1;
        
        const options = this.categories.map(cat => ({
            text: cat.nom,
            action: () => this.selectDonationType(cat.code, cat.nom)
        }));

        this.addMessage("Quel type de don souhaitez-vous faire ? 🌱", true, options);
    }

    async selectDonationType(code, nom) {
        this.donationData.type_don = code;
        this.addMessage(nom, false);
        
        document.getElementById('type-don').value = code;
        document.getElementById('type-don').dispatchEvent(new Event('change'));
        
        await this.delay(500);
        this.scrollToBottom();
        this.showTyping();
        await this.delay(800);
        this.hideTyping();
        
        if (code === 'money') {
            this.askAmount();
        } else {
            this.askCity();
        }
        
        this.scrollToBottom();
    }

    async askAmount() {
        this.currentStep = 2;
        const minAmount = window.CHATBOT_CONFIG?.minDonationAmount || 50;
        const currency = window.CHATBOT_CONFIG?.currency || 'TND';
        this.addMessage(`💰 Quel montant souhaitez-vous donner ? (Minimum ${minAmount} ${currency})`, true);
        await this.delay(300);
        this.scrollToBottom();
        this.showInput();
    }

    async askCity() {
        this.currentStep = 10;
        this.addMessage("🏙️ Dans quelle ville êtes-vous ?", true);
        await this.delay(300);
        this.scrollToBottom();
        this.showInput();
    }

    async askPostalCode() {
        this.currentStep = 11;
        this.addMessage("📮 Quel est votre code postal ? (4 chiffres)", true);
        await this.delay(300);
        this.scrollToBottom();
        this.showInput();
    }

    async askPhone() {
        this.currentStep = 12;
        this.addMessage("📱 Quel est votre numéro de téléphone ? (8 chiffres)", true);
        await this.delay(300);
        this.scrollToBottom();
        this.showInput();
    }

    async askDescription() {
        this.currentStep = 13;
        this.addMessage("📝 Pouvez-vous décrire votre don ? (minimum 10 caractères)", true);
        await this.delay(300);
        this.scrollToBottom();
        this.showInput();
    }

    async askAssociation() {
        this.currentStep = 20;
        
        const associationSelect = document.getElementById('association');
        const options = [];
        
        associationSelect.querySelectorAll('option').forEach(option => {
            if (option.value) {
                options.push({
                    text: option.textContent,
                    type: 'association',
                    action: () => this.selectAssociation(option.value, option.textContent)
                });
            }
        });

        this.addMessage("Quelle association souhaitez-vous soutenir ? 🏢", true, options);
        await this.delay(300);
        this.scrollToBottom();
    }

    async selectAssociation(id, nom) {
        this.donationData.association_id = id;
        this.addMessage(nom, false);
        
        document.getElementById('association').value = id;
        
        await this.delay(500);
        this.scrollToBottom();
        this.showTyping();
        await this.delay(800);
        this.hideTyping();
        this.askEmail();
        this.scrollToBottom();
    }

    async askEmail() {
        this.currentStep = 21;
        this.addMessage("📧 Quelle est votre adresse email ?", true);
        await this.delay(300);
        this.scrollToBottom();
        this.showInput();
    }

    showInput() {
        document.getElementById('chat-input-container').style.display = 'block';
        document.getElementById('chat-input').focus();
    }

    hideInput() {
        document.getElementById('chat-input-container').style.display = 'none';
        document.getElementById('chat-input').value = '';
    }

    async handleUserInput() {
        const input = document.getElementById('chat-input');
        const value = input.value.trim();
        
        if (!value) return;
        
        this.addMessage(value, false);
        this.hideInput();
        
        await this.delay(500);
        this.scrollToBottom();
        this.showTyping();
        await this.delay(800);
        this.hideTyping();
        this.scrollToBottom();
        
        switch (this.currentStep) {
            case 2:
                const minAmount = window.CHATBOT_CONFIG?.minDonationAmount || 50;
                const currency = window.CHATBOT_CONFIG?.currency || 'TND';
                if (parseFloat(value) >= minAmount) {
                    this.donationData.montant = value;
                    document.getElementById('custom-amount').value = value;
                    await this.delay(300);
                    this.scrollToBottom();
                    this.askAssociation();
                } else {
                    this.addMessage(`Le montant minimum est de ${minAmount} ${currency}. Veuillez réessayer.`, true);
                    await this.delay(300);
                    this.scrollToBottom();
                    this.showInput();
                }
                break;
                
            case 10:
                if (value.length >= 2) {
                    this.donationData.ville = value;
                    document.getElementById('ville-autre').value = value;
                    await this.delay(300);
                    this.scrollToBottom();
                    this.askPostalCode();
                } else {
                    this.addMessage("Veuillez entrer une ville valide.", true);
                    await this.delay(300);
                    this.scrollToBottom();
                    this.showInput();
                }
                break;
                
            case 11:
                if (/^\d{4}$/.test(value)) {
                    this.donationData.cp = value;
                    document.getElementById('cp-autre').value = value;
                    await this.delay(300);
                    this.scrollToBottom();
                    this.askPhone();
                } else {
                    this.addMessage("Le code postal doit contenir 4 chiffres.", true);
                    await this.delay(300);
                    this.scrollToBottom();
                    this.showInput();
                }
                break;
                
            case 12:
                if (/^\d{8}$/.test(value)) {
                    this.donationData.tel = value;
                    document.getElementById('tel-autre').value = value;
                    await this.delay(300);
                    this.scrollToBottom();
                    this.askDescription();
                } else {
                    this.addMessage("Le téléphone doit contenir 8 chiffres.", true);
                    await this.delay(300);
                    this.scrollToBottom();
                    this.showInput();
                }
                break;
                
            case 13:
                if (value.length >= 10) {
                    this.donationData.description = value;
                    document.getElementById('description-don').value = value;
                    await this.delay(300);
                    this.scrollToBottom();
                    this.askAssociation();
                } else {
                    this.addMessage("La description doit contenir au moins 10 caractères.", true);
                    await this.delay(300);
                    this.scrollToBottom();
                    this.showInput();
                }
                break;
                
            case 21:
                if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    this.donationData.email = value;
                    document.getElementById('email').value = value;
                    await this.delay(300);
                    this.scrollToBottom();
                    this.finishConversation();
                } else {
                    this.addMessage("Veuillez entrer un email valide.", true);
                    await this.delay(300);
                    this.scrollToBottom();
                    this.showInput();
                }
                break;
        }
        
        await this.delay(100);
        this.scrollToBottom();
    }

    async finishConversation() {
        this.addMessage("Parfait ! J'ai rempli le formulaire pour vous. 🎉", true);
        
        await this.delay(1000);
        this.showTyping();
        await this.delay(1000);
        this.hideTyping();
        
        const options = [{
            text: "Valider mon don",
            type: 'validate',
            action: () => this.submitForm()
        }];
        
        this.addMessage("Vous pouvez maintenant valider votre don ! 🌟", true, options);
    }

    submitForm() {
        this.addMessage("Validation en cours...", false);
        document.getElementById('chatbot-window').classList.remove('active');
        
        setTimeout(() => {
            document.getElementById('submit-btn').click();
        }, 500);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DonationChatbot();
});
