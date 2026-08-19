<div id="ai-chat-widget" class="ai-chat-widget">
    <!-- Floating Button -->
    <button id="ai-chat-toggle" class="ai-chat-toggle-btn" aria-label="Abrir asistente IA">
        <span class="ai-chat-btn-icon">🤖</span>
        <span class="ai-chat-btn-text">Mi IA</span>
        <span class="ai-chat-pulse"></span>
    </button>

    <!-- Chat Window -->
    <div id="ai-chat-window" class="ai-chat-window">
        <!-- Header -->
        <div class="ai-chat-header">
            <div class="ai-chat-header-info">
                <span class="ai-chat-header-icon">🤖</span>
                <div>
                    <h6 class="ai-chat-title">Tu Asistente IA</h6>
                    <small class="ai-chat-subtitle">En linea - lista para ayudarte</small>
                </div>
            </div>
            <div class="ai-chat-header-actions">
                <button id="ai-chat-minimize" class="ai-chat-header-btn" aria-label="Minimizar" title="Minimizar">
                    <i class="bi bi-dash"></i>
                </button>
                <button id="ai-chat-close" class="ai-chat-header-btn" aria-label="Cerrar" title="Cerrar">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <!-- Chat Body -->
        <div id="ai-chat-body" class="ai-chat-body">
            <!-- Welcome Message -->
            <div id="ai-welcome" class="ai-welcome">
                <div class="ai-welcome-icon">👋</div>
                <h5>Hola, {{ auth()->user()->name ?? 'Usuario' }}!</h5>
                <p class="ai-welcome-text">
                    Estoy aqui para ayudarte a consultar la informacion de tu negocio.<br>
                    ¿Que te gustaria saber?
                </p>
                <div class="ai-suggestions">
                    <button class="ai-suggestions-btn" data-prompt="¿Cuánto vendimos hoy?">
                        <i class="bi bi-graph-up"></i> Ventas de hoy
                    </button>
                    <button class="ai-suggestions-btn" data-prompt="¿Que productos tienen stock bajo?">
                        <i class="bi bi-box-seam"></i> Inventario
                    </button>
                    <button class="ai-suggestions-btn" data-prompt="Listar clientes activos">
                        <i class="bi bi-people"></i> Clientes
                    </button>
                    <button class="ai-suggestions-btn" data-prompt="¿Cuánto se debe a cuentas por cobrar?">
                        <i class="bi bi-cash-stack"></i> Cuentas por cobrar
                    </button>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="ai-messages" class="ai-messages"></div>
        </div>

        <!-- Chat Input -->
        <div id="ai-chat-input-area" class="ai-chat-input-area">
            <form id="ai-chat-form" class="ai-chat-form">
                <input
                    type="text"
                    id="ai-chat-input"
                    class="ai-chat-input"
                    placeholder="Escribe tu pregunta..."
                    autocomplete="off"
                    aria-label="Escribe tu pregunta al asistente IA"
                >
                <button type="submit" id="ai-chat-send" class="ai-chat-send-btn" aria-label="Enviar mensaje">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>

        <!-- Typing Indicator -->
        <div id="ai-typing" class="ai-typing-indicator" style="display: none;">
            <div class="ai-typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <small class="ai-typing-text">Escribiendo...</small>
        </div>
    </div>
</div>

<style>
.ai-chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Figtree', sans-serif;
}

/* Floating Button */
.ai-chat-toggle-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 50px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    transition: all 0.3s ease;
    position: relative;
    z-index: 10001;
}

.ai-chat-toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(56, 189, 248, 0.4);
}

.ai-chat-toggle-btn:hover .ai-chat-btn-text {
    display: inline;
}

.ai-chat-btn-icon {
    font-size: 20px;
}

.ai-chat-btn-text {
    display: none;
}

@media (min-width: 768px) {
    .ai-chat-btn-text {
        display: inline;
    }
}

.ai-chat-pulse {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50px;
    border: 2px solid rgba(56, 189, 248, 0.5);
    animation: ai-pulse 2s ease-in-out infinite;
}

@keyframes ai-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.6;
    }
}

/* Chat Window */
.ai-chat-window {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 30px);
    height: 500px;
    max-height: calc(100vh - 100px);
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s ease;
}

.ai-chat-window.ai-chat-open {
    display: flex;
    opacity: 1;
    transform: translateY(0) scale(1);
}

body.dark-mode .ai-chat-window {
    background: #1e293b;
    border-color: #334155;
}

/* Header */
.ai-chat-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.ai-chat-header-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ai-chat-header-icon {
    font-size: 24px;
}

.ai-chat-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.ai-chat-subtitle {
    color: #94a3b8;
    font-size: 11px;
}

.ai-chat-header-actions {
    display: flex;
    gap: 4px;
}

.ai-chat-header-btn {
    background: transparent;
    color: #fff;
    border: none;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 16px;
}

.ai-chat-header-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Body */
.ai-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    scroll-behavior: smooth;
}

.ai-welcome {
    text-align: center;
    padding: 30px 20px;
}

.ai-welcome-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.ai-welcome h5 {
    color: #1e293b;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

body.dark-mode .ai-welcome h5 {
    color: #f1f5f9;
}

.ai-welcome-text {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 20px;
    line-height: 1.5;
}

body.dark-mode .ai-welcome-text {
    color: #94a3b8;
}

.ai-suggestions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.ai-suggestions-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

body.dark-mode .ai-suggestions-btn {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
}

.ai-suggestions-btn:hover {
    background: #eff6ff;
    border-color: #38bdf8;
    color: #0284c7;
}

body.dark-mode .ai-suggestions-btn:hover {
    background: #1e293b;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Messages */
.ai-messages {
    display: none;
    flex-direction: column;
    gap: 12px;
}

.ai-messages.ai-messages-visible {
    display: flex;
}

.ai-message {
    display: flex;
    gap: 8px;
    animation: ai-message-slide-in 0.3s ease;
}

@keyframes ai-message-slide-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.ai-message-user {
    flex-direction: row-reverse;
}

.ai-message-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.ai-message-assistant .ai-message-avatar {
    background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
    color: #fff;
}

.ai-message-user .ai-message-avatar {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #fff;
}

.ai-message-content {
    max-width: 80%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.5;
    word-wrap: break-word;
}

.ai-message-assistant .ai-message-content {
    background: #f1f5f9;
    color: #1e293b;
    border-top-left-radius: 4px;
}

body.dark-mode .ai-message-assistant .ai-message-content {
    background: #0f172a;
    color: #f1f5f9;
    border-color: #334155;
}

.ai-message-user .ai-message-content {
    background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
    color: #fff;
    border-top-right-radius: 4px;
}

/* Input area */
.ai-chat-input-area {
    border-top: 1px solid #e2e8f0;
    padding: 12px;
    background: #fafbfc;
}

body.dark-mode .ai-chat-input-area {
    background: #0f172a;
    border-color: #334155;
}

.ai-chat-form {
    display: flex;
    gap: 8px;
    align-items: center;
}

.ai-chat-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 25px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s;
}

.ai-chat-input:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
}

body.dark-mode .ai-chat-input {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
}

body.dark-mode .ai-chat-input:focus {
    border-color: #38bdf8;
}

.ai-chat-send-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
    flex-shrink: 0;
}

.ai-chat-send-btn:hover {
    transform: scale(1.05);
}

.ai-chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Typing indicator */
.ai-typing-indicator {
    position: absolute;
    bottom: 60px;
    left: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.95);
    padding: 10px 14px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

body.dark-mode .ai-typing-indicator {
    background: rgba(30, 41, 59, 0.95);
}

.ai-typing-dots {
    display: flex;
    gap: 4px;
}

.ai-typing-dots span {
    width: 6px;
    height: 6px;
    background: #64748b;
    border-radius: 50%;
    animation: ai-typing-dot 1.4s ease-in-out infinite;
}

.ai-typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.ai-typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes ai-typing-dot {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-6px);
        opacity: 1;
    }
}

.ai-typing-text {
    color: #64748b;
    font-size: 11px;
    font-style: italic;
}

/* Error state */
.ai-chat-error {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 12px;
    display: none;
}

body.dark-mode .ai-chat-error {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}

/* Mobile responsive */
@media (max-width: 480px) {
    .ai-chat-widget {
        bottom: 10px;
        right: 10px;
        left: 10px;
    }

    .ai-chat-window {
        width: 100%;
        right: 0;
        bottom: 50px;
        height: calc(100vh - 60px);
        max-height: 100vh;
        border-radius: 16px 16px 0 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const API_BASE = "/api/ai";

    // Debug: log API base in console
    console.log('[AI Chat] API Base:', API_BASE);

    const widget = document.getElementById('ai-chat-widget');
    if (!widget) return;

    const toggleBtn = document.getElementById('ai-chat-toggle');
    const chatWindow = document.getElementById('ai-chat-window');
    const chatBody = document.getElementById('ai-chat-body');
    const welcome = document.getElementById('ai-welcome');
    const messagesContainer = document.getElementById('ai-messages');
    const form = document.getElementById('ai-chat-form');
    const input = document.getElementById('ai-chat-input');
    const sendBtn = document.getElementById('ai-chat-send');
    const typingIndicator = document.getElementById('ai-typing');
    const minimizeBtn = document.getElementById('ai-chat-minimize');
    const closeBtn = document.getElementById('ai-chat-close');

    let currentConversationId = null;
    let isProcessing = false;
    let abortController = null;

    function openChat() {
        chatWindow.classList.add('ai-chat-open');
        input.focus();
    }

    function closeChat() {
        chatWindow.classList.remove('ai-chat-open');
    }

    function toggleChat() {
        if (chatWindow.classList.contains('ai-chat-open')) {
            closeChat();
        } else {
            openChat();
        }
    }

    function scrollToBottom() {
        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 50);
    }

    function showTyping(show) {
        typingIndicator.style.display = show ? 'flex' : 'none';
        if (show) {
            scrollToBottom();
        }
    }

    function addMessage(role, content, isHtml = false) {
        if (welcome) {
            welcome.style.display = 'none';
        }
        messagesContainer.classList.add('ai-messages-visible');

        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-message ai-message-${role}`;

        const avatar = document.createElement('div');
        avatar.className = 'ai-message-avatar';
        avatar.textContent = role === 'assistant' ? '🤖' : '👤';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'ai-message-content';

        if (isHtml) {
            contentDiv.innerHTML = content;
        } else {
            contentDiv.textContent = content;
            contentDiv.style.whiteSpace = 'pre-wrap';
        }

        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();

        return contentDiv;
    }

    function showError(message) {
        let errorDiv = document.getElementById('ai-chat-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'ai-chat-error';
            errorDiv.className = 'ai-chat-error';
            chatBody.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        scrollToBottom();
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    }

    async function sendMessage(text) {
        if (!text.trim() || isProcessing) return;

        isProcessing = true;
        addMessage('user', text);
        input.value = '';
        sendBtn.disabled = true;
        showTyping(true);

        abortController = new AbortController();

        try {
            const response = await fetch(API_BASE + '/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message: text,
                    conversation_id: currentConversationId
                }),
                signal: abortController.signal
            });

            if (!response.ok) {
                let errorData = {};
                try {
                    errorData = await response.json();
                } catch (e) {
                    errorData = { error: 'Error al procesar tu mensaje (' + response.status + ')' };
                }
                throw new Error(errorData.message || errorData.error || 'Error al procesar tu mensaje');
            }

            const data = await response.json();

            if (data.conversation_id) {
                currentConversationId = data.conversation_id;
            }

            showTyping(false);

            if (data.message) {
                addMessage('assistant', data.message);
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            showTyping(false);
            showError(error.message);
        } finally {
            isProcessing = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    async function sendMessageStream(text) {
        if (!text.trim() || isProcessing) return;

        isProcessing = true;
        addMessage('user', text);
        input.value = '';
        sendBtn.disabled = true;
        showTyping(true);

        let assistantContentEl = null;
        let assistantContent = '';
        let showingThinking = false;

        // Create message bubble for assistant
        messagesContainer.classList.add('ai-messages-visible');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'ai-message ai-message-assistant';

        const avatar = document.createElement('div');
        avatar.className = 'ai-message-avatar';
        avatar.textContent = '🤖';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'ai-message-content';
        contentDiv.style.whiteSpace = 'pre-wrap';

        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);
        assistantContentEl = contentDiv;

        abortController = new AbortController();

        try {
            const response = await fetch(API_BASE + '/chat?stream=1', {
                method: 'POST',
                headers: {
                    'Accept': 'text/event-stream',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message: text,
                    conversation_id: currentConversationId
                }),
                signal: abortController.signal
            });

            if (!response.ok) {
                let errorData = {};
                try {
                    errorData = await response.json();
                } catch (e) {
                    errorData = { error: 'Error al procesar tu mensaje (' + response.status + ')' };
                }
                showTyping(false);
                showError(errorData.error || errorData.message || 'Error al procesar tu mensaje');
                isProcessing = false;
                sendBtn.disabled = false;
                return;
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop() || '';

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const dataStr = line.slice(6);
                        if (dataStr === '[DONE]') continue;

                        try {
                            const data = JSON.parse(dataStr);

                            if (data.type === 'conversation_start') {
                                currentConversationId = data.conversation_id;
                            }

                            if (data.type === 'thinking' && !assistantContent) {
                                if (!showingThinking) {
                                    showingThinking = true;
                                    assistantContentEl.textContent = 'Analizando...';
                                    scrollToBottom();
                                }
                            }

                            if (data.type === 'thinking_done' && showingThinking) {
                                showingThinking = false;
                                assistantContentEl.textContent = '';
                            }

                            if (data.type === 'text' && data.content) {
                                if (showingThinking) {
                                    showingThinking = false;
                                    assistantContentEl.textContent = '';
                                }
                                assistantContent += data.content;
                                assistantContentEl.textContent = assistantContent;
                                scrollToBottom();
                            }

                            if (data.type === 'error') {
                                if (showingThinking) {
                                    showingThinking = false;
                                    assistantContentEl.textContent = '';
                                }
                                showError(data.message);
                            }
                        } catch (e) {
                            // Ignore parse errors
                        }
                    }
                }
            }

            showTyping(false);

            if (!assistantContent) {
                addMessage('assistant', 'No pude obtener una respuesta. Por favor, intenta de nuevo.');
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            showTyping(false);
            showError(error.message);
        } finally {
            isProcessing = false;
            sendBtn.disabled = false;
            input.focus();
            if (assistantContentEl) {
                assistantContentEl.style.whiteSpace = 'normal';
            }
        }
    }

    function handlePromptClick(prompt) {
        sendMessage(prompt);
    }

    // Event Listeners
    toggleBtn.addEventListener('click', toggleChat);
    minimizeBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', () => {
        closeChat();
        if (abortController) {
            abortController.abort();
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessageStream(input.value);
    });

    document.querySelectorAll('.ai-suggestions-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const prompt = btn.dataset.prompt;
            if (prompt) {
                handlePromptClick(prompt);
            }
        });
    });

    // Handle ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && chatWindow.classList.contains('ai-chat-open')) {
            closeChat();
        }
    });
});
</script>
