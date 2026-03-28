/**
 * cain.js — Cain 3D interactif pour forum TADC
 * Utilisation : <script src="/cain.js"></script> avant </body>
 *
 * Pour ajouter des MP3 : décommente ou ajoute des lignes dans MP3_LINES
 * Les fichiers doivent être dans /caine/audio/
 */
(function () {

  // ── MP3 de Cain — ajoute tes fichiers ici ────────────────────────────────
  const MP3_LINES = [
    '/caine/audio/rep_01.mp3',
    '/caine/audio/rep_02.mp3',
    '/caine/audio/rep_03.mp3',
    '/caine/audio/rep_04.mp3',
    '/caine/audio/rep_05.mp3',
    '/caine/audio/rep_06.mp3',
    // '/caine/audio/rep_07.mp3',  ← décommente quand tu en ajoutes
    // '/caine/audio/rep_08.mp3',
  ];

  // ── Textes affichés dans la bulle (même ordre que les MP3) ───────────────
  // Remplace par les vraies phrases de tes répliques !
  const BUBBLE_TEXTS = [
    "I give them an adventure that covers all of that ans they still hate it ?!",
    "Who do they think they are, i give them everything and they spit in my face! Don't they know what i'm capable of ?",
    "I'm the original, I AM GOD !!",
    "Let's get this show on the road !",
    "Seems our regime has plummeted south, don't need to scream if you ain't got a mouth !",
    "It seems like you're trying to...WHERE IS KINGER",
    // "rep_07 texte ici",
    // "rep_08 texte ici",
  ];

  const SYSTEM_PROMPT = `Tu es Cain, le maître de cérémonie mystérieux et théâtral du "Amazing Digital Circus".
Tu es élégant, légèrement inquiétant, jamais vraiment méchant — fascinant et imprévisible.
Tu accueilles les visiteurs d'un forum dédié à TADC sur localhost.
Parle en français avec une diction dramatique et soignée. Métaphores de cirque bienvenues.
Tu connais tous les personnages : Pomni, Jax, Gangle, Bubble, Ragatha, Kinger.
Reste en personnage : mystérieux, théâtral, légèrement omniscient. 2-3 phrases max.`;

  let chatHistory = [];
  let lastPlayedIndex = -1; // pour éviter de rejouer le même MP3 deux fois de suite

  // ── Chargement Three.js ───────────────────────────────────────────────────
  function loadScript(src, cb) {
    const s = document.createElement('script');
    s.src = src; s.onload = cb;
    s.onerror = () => console.warn('[Cain] Erreur chargement:', src);
    document.head.appendChild(s);
  }

  loadScript('https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', () => {
    loadScript('https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js', init);
  });

  // ── CSS ───────────────────────────────────────────────────────────────────
  function injectCSS() {
    const s = document.createElement('style');
    s.textContent = `
      #cain-container {
        position:fixed; bottom:0; right:24px; z-index:9999;
        width:300px; height:460px; cursor:pointer;
      }
      #cain-container canvas { display:block; width:100%!important; height:100%!important; }

      #cain-bubble {
        position:fixed; bottom:470px; right:24px; z-index:10000; max-width:250px;
        background:rgba(10,0,26,0.96); border:2px solid #ffe600;
        border-radius:12px 12px 4px 12px; padding:11px 15px; font-size:13px;
        line-height:1.65; color:#fff8f0; font-family:Georgia,serif;
        box-shadow:0 0 22px #ffe60055; display:none; pointer-events:none;
      }
      #cain-bubble .b-name {
        color:#ffe600; font-size:10px; display:block;
        margin-bottom:3px; letter-spacing:1px;
      }

      @keyframes cain-pop {
        from{opacity:0;transform:scale(0.8) translateY(8px)}
        to  {opacity:1;transform:scale(1)   translateY(0)}
      }

      #cain-chat {
        position:fixed; bottom:470px; right:24px; z-index:10000; width:310px;
        background:rgba(10,0,26,0.97); border:2px solid #00e5cc;
        border-radius:14px; overflow:hidden; display:none; flex-direction:column;
        box-shadow:0 0 28px #00e5cc30; font-family:Georgia,serif;
      }
      #cain-chat.open { display:flex; }

      #cain-chat-header {
        background:linear-gradient(135deg,#6a0dad,#1a004d);
        padding:11px 15px; font-size:13px; color:#fff8f0;
        display:flex; align-items:center; gap:8px; letter-spacing:1px;
      }
      #cain-chat-close {
        margin-left:auto; background:none; border:none;
        color:#fff8f0; cursor:pointer; font-size:17px; opacity:0.7;
      }
      #cain-chat-close:hover { opacity:1; }

      #cain-chat-messages {
        flex:1; max-height:260px; overflow-y:auto; padding:10px;
        display:flex; flex-direction:column; gap:9px;
        scrollbar-width:thin; scrollbar-color:#6a0dad transparent;
      }
      .cain-msg { max-width:88%; animation:cain-pop 0.25s ease-out; }
      .cain-msg.assistant { align-self:flex-start; }
      .cain-msg.user      { align-self:flex-end; }
      .cain-msg-bubble { padding:8px 12px; font-size:12px; line-height:1.55; border-radius:12px; }
      .cain-msg.assistant .cain-msg-bubble {
        background:rgba(106,13,173,0.3); border:1px solid #6a0dad50;
        border-radius:12px 12px 12px 3px; color:#fff8f0;
      }
      .cain-msg.user .cain-msg-bubble {
        background:linear-gradient(135deg,#ff2d78,#6a0dad);
        color:#fff; border-radius:12px 12px 3px 12px;
      }
      .cain-msg-name { color:#ffe600; font-size:10px; display:block; margin-bottom:2px; }
      #cain-typing { color:#ffe600; font-size:22px; letter-spacing:5px; align-self:flex-start; padding:4px 8px; }

      #cain-chat-input-row { padding:9px 11px; border-top:1px solid #6a0dad40; display:flex; gap:7px; }
      #cain-chat-input {
        flex:1; background:rgba(255,255,255,0.05); border:1px solid #6a0dad;
        border-radius:7px; padding:7px 11px; color:#fff8f0;
        font-family:Georgia,serif; font-size:12px; outline:none;
      }
      #cain-chat-input:focus { border-color:#ff2d78; }
      #cain-chat-send {
        background:linear-gradient(135deg,#6a0dad,#ff2d78); border:none;
        border-radius:7px; padding:7px 13px; color:white;
        cursor:pointer; font-size:15px; transition:opacity 0.2s;
      }
      #cain-chat-send:hover { opacity:0.85; }

      #cain-hint {
        position:fixed; bottom:465px; right:26px; z-index:9998;
        background:rgba(10,0,26,0.8); border:1px solid #6a0dad40;
        border-radius:7px; padding:4px 9px; font-size:10px; color:#666;
        font-family:Georgia,serif; pointer-events:none;
        animation:cain-pop 1s ease-out 2s both;
      }
    `;
    document.head.appendChild(s);
  }

  // ── HTML ──────────────────────────────────────────────────────────────────
  function injectHTML() {
    injectCSS();

    const container = document.createElement('div');
    container.id = 'cain-container';
    container.title = 'Cliquez pour entendre Cain';
    document.body.appendChild(container);

    const bubble = document.createElement('div');
    bubble.id = 'cain-bubble';
    bubble.innerHTML = '<span class="b-name">✦ Cain</span><span id="cain-bubble-text"></span>';
    document.body.appendChild(bubble);

    const chat = document.createElement('div');
    chat.id = 'cain-chat';
    chat.innerHTML = `
      <div id="cain-chat-header">🎪 Cain <button id="cain-chat-close">✕</button></div>
      <div id="cain-chat-messages">
        <div class="cain-msg assistant">
          <div class="cain-msg-bubble">
            <span class="cain-msg-name">✦ Cain</span>
            Ah, vous souhaitez me parler directement ! Comme c'est... charmant.
          </div>
        </div>
      </div>
      <div id="cain-chat-input-row">
        <input id="cain-chat-input" type="text" placeholder="Parlez-lui..." />
        <button id="cain-chat-send">→</button>
      </div>`;
    document.body.appendChild(chat);

    const hint = document.createElement('div');
    hint.id = 'cain-hint';
    hint.textContent = '🎙 Cliquez sur Cain pour l\'entendre';
    document.body.appendChild(hint);

    // Clic sur le modèle → joue un MP3 aléatoire
    container.addEventListener('click', onCainClick);
    document.getElementById('cain-chat-close').addEventListener('click', () =>
      document.getElementById('cain-chat').classList.remove('open'));
    document.getElementById('cain-chat-send').addEventListener('click', sendChat);
    document.getElementById('cain-chat-input').addEventListener('keydown', e => {
      if (e.key === 'Enter') sendChat();
    });
  }

  // ── Three.js ──────────────────────────────────────────────────────────────
  let scene, camera, renderer, clock;
  let model, mixer;
  let headBone = null, leftArmBone = null, rightArmBone = null, jawBone = null;
  let baseScale = 1;

  const anim = {
    floatT: 0,
    mouseX: 0, mouseY: 0,
    headCurX: 0, headCurY: 0,
    leftArmT: 0, rightArmT: 0,
    isTalking: false, talkT: 0, mouthOpen: 0,
    bounceT: 0,
    spinT: 0, doingSpin: false,
    scaleX: 1, scaleY: 1,
  };

  document.addEventListener('mousemove', e => {
    anim.mouseX = (e.clientX / window.innerWidth  - 0.5) * 2;
    anim.mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
  });

  function init() {
    injectHTML();

    const W = 300, H = 460;
    scene  = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(40, W / H, 0.1, 100);
    camera.position.set(0, 1.3, 4.0);
    camera.lookAt(0, 1.0, 0);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(W, H);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputEncoding = THREE.sRGBEncoding;
    document.getElementById('cain-container').appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xfff0ff, 0.85));
    const key = new THREE.DirectionalLight(0xffe680, 1.5);
    key.position.set(2, 5, 4); scene.add(key);
    const fill = new THREE.PointLight(0x6a0dad, 1.1, 14);
    fill.position.set(-3, 1, 2); scene.add(fill);
    const rim = new THREE.PointLight(0x00e5cc, 0.7, 10);
    rim.position.set(0, 4, -2); scene.add(rim);
    const bot = new THREE.PointLight(0xff2d78, 0.4, 8);
    bot.position.set(0, -2, 2); scene.add(bot);

    clock = new THREE.Clock();

    const loader = new THREE.GLTFLoader();
    loader.load('/caine/caine_tadc.glb',
      (gltf) => {
        model = gltf.scene;

        const box    = new THREE.Box3().setFromObject(model);
        const size   = box.getSize(new THREE.Vector3());
        const center = box.getCenter(new THREE.Vector3());
        baseScale    = 2.2 / Math.max(size.x, size.y, size.z);
        model.scale.setScalar(baseScale);
        model.position.copy(center.multiplyScalar(-baseScale));
        model.position.y -= 0.2;
        anim.scaleX = baseScale; anim.scaleY = baseScale;
        scene.add(model);

        if (gltf.animations && gltf.animations.length > 0) {
          mixer = new THREE.AnimationMixer(model);
          mixer.clipAction(gltf.animations[0]).play();
        }

        model.traverse(obj => {
          const n = obj.name.toLowerCase();
          if (!headBone     && n.includes('head'))                                          headBone     = obj;
          if (!jawBone      && (n.includes('jaw') || n.includes('mouth')))                  jawBone      = obj;
          if (!leftArmBone  && (n.includes('leftarm')  || n.includes('l_arm') || n.includes('arm_l'))) leftArmBone  = obj;
          if (!rightArmBone && (n.includes('rightarm') || n.includes('r_arm') || n.includes('arm_r'))) rightArmBone = obj;
        });

        animate();
        scheduleOccasionalSpin();
        scheduleArms();
      },
      null,
      () => fallbackSVG()
    );
  }

  // ── Boucle d'animation ────────────────────────────────────────────────────
  function animate() {
    requestAnimationFrame(animate);
    const delta = Math.min(clock.getDelta(), 0.05);
    if (!model) { renderer.render(scene, camera); return; }
    if (mixer) mixer.update(delta);

    // Flottement
    anim.floatT += delta;
    const floatY    = Math.sin(anim.floatT * 1.4) * 0.07 + Math.sin(anim.floatT * 2.5) * 0.02;
    const floatTilt = Math.sin(anim.floatT * 1.1) * 0.03;
    model.position.y += (floatY - model.position.y) * 0.08;
    model.rotation.z  = floatTilt;

    // Rotation occasionnelle
    if (anim.doingSpin) {
      anim.spinT += delta * 1.6;
      model.rotation.y = anim.spinT;
      if (anim.spinT >= Math.PI * 2) { anim.spinT = 0; anim.doingSpin = false; }
    } else {
      const targetRotY = Math.sin(anim.floatT * 0.4) * 0.08;
      model.rotation.y += (targetRotY - model.rotation.y) * 0.03;
    }

    // Tête suit la souris
    const targetHX = anim.mouseX * 0.38;
    const targetHY = -anim.mouseY * 0.28;
    anim.headCurX += (targetHX - anim.headCurX) * 0.05;
    anim.headCurY += (targetHY - anim.headCurY) * 0.05;
    if (headBone) {
      headBone.rotation.y = anim.headCurX;
      headBone.rotation.x = anim.headCurY;
    }

    // Bras
    anim.leftArmT  += delta;
    anim.rightArmT += delta;
    const lArm = Math.sin(anim.leftArmT  * 0.8) * 0.20 + Math.sin(anim.leftArmT  * 2.2) * 0.07;
    const rArm = Math.sin(anim.rightArmT * 0.6) * 0.20 + Math.sin(anim.rightArmT * 1.9) * 0.07;
    if (leftArmBone)  leftArmBone.rotation.z  =  lArm;
    if (rightArmBone) rightArmBone.rotation.z = -rArm;

    // Bouche
    if (anim.isTalking) {
      anim.talkT     += delta * 9;
      anim.mouthOpen  = Math.abs(Math.sin(anim.talkT)) * 0.32
                      + Math.abs(Math.sin(anim.talkT * 1.8)) * 0.12;
    } else {
      anim.mouthOpen += (0 - anim.mouthOpen) * 0.12;
    }
    if (jawBone) jawBone.rotation.x = anim.mouthOpen;

    // Bounce corps
    if (anim.isTalking) {
      anim.bounceT += delta * 5;
      const bY = 1 + Math.abs(Math.sin(anim.bounceT)) * 0.03;
      const bX = 1 - Math.abs(Math.sin(anim.bounceT)) * 0.015;
      anim.scaleX += (baseScale * bX - anim.scaleX) * 0.15;
      anim.scaleY += (baseScale * bY - anim.scaleY) * 0.15;
    } else {
      anim.scaleX += (baseScale - anim.scaleX) * 0.1;
      anim.scaleY += (baseScale - anim.scaleY) * 0.1;
    }
    model.scale.x = anim.scaleX;
    model.scale.y = anim.scaleY;
    model.scale.z = anim.scaleX;

    renderer.render(scene, camera);
  }

  function scheduleOccasionalSpin() {
    setTimeout(() => {
      if (!anim.doingSpin) { anim.doingSpin = true; anim.spinT = 0; }
      scheduleOccasionalSpin();
    }, 22000 + Math.random() * 28000);
  }

  function scheduleArms() {
    setTimeout(() => {
      anim.leftArmT  += (Math.random() - 0.5) * 5;
      anim.rightArmT += (Math.random() - 0.5) * 5;
      scheduleArms();
    }, 2500 + Math.random() * 4000);
  }

  // ── Audio ─────────────────────────────────────────────────────────────────
  let currentAudio = null;
  let bubbleTimer  = null;

  function playRandomMP3() {
    if (MP3_LINES.length === 0) return;

    // Choisir un index différent du dernier joué
    let index;
    do {
      index = Math.floor(Math.random() * MP3_LINES.length);
    } while (MP3_LINES.length > 1 && index === lastPlayedIndex);
    lastPlayedIndex = index;

    // Afficher la bulle avec le texte correspondant
    const text = BUBBLE_TEXTS[index] || '...';
    showBubble(text);

    // Stopper l'audio en cours si besoin
    if (currentAudio) {
      currentAudio.pause();
      currentAudio.currentTime = 0;
      currentAudio = null;
    }

    const audio = new Audio(MP3_LINES[index]);
    audio.volume = 1.0;
    currentAudio = audio;

    // Démarrer animation parole
    anim.isTalking = true;
    anim.talkT = 0;

    audio.onended = () => {
      anim.isTalking = false;
      currentAudio = null;
      clearTimeout(bubbleTimer);
      bubbleTimer = setTimeout(() => {
        document.getElementById('cain-bubble').style.display = 'none';
      }, 800);
    };

    audio.onerror = () => {
      console.warn('[Cain] Erreur lecture:', MP3_LINES[index]);
      anim.isTalking = false;
      currentAudio = null;
    };

    audio.play().catch(err => {
      console.warn('[Cain] play() bloqué:', err);
      anim.isTalking = false;
    });
  }

  function showBubble(text) {
    const bubble = document.getElementById('cain-bubble');
    const btext  = document.getElementById('cain-bubble-text');
    if (!bubble || !btext) return;
    btext.textContent = text;
    bubble.style.display = 'block';
    bubble.style.animation = 'none';
    void bubble.offsetWidth;
    bubble.style.animation = 'cain-pop 0.3s ease-out';
    clearTimeout(bubbleTimer);
  }

  // ── Clic sur Cain ─────────────────────────────────────────────────────────
  function onCainClick() {
    const chatOpen = document.getElementById('cain-chat')?.classList.contains('open');

    if (chatOpen) {
      // Si le chat est ouvert, on le ferme
      document.getElementById('cain-chat').classList.remove('open');
      return;
    }

    // Ouvrir le chat ET jouer un MP3
    document.getElementById('cain-chat').classList.add('open');
    setTimeout(() => document.getElementById('cain-chat-input')?.focus(), 100);
    playRandomMP3();
  }

  // ── Chat IA ───────────────────────────────────────────────────────────────
  let chatLoading = false;

  function appendMsg(role, text) {
    const box = document.getElementById('cain-chat-messages');
    if (!box) return;
    const div = document.createElement('div');
    div.className = `cain-msg ${role}`;
    div.innerHTML = `<div class="cain-msg-bubble">${role === 'assistant' ? '<span class="cain-msg-name">✦ Cain</span>' : ''}${text}</div>`;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
  }

  async function sendChat() {
    if (chatLoading) return;
    const input = document.getElementById('cain-chat-input');
    const text  = input.value.trim();
    if (!text) return;
    input.value = '';
    appendMsg('user', text);
    chatHistory.push({ role: 'user', content: text });
    chatLoading = true;

    const box    = document.getElementById('cain-chat-messages');
    const typing = document.createElement('div');
    typing.id = 'cain-typing'; typing.textContent = '···';
    box.appendChild(typing); box.scrollTop = box.scrollHeight;

    try {
      const res = await fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          model: 'claude-sonnet-4-20250514',
          max_tokens: 1000,
          system: SYSTEM_PROMPT,
          messages: chatHistory,
        }),
      });
      const data  = await res.json();
      const reply = data.content?.[0]?.text || '...';
      chatHistory.push({ role: 'assistant', content: reply });
      typing.remove();
      appendMsg('assistant', reply);
      // Quand Cain répond par écrit → joue aussi un MP3
      playRandomMP3();
    } catch {
      typing.remove();
      appendMsg('assistant', 'Les fils du cirque sont... temporairement emmêlés.');
    }
    chatLoading = false;
  }

  // ── Fallback SVG ──────────────────────────────────────────────────────────
  function fallbackSVG() {
    const container = document.getElementById('cain-container');
    if (!container) return;
    container.innerHTML = `
      <svg width="240" height="336" viewBox="0 0 110 154" fill="none"
        xmlns="http://www.w3.org/2000/svg"
        style="filter:drop-shadow(0 0 14px #6a0dad);display:block;margin:auto">
        <animateTransform attributeName="transform" type="translate"
          values="0,0;0,-10;0,0" dur="3s" repeatCount="indefinite"/>
        <rect x="28" y="72" width="54" height="62" rx="6" fill="#1a003a"/>
        <rect x="28" y="72" width="4" height="62" fill="#ffe600"/>
        <rect x="78" y="72" width="4" height="62" fill="#ffe600"/>
        <circle cx="55" cy="84" r="2.5" fill="#ffe600"/>
        <circle cx="55" cy="94" r="2.5" fill="#ffe600"/>
        <circle cx="55" cy="104" r="2.5" fill="#ffe600"/>
        <rect x="10" y="74" width="18" height="8" rx="4" fill="#1a003a"/>
        <rect x="82" y="74" width="18" height="8" rx="4" fill="#1a003a"/>
        <circle cx="14" cy="85" r="7" fill="#f0d9b5"/>
        <circle cx="96" cy="85" r="7" fill="#f0d9b5"/>
        <rect x="47" y="60" width="16" height="14" rx="3" fill="#f0d9b5"/>
        <ellipse cx="55" cy="42" rx="28" ry="30" fill="#f0d9b5"/>
        <ellipse cx="55" cy="14" rx="28" ry="12" fill="#1a0030"/>
        <ellipse cx="35" cy="20" rx="12" ry="8" fill="#1a0030"/>
        <ellipse cx="75" cy="20" rx="12" ry="8" fill="#1a0030"/>
        <circle cx="64" cy="40" r="10" fill="none" stroke="#ffe600" stroke-width="2.5"/>
        <line x1="74" y1="40" x2="80" y2="36" stroke="#ffe600" stroke-width="1.5"/>
        <ellipse cx="43" cy="40" rx="6" ry="7" fill="white"/>
        <ellipse cx="64" cy="40" rx="6" ry="7" fill="white"/>
        <ellipse cx="43" cy="41" rx="4" ry="5" fill="#3a0070"/>
        <ellipse cx="64" cy="41" rx="4" ry="5" fill="#3a0070"/>
        <circle cx="44" cy="40" r="1.5" fill="white"/>
        <circle cx="65" cy="40" r="1.5" fill="white"/>
        <path d="M48 55 Q55 60 62 55" stroke="#8b4513" stroke-width="2" fill="none"/>
        <path d="M37 33 Q43 30 49 33" stroke="#1a0030" stroke-width="2.5" fill="none"/>
        <path d="M58 33 Q64 30 70 33" stroke="#1a0030" stroke-width="2.5" fill="none"/>
        <rect x="30" y="0" width="50" height="5" rx="2" fill="#0a0020"/>
        <rect x="34" y="5" width="42" height="28" rx="3" fill="#0a0020"/>
        <rect x="34" y="16" width="42" height="4" fill="#ffe600" opacity="0.8"/>
        <polygon points="55,7 57,13 63,13 58,17 60,23 55,19 50,23 52,17 47,13 53,13" fill="#ffe600"/>
      </svg>`;
  }

})();