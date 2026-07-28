<?php $pageTitle = 'Scan QR Code'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/student/dashboard" class="back-link">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
        <h1>Scan QR Code</h1>
        <p>Point your camera at the QR code displayed in class</p>
    </div>
</div>

<div style="max-width:500px;margin:0 auto">

    <!-- Camera Scanner -->
    <div class="panel" style="margin-bottom:20px">
        <div class="panel-header">
            <h2><i class="fas fa-camera" style="color:var(--primary);margin-right:8px"></i>Camera Scanner</h2>
            <span id="scanStatus" class="badge badge-pending">Ready</span>
        </div>
        <div style="padding:20px;text-align:center">
            <!-- Video feed -->
            <div style="position:relative;background:#000;border-radius:16px;
                        overflow:hidden;margin-bottom:16px;aspect-ratio:1">
                <video id="qrVideo" style="width:100%;height:100%;object-fit:cover"
                       playsinline autoplay muted></video>
                <!-- Scanning overlay -->
                <div style="position:absolute;inset:0;display:flex;align-items:center;
                            justify-content:center;pointer-events:none">
                    <div id="scanOverlay" style="width:220px;height:220px;position:relative">
                        <!-- Corner brackets -->
                        <div style="position:absolute;top:0;left:0;width:40px;height:40px;
                                    border-top:3px solid var(--primary);border-left:3px solid var(--primary);
                                    border-radius:4px 0 0 0"></div>
                        <div style="position:absolute;top:0;right:0;width:40px;height:40px;
                                    border-top:3px solid var(--primary);border-right:3px solid var(--primary);
                                    border-radius:0 4px 0 0"></div>
                        <div style="position:absolute;bottom:0;left:0;width:40px;height:40px;
                                    border-bottom:3px solid var(--primary);border-left:3px solid var(--primary);
                                    border-radius:0 0 0 4px"></div>
                        <div style="position:absolute;bottom:0;right:0;width:40px;height:40px;
                                    border-bottom:3px solid var(--primary);border-right:3px solid var(--primary);
                                    border-radius:0 0 4px 0"></div>
                        <!-- Scanning line -->
                        <div id="scanLine" style="position:absolute;left:5px;right:5px;
                                                  height:2px;background:var(--primary);
                                                  opacity:.8;top:50%;
                                                  animation:scanLine 2s ease-in-out infinite"></div>
                    </div>
                </div>
                <!-- Canvas for processing -->
                <canvas id="qrCanvas" style="display:none"></canvas>
            </div>

            <div id="cameraError" style="display:none" class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="cameraErrorMsg">Camera access denied.</span>
            </div>

            <div style="display:flex;gap:10px;justify-content:center">
                <button id="startBtn" onclick="startCamera()" class="btn btn-primary">
                    <i class="fas fa-camera"></i> Start Camera
                </button>
                <button id="stopBtn" onclick="stopCamera()" class="btn btn-secondary"
                        style="display:none">
                    <i class="fas fa-stop"></i> Stop
                </button>
                <button id="switchBtn" onclick="switchCamera()" class="btn btn-secondary"
                        style="display:none" title="Switch camera">
                    <i class="fas fa-sync"></i>
                </button>
            </div>
        </div>
    </div>


    <!-- Result -->
    <div id="scanResult" style="display:none" class="panel">
        <div id="scanResultContent" style="padding:24px;text-align:center"></div>
    </div>

</div>

<style>
@keyframes scanLine {
    0%, 100% { top: 10%; }
    50%       { top: 88%; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let videoStream    = null;
let scanInterval   = null;
let facingMode     = 'environment'; // back camera
let scanning       = false;

const video    = document.getElementById('qrVideo');
const canvas   = document.getElementById('qrCanvas');
const ctx      = canvas.getContext('2d');
const statusEl = document.getElementById('scanStatus');
const startBtn = document.getElementById('startBtn');
const stopBtn  = document.getElementById('stopBtn');
const switchBtn= document.getElementById('switchBtn');
const errDiv   = document.getElementById('cameraError');
const errMsg   = document.getElementById('cameraErrorMsg');
const baseUrl  = '<?= BASE_URL ?>';

async function startCamera() {
    try {
        errDiv.style.display = 'none';
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: facingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        videoStream         = stream;
        video.srcObject     = stream;
        scanning            = true;
        startBtn.style.display = 'none';
        stopBtn.style.display  = 'inline-flex';
        switchBtn.style.display= 'inline-flex';
        statusEl.textContent   = 'Scanning...';
        statusEl.className     = 'badge badge-active';
        scanInterval = setInterval(scanFrame, 300);
    } catch (err) {
        errDiv.style.display = 'flex';
        errMsg.textContent = err.name === 'NotAllowedError'
            ? 'Camera access was denied. Please allow camera access and try again.'
            : 'Could not access camera: ' + err.message;
    }
}

function stopCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(t => t.stop());
        videoStream = null;
    }
    clearInterval(scanInterval);
    scanning               = false;
    startBtn.style.display = 'inline-flex';
    stopBtn.style.display  = 'none';
    switchBtn.style.display= 'none';
    statusEl.textContent   = 'Ready';
    statusEl.className     = 'badge badge-pending';
}

async function switchCamera() {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    stopCamera();
    await startCamera();
}

function scanFrame() {
    if (!scanning || video.readyState !== video.HAVE_ENOUGH_DATA) return;

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code      = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: 'dontInvert'
    });

    if (code) {
        handleQrResult(code.data);
    }
}

function handleQrResult(url) {
    // Stop scanning
    stopCamera();
    clearInterval(scanInterval);

    statusEl.textContent = 'QR Found!';
    statusEl.className   = 'badge badge-active';

    // Check if it's our attend URL
    const attendPattern = /\/attend\/([a-f0-9]+)/i;
    const match         = url.match(attendPattern);

    if (match || url.startsWith('http')) {
        showResult('loading', 'Processing...', 'Marking your attendance...');

        // Navigate to the attend URL
        if (match) {
            window.location.href = baseUrl + '/attend/' + match[1];
        } else {
            window.location.href = url;
        }
    } else {
        showResult('error', '❌ Invalid QR Code',
            'This QR code is not from the attendance system. Please scan the correct code.');
    }
}

function manualEntry(e) {
    e.preventDefault();
    const val = document.getElementById('manualCode').value.trim();
    if (!val) return;

    const attendPattern = /\/attend\/([a-f0-9]+)/i;
    const tokenPattern  = /^[a-f0-9]{40,}$/i;
    const match         = val.match(attendPattern);
    const tokenMatch    = val.match(tokenPattern);

    if (match) {
        window.location.href = baseUrl + '/attend/' + match[1];
    } else if (tokenMatch) {
        window.location.href = baseUrl + '/attend/' + val;
    } else if (val.startsWith('http')) {
        window.location.href = val;
    } else {
        showResult('error', '❌ Invalid Code', 'Please paste the full URL or token from your lecturer.');
    }
}

function showResult(type, title, message) {
    const el = document.getElementById('scanResult');
    const content = document.getElementById('scanResultContent');
    el.style.display = 'block';

    const colors = { loading: 'var(--primary)', success: 'var(--success)', error: 'var(--danger)' };
    content.innerHTML = `
        <div style="font-size:2.5rem;margin-bottom:12px">
            ${type === 'loading' ? '<i class="fas fa-spinner fa-spin" style="color:var(--primary)"></i>' : title.split(' ')[0]}
        </div>
        <h3 style="color:var(--text);margin-bottom:8px">${type === 'loading' ? title : title.substring(2)}</h3>
        <p style="color:var(--text-muted);font-size:.875rem">${message}</p>
    `;
    el.style.borderTop = '4px solid ' + (colors[type] || 'var(--primary)');
    el.scrollIntoView({ behavior: 'smooth' });
}

// Auto-start if user visited this page intentionally
// (uncomment below to auto-start camera)
// startCamera();
</script>