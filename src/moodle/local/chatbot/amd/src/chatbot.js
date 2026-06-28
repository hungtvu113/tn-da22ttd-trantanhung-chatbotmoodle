// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Floating chatbot widget logic for local_chatbot.
 *
 * @module     local_chatbot/chatbot
 * @copyright  2026 Khoa luan tot nghiep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax', 'core/str'], function(Ajax, Str) {

    var SELECTORS = {
        root: '#local-chatbot-root',
        launcher: '#local-chatbot-launcher',
        panel: '#local-chatbot-panel',
        closeBtn: '#local-chatbot-close',
        messages: '#local-chatbot-messages',
        form: '#local-chatbot-form',
        input: '#local-chatbot-input'
    };

    var state = {
        sending: false,
        conversationId: '',
        strings: {
            thinking: 'Đang suy nghĩ...',
            error: 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.',
            sources: 'Nguồn tham khảo'
        }
    };

    /**
     * Generate a short random conversation id.
     * @return {string}
     */
    function generateConversationId() {
        return 'c' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
    }

    /**
     * Escape HTML special chars to prevent XSS in injected bubbles.
     * @param {string} text
     * @return {string}
     */
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    /**
     * Append a message bubble.
     * @param {string} role 'user' or 'bot'
     * @param {string} text
     * @param {Array} sources optional
     * @return {HTMLElement} the appended row element
     */
    function appendMessage(role, text, sources) {
        var messages = document.querySelector(SELECTORS.messages);
        if (!messages) {
            return null;
        }
        var row = document.createElement('div');
        row.className = 'local-chatbot-msg local-chatbot-msg-' + role;

        var bubble = document.createElement('div');
        bubble.className = 'local-chatbot-bubble';
        bubble.innerHTML = escapeHtml(text);
        row.appendChild(bubble);

        if (sources && sources.length) {
            var srcDiv = document.createElement('div');
            srcDiv.className = 'local-chatbot-sources';
            srcDiv.innerHTML = escapeHtml(state.strings.sources) + ': ';
            sources.forEach(function(s) {
                if (s.url) {
                    var a = document.createElement('a');
                    a.href = s.url;
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    a.textContent = s.title || s.url;
                    srcDiv.appendChild(a);
                } else if (s.title) {
                    var span = document.createElement('span');
                    span.textContent = s.title + ' ';
                    srcDiv.appendChild(span);
                }
            });
            bubble.appendChild(srcDiv);
        }

        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
        return row;
    }

    /**
     * Show / hide the chat panel.
     * @param {boolean} show
     */
    function togglePanel(show) {
        var panel = document.querySelector(SELECTORS.panel);
        if (!panel) {
            return;
        }
        if (show) {
            panel.hidden = false;
            panel.setAttribute('aria-hidden', 'false');
            var input = document.querySelector(SELECTORS.input);
            if (input) {
                setTimeout(function() {
                    input.focus();
                }, 50);
            }
        } else {
            panel.hidden = true;
            panel.setAttribute('aria-hidden', 'true');
        }
    }

    /**
     * Send a message to the backend via the Moodle external function.
     * @param {string} text
     */
    function sendMessage(text) {
        if (state.sending) {
            return;
        }
        state.sending = true;

        appendMessage('user', text);
        var typingRow = appendMessage('bot', state.strings.thinking);
        if (typingRow) {
            typingRow.classList.add('local-chatbot-typing');
        }

        var request = {
            methodname: 'local_chatbot_send_message',
            args: {
                message: text,
                conversationid: state.conversationId
            }
        };

        Ajax.call([request])[0]
            .then(function(response) {
                if (typingRow && typingRow.parentNode) {
                    typingRow.parentNode.removeChild(typingRow);
                }
                if (response && response.success) {
                    appendMessage('bot', response.reply || '', response.sources || []);
                } else {
                    var msg = state.strings.error;
                    if (response && response.errordetail) {
                        msg += '\n\n[DEV] ' + response.errordetail;
                    }
                    appendMessage('bot', msg);
                }
                return null;
            })
            .catch(function() {
                if (typingRow && typingRow.parentNode) {
                    typingRow.parentNode.removeChild(typingRow);
                }
                appendMessage('bot', state.strings.error);
            })
            .then(function() {
                state.sending = false;
            });
    }

    /**
     * Auto-resize textarea up to 5 rows.
     * @param {HTMLTextAreaElement} el
     */
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    /**
     * Bind DOM event handlers.
     */
    function bindEvents() {
        var launcher = document.querySelector(SELECTORS.launcher);
        var closeBtn = document.querySelector(SELECTORS.closeBtn);
        var form = document.querySelector(SELECTORS.form);
        var input = document.querySelector(SELECTORS.input);

        if (launcher) {
            launcher.addEventListener('click', function() {
                var panel = document.querySelector(SELECTORS.panel);
                togglePanel(panel && panel.hidden);
            });
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                togglePanel(false);
            });
        }
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!input) {
                    return;
                }
                var text = (input.value || '').trim();
                if (!text) {
                    return;
                }
                input.value = '';
                autoResize(input);
                sendMessage(text);
            });
        }
        if (input) {
            input.addEventListener('input', function() {
                autoResize(input);
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (form) {
                        form.dispatchEvent(new Event('submit', {cancelable: true}));
                    }
                }
            });
        }
    }

    /**
     * Load translated strings asynchronously.
     */
    function loadStrings() {
        Str.get_strings([
            {key: 'widget_thinking', component: 'local_chatbot'},
            {key: 'widget_error', component: 'local_chatbot'},
            {key: 'widget_sources', component: 'local_chatbot'}
        ]).then(function(s) {
            state.strings.thinking = s[0];
            state.strings.error = s[1];
            state.strings.sources = s[2];
            return null;
        }).catch(function() {
            // Keep defaults.
        });
    }

    return {
        /**
         * Entry point invoked by PHP via js_call_amd.
         */
        init: function() {
            if (!document.querySelector(SELECTORS.root)) {
                return;
            }
            state.conversationId = generateConversationId();
            loadStrings();
            bindEvents();
        }
    };
});
