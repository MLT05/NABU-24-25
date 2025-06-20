document.addEventListener("DOMContentLoaded", () => {
    const chatContainer = document.querySelector(".chat-container");
    if (!chatContainer) return;

    // --- VARIÁVEIS GLOBAIS DO CHAT ---
    const myUserId = chatContainer.dataset.myId;
    let activePartnerId = null;
    let pollingInterval = null;
    let typingTimer;

    // --- ELEMENTOS DA INTERFACE ---
    const usersListContainer = document.querySelector(".users-list");
    const chatHeader = document.querySelector(".chat-header");
    const messagesContainer = document.querySelector(".chat-messages");
    const chatInputArea = document.querySelector(".chat-input");
    const messageForm = document.getElementById("message-form");
    const messageInput = document.getElementById("message-input");
    const imageInput = document.getElementById("image-input");
    const imageButtonTrigger = document.getElementById("image-button-trigger");
    const recordAudioButton = document.getElementById("record-audio-button");

    // --- LÓGICA PRINCIPAL ---

    // Abre uma conversa diretamente se o ID estiver na URL (vindo de uma notificação)
    function openChatFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const userIdFromUrl = urlParams.get("user");
        if (userIdFromUrl) {
            const userToSelect = document.querySelector(
                `.user-item[data-userid='${userIdFromUrl}']`
            );
            if (userToSelect) {
                // Pequeno delay para garantir que todos os elementos estão prontos
                setTimeout(() => userToSelect.click(), 100);
            }
        }
    }

    // Adiciona o evento de clique a cada utilizador na lista da esquerda
    usersListContainer.querySelectorAll(".user-item").forEach((item) => {
        item.addEventListener("click", () => {
            usersListContainer
                .querySelectorAll(".user-item")
                .forEach((i) => i.classList.remove("active"));
            item.classList.add("active");
            activePartnerId = item.dataset.userid;
            loadConversation(
                item.dataset.userid,
                item.dataset.username,
                item.dataset.pfp,
                item.dataset.online
            );
        });
    });

    // Inicia e gere uma conversa com um utilizador
    async function loadConversation(
        partnerId,
        partnerUsername,
        partnerPfp,
        partnerIsOnline
    ) {
        if (pollingInterval) clearInterval(pollingInterval);

        const statusText = partnerIsOnline == "1" ? "Online" : "Offline";
        const statusClass = partnerIsOnline == "1" ? "online" : "";
        chatHeader.innerHTML = `
            <img src="${partnerPfp}" alt="pfp" class="nav-profile-pfp rounded-circle me-3">
            <div class="header-user-info">
                <span class="username">${partnerUsername}</span>
                <span class="header-status ${statusClass}">${statusText}</span>
            </div>`;

        chatHeader.style.display = "flex";
        chatInputArea.style.display = "flex";
        messageInput.focus();

        await fetchAndDisplayMessages(partnerId);
        // Inicia a verificação de novas mensagens e estados a cada 3 segundos
        pollingInterval = setInterval(
            () => fetchAndDisplayMessages(partnerId),
            3000
        );
    }

    // Busca as mensagens e o estado do parceiro, e atualiza o ecrã
    async function fetchAndDisplayMessages(partnerId) {
        try {
            const response = await fetch(
                `api/fetch_messages.php?partner_id=${partnerId}`
            );
            if (!response.ok) return;
            const data = await response.json();

            const messages = data.messages;
            // Guarda a posição do scroll para a manter se não estivermos no fundo
            const wasScrolledToBottom =
                messagesContainer.scrollTop + messagesContainer.clientHeight >=
                messagesContainer.scrollHeight - 20;

            messagesContainer.innerHTML = "";
            if (messages.length === 0 && !data.partner_status) {
                messagesContainer.innerHTML =
                    '<div class="text-center text-muted mt-5">Ainda não há mensagens.</div>';
                return;
            }

            // 1. Desenha as mensagens reais
            messages.forEach((msg) => {
                const bubble = document.createElement("div");
                bubble.classList.add(
                    "message-bubble",
                    msg.sender_id == myUserId ? "sent" : "received"
                );
                if (msg.message_type === "image") {
                    bubble.innerHTML = `<img src="${msg.message_content}" style="max-width:200px; border-radius:10px; cursor:pointer;" onclick="window.open(this.src, '_blank')">`;
                } else if (msg.message_type === "audio") {
                    bubble.innerHTML = `<div class="audio-player-container" data-audiourl="${msg.message_content}"><button class="play-pause-btn"><i class="fas fa-play"></i></button><div class="waveform-container" id="waveform-${msg.id}"></div></div>`;
                } else {
                    bubble.textContent = msg.message_content;
                }
                messagesContainer.appendChild(bubble);
            });

            initializeAudioPlayers();

            // 2. Adiciona a bolha de "a escrever..." ou "a gravar..." se o parceiro estiver ativo
            if (data.partner_status) {
                const typingBubble = document.createElement("div");
                typingBubble.classList.add("message-bubble", "received");
                let activityText = "";
                if (data.partner_status === "typing") {
                    activityText = `<div class="typing-indicator-bubble"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>`;
                } else if (data.partner_status === "recording") {
                    activityText = `<div style="font-style: italic; color: #888; padding: 10px 15px;">A gravar áudio...</div>`;
                }
                typingBubble.innerHTML = activityText;
                messagesContainer.appendChild(typingBubble);
            }

            // 3. Adiciona o indicador de "Visto" se a última mensagem for nossa e estiver lida
            const lastMessage =
                messages.length > 0 ? messages[messages.length - 1] : null;
            if (
                lastMessage &&
                lastMessage.sender_id == myUserId &&
                lastMessage.is_read == 1
            ) {
                const seenIndicator = document.createElement("div");
                seenIndicator.className = "seen-indicator";
                seenIndicator.textContent = "Visto";
                messagesContainer.appendChild(seenIndicator);
            }

            // Mantém o scroll no fundo apenas se já lá estivesse
            if (wasScrolledToBottom) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        } catch (error) {
            console.error("Erro ao buscar mensagens:", error);
        }
    }

    // Inicializa os leitores de áudio personalizados
    function initializeAudioPlayers() {
        document.querySelectorAll(".audio-player-container").forEach((player) => {
            const waveformContainer = player.querySelector(".waveform-container");
            if (!waveformContainer || waveformContainer.dataset.initialized) return;
            waveformContainer.dataset.initialized = "true";

            const playBtn = player.querySelector(".play-pause-btn");
            const audioUrl = player.dataset.audiourl;
            const isSent = player.closest(".message-bubble.sent");

            const wavesurfer = WaveSurfer.create({
                container: waveformContainer,
                waveColor: isSent ? "#a994d4" : "#dcdcdc",
                progressColor: isSent ? "#ffffff" : "#5a00a1",
                barWidth: 3,
                barRadius: 3,
                height: 40,
                cursorWidth: 0,
            });
            wavesurfer.load(audioUrl);
            playBtn.onclick = (e) => {
                e.stopPropagation();
                wavesurfer.playPause();
            };
            wavesurfer.on(
                "play",
                () => (playBtn.innerHTML = '<i class="fas fa-pause"></i>')
            );
            wavesurfer.on(
                "pause",
                () => (playBtn.innerHTML = '<i class="fas fa-play"></i>')
            );
            wavesurfer.on(
                "finish",
                () => (playBtn.innerHTML = '<i class="fas fa-play"></i>')
            );
        });
    }

    // Envia a mensagem (texto, imagem ou áudio)
    async function sendMessage(formData) {
        if (!activePartnerId) return;
        if (!formData) {
            formData = new FormData(messageForm);
            if (!formData.get("message_content").trim()) return;
        }
        formData.append("receiver_id", activePartnerId);
        messageInput.value = "";
        imageInput.value = "";

        await fetch("api/send_message.php", { method: "POST", body: formData });

        clearTimeout(typingTimer);
        updateUserActivity("clear"); // Limpa o estado "a escrever" depois de enviar
        await fetchAndDisplayMessages(activePartnerId);
    }

    // Atualiza o estado de atividade (typing, recording, clear) no servidor
    function updateUserActivity(activity) {
        if (!activePartnerId) return;
        const formData = new FormData();
        formData.append("recipient_id", activePartnerId);
        formData.append("activity_type", activity);
        fetch("api/update_user_activity.php", { method: "POST", body: formData });
    }

    // --- EVENT LISTENERS ---
    messageForm.addEventListener("submit", (e) => {
        e.preventDefault();
        sendMessage();
    });
    imageButtonTrigger.addEventListener("click", () => imageInput.click());
    imageInput.addEventListener("change", () => {
        if (imageInput.files.length > 0) sendMessage(new FormData(messageForm));
    });
    messageInput.addEventListener("input", () => {
        clearTimeout(typingTimer);
        updateUserActivity("typing");
        typingTimer = setTimeout(() => updateUserActivity("clear"), 2500);
    });

    let mediaRecorder,
        audioChunks = [],
        isRecording = false;
    recordAudioButton.addEventListener("click", () => {
        isRecording ? stopAudioRecording() : startAudioRecording();
    });

    async function startAudioRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            isRecording = true;
            updateUserActivity("recording");
            recordAudioButton.innerHTML = '<i class="fas fa-stop"></i>';
            recordAudioButton.style.color = "red";
            audioChunks = [];
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.start();
            mediaRecorder.addEventListener("dataavailable", (e) =>
                audioChunks.push(e.data)
            );
            mediaRecorder.addEventListener("stop", () => {
                stream.getTracks().forEach((track) => track.stop());
                const audioBlob = new Blob(audioChunks, { type: "audio/webm" });
                const audioFile = new File([audioBlob], "recording.webm", {
                    type: "audio/webm",
                });
                const formData = new FormData();
                formData.append("audio_file", audioFile);
                sendMessage(formData);
            });
        } catch (error) {
            alert("Erro ao aceder ao microfone.");
        }
    }

    function stopAudioRecording() {
        if (mediaRecorder && isRecording) {
            mediaRecorder.stop();
            isRecording = false;
            updateUserActivity("clear");
            recordAudioButton.innerHTML = '<i class="fas fa-microphone"></i>';
            recordAudioButton.style.color = "";
        }
    }

    openChatFromUrl();
});
