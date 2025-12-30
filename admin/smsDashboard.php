<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .sms-container {
            height: calc(100vh - 56px);
            max-height: calc(100vh - 56px);
        }

        .conversations-sidebar {
            height: 100%;
            border-right: 1px solid #dee2e6;
            background: white;
            overflow-y: auto;
        }

        .chat-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .message-bubble {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
        }

        .message-client {
            background: #e9ecef;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }

        .message-admin {
            background: #0d6efd;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 4px;
        }

        .conversation-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .conversation-item:hover {
            background: #f8f9fa;
        }

        .conversation-item.active {
            background: #e7f3ff;
            border-left: 3px solid #0d6efd;
        }

        .conversation-item .phone {
            font-weight: 600;
            color: #212529;
        }

        .conversation-item .last-message {
            color: #6c757d;
            font-size: 0.875rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .conversation-item .time {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        .unread-badge {
            background: #0d6efd;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .compose-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #dee2e6;
        }

        .empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
            text-align: center;
        }

        .search-box {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .conversations-sidebar {
                display: none;
            }

            .conversations-sidebar.show {
                display: block;
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1050;
            }

            .message-bubble {
                max-width: 85%;
            }
        }

        .typing-indicator {
            display: none;
            padding: 10px;
            color: #6c757d;
            font-style: italic;
            font-size: 0.875rem;
        }

        .sender-info {
            padding: 15px;
            background: white;
            border-bottom: 2px solid #dee2e6;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
        }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <button class="btn btn-outline-light d-md-none" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-chat-dots"></i> SMS Management
            </span>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-primary me-2" id="unreadBadge">0</span>
                <button class="btn btn-outline-light btn-sm" onclick="refreshConversations()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0">
        <div class="row g-0 sms-container">
            <!-- Conversations Sidebar -->
            <div class="col-md-4 col-lg-3 conversations-sidebar" id="conversationsSidebar">
                <!-- Search Box -->
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search conversations...">
                    </div>
                </div>

                <!-- Conversations List -->
                <div id="conversationsList">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8 col-lg-9 chat-container">
                <div id="emptyChatState" class="empty-state">
                    <div>
                        <i class="bi bi-chat-text" style="font-size: 4rem; color: #dee2e6;"></i>
                        <h5 class="mt-3">Select a conversation</h5>
                        <p class="text-muted">Choose a conversation from the left to start messaging</p>
                    </div>
                </div>

                <div id="chatArea" style="display: none; height: 100%; display: flex; flex-direction: column;">
                    <!-- Sender Info -->
                    <div class="sender-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1" id="chatClientName">Client Name</h6>
                                <small class="text-muted" id="chatClientPhone">+1234567890</small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="closeChat()">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div class="messages-container" id="messagesContainer">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading messages...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div class="typing-indicator" id="typingIndicator">
                        <i class="bi bi-three-dots"></i> Client is typing...
                    </div>

                    <!-- Compose Area -->
                    <div class="compose-area">
                        <form id="messageForm" onsubmit="sendMessage(event)">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">From:</span>
                                        <input type="text" class="form-control" id="senderNumber"
                                            placeholder="Your number" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <textarea class="form-control" id="messageInput" rows="2"
                                            placeholder="Type your message..." required></textarea>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-send"></i> Send
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        <span id="charCount">0</span>/1600 characters
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentConversationId = null;
        let currentClientPhone = null;
        let refreshInterval = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            loadConversations();
            loadUnreadCount();
            startAutoRefresh();

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', debounce(searchConversations, 500));

            // Character counter
            document.getElementById('messageInput').addEventListener('input', updateCharCount);

            // Toggle sidebar on mobile
            document.getElementById('toggleSidebar').addEventListener('click', function () {
                document.getElementById('conversationsSidebar').classList.toggle('show');
            });
        });

        // Load conversations
        async function loadConversations() {
            try {
                const response = await fetch('api.php?action=get_conversations');
                const data = await response.json();

                if (data.success) {
                    displayConversations(data.data);
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            }
        }

        // Display conversations
        function displayConversations(conversations) {
            const container = document.getElementById('conversationsList');

            if (conversations.length === 0) {
                container.innerHTML = `
                    <div class="text-center p-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <p class="mt-2">No conversations yet</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = conversations.map(conv => `
                <div class="conversation-item ${conv.conversation_id === currentConversationId ? 'active' : ''}" 
                     onclick="openConversation('${conv.conversation_id}', '${conv.client_phone}', '${conv.client_name || ''}')">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="phone">
                            <i class="bi bi-person-circle me-1"></i>
                            ${conv.client_name || conv.client_phone}
                        </div>
                        <div class="d-flex align-items-center">
                            ${conv.unread_count > 0 ? `<span class="unread-badge me-2">${conv.unread_count}</span>` : ''}
                            <span class="time">${formatTime(conv.last_message_at)}</span>
                        </div>
                    </div>
                    <div class="last-message">${conv.last_message || 'No messages yet'}</div>
                </div>
            `).join('');
        }

        // Open conversation
        async function openConversation(conversationId, phone, name) {
            currentConversationId = conversationId;
            currentClientPhone = phone;

            // Hide empty state, show chat
            document.getElementById('emptyChatState').style.display = 'none';
            document.getElementById('chatArea').style.display = 'flex';

            // Update client info
            document.getElementById('chatClientName').textContent = name || 'Unknown';
            document.getElementById('chatClientPhone').textContent = phone;

            // Mark as read
            await markAsRead(conversationId);

            // Load messages
            await loadMessages(conversationId);

            // Close sidebar on mobile
            document.getElementById('conversationsSidebar').classList.remove('show');

            // Update conversations list
            loadConversations();
        }

        // Load messages
        async function loadMessages(conversationId) {
            const container = document.getElementById('messagesContainer');
            container.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';

            try {
                const response = await fetch(`api.php?action=get_messages&conversation_id=${conversationId}`);
                const data = await response.json();

                if (data.success) {
                    displayMessages(data.data);
                }
            } catch (error) {
                console.error('Error loading messages:', error);
                container.innerHTML = '<div class="alert alert-danger m-3">Failed to load messages</div>';
            }
        }

        // Display messages
        function displayMessages(messages) {
            const container = document.getElementById('messagesContainer');

            if (messages.length === 0) {
                container.innerHTML = '<div class="text-center p-4 text-muted">No messages yet. Start the conversation!</div>';
                return;
            }

            container.innerHTML = messages.map(msg => `
                <div class="d-flex ${msg.sender_type === 'admin' ? 'justify-content-end' : 'justify-content-start'}">
                    <div class="message-bubble message-${msg.sender_type}">
                        ${msg.sender_type === 'admin' && msg.sender_name ?
                    `<div class="fw-bold mb-1" style="font-size: 0.875rem;">${msg.sender_name}</div>` : ''}
                        <div>${escapeHtml(msg.message_text)}</div>
                        <div class="message-time text-end">
                            ${formatTime(msg.created_at)}
                            ${msg.status === 'failed' ? '<i class="bi bi-exclamation-circle text-danger ms-1"></i>' : ''}
                        </div>
                    </div>
                </div>
            `).join('');

            // Scroll to bottom
            container.scrollTop = container.scrollHeight;
        }

        // Send message
        async function sendMessage(event) {
            event.preventDefault();

            const messageInput = document.getElementById('messageInput');
            const senderNumber = document.getElementById('senderNumber').value;
            const message = messageInput.value.trim();

            if (!message || !currentClientPhone) return;

            try {
                const response = await fetch('api.php?action=send_message', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: currentClientPhone,
                        from: senderNumber,
                        message: message
                    })
                });

                const data = await response.json();

                if (data.success) {
                    messageInput.value = '';
                    updateCharCount();
                    await loadMessages(currentConversationId);
                    await loadConversations();
                } else {
                    alert('Failed to send message: ' + data.error);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            }
        }

        // Mark as read
        async function markAsRead(conversationId) {
            try {
                await fetch('api.php?action=mark_read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ conversation_id: conversationId })
                });
                loadUnreadCount();
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        }

        // Search conversations
        async function searchConversations() {
            const query = document.getElementById('searchInput').value.trim();

            if (query.length < 2) {
                loadConversations();
                return;
            }

            try {
                const response = await fetch(`api.php?action=search&q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.success) {
                    displayConversations(data.data);
                }
            } catch (error) {
                console.error('Error searching:', error);
            }
        }

        // Load unread count
        async function loadUnreadCount() {
            try {
                const response = await fetch('api.php?action=get_unread_count');
                const data = await response.json();

                if (data.success) {
                    const badge = document.getElementById('unreadBadge');
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        }

        // Refresh conversations
        function refreshConversations() {
            loadConversations();
            loadUnreadCount();
            if (currentConversationId) {
                loadMessages(currentConversationId);
            }
        }

        // Auto-refresh
        function startAutoRefresh() {
            refreshInterval = setInterval(refreshConversations, 30000); // Every 30 seconds
        }

        // Close chat
        function closeChat() {
            currentConversationId = null;
            currentClientPhone = null;
            document.getElementById('chatArea').style.display = 'none';
            document.getElementById('emptyChatState').style.display = 'flex';
        }

        // Update character count
        function updateCharCount() {
            const input = document.getElementById('messageInput');
            const count = document.getElementById('charCount');
            count.textContent = input.value.length;

            if (input.value.length > 1600) {
                count.classList.add('text-danger');
            } else {
                count.classList.remove('text-danger');
            }
        }

        // Utility functions
        function formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            if (diff < 604800000) return Math.floor(diff / 86400000) + 'd ago';

            return date.toLocaleDateString();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function () {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>

</html>