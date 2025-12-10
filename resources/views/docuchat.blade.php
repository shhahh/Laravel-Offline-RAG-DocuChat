<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuChat AI | Intelligent Document Analysis</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* --- MODERN STYLING --- */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Glassmorphism Card */
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1100px;
            height: 90vh;
            display: flex; /* Flexbox zaroori hai layout ke liye */
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* --- SIDEBAR --- */
        .sidebar {
            background: #f8f9fa;
            width: 35%; /* Fixed Width */
            padding: 25px;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
        }

        .upload-zone {
            border: 2px dashed #0d6efd;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            background: #f1f8ff;
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .upload-zone:hover {
            background: #e1f0ff;
            transform: scale(1.02);
        }

        /* Recent Files List Styling */
        .file-list {
            flex-grow: 1; /* Bachi huyi jagah lega */
            overflow-y: auto; /* Scroll sirf yahan aayega */
            padding-right: 5px;
        }
        
        .file-item {
            background: white;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.2s;
        }
        .file-item:hover {
            border-color: #0d6efd;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* --- CHAT PANEL --- */
        .chat-panel {
            width: 65%; /* Fixed Width */
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
        }

        .chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #ffffff;
            background-image: radial-gradient(#e5e5f7 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Chat Bubbles */
        .message {
            display: flex;
            margin-bottom: 15px;
            animation: fadeIn 0.3s ease;
        }
        .message.user { justify-content: flex-end; }
        .message.ai { justify-content: flex-start; }

        .bubble {
            max-width: 75%;
            padding: 12px 18px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .message.user .bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .message.ai .bubble {
            background: #f1f1f1;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Typing Dots */
        .typing-dots span {
            display: inline-block; width: 6px; height: 6px; background: #888;
            border-radius: 50%; margin: 0 2px; animation: bounce 1.4s infinite ease-in-out both;
        }
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    </style>
</head>
<body>

    <div class="main-card">
        
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h4 class="fw-bold mb-1">DocuChat <span class="text-primary">AI</span></h4>
            <p class="text-muted small mb-3">Stored Files: {{ count($documents) }}</p>

            @if(session('success'))
                <div class="alert alert-success small p-2 mb-3 border-0 shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Upload Area -->
            <form action="{{ route('doc.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="pdf_file" class="upload-zone w-100">
                    <i class="bi bi-cloud-arrow-up-fill text-primary fs-2"></i>
                    <h6 class="mt-2 fw-bold small">Upload PDF</h6>
                    <input type="file" name="pdf_file" id="pdf_file" class="d-none" onchange="this.form.submit()">
                </label>
            </form>

            <!-- File List Area -->
            <div class="file-list">
                <h6 class="text-uppercase text-muted small fw-bold mb-2">History</h6>
                
                @foreach($documents as $doc)
                <div class="file-item">
                    <div class="bg-light text-danger rounded p-2">
                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 140px;">{{ $doc->filename }}</h6>
                        <small class="text-muted" style="font-size: 10px;">
                            {{ $doc->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
                @endforeach

                @if($documents->isEmpty())
                    <p class="text-center text-muted small mt-3">No files uploaded yet.</p>
                @endif
            </div>

            <!-- Footer Badge -->
            <div class="mt-3 pt-3 border-top">
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-shield-lock-fill text-success"></i>
                    <small style="font-size: 11px;">100% Secure & Offline Database</small>
                </div>
            </div>
        </div>

        <!-- CHAT PANEL -->
        <div class="chat-panel">
            <div class="chat-header">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-robot fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Document Assistant</h6>
                    <small class="text-success" style="font-size: 12px;">● Online (Phi-3.5 Local)</small>
                </div>
            </div>

            <div id="chat-box" class="chat-body">
                <div class="message ai">
                    <div class="bubble">
                        Hello! 👋 Upload a PDF on the left, and I'll read it for you.
                    </div>
                </div>
            </div>

            <div class="p-3 bg-white border-top">
                <div class="input-group">
                    <input type="text" id="user-input" class="form-control border-0 bg-light py-3 px-4 rounded-pill" placeholder="Type your question..." style="box-shadow: none;">
                    <button class="btn btn-primary rounded-circle ms-2 d-flex align-items-center justify-content-center shadow-sm" id="send-btn" style="width: 50px; height: 50px;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT -->
    <script>
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        function appendMessage(text, sender) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${sender}`;
            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            bubble.innerHTML = text;
            msgDiv.appendChild(bubble);
            chatBox.appendChild(msgDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typing-indicator';
            typingDiv.className = 'message ai';
            typingDiv.innerHTML = `<div class="bubble typing-dots"><span></span><span></span><span></span></div>`;
            chatBox.appendChild(typingDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function removeTyping() {
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            appendMessage(message, 'user');
            userInput.value = '';
            showTyping();

            try {
                const response = await fetch("{{ route('doc.chat') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                removeTyping();

                if (data.status === 'success') {
                    appendMessage(data.reply, 'ai');
                } else {
                    appendMessage("⚠️ Error: " + data.reply, 'ai');
                }
            } catch (error) {
                removeTyping();
                appendMessage("❌ System Error. Check Foundry.", 'ai');
            }
        }

        sendBtn.addEventListener('click', sendMessage);
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>