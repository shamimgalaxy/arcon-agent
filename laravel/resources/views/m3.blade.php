{{-- resources/views/m3.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ArcOnAgent — M3: Arc Integration + Scale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.ethers.io/lib/ethers-5.7.umd.min.js"></script>
<style>
:root {
--bg: #050810;
--bg2: #0a0f1e;
--surface: #0d1224;
--surface2: #111829;
--surface3: #161e33;
--border: rgba(99,130,255,0.12);
--border2: rgba(99,130,255,0.25);
--border3: rgba(99,130,255,0.4);
--text: #f0f4ff;
--text2: #8b96b8;
--text3: #4a5270;
--blue: #4f7cff;
--blue2: #6390ff;
--blue-glow: rgba(79,124,255,0.2);
--cyan: #00d4ff;
--purple: #a855f7;
--green: #00e5a0;
--amber: #f59e0b;
--red: #ff4d6d;
--gold: #fbbf24;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;overflow-x:hidden;font-size:14px;line-height:1.6;}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(79,124,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(79,124,255,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;z-index:0;}

nav{position:sticky;top:0;z-index:200;background:rgba(5,8,16,0.92);border-bottom:1px solid var(--border);backdrop-filter:blur(24px);height:62px;display:flex;align-items:center;padding:0 1.75rem;gap:1.5rem;}
.brand{font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:var(--text);letter-spacing:1px;display:flex;align-items:center;gap:8px;white-space:nowrap;text-decoration:none;}
.brand-icon{width:28px;height:28px;background:linear-gradient(135deg,var(--blue),var(--purple));border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.brand span.hl{color:var(--blue2);}
.nav-links{display:flex;gap:2px;flex:1;overflow-x:auto;}
.nav-btn{font-family:'Inter',sans-serif;font-size:12px;font-weight:500;padding:6px 14px;border:none;border-radius:7px;cursor:pointer;transition:all 0.2s;background:transparent;color:var(--text2);white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;}
.nav-btn:hover{background:var(--surface2);color:var(--text);}
.nav-btn.active{background:rgba(79,124,255,0.15);color:var(--blue2);border:1px solid rgba(79,124,255,0.2);}
.live-pill{margin-left:auto;display:flex;align-items:center;gap:6px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--green);background:rgba(0,229,160,0.08);border:1px solid rgba(0,229,160,0.2);padding:4px 12px;border-radius:20px;letter-spacing:0.5px;flex-shrink:0;}
.live-dot{width:6px;height:6px;background:var(--green);border-radius:50%;animation:pulse 1.5s infinite;box-shadow:0 0 8px var(--green);}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.5;transform:scale(0.8)}}

.container{max-width:1300px;margin:0 auto;padding:2rem 1.75rem;}

/* TABS */
.tab-bar{display:flex;gap:4px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:4px;margin-bottom:1.75rem;width:fit-content;}
.tab-btn{font-size:12px;font-weight:600;padding:8px 20px;border:none;border-radius:9px;cursor:pointer;transition:all 0.2s;background:transparent;color:var(--text2);letter-spacing:0.3px;}
.tab-btn:hover{color:var(--text);background:var(--surface2);}
.tab-btn.active{background:linear-gradient(135deg,rgba(79,124,255,0.2),rgba(168,85,247,0.15));color:var(--blue2);border:1px solid rgba(79,124,255,0.25);}
.tab-content{display:none;}
.tab-content.active{display:block;animation:fadeIn 0.25s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}

/* M3 HERO BANNER */
.m3-banner{background:var(--surface);border:1px solid rgba(168,85,247,0.25);border-radius:16px;padding:1.75rem 2rem;margin-bottom:1.75rem;display:flex;align-items:center;justify-content:space-between;gap:1.5rem;position:relative;overflow:hidden;}
.m3-banner::before{content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(168,85,247,0.08),transparent 70%);pointer-events:none;}
.m3-banner-left{}
.m3-eyebrow{font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--purple);margin-bottom:6px;}
.m3-banner-title{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.5px;margin-bottom:4px;}
.m3-banner-sub{font-size:13px;color:var(--text2);}
.m3-banner-right{display:flex;gap:1rem;flex-shrink:0;}
.m3-stat{text-align:center;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:0.85rem 1.25rem;min-width:90px;}
.m3-stat-val{font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:700;color:var(--purple);}
.m3-stat-lbl{font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:1px;margin-top:3px;}

/* PANELS */
.panel{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:1.25rem;}
.panel-header{background:var(--bg2);border-bottom:1px solid var(--border);padding:0.9rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.panel-title{font-size:12px;font-weight:700;color:var(--text);}
.panel-meta{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--text3);}
.panel-body{padding:1.5rem;}

/* FORMS */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group label{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text3);}
.form-control{background:var(--bg2);border:1px solid var(--border);border-radius:9px;color:var(--text);font-family:'Inter',sans-serif;font-size:14px;font-weight:500;padding:10px 13px;outline:none;transition:all 0.2s;width:100%;-webkit-appearance:none;}
.form-control:focus{border-color:rgba(79,124,255,0.5);box-shadow:0 0 0 3px rgba(79,124,255,0.1);}
.form-control option{background:var(--surface);}
.form-hint{font-size:11px;color:var(--text3);margin-top:2px;}
.btn-submit{font-size:13px;font-weight:700;padding:11px 26px;background:linear-gradient(135deg,#00c97a,#00e5a0);color:#001a12;border:none;border-radius:9px;cursor:pointer;transition:all 0.2s;}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 0 20px rgba(0,229,160,0.3);}
.btn-submit:disabled{opacity:0.4;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-purple-solid{font-size:13px;font-weight:700;padding:11px 26px;background:linear-gradient(135deg,rgba(168,85,247,0.8),rgba(168,85,247,1));color:#fff;border:none;border-radius:9px;cursor:pointer;transition:all 0.2s;}
.btn-purple-solid:hover{transform:translateY(-1px);box-shadow:0 0 20px rgba(168,85,247,0.3);}
.btn-purple-solid:disabled{opacity:0.4;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-secondary{font-size:12px;font-weight:600;padding:9px 18px;background:transparent;color:var(--text2);border:1px solid var(--border);border-radius:8px;cursor:pointer;transition:all 0.2s;}
.btn-secondary:hover{background:var(--surface2);border-color:var(--border2);color:var(--text);}
.btn-danger{font-size:12px;font-weight:600;padding:6px 12px;background:rgba(255,77,109,0.08);color:var(--red);border:1px solid rgba(255,77,109,0.25);border-radius:7px;cursor:pointer;}
.btn-amber{font-size:13px;font-weight:700;padding:11px 26px;background:linear-gradient(135deg,rgba(245,158,11,0.8),rgba(245,158,11,1));color:#1a0f00;border:none;border-radius:9px;cursor:pointer;transition:all 0.2s;}
.btn-amber:hover{transform:translateY(-1px);box-shadow:0 0 20px rgba(245,158,11,0.3);}
.btn-amber:disabled{opacity:0.4;cursor:not-allowed;transform:none;box-shadow:none;}

/* WALLET CONNECT PILL */
.wallet-pill{display:inline-flex;align-items:center;gap:8px;background:var(--bg2);border:1px solid var(--border);border-radius:20px;padding:6px 14px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text3);}
.wallet-pill.connected{border-color:rgba(0,229,160,0.3);color:var(--green);}
.wallet-dot{width:7px;height:7px;border-radius:50%;background:var(--text3);flex-shrink:0;}
.wallet-dot.connected{background:var(--green);box-shadow:0 0 6px var(--green);}

/* BURN STEPS */
.burn-steps{display:flex;flex-direction:column;gap:1rem;margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);}
.burn-step-header{display:flex;align-items:center;gap:10px;margin-bottom:0.85rem;}
.burn-step-num{width:22px;height:22px;border-radius:50%;background:rgba(79,124,255,0.15);border:1px solid rgba(79,124,255,0.3);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--blue2);flex-shrink:0;}
.burn-step-label{font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text3);}

/* BADGES */
.badge{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;display:inline-block;letter-spacing:0.5px;}
.badge-confirmed,.badge-attested,.badge-minted{color:#00c97a;background:rgba(0,229,160,0.1);border:1px solid rgba(0,229,160,0.2);}
.badge-pending,.badge-burned{color:var(--amber);background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);}
.badge-failed{color:var(--red);background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.2);}
.badge-success{color:#00c97a;background:rgba(0,229,160,0.1);border:1px solid rgba(0,229,160,0.2);}
.badge-partial{color:var(--amber);background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);}

/* TABLES */
table{width:100%;border-collapse:collapse;}
th{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text3);padding:0.7rem 1rem;text-align:left;border-bottom:1px solid var(--border);background:var(--bg2);white-space:nowrap;}
td{padding:0.75rem 1rem;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:rgba(79,124,255,0.03);}
.table-wrap{overflow-x:auto;}
.mono{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text2);}
.green-val{font-family:'JetBrains Mono',monospace;color:var(--green);font-weight:600;}
.fw{font-weight:600;color:var(--text);}

/* RESULT BOXES */
.result-box{border-radius:12px;padding:1.25rem 1.5rem;margin-top:1rem;}
.result-box-success{background:rgba(0,229,160,0.06);border:1px solid rgba(0,229,160,0.2);}
.result-box-error{background:rgba(255,77,109,0.06);border:1px solid rgba(255,77,109,0.25);}
.result-box-info{background:rgba(79,124,255,0.06);border:1px solid rgba(79,124,255,0.2);}
.result-title{font-size:13px;font-weight:700;margin-bottom:8px;}
.result-title-green{color:var(--green);}
.result-title-red{color:var(--red);}
.result-title-blue{color:var(--blue2);}
.result-row{display:flex;justify-content:space-between;font-size:12px;color:var(--text2);padding:4px 0;border-bottom:1px solid rgba(255,255,255,0.04);}
.result-row:last-child{border-bottom:none;}
.result-row span:last-child{font-family:'JetBrains Mono',monospace;color:var(--text);}
.result-code{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text2);background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 12px;margin-top:8px;overflow-x:auto;white-space:pre-wrap;word-break:break-all;}

/* CHAIN FLOW */
.chain-flow{display:flex;align-items:center;gap:0;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;overflow-x:auto;}
.chain-step{display:flex;flex-direction:column;align-items:center;gap:4px;flex-shrink:0;}
.chain-step-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;border:1px solid var(--border);}
.chain-step-label{font-size:10px;font-weight:700;letter-spacing:0.5px;color:var(--text3);text-transform:uppercase;text-align:center;max-width:70px;}
.chain-arrow{font-size:16px;color:var(--text3);padding:0 6px;margin-bottom:18px;}
.step-blue{background:rgba(79,124,255,0.1);border-color:rgba(79,124,255,0.25);}
.step-purple{background:rgba(168,85,247,0.1);border-color:rgba(168,85,247,0.25);}
.step-green{background:rgba(0,229,160,0.1);border-color:rgba(0,229,160,0.25);}
.step-amber{background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.25);}

/* COORDINATION TASKS */
.task-list{display:flex;flex-direction:column;gap:0.75rem;margin:1rem 0;}
.task-item{background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:0.9rem 1rem;display:grid;grid-template-columns:1fr 130px 1fr 30px;gap:0.75rem;align-items:end;}
.task-item .form-group label{font-size:9px;}
.task-dep-badge{font-size:10px;color:var(--cyan);background:rgba(0,212,255,0.08);border:1px solid rgba(0,212,255,0.2);padding:3px 10px;border-radius:20px;width:fit-content;margin-top:4px;}

/* STATS ROW */
.stats-row-3{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:1.25rem 1.5rem;}
.stat-lbl{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);margin-bottom:8px;}
.stat-val{font-family:'JetBrains Mono',monospace;font-size:26px;font-weight:700;}
.stat-accent{width:28px;height:3px;border-radius:2px;margin-top:8px;}

/* COORD RESULT */
.coord-results{display:flex;flex-direction:column;gap:8px;margin-top:1rem;}
.coord-result-item{display:flex;align-items:center;justify-content:space-between;background:var(--bg2);border:1px solid var(--border);border-radius:9px;padding:0.75rem 1rem;}
.coord-result-left{display:flex;align-items:center;gap:10px;}
.coord-result-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;}
.coord-result-name{font-weight:600;font-size:13px;color:var(--text);}
.coord-result-amount{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text2);}
.coord-result-right{display:flex;align-items:center;gap:10px;}

/* LOADING */
.loading{text-align:center;padding:2.5rem;font-size:13px;color:var(--text3);}
.spinner{display:inline-block;width:18px;height:18px;border:2px solid var(--border);border-top-color:var(--blue);border-radius:50%;animation:spin 0.7s linear infinite;margin-right:8px;vertical-align:middle;}
@keyframes spin{to{transform:rotate(360deg)}}
.empty{text-align:center;padding:2rem;color:var(--text3);font-size:13px;}

/* TOAST */
#toast{position:fixed;bottom:1.5rem;right:1.5rem;padding:13px 20px;border-radius:12px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;z-index:9999;transform:translateY(80px);opacity:0;transition:all 0.3s;max-width:360px;border:1px solid;backdrop-filter:blur(20px);}
#toast.show{transform:translateY(0);opacity:1;}
#toast.success{color:var(--green);border-color:rgba(0,229,160,0.3);background:rgba(0,229,160,0.08);}
#toast.error{color:var(--red);border-color:rgba(255,77,109,0.3);background:rgba(255,77,109,0.08);}

@media(max-width:900px){
  .form-grid,.stats-row-3{grid-template-columns:1fr;}
  .task-item{grid-template-columns:1fr 1fr;}
  .m3-banner{flex-direction:column;}
  .m3-banner-right{width:100%;justify-content:space-between;}
}
</style>
</head>
<body>

<nav>
  <a href="/" class="brand"><div class="brand-icon">⚡</div><span class="hl">ArcOn</span>Agent</a>
  <div class="nav-links">
    <a href="/" class="nav-btn">Home</a>
    <a href="/" class="nav-btn">Dashboard</a>
    <a href="/" class="nav-btn">Send Payment</a>
    <a href="/" class="nav-btn">Agents</a>
    <a href="/" class="nav-btn">Triggers</a>
    <a href="/" class="nav-btn">Payments</a>
    <a href="/" class="nav-btn">Audit Logs</a>
    <a href="/" class="nav-btn">API Docs</a>
    <a href="#" class="nav-btn active">M3 · Arc + Scale</a>
  </div>
  <div class="live-pill"><span class="live-dot"></span>Live · Base Sepolia</div>
</nav>

<div class="container">

  <!-- M3 BANNER -->
  <div class="m3-banner">
    <div class="m3-banner-left">
      <div class="m3-eyebrow">Milestone 03 · $4,000 USDC · Arc Integration + Scale</div>
      <div class="m3-banner-title">Arc Integration + Scale</div>
      <div class="m3-banner-sub">CCTP cross-chain transfers · Multi-agent coordination · Production deployment</div>
    </div>
    <div class="m3-banner-right">
      <div class="m3-stat">
        <div class="m3-stat-val" id="stat-crosschain">—</div>
        <div class="m3-stat-lbl">Cross-chain</div>
      </div>
      <div class="m3-stat">
        <div class="m3-stat-val" id="stat-coordinations">—</div>
        <div class="m3-stat-lbl">Coordinations</div>
      </div>
      <div class="m3-stat">
        <div class="m3-stat-val" id="stat-chains">5+</div>
        <div class="m3-stat-lbl">Chains</div>
      </div>
    </div>
  </div>

  <!-- TAB BAR -->
  <div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('cctp',this)">⛓️ CCTP Cross-chain</button>
    <button class="tab-btn" onclick="switchTab('coordination',this)">🤖 Multi-agent Coordination</button>
    <button class="tab-btn" onclick="switchTab('history',this)">📋 Transfer History</button>
  </div>

  <!-- ═══════════════════════════════════════ TAB: CCTP ═══════════════════════════════════════ -->
  <div id="tab-cctp" class="tab-content active">

    <!-- CCTP Flow Explainer -->
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">CCTP Burn-and-Mint Flow</span>
        <span class="panel-meta">Circle Cross-Chain Transfer Protocol V2</span>
      </div>
      <div class="panel-body">
        <div class="chain-flow">
          <div class="chain-step">
            <div class="chain-step-icon step-blue">🤖</div>
            <div class="chain-step-label">Agent Wallet</div>
          </div>
          <div class="chain-arrow">→</div>
          <div class="chain-step">
            <div class="chain-step-icon step-amber">🔥</div>
            <div class="chain-step-label">Burn USDC</div>
          </div>
          <div class="chain-arrow">→</div>
          <div class="chain-step">
            <div class="chain-step-icon step-purple">🔮</div>
            <div class="chain-step-label">Iris Attestation</div>
          </div>
          <div class="chain-arrow">→</div>
          <div class="chain-step">
            <div class="chain-step-icon step-green">✅</div>
            <div class="chain-step-label">Mint on Dest</div>
          </div>
          <div class="chain-arrow">→</div>
          <div class="chain-step">
            <div class="chain-step-icon step-blue">💰</div>
            <div class="chain-step-label">Receive USDC</div>
          </div>
        </div>
        <div style="font-size:12px;color:var(--text3);line-height:1.8;">
          CCTP burns USDC on the source chain, Circle's Iris service attests the burn, then native USDC is minted on the destination chain. No wrapped tokens, no liquidity pools — pure 1:1 native transfer.
        </div>
      </div>
    </div>

    <!-- STEP 1: INITIATE -->
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Step 1 — Initiate Cross-chain Transfer</span>
        <span class="panel-meta">POST /api/v1/crosschain/initiate</span>
      </div>
      <div class="panel-body">
        <div class="form-grid" style="margin-bottom:1rem;">
          <div class="form-group">
            <label for="cc-agent">Agent (Sender)</label>
            <select class="form-control" id="cc-agent">
              <option value="">— Select Agent —</option>
            </select>
            <div class="form-hint">Uses agent's SCA wallet on Base-Sepolia</div>
          </div>
          <div class="form-group">
            <label for="cc-dest-chain">Destination Chain</label>
            <select class="form-control" id="cc-dest-chain">
              <option value="">— Select Destination —</option>
              <option value="ethereum-sepolia">Ethereum Sepolia (Domain 0)</option>
              <option value="avalanche-fuji">Avalanche Fuji (Domain 1)</option>
              <option value="op-sepolia">OP Sepolia (Domain 2)</option>
              <option value="arbitrum-sepolia">Arbitrum Sepolia (Domain 3)</option>
              <option value="polygon-amoy">Polygon Amoy (Domain 7)</option>
            </select>
          </div>
          <div class="form-group">
            <label for="cc-dest-address">Destination Address</label>
            <input type="text" class="form-control" id="cc-dest-address" placeholder="0x... wallet on destination chain">
          </div>
          <div class="form-group">
            <label for="cc-amount">Amount (USDC)</label>
            <input type="number" class="form-control" id="cc-amount" placeholder="0.000001" step="0.000001" min="0.000001">
          </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
          <button class="btn-submit" id="cc-initiate-btn" onclick="initiateCrossChain()">⛓️ Initiate Transfer</button>
          <span id="cc-status" style="font-size:12px;color:var(--text3);"></span>
        </div>
      </div>
    </div>

    <!-- STEP 2+3: BURN + SUBMIT (shown after initiate) -->
    <div id="cc-result" style="display:none">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Transfer Initiated</span>
          <span class="panel-meta" id="cc-result-id"></span>
        </div>
        <div class="panel-body">

          <!-- Initiate result summary -->
          <div class="result-box result-box-info" id="cc-result-body"></div>

          <div class="burn-steps">

            <!-- Step 2: Connect MetaMask + Burn -->
            <div>
              <div class="burn-step-header">
                <div class="burn-step-num">2</div>
                <div class="burn-step-label">Connect Wallet &amp; Burn USDC On-chain</div>
              </div>

              <!-- Wallet status row -->
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:0.85rem;flex-wrap:wrap;">
                <button class="btn-secondary" id="connect-wallet-btn" onclick="connectWallet()">🦊 Connect MetaMask</button>
                <div class="wallet-pill" id="wallet-pill">
                  <span class="wallet-dot" id="wallet-dot"></span>
                  <span id="wallet-address">Not connected</span>
                </div>
              </div>

              <!-- Burn button -->
              <div style="display:flex;gap:10px;align-items:center;">
                <button class="btn-amber" id="burn-btn" onclick="burnOnChain()" disabled>🔥 Approve &amp; Burn USDC</button>
                <span id="burn-status" style="font-size:12px;color:var(--text3);"></span>
              </div>
              <div class="form-hint" style="margin-top:8px;">
                Connects to Base Sepolia, approves TokenMessenger, calls <code style="color:var(--blue2);font-size:10px;">depositForBurn()</code>, and auto-fills the hash below.
              </div>
            </div>

            <!-- Step 3: Submit hash (auto-filled after burn) -->
            <div style="padding-top:1rem;border-top:1px solid var(--border);">
              <div class="burn-step-header">
                <div class="burn-step-num">3</div>
                <div class="burn-step-label">Submit Burn TX Hash</div>
              </div>
              <div style="display:flex;gap:10px;align-items:flex-end;">
                <div class="form-group" style="flex:1;">
                  <label for="cc-burn-hash">Burn Transaction Hash</label>
                  <input type="text" class="form-control" id="cc-burn-hash" placeholder="Auto-filled after burn, or paste manually">
                </div>
                <button class="btn-secondary" onclick="submitBurnHash()">Submit Hash</button>
              </div>
              <div class="form-hint" style="margin-top:6px;">
                Hash is auto-filled after on-chain burn. Click Submit to save &amp; track attestation via Iris.
              </div>
            </div>

          </div><!-- /burn-steps -->
        </div>
      </div>
    </div>

    <!-- STEP 4: CHECK ATTESTATION -->
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Step 4 — Check Attestation Status</span>
        <span class="panel-meta">GET /api/v1/crosschain/{id}/status</span>
      </div>
      <div class="panel-body">
        <div style="display:flex;gap:10px;align-items:flex-end;">
          <div class="form-group" style="flex:1;">
            <label for="cc-check-id">Transfer ID</label>
            <input type="number" class="form-control" id="cc-check-id" placeholder="e.g. 1">
          </div>
          <button class="btn-secondary" onclick="checkAttestation()">🔮 Check Status</button>
        </div>
        <div id="cc-attest-result" style="margin-top:1rem;display:none;"></div>
      </div>
    </div>

    <!-- CONTRACT INFO -->
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Contract Reference</span>
        <span class="panel-meta">Base Sepolia Testnet</span>
      </div>
      <div class="panel-body">
        <div class="form-grid">
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);margin-bottom:8px;">USDC Contract</div>
            <div class="mono" style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:11px;word-break:break-all;">0x036CbD53842c5426634e7929541eC2318f3dCF7e</div>
          </div>
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);margin-bottom:8px;">Token Messenger (CCTP)</div>
            <div class="mono" style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:11px;word-break:break-all;">0x9f3B8679c73C2Fef8b59B4f3444d4e156fb70AA5</div>
          </div>
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);margin-bottom:8px;">Iris Attestation API</div>
            <div class="mono" style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:11px;word-break:break-all;">https://iris-api-sandbox.circle.com/v2/messages</div>
          </div>
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);margin-bottom:8px;">Source Domain</div>
            <div class="mono" style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:11px;">Base Sepolia → Domain ID: 6</div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /tab-cctp -->

  <!-- ═══════════════════════════════════════ TAB: COORDINATION ═══════════════════════════════════════ -->
  <div id="tab-coordination" class="tab-content">

    <div class="stats-row-3">
      <div class="stat-card">
        <div class="stat-lbl">Total Coordinations</div>
        <div class="stat-val" style="color:var(--purple);" id="coord-total">—</div>
        <div class="stat-accent" style="background:var(--purple);"></div>
      </div>
      <div class="stat-card">
        <div class="stat-lbl">Tasks Succeeded</div>
        <div class="stat-val" style="color:var(--green);" id="coord-succeeded">—</div>
        <div class="stat-accent" style="background:var(--green);"></div>
      </div>
      <div class="stat-card">
        <div class="stat-lbl">Tasks Failed</div>
        <div class="stat-val" style="color:var(--red);" id="coord-failed">—</div>
        <div class="stat-accent" style="background:var(--red);"></div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Execute Multi-agent Coordination</span>
        <span class="panel-meta">POST /api/v1/coordination/execute</span>
      </div>
      <div class="panel-body">

        <div style="margin-bottom:1.25rem;">
          <div class="form-group">
            <label for="coord-orchestrator">Orchestrator Agent</label>
            <select class="form-control" id="coord-orchestrator" style="max-width:320px;">
              <option value="">— Select Orchestrator —</option>
            </select>
          </div>
          <div class="form-hint" style="margin-top:6px;">This agent's wallet will be the sender for all tasks. Use SCA agents (4+5) for gas-free execution.</div>
        </div>

        <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text3);margin-bottom:0.75rem;">Task Queue</div>

        <div class="task-list" id="coord-tasks">
          <div class="task-item" id="task-0">
            <div class="form-group">
              <label>Receiver Agent</label>
              <select class="form-control task-receiver"><option value="">— Agent —</option></select>
            </div>
            <div class="form-group">
              <label>Amount (USDC)</label>
              <input type="number" class="form-control task-amount" placeholder="0.10" step="0.000001" min="0.000001">
            </div>
            <div class="form-group">
              <label>Note</label>
              <input type="text" class="form-control task-note" placeholder="Task A payment">
            </div>
            <div class="form-group">
              <label>&nbsp;</label>
              <button style="background:rgba(255,77,109,0.08);border:1px solid rgba(255,77,109,0.2);color:var(--red);border-radius:8px;padding:10px;cursor:pointer;font-size:13px;width:100%;" onclick="removeTask(0)">✕</button>
            </div>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-bottom:1.25rem;flex-wrap:wrap;">
          <button class="btn-secondary" onclick="addTask()">+ Add Task</button>
          <button class="btn-secondary" onclick="addDependentTask()">+ Add Dependent Task</button>
        </div>

        <div style="background:rgba(168,85,247,0.06);border:1px solid rgba(168,85,247,0.2);border-radius:10px;padding:0.85rem 1.1rem;margin-bottom:1.25rem;font-size:12px;color:var(--text2);line-height:1.8;">
          <span style="color:var(--purple);font-weight:700;">Dependent tasks</span> only execute if their required prior task succeeded. Use "Add Dependent Task" to chain payments sequentially.
        </div>

        <div style="display:flex;gap:10px;align-items:center;">
          <button class="btn-purple-solid" id="coord-execute-btn" onclick="executeCoordination()">🤖 Execute Coordination</button>
          <span id="coord-status" style="font-size:12px;color:var(--text3);"></span>
        </div>
      </div>
    </div>

    <!-- COORDINATION RESULT -->
    <div id="coord-result" style="display:none;">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Coordination Result</span>
          <span class="panel-meta" id="coord-result-summary"></span>
        </div>
        <div class="panel-body">
          <div class="coord-results" id="coord-result-items"></div>
        </div>
      </div>
    </div>

  </div><!-- /tab-coordination -->

  <!-- ═══════════════════════════════════════ TAB: HISTORY ═══════════════════════════════════════ -->
  <div id="tab-history" class="tab-content">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">

      <div class="panel" style="margin-bottom:0;">
        <div class="panel-header">
          <span class="panel-title">Cross-chain Transfers</span>
          <button onclick="loadTransferHistory()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace;">↻ Refresh</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>Agent</th><th>Destination</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody id="cc-history-body"><tr><td colspan="5" class="loading"><span class="spinner"></span>Loading...</td></tr></tbody>
          </table>
        </div>
      </div>

      <div class="panel" style="margin-bottom:0;">
        <div class="panel-header">
          <span class="panel-title">Coordination Payments</span>
          <button onclick="loadCoordHistory()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace;">↻ Refresh</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>Orchestrator</th><th>Receiver</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody id="coord-history-body"><tr><td colspan="5" class="loading"><span class="spinner"></span>Loading...</td></tr></tbody>
          </table>
        </div>
      </div>

    </div>

  </div><!-- /tab-history -->

</div><!-- /container -->

<div id="toast"></div>

<script>
const API = '/api';
let agents = [];
let taskCount = 1;
let currentTransferId = null;
let coordStats = { total: 0, succeeded: 0, failed: 0 };

/* ─── CONTRACTS ─────────────────────────────────────────────────────────────── */
const USDC_ADDRESS       = '0x036CbD53842c5426634e7929541eC2318f3dCF7e';
const TOKEN_MESSENGER    = '0x9f3B8679c73C2Fef8b59B4f3444d4e156fb70AA5';
const BASE_SEPOLIA_CHAIN = 84532;

const USDC_ABI = [
  'function approve(address spender, uint256 amount) returns (bool)',
  'function allowance(address owner, address spender) view returns (uint256)',
];
const MESSENGER_ABI = [
  'function depositForBurn(uint256 amount, uint32 destinationDomain, bytes32 mintRecipient, address burnToken) returns (uint64)',
];
const DOMAIN_MAP = {
  'ethereum-sepolia': 0,
  'avalanche-fuji':   1,
  'op-sepolia':       2,
  'arbitrum-sepolia': 3,
  'polygon-amoy':     7,
};

let web3Provider, web3Signer, walletAddress;

/* ─── API HELPERS ───────────────────────────────────────────────────────────── */
async function apiFetch(path, opts = {}) {
  const res = await fetch(API + path, {
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', ...(opts.headers || {}) },
    ...opts,
  });
  let data;
  try { data = await res.json(); } catch(_) { data = {}; }
  data._status = res.status;
  data._ok = res.ok;
  return data;
}

async function apiFetchV1(path, opts = {}) {
  const token = localStorage.getItem('arcon_token');
  return apiFetch('/v1' + path, {
    ...opts,
    headers: { ...(token ? { 'Authorization': 'Bearer ' + token } : {}), ...(opts.headers || {}) },
  });
}

/* ─── UI HELPERS ────────────────────────────────────────────────────────────── */
function toast(msg, type = 'success') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = 'show ' + type;
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.className = ''; }, 4500);
}

function switchTab(name, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
  if (name === 'history') { loadTransferHistory(); loadCoordHistory(); }
}

async function loadAgents() {
  const res = await apiFetch('/agents');
  agents = res.data || res;
  if (!Array.isArray(agents)) return;
  const opts = agents.map(a => `<option value="${a.id}">${a.name}</option>`).join('');
  ['cc-agent', 'coord-orchestrator'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.innerHTML = `<option value="">— Select —</option>` + opts;
  });
  document.querySelectorAll('.task-receiver').forEach(el => {
    el.innerHTML = `<option value="">— Agent —</option>` + opts;
  });
}

/* ─── WALLET / WEB3 ─────────────────────────────────────────────────────────── */
async function connectWallet() {
  if (!window.ethereum) {
    toast('MetaMask not found — please install it', 'error');
    return;
  }
  try {
    web3Provider = new ethers.providers.Web3Provider(window.ethereum);
    await web3Provider.send('eth_requestAccounts', []);

    // Switch/add Base Sepolia
    try {
      await window.ethereum.request({
        method: 'wallet_switchEthereumChain',
        params: [{ chainId: '0x' + BASE_SEPOLIA_CHAIN.toString(16) }],
      });
    } catch (switchErr) {
      if (switchErr.code === 4902) {
        await window.ethereum.request({
          method: 'wallet_addEthereumChain',
          params: [{
            chainId: '0x' + BASE_SEPOLIA_CHAIN.toString(16),
            chainName: 'Base Sepolia',
            nativeCurrency: { name: 'ETH', symbol: 'ETH', decimals: 18 },
            rpcUrls: ['https://sepolia.base.org'],
            blockExplorerUrls: ['https://sepolia.basescan.org'],
          }],
        });
      } else { throw switchErr; }
    }

    web3Signer   = web3Provider.getSigner();
    walletAddress = await web3Signer.getAddress();

    // Update UI
    const short = walletAddress.substring(0,6) + '…' + walletAddress.slice(-4);
    document.getElementById('wallet-address').textContent = short;
    document.getElementById('wallet-pill').classList.add('connected');
    document.getElementById('wallet-dot').classList.add('connected');
    document.getElementById('connect-wallet-btn').textContent = '✓ Connected';
    document.getElementById('burn-btn').disabled = false;
    toast('Wallet connected: ' + short);
  } catch(e) {
    toast('Wallet connection failed: ' + (e.message || e), 'error');
  }
}

async function burnOnChain() {
  if (!web3Signer)         { toast('Connect MetaMask first', 'error'); return; }
  if (!currentTransferId)  { toast('Initiate a transfer first', 'error'); return; }

  const amount      = parseFloat(document.getElementById('cc-amount').value);
  const destChain   = document.getElementById('cc-dest-chain').value;
  const destAddress = document.getElementById('cc-dest-address').value.trim();

  if (!amount || !destChain || !destAddress) {
    toast('Transfer details missing — fill the form above', 'error'); return;
  }

  const destDomain = DOMAIN_MAP[destChain];
  if (destDomain === undefined) { toast('Unknown destination chain', 'error'); return; }

  const btn    = document.getElementById('burn-btn');
  const status = document.getElementById('burn-status');
  btn.disabled = true;

  try {
    const amountWei     = ethers.BigNumber.from(Math.round(amount * 1_000_000).toString());
    const mintRecipient = ethers.utils.hexZeroPad(destAddress, 32);

    // ── Step 1: Approve ──────────────────────────────────────────────────────
    status.innerHTML = '<span class="spinner"></span>Step 1/2 — Checking allowance…';
    const usdc      = new ethers.Contract(USDC_ADDRESS, USDC_ABI, web3Signer);
    const allowance = await usdc.allowance(walletAddress, TOKEN_MESSENGER);

    if (allowance.lt(amountWei)) {
      status.innerHTML = '<span class="spinner"></span>Step 1/2 — Approve USDC in MetaMask…';
      const approveTx = await usdc.approve(TOKEN_MESSENGER, amountWei);
      status.innerHTML = '<span class="spinner"></span>Waiting for approval confirmation…';
      await approveTx.wait();
      toast('USDC approved ✓');
    } else {
      toast('Allowance sufficient — skipping approve');
    }

    // ── Step 2: Burn ─────────────────────────────────────────────────────────
    status.innerHTML = '<span class="spinner"></span>Step 2/2 — Confirm burn in MetaMask…';
    const messenger = new ethers.Contract(TOKEN_MESSENGER, MESSENGER_ABI, web3Signer);
    const burnTx    = await messenger.depositForBurn(amountWei, destDomain, mintRecipient, USDC_ADDRESS);

    status.innerHTML = '<span class="spinner"></span>Waiting for burn confirmation…';
    const receipt = await burnTx.wait();
    const txHash  = receipt.transactionHash;

    // Auto-fill hash and submit to backend
    document.getElementById('cc-burn-hash').value = txHash;
    status.textContent = '';
    toast('🔥 Burned on-chain! Submitting hash to backend…');
    await submitBurnHash();

  } catch(e) {
    const msg = e.reason || e.data?.message || e.message || 'Unknown error';
    toast('Burn failed: ' + msg, 'error');
    status.textContent = '';
  }

  btn.disabled = false;
}

/* ─── CCTP API CALLS ────────────────────────────────────────────────────────── */
async function initiateCrossChain() {
  const agentId     = document.getElementById('cc-agent').value;
  const destChain   = document.getElementById('cc-dest-chain').value;
  const destAddress = document.getElementById('cc-dest-address').value.trim();
  const amount      = document.getElementById('cc-amount').value;

  if (!agentId || !destChain || !destAddress || !amount) {
    toast('Fill in all fields', 'error'); return;
  }

  const btn    = document.getElementById('cc-initiate-btn');
  const status = document.getElementById('cc-status');
  btn.disabled = true;
  status.innerHTML = '<span class="spinner"></span>Initiating…';

  try {
    const res = await apiFetchV1('/crosschain/initiate', {
      method: 'POST',
      body: JSON.stringify({ agent_id: parseInt(agentId), destination_chain: destChain, destination_address: destAddress, amount: parseFloat(amount) }),
    });

    if (res.success || res._ok) {
      currentTransferId = res.transfer_id;
      document.getElementById('cc-result').style.display = 'block';
      document.getElementById('cc-result-id').textContent = 'Transfer ID: #' + res.transfer_id;
      const instr = res.instructions || {};
      document.getElementById('cc-result-body').innerHTML = `
        <div class="result-title result-title-blue">Transfer Recorded — ID #${res.transfer_id}</div>
        <div class="result-row"><span>Token Messenger</span><span>${(instr.token_messenger||'').substring(0,14)}…</span></div>
        <div class="result-row"><span>USDC Contract</span><span>${(instr.usdc_contract||'').substring(0,14)}…</span></div>
        <div class="result-row"><span>Destination Domain</span><span>${instr.destination_domain ?? '—'}</span></div>
        <div class="result-row"><span>Amount (wei)</span><span>${instr.amount_wei || '—'}</span></div>`;
      document.getElementById('cc-check-id').value = res.transfer_id;
      document.getElementById('cc-result').scrollIntoView({ behavior: 'smooth', block: 'start' });
      toast('Transfer recorded — now connect wallet and burn USDC below.');
      updateM3Stats('crosschain');
    } else {
      toast(res.message || res.error || 'Failed to initiate', 'error');
    }
  } catch(e) {
    toast('Error: ' + e.message, 'error');
  }

  btn.disabled = false;
  status.textContent = '';
}

async function submitBurnHash() {
  const hash = document.getElementById('cc-burn-hash').value.trim();
  if (!hash)               { toast('Enter the burn tx hash', 'error'); return; }
  if (!currentTransferId)  { toast('No active transfer', 'error'); return; }

  const res = await apiFetchV1(`/crosschain/${currentTransferId}/burn-hash`, {
    method: 'POST',
    body: JSON.stringify({ tx_hash: hash }),
  });

  if (res.success || res._ok) {
    toast('Hash submitted ✓ Status: ' + (res.status || 'burned') + ' — use Step 4 to check attestation');
  } else {
    toast(res.message || 'Failed to submit hash', 'error');
  }
}

async function checkAttestation() {
  const id = document.getElementById('cc-check-id').value;
  if (!id) { toast('Enter transfer ID', 'error'); return; }

  const resultEl = document.getElementById('cc-attest-result');
  resultEl.style.display = 'block';
  resultEl.innerHTML = '<div class="loading"><span class="spinner"></span>Checking Iris attestation…</div>';

  const res      = await apiFetchV1(`/crosschain/${id}/status`);
  const transfer = res.transfer || {};
  const attest   = res.attestation || {};

  const statusColor = {
    pending: 'var(--amber)', burned: 'var(--amber)',
    attested: 'var(--blue2)', minted: 'var(--green)', failed: 'var(--red)'
  }[transfer.status] || 'var(--text2)';

  resultEl.innerHTML = `
    <div class="result-box result-box-info">
      <div class="result-title result-title-blue">Transfer #${id} Status</div>
      <div class="result-row"><span>Status</span><span style="color:${statusColor};font-weight:700;">${transfer.status || '—'}</span></div>
      <div class="result-row"><span>Destination Chain</span><span>${transfer.destination_chain || '—'}</span></div>
      <div class="result-row"><span>Amount</span><span>${transfer.amount_usdc || '—'} USDC</span></div>
      <div class="result-row"><span>Burn TX</span><span>${transfer.burn_tx_hash ? transfer.burn_tx_hash.substring(0,16)+'…' : 'Not submitted'}</span></div>
      <div class="result-row"><span>Iris Attestation</span><span>${attest.status || (transfer.attestation ? 'complete' : 'pending')}</span></div>
      ${transfer.attestation ? `<div class="result-code">${transfer.attestation.substring(0,80)}…</div>` : ''}
    </div>`;
}

/* ─── COORDINATION ──────────────────────────────────────────────────────────── */
function addTask(dependsOn = null) {
  const idx  = taskCount++;
  const opts = agents.map(a => `<option value="${a.id}">${a.name}</option>`).join('');
  const item = document.createElement('div');
  item.className = 'task-item';
  item.id = 'task-' + idx;
  item.innerHTML = `
    <div class="form-group">
      <label>Receiver Agent</label>
      <select class="form-control task-receiver"><option value="">— Agent —</option>${opts}</select>
      ${dependsOn !== null ? `<div class="task-dep-badge">Depends on Task ${dependsOn + 1}</div>` : ''}
    </div>
    <div class="form-group">
      <label>Amount (USDC)</label>
      <input type="number" class="form-control task-amount" placeholder="0.10" step="0.000001" min="0.000001">
    </div>
    <div class="form-group">
      <label>Note</label>
      <input type="text" class="form-control task-note" placeholder="Task note...">
    </div>
    <div class="form-group">
      <label>&nbsp;</label>
      <button style="background:rgba(255,77,109,0.08);border:1px solid rgba(255,77,109,0.2);color:var(--red);border-radius:8px;padding:10px;cursor:pointer;font-size:13px;width:100%;" onclick="removeTask(${idx})">✕</button>
    </div>`;
  if (dependsOn !== null) item.dataset.dependsOn = dependsOn;
  document.getElementById('coord-tasks').appendChild(item);
}

function addDependentTask() {
  const tasks   = document.querySelectorAll('.task-item');
  const lastIdx = tasks.length - 1;
  addTask(lastIdx);
}

function removeTask(idx) {
  const el = document.getElementById('task-' + idx);
  if (el) el.remove();
}

async function executeCoordination() {
  const orchestratorId = document.getElementById('coord-orchestrator').value;
  if (!orchestratorId) { toast('Select an orchestrator agent', 'error'); return; }

  const taskItems = document.querySelectorAll('.task-item');
  const tasks = [];
  let valid = true;

  taskItems.forEach((item, idx) => {
    const receiver = item.querySelector('.task-receiver')?.value;
    const amount   = item.querySelector('.task-amount')?.value;
    const note     = item.querySelector('.task-note')?.value;
    const dep      = item.dataset.dependsOn;
    if (!receiver || !amount) { valid = false; return; }
    const task = { receiver_agent_id: parseInt(receiver), amount: parseFloat(amount) };
    if (note) task.note = note;
    if (dep !== undefined) task.depends_on = parseInt(dep);
    tasks.push(task);
  });

  if (!valid || !tasks.length) { toast('Fill all task fields', 'error'); return; }

  const btn    = document.getElementById('coord-execute-btn');
  const status = document.getElementById('coord-status');
  btn.disabled = true;
  status.innerHTML = '<span class="spinner"></span>Executing…';

  try {
    const res = await apiFetchV1('/coordination/execute', {
      method: 'POST',
      body: JSON.stringify({ orchestrator_agent_id: parseInt(orchestratorId), tasks }),
    });

    document.getElementById('coord-result').style.display = 'block';
    const results   = res.results || {};
    const succeeded = res.succeeded ?? 0;
    const failed    = res.failed ?? 0;

    document.getElementById('coord-result-summary').textContent =
      `${succeeded} succeeded · ${failed} failed · ${res.total_tasks || tasks.length} total`;

    const items = Object.values(results).map((r, i) => `
      <div class="coord-result-item">
        <div class="coord-result-left">
          <div class="coord-result-icon" style="background:${r.success ? 'rgba(0,229,160,0.1)' : 'rgba(255,77,109,0.1)'};border:1px solid ${r.success ? 'rgba(0,229,160,0.2)' : 'rgba(255,77,109,0.2)'};">
            ${r.success ? '✓' : r.skipped ? '⊘' : '✕'}
          </div>
          <div>
            <div class="coord-result-name">Task ${i + 1} → ${r.receiver || '—'}</div>
            <div class="coord-result-amount">${r.amount ? r.amount + ' USDC' : r.reason || '—'}</div>
          </div>
        </div>
        <div class="coord-result-right">
          ${r.circle_payment_id ? `<span class="mono" style="font-size:11px;">${r.circle_payment_id.substring(0,10)}…</span>` : ''}
          <span class="badge ${r.success ? 'badge-success' : r.skipped ? 'badge-pending' : 'badge-failed'}">${r.success ? 'success' : r.skipped ? 'skipped' : 'failed'}</span>
        </div>
      </div>`).join('');

    document.getElementById('coord-result-items').innerHTML = items;

    coordStats.total++;
    coordStats.succeeded += succeeded;
    coordStats.failed    += failed;
    document.getElementById('coord-total').textContent     = coordStats.total;
    document.getElementById('coord-succeeded').textContent = coordStats.succeeded;
    document.getElementById('coord-failed').textContent    = coordStats.failed;

    toast(failed === 0
      ? `All ${succeeded} tasks executed successfully!`
      : `${succeeded} tasks done, ${failed} failed`,
      failed > succeeded ? 'error' : 'success');

  } catch(e) {
    toast('Error: ' + e.message, 'error');
  }

  btn.disabled = false;
  status.textContent = '';
}

/* ─── HISTORY ───────────────────────────────────────────────────────────────── */
async function loadTransferHistory() {
  const tbody = document.getElementById('cc-history-body');
  tbody.innerHTML = '<tr><td colspan="5" class="loading"><span class="spinner"></span>Loading…</td></tr>';
  try {
    const res  = await apiFetchV1('/crosschain');
    const list = res.data || res;
    if (!Array.isArray(list) || !list.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="empty">No cross-chain transfers yet</td></tr>';
      document.getElementById('stat-crosschain').textContent = '0';
      return;
    }
    document.getElementById('stat-crosschain').textContent = list.length;
    tbody.innerHTML = list.map(t => `
      <tr>
        <td class="mono">${String(t.id).padStart(3,'0')}</td>
        <td class="fw">${t.agent?.name || '—'}</td>
        <td class="mono" style="font-size:11px;">${t.destination_chain || '—'}</td>
        <td class="green-val">${parseFloat(t.amount_usdc||0).toFixed(4)}</td>
        <td><span class="badge badge-${t.status || 'pending'}">${t.status || 'pending'}</span></td>
      </tr>`).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="5" class="empty">Error loading transfers</td></tr>';
  }
}

async function loadCoordHistory() {
  const tbody = document.getElementById('coord-history-body');
  tbody.innerHTML = '<tr><td colspan="5" class="loading"><span class="spinner"></span>Loading…</td></tr>';
  try {
    const res  = await apiFetch('/payments');
    const all  = res.data || res;
    const coordPayments = Array.isArray(all)
      ? all.filter(p => p.note && (p.note.includes('coordination') || p.note.includes('coord')))
      : [];
    if (!coordPayments.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="empty">No coordination payments yet</td></tr>';
      document.getElementById('stat-coordinations').textContent = '0';
      return;
    }
    document.getElementById('stat-coordinations').textContent = coordPayments.length;
    tbody.innerHTML = coordPayments.map(p => `
      <tr>
        <td class="mono">${String(p.id).padStart(3,'0')}</td>
        <td class="fw">${p.sender_agent?.name || '—'}</td>
        <td class="fw">${p.receiver_agent?.name || '—'}</td>
        <td class="green-val">${parseFloat(p.amount).toFixed(6)}</td>
        <td><span class="badge badge-${p.status||'pending'}">${p.status||'pending'}</span></td>
      </tr>`).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="5" class="empty">Error loading payments</td></tr>';
  }
}

function updateM3Stats(type) {
  if (type === 'crosschain') {
    const el = document.getElementById('stat-crosschain');
    el.textContent = (parseInt(el.textContent) || 0) + 1;
  }
}

/* ─── AUTH ──────────────────────────────────────────────────────────────────── */
async function ensureToken() {
  if (localStorage.getItem('arcon_token')) return;
  try {
    const res = await apiFetch('/v1/auth/token', {
      method: 'POST',
      body: JSON.stringify({ email: 'admin@arconagent.com', password: 'secret123' }),
    });
    if (res.token) localStorage.setItem('arcon_token', res.token);
  } catch(_) {}
}

/* ─── INIT ──────────────────────────────────────────────────────────────────── */
(async () => {
  await ensureToken();
  await loadAgents();
  await loadTransferHistory();
})();
</script>
</body>
</html>