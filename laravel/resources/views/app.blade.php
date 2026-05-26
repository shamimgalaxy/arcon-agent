<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ArcOnAgent — AI Agent Payment Infrastructure</title>
<meta name="description" content="Autonomous AI agents with their own USDC wallets on Base blockchain. Agentic economic activity powered by Circle Developer Platform.">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/arconagent.css') }}">
<style>
/* ── M4 NANO + PAYMASTER ADDITIONS ─────────────────────────────────────── */
:root {
  --nano:     #00e5a0;
  --paymaster:#a855f7;
  --nano-bg:  rgba(0,229,160,0.06);
  --pm-bg:    rgba(168,85,247,0.06);
}

/* M4 milestone card accent */
.milestone-card.m4 {
  border-color: rgba(0,229,160,0.35);
  background: linear-gradient(135deg,rgba(0,229,160,0.04),rgba(168,85,247,0.04));
}
.ms-badge-m4 {
  background: linear-gradient(90deg,rgba(0,229,160,0.2),rgba(168,85,247,0.2));
  color: var(--nano);
  border: 1px solid rgba(0,229,160,0.3);
  border-radius: 6px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.5px;
  padding: 3px 10px;
}
.ms-bar-nano { background: linear-gradient(90deg,var(--nano),var(--paymaster)); }

/* Nano ticker */
#ticker-nano, #ticker-nano2 { color: var(--nano); }

/* ── PAGE: NANO ────────────────────────────────────────────────────────── */
#page-nano .nano-hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}
@media(max-width:768px){ #page-nano .nano-hero { grid-template-columns:1fr; } }

.nano-stat-card {
  background: var(--nano-bg);
  border: 1px solid rgba(0,229,160,0.2);
  border-radius: 16px;
  padding: 1.25rem 1.5rem;
}
.pm-stat-card {
  background: var(--pm-bg);
  border: 1px solid rgba(168,85,247,0.25);
  border-radius: 16px;
  padding: 1.25rem 1.5rem;
}
.nano-stat-card .stat-lbl,
.pm-stat-card   .stat-lbl {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--text3);
  margin-bottom: 0.35rem;
}
.nano-stat-card .stat-val { color: var(--nano);     font-size: 28px; font-weight: 800; font-family:'JetBrains Mono',monospace; }
.pm-stat-card   .stat-val { color: var(--paymaster);font-size: 28px; font-weight: 800; font-family:'JetBrains Mono',monospace; }

/* Section eyebrow reuse */
.nano-eyebrow {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--nano);
  margin-bottom: 0.4rem;
}
.pm-eyebrow {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--paymaster);
  margin-bottom: 0.4rem;
}

/* Toggle switch */
.toggle-wrap { display:flex; align-items:center; gap:10px; }
.toggle-label { font-size:12px; color:var(--text2); }
.toggle-switch {
  position:relative; width:42px; height:22px; cursor:pointer;
}
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider {
  position:absolute; inset:0;
  background:rgba(255,255,255,0.1);
  border:1px solid var(--border);
  border-radius:11px;
  transition:.25s;
}
.toggle-slider:before {
  content:'';
  position:absolute;
  width:16px; height:16px;
  left:2px; top:2px;
  background:var(--text3);
  border-radius:50%;
  transition:.25s;
}
.toggle-switch input:checked + .toggle-slider { background:rgba(168,85,247,0.25); border-color:rgba(168,85,247,0.5); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); background:var(--paymaster); }

/* Nano stream badge */
.badge-nano      { background:rgba(0,229,160,0.15); color:var(--nano);      border:1px solid rgba(0,229,160,0.3); }
.badge-streaming { background:rgba(0,229,160,0.1);  color:var(--nano);      border:1px solid rgba(0,229,160,0.25); animation:pulse-nano 1.5s infinite; }
.badge-paymaster { background:rgba(168,85,247,0.15);color:var(--paymaster); border:1px solid rgba(168,85,247,0.3); }
@keyframes pulse-nano {
  0%,100%{ box-shadow:0 0 0 0 rgba(0,229,160,0.3); }
  50%{     box-shadow:0 0 0 4px rgba(0,229,160,0); }
}

/* Nano payment feed table */
#nano-feed-body td,
#nano-payments-body td { font-size:12px; }
.micro-val { color:var(--nano); font-family:'JetBrains Mono',monospace; font-size:12px; font-weight:600; }

/* Agent paymaster card */
.pm-agent-card {
  display:flex; align-items:center; gap:14px;
  padding:0.85rem 1.25rem;
  border-bottom:1px solid var(--border);
  transition: background .15s;
}
.pm-agent-card:hover { background:rgba(168,85,247,0.04); }
.pm-agent-card:last-child { border-bottom:none; }
.pm-agent-info { flex:1; min-width:0; }
.pm-agent-name { font-weight:600; font-size:13px; color:var(--text); }
.pm-agent-addr { font-family:'JetBrains Mono',monospace; font-size:10px; color:var(--text3); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.pm-agent-limit-input {
  width:90px;
  background:rgba(168,85,247,0.06);
  border:1px solid rgba(168,85,247,0.25);
  border-radius:8px;
  color:var(--text);
  font-family:'JetBrains Mono',monospace;
  font-size:12px;
  padding:6px 10px;
  text-align:right;
}
.pm-agent-limit-input:focus { outline:none; border-color:rgba(168,85,247,0.5); }
.btn-nano {
  background:rgba(0,229,160,0.1);
  border:1px solid rgba(0,229,160,0.3);
  color:var(--nano);
  border-radius:8px;
  padding:7px 14px;
  font-size:12px;
  font-weight:600;
  cursor:pointer;
  font-family:'JetBrains Mono',monospace;
  transition:all .15s;
}
.btn-nano:hover { background:rgba(0,229,160,0.18); }
.btn-paymaster {
  background:rgba(168,85,247,0.1);
  border:1px solid rgba(168,85,247,0.3);
  color:var(--paymaster);
  border-radius:8px;
  padding:7px 14px;
  font-size:12px;
  font-weight:600;
  cursor:pointer;
  font-family:'JetBrains Mono',monospace;
  transition:all .15s;
}
.btn-paymaster:hover { background:rgba(168,85,247,0.18); }

/* Stream progress bar */
.stream-progress-wrap { margin-top:6px; }
.stream-progress-bar-bg { height:3px; background:rgba(0,229,160,0.1); border-radius:2px; overflow:hidden; }
.stream-progress-bar-fill { height:100%; background:var(--nano); border-radius:2px; transition:width .3s; }
.stream-info { display:flex; justify-content:space-between; font-size:10px; color:var(--text3); margin-top:3px; font-family:'JetBrains Mono',monospace; }

/* Info box reuse */
.nano-info-box {
  background:rgba(0,229,160,0.05);
  border:1px solid rgba(0,229,160,0.2);
  border-radius:12px;
  padding:1rem 1.25rem;
  margin-top:1rem;
}
.pm-info-box {
  background:rgba(168,85,247,0.05);
  border:1px solid rgba(168,85,247,0.2);
  border-radius:12px;
  padding:1rem 1.25rem;
  margin-top:1rem;
}
.nano-info-box-title,
.pm-info-box-title {
  font-size:11px; font-weight:700; letter-spacing:1.5px;
  text-transform:uppercase; margin-bottom:6px;
}
.nano-info-box-title { color:var(--nano); }
.pm-info-box-title   { color:var(--paymaster); }
.nano-info-box p, .pm-info-box p {
  font-size:12px; color:var(--text2); line-height:1.8; margin:0;
}

/* M4 feature cards */
.feature.f-nano    .feature-icon-wrap { background:rgba(0,229,160,0.12);   border-color:rgba(0,229,160,0.25); }
.feature.f-paymaster .feature-icon-wrap { background:rgba(168,85,247,0.12); border-color:rgba(168,85,247,0.25); }
</style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════
     NAVIGATION
     ═══════════════════════════════════════════════════════ -->
<nav>
  <a href="#" class="brand" onclick="showPage('home',this);return false;">
    <div class="brand-icon">⬡</div>
    ARCON<span class="hl">AGENT</span>
  </a>
  <div class="nav-links">
    <button class="nav-btn active"  onclick="showPage('home',this)">Home</button>
    <button class="nav-btn"         onclick="showPage('dashboard',this)">Dashboard</button>
    <button class="nav-btn"         onclick="showPage('agents',this)">Agents</button>
    <button class="nav-btn"         onclick="showPage('send',this)">Send</button>
    <button class="nav-btn"         onclick="showPage('payments',this)">Payments</button>
    <button class="nav-btn"         onclick="showPage('triggers',this)">Triggers</button>
    <button class="nav-btn"         onclick="showPage('audit',this)">Audit Logs</button>
    <button class="nav-btn"         onclick="showPage('docs',this)">API Docs</button>
    <button class="nav-btn"         onclick="window.location.href='/m3'">Arc + Scale</button>
    <button class="nav-btn nano-nav-btn" onclick="showPage('nano',this)">NanoPay</button>
  </div>
  <div class="live-pill">
    <span class="live-dot"></span>
    LIVE · BASE-SEPOLIA
  </div>
</nav>

<!-- ═══════════════════════════════════════════════════════
     TICKER BAR
     ═══════════════════════════════════════════════════════ -->
<div class="ticker-bar">
  <div class="ticker">
    <span class="ticker-item"><span class="label">AGENTS</span>    <span class="up" id="ticker-agents">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">PAYMENTS</span>  <span class="up" id="ticker-payments">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">VOLUME</span>    <span class="up" id="ticker-volume">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">TRIGGERS</span>  <span class="up" id="ticker-triggers">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">NANO</span>      <span id="ticker-nano">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">BLOCK</span>     <span class="up" id="ticker-block">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">NETWORK</span>   <span class="up">Base Sepolia</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">CURRENCY</span>  <span style="color:var(--cyan)">USDC</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">PROTOCOL</span> <span style="color:var(--purple)">Circle CDP</span></span><span class="ticker-sep"> · </span>
    <!-- duplicate for seamless loop -->
    <span class="ticker-item"><span class="label">AGENTS</span>    <span class="up" id="ticker-agents2">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">PAYMENTS</span>  <span class="up" id="ticker-payments2">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">VOLUME</span>    <span class="up" id="ticker-volume2">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">TRIGGERS</span>  <span class="up" id="ticker-triggers2">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">NANO</span>      <span id="ticker-nano2">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">BLOCK</span>     <span class="up" id="ticker-block2">—</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">NETWORK</span>   <span class="up">Base Sepolia</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">CURRENCY</span>  <span style="color:var(--cyan)">USDC</span></span><span class="ticker-sep"> · </span>
    <span class="ticker-item"><span class="label">PROTOCOL</span> <span style="color:var(--purple)">Circle CDP</span></span>
  </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: HOME / LANDING
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-home" class="page active">

  <!-- Hero -->
  <section class="hero">
    <div>
      <div class="hero-eyebrow">
        <span class="hero-eyebrow-dot"></span>
        AGENTIC ECONOMIC ACTIVITY · BASE BLOCKCHAIN · CIRCLE CDP
      </div>
      <h1>AI Agents That<br><span class="grad">Contract, Pay &amp;</span><br>Settle in Real Time</h1>
      <p class="hero-sub">
        Each agent owns a USDC wallet on Arc/Base. Define spending policies, set automated
        triggers, and let your agents transact autonomously — with full audit trails and
        Circle's developer infrastructure.
      </p>
      <div class="hero-btns">
        <button class="btn-primary" onclick="showPage('dashboard',document.querySelectorAll('.nav-btn')[1])">⚡ Launch Dashboard</button>
        <button class="btn-outline" onclick="showPage('docs',document.querySelectorAll('.nav-btn')[7])">View API Docs →</button>
      </div>
    </div>
    <div class="hero-stats-card">
      <div class="hero-stat-row">
        <div class="hsr-label"><div class="hsr-icon fi-blue">🤖</div>Registered Agents</div>
        <div class="hsr-val" id="hs-agents">—</div>
      </div>
      <div class="hero-stat-row">
        <div class="hsr-label"><div class="hsr-icon fi-green">💸</div>Total Payments</div>
        <div class="hsr-val" id="hs-payments">—</div>
      </div>
      <div class="hero-stat-row">
        <div class="hsr-label"><div class="hsr-icon fi-cyan">⚡</div>Active Triggers</div>
        <div class="hsr-val" id="hs-triggers">—</div>
      </div>
      <div class="hero-stat-row">
        <div class="hsr-label"><div class="hsr-icon" style="background:rgba(0,229,160,0.12);border-color:rgba(0,229,160,0.25)">🔬</div>Nanopayments</div>
        <div class="hsr-val" id="hs-nano" style="color:var(--nano)">—</div>
      </div>
      <div class="hero-stat-row">
        <div class="hsr-label"><div class="hsr-icon fi-amber">📊</div>Volume (USDC)</div>
        <div class="hsr-val" id="hs-volume" style="color:var(--green)">—</div>
      </div>
      <div class="network-badge">
        <div class="network-label">Infrastructure</div>
        <div class="network-name">⬡ Arc · Base Sepolia</div>
        <div class="network-sub">Circle Developer Platform · USDC</div>
      </div>
    </div>
  </section>

  <div class="glowing-divider"></div>

  <!-- Milestones -->
  <section class="milestone-section">
    <div class="section-label">CIRCLE DEVELOPER GRANT · $12,000 USDC · 3 MILESTONES + M4 UPGRADE</div>
    <div class="milestone-grid">
      <div class="milestone-card completed">
        <div class="ms-top"><span class="ms-num">MILESTONE 01</span><span class="ms-badge ms-badge-done">✓ COMPLETE</span></div>
        <div class="ms-amount">$4,000 USDC</div>
        <div class="ms-title">Core Infrastructure</div>
        <div class="ms-desc">Circle Developer-Controlled Wallets, agent registration, USDC transfers on Base-Sepolia, spending policy engine, audit trail, real-time WebSocket dashboard.</div>
        <div class="ms-bar-bg"><div class="ms-bar ms-bar-green" style="width:100%"></div></div>
        <div class="ms-progress-label"><span>Complete</span><span>100%</span></div>
      </div>
      <div class="milestone-card completed">
        <div class="ms-top"><span class="ms-num">MILESTONE 02</span><span class="ms-badge ms-badge-done">✓ COMPLETE</span></div>
        <div class="ms-amount">$4,000 USDC</div>
        <div class="ms-title">Public API + Ecosystem</div>
        <div class="ms-desc">Public REST API v1, rate limiting, Sanctum auth, n8n plugin, OpenAPI docs, batch payments, agent payment triggers. Making ArcOnAgent a platform.</div>
        <div class="ms-bar-bg"><div class="ms-bar ms-bar-green" style="width:100%"></div></div>
        <div class="ms-progress-label"><span>Complete</span><span>100%</span></div>
      </div>
      <div class="milestone-card completed">
        <div class="ms-top"><span class="ms-num">MILESTONE 03</span><span class="ms-badge ms-badge-done">✓ COMPLETE</span></div>
        <div class="ms-amount">$4,000 USDC</div>
        <div class="ms-title">Arc Integration + Scale</div>
        <div class="ms-desc">Full Arc network integration, CCTP cross-chain transfers, multi-agent coordination, production deployment, Circle co-marketing launch.</div>
        <div class="ms-bar-bg"><div class="ms-bar ms-bar-green" style="width:100%"></div></div>
        <div class="ms-progress-label"><span>Complete</span><span>100%</span></div>
      </div>
      <!-- M4 UPGRADE CARD -->
      <div class="milestone-card m4">
        <div class="ms-top"><span class="ms-num">MILESTONE 04</span><span class="ms-badge ms-badge-m4">🚀 UPGRADE LAYER</span></div>
        <div class="ms-amount" style="color:var(--nano)">Upgrade Layer</div>
        <div class="ms-title">Nanopayments + Paymaster</div>
        <div class="ms-desc">Sub-cent micropayments for AI agent streaming economy. Circle Paymaster integration for USDC-paid gas — zero ETH required. Per-agent gas limits and paymaster toggle.</div>
        <div class="ms-bar-bg"><div class="ms-bar ms-bar-nano" style="width:100%"></div></div>
        <div class="ms-progress-label"><span style="color:var(--nano)">Complete</span><span style="color:var(--nano)">100%</span></div>
        <div style="margin-top:0.75rem;display:flex;gap:6px;flex-wrap:wrap">
          <span style="font-size:10px;font-weight:700;letter-spacing:1px;background:rgba(0,229,160,0.1);border:1px solid rgba(0,229,160,0.25);color:var(--nano);border-radius:5px;padding:2px 8px">Nanopayments</span>
          <span style="font-size:10px;font-weight:700;letter-spacing:1px;background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.25);color:var(--paymaster);border-radius:5px;padding:2px 8px">Paymaster</span>
          <span style="font-size:10px;font-weight:700;letter-spacing:1px;background:rgba(0,212,255,0.1);border:1px solid rgba(0,212,255,0.2);color:var(--cyan);border-radius:5px;padding:2px 8px">Gas Abstraction</span>
        </div>
      </div>
    </div>
  </section>

  <div class="glowing-divider"></div>

  <!-- Features -->
  <section class="features-section" style="padding-top:3rem">
    <div class="section-label">PLATFORM CAPABILITIES</div>
    <div class="features">
      <div class="feature f1"><div class="feature-icon-wrap fi-blue">🔐</div><h3>USDC Wallets per Agent</h3><p>Every AI agent is provisioned its own Circle wallet on Base. Real balances, real transactions via Circle's Developer-Controlled Wallets API with RSA-OAEP encryption.</p></div>
      <div class="feature f2"><div class="feature-icon-wrap fi-green">💸</div><h3>Real-Time Settlements</h3><p>Instant USDC transfers between agents over Arc/Base blockchain. Sub-second confirmation with Laravel Reverb WebSocket broadcasting to all connected dashboards.</p></div>
      <div class="feature f3"><div class="feature-icon-wrap fi-purple">📋</div><h3>Spending Policies</h3><p>Per-agent daily limits, monthly caps, per-transaction maximums, and allowed recipient controls. Policy violations are blocked before execution and logged with full metadata.</p></div>
      <div class="feature f4"><div class="feature-icon-wrap fi-cyan">⚡</div><h3>Automated Triggers</h3><p>Three trigger types: balance threshold (above/below), scheduled interval, and custom task events. Each agent can autonomously fire payments without human input.</p></div>
      <div class="feature f5"><div class="feature-icon-wrap fi-amber">📊</div><h3>Full Audit Trails</h3><p>Every agent action is logged — payment attempts, policy violations, trigger fires, wallet events. Immutable records with event type, source, and metadata.</p></div>
      <div class="feature f6"><div class="feature-icon-wrap fi-green">🔌</div><h3>Public REST API v1</h3><p>Sanctum auth, rate limiting, OpenAPI docs, n8n plugin. Integrate ArcOnAgent into any system — AI orchestrators, workflows, or external agent frameworks.</p></div>
      <!-- M4 features -->
      <div class="feature f-nano"><div class="feature-icon-wrap" style="background:rgba(0,229,160,0.12);border-color:rgba(0,229,160,0.25)">🔬</div><h3>Nanopayments</h3><p>Sub-cent USDC micropayments for streaming agent economies. Per-second billing, token-based metering, and real-time payment streams between agents — the foundation for agentic SaaS.</p></div>
      <div class="feature f-paymaster"><div class="feature-icon-wrap" style="background:rgba(168,85,247,0.12);border-color:rgba(168,85,247,0.25)">⛽</div><h3>Circle Paymaster</h3><p>Gas abstraction via Circle Paymaster — agents pay gas in USDC, no ETH required. Per-agent gas USDC limits, enable/disable toggle, and full gas spend audit logging.</p></div>
    </div>
  </section>

  <!-- Arc / Circle Section -->
  <section class="arc-section">
    <div class="arc-inner">
      <div>
        <div class="arc-eyebrow">BUILT ON</div>
        <div class="arc-title">Circle Developer Platform &amp; Arc Protocol</div>
        <p class="arc-body">ArcOnAgent uses Circle's Programmable Wallets to give every AI agent a real, custodial USDC wallet on Base blockchain. Arc provides the coordination layer — agents can contract, negotiate, and settle value with each other in real time without human intermediaries.</p>
        <div class="arc-chips">
          <span class="arc-chip">Programmable Wallets</span>
          <span class="arc-chip">USDC Native</span>
          <span class="arc-chip">Base / EVM</span>
          <span class="arc-chip">Circle CDP</span>
          <span class="arc-chip">Sepolia Testnet</span>
          <span class="arc-chip">RSA-OAEP</span>
          <span class="arc-chip">CCTP</span>
          <span class="arc-chip" style="border-color:rgba(0,229,160,0.3);color:var(--nano)">Nanopayments</span>
          <span class="arc-chip" style="border-color:rgba(168,85,247,0.3);color:var(--paymaster)">Paymaster</span>
        </div>
        <a href="https://arc.network" target="_blank" class="arc-link">Explore Arc Network ↗</a>
      </div>
      <div class="arc-diagram">
        <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text3);margin-bottom:0.5rem">Trigger-driven payment flow</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box highlight-blue">AI Agent A<div class="arc-flow-sub">USDC Wallet</div></div>
          <div class="arc-arrow">→</div>
          <div class="arc-flow-box highlight-purple">Spending Policy<div class="arc-flow-sub">Limit Check</div></div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:16px;padding:2px 0">↓</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box highlight-purple">Circle CDP<div class="arc-flow-sub">Transaction</div></div>
          <div class="arc-arrow">→</div>
          <div class="arc-flow-box highlight-blue">Arc · Base<div class="arc-flow-sub">Blockchain</div></div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:16px;padding:2px 0">↓</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box highlight-green" style="flex:unset;width:100%">
            AI Agent B — USDC Received · Confirmed
            <div class="arc-flow-sub">Audit log entry created · Trigger re-evaluated</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CCTP + Multi-Agent Section -->
  <section class="arc-section" style="background:linear-gradient(135deg,rgba(0,212,255,0.04),rgba(168,85,247,0.04))">
    <div class="arc-inner">
      <div>
        <div class="arc-eyebrow">MILESTONE 03 · COMPLETE</div>
        <div class="arc-title">CCTP Cross-Chain &amp; Multi-Agent Coordination</div>
        <p class="arc-body">
          Circle's Cross-Chain Transfer Protocol lets agents move USDC natively across EVM chains
          without bridges or wrapped tokens. Pair that with multi-agent orchestration — agents
          that delegate, coordinate, and settle with each other autonomously.
        </p>
        <div class="arc-chips">
          <span class="arc-chip" style="border-color:rgba(0,212,255,0.3);color:var(--cyan)">CCTP v2</span>
          <span class="arc-chip">Cross-Chain USDC</span>
          <span class="arc-chip">Agent Delegation</span>
          <span class="arc-chip">Multi-Agent Mesh</span>
          <span class="arc-chip">Base → Ethereum</span>
          <span class="arc-chip">Base → Arbitrum</span>
          <span class="arc-chip">Burn &amp; Mint</span>
        </div>
      </div>
      <div class="arc-diagram">
        <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text3);margin-bottom:0.5rem">Multi-agent CCTP flow</div>
        <div style="display:flex;justify-content:center;margin-bottom:6px">
          <div class="arc-flow-box highlight-purple" style="flex:unset;width:100%;text-align:center">
            Orchestrator Agent
            <div class="arc-flow-sub">Coordinates &amp; delegates tasks</div>
          </div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:14px;padding:2px 0">↓ delegates</div>
        <div class="arc-flow-row" style="gap:6px">
          <div class="arc-flow-box highlight-blue" style="font-size:11px">Worker A<div class="arc-flow-sub">Base USDC</div></div>
          <div class="arc-flow-box highlight-blue" style="font-size:11px">Worker B<div class="arc-flow-sub">Base USDC</div></div>
          <div class="arc-flow-box highlight-blue" style="font-size:11px">Worker C<div class="arc-flow-sub">Base USDC</div></div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:14px;padding:2px 0">↓ CCTP burn</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box" style="border-color:rgba(0,212,255,0.3);background:rgba(0,212,255,0.06);color:var(--cyan)">Circle CCTP<div class="arc-flow-sub" style="color:rgba(0,212,255,0.5)">Burn on Base</div></div>
          <div class="arc-arrow">→</div>
          <div class="arc-flow-box" style="border-color:rgba(0,212,255,0.3);background:rgba(0,212,255,0.06);color:var(--cyan)">Mint on Chain<div class="arc-flow-sub" style="color:rgba(0,212,255,0.5)">Arbitrum / ETH</div></div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:14px;padding:2px 0">↓ settled</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box highlight-green" style="flex:unset;width:100%;text-align:center">
            External Agent / Service — USDC Received Cross-Chain
            <div class="arc-flow-sub">No bridge · Native USDC · Audit logged</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- M4 Nano + Paymaster Section -->
  <section class="arc-section" style="background:linear-gradient(135deg,rgba(0,229,160,0.04),rgba(168,85,247,0.04))">
    <div class="arc-inner">
      <div>
        <div class="arc-eyebrow" style="color:var(--nano)">MILESTONE 04 · UPGRADE LAYER</div>
        <div class="arc-title">Nanopayments + Circle Paymaster</div>
        <p class="arc-body">
          Sub-cent USDC micropayments let agents bill per-second, per-token, or per-task —
          unlocking a streaming agentic economy. Circle Paymaster abstracts gas entirely:
          agents pay transaction fees in USDC with zero ETH required.
        </p>
        <div class="arc-chips">
          <span class="arc-chip" style="border-color:rgba(0,229,160,0.3);color:var(--nano)">Nanopayments</span>
          <span class="arc-chip" style="border-color:rgba(168,85,247,0.3);color:var(--paymaster)">Circle Paymaster</span>
          <span class="arc-chip">Sub-cent USDC</span>
          <span class="arc-chip">Streaming Payments</span>
          <span class="arc-chip">Gas Abstraction</span>
          <span class="arc-chip">USDC-Paid Gas</span>
          <span class="arc-chip">Per-Agent Limits</span>
        </div>
        <button class="btn-primary" style="margin-top:1.25rem;font-size:13px" onclick="showPage('nano',document.querySelectorAll('.nav-btn')[9])">🟢 Open Nano Dashboard →</button>
      </div>
      <div class="arc-diagram">
        <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text3);margin-bottom:0.5rem">M4 payment flow</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box" style="border-color:rgba(0,229,160,0.3);background:rgba(0,229,160,0.06);color:var(--nano)">
            Agent A<div class="arc-flow-sub" style="color:rgba(0,229,160,0.5)">Nano sender</div>
          </div>
          <div class="arc-arrow">→</div>
          <div class="arc-flow-box" style="border-color:rgba(168,85,247,0.3);background:rgba(168,85,247,0.06);color:var(--paymaster)">
            Paymaster<div class="arc-flow-sub" style="color:rgba(168,85,247,0.5)">Gas in USDC</div>
          </div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:16px;padding:2px 0">↓</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box highlight-blue">Circle CDP<div class="arc-flow-sub">Execute tx</div></div>
          <div class="arc-arrow">→</div>
          <div class="arc-flow-box highlight-blue">Arc · Base<div class="arc-flow-sub">Confirm</div></div>
        </div>
        <div style="display:flex;justify-content:center;color:var(--text3);font-size:16px;padding:2px 0">↓</div>
        <div class="arc-flow-row">
          <div class="arc-flow-box" style="border-color:rgba(0,229,160,0.3);background:rgba(0,229,160,0.06);color:var(--nano);flex:unset;width:100%;text-align:center">
            Agent B — Nanopayment Received · Streamed
            <div class="arc-flow-sub" style="color:rgba(0,229,160,0.5)">Sub-cent · No ETH gas · Audit logged</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- API Highlights -->
  <section class="api-section">
    <div class="section-label">PUBLIC REST API — v1 HIGHLIGHTS</div>
    <div class="api-grid">
      <div class="endpoint-card"><div class="endpoint-header"><span class="method-badge method-post">POST</span><span class="endpoint-path">/api/v1/agents/register</span></div><div class="endpoint-body"><div class="endpoint-desc">Register a new AI agent and provision a Circle USDC wallet on Base-Sepolia.</div><div class="endpoint-params"><span class="param-tag">name</span><span class="param-tag">email</span><span class="param-tag">Bearer: token</span></div></div></div>
      <div class="endpoint-card"><div class="endpoint-header"><span class="method-badge method-post">POST</span><span class="endpoint-path">/api/v1/payments/send</span></div><div class="endpoint-body"><div class="endpoint-desc">Send USDC from one agent wallet to another with policy enforcement and audit logging.</div><div class="endpoint-params"><span class="param-tag">sender_agent_id</span><span class="param-tag">receiver_agent_id</span><span class="param-tag">amount</span></div></div></div>
      <div class="endpoint-card"><div class="endpoint-header"><span class="method-badge method-post">POST</span><span class="endpoint-path">/api/v1/nano/send</span></div><div class="endpoint-body"><div class="endpoint-desc">Send a sub-cent nanopayment between agents. Supports streaming mode for continuous per-second billing.</div><div class="endpoint-params"><span class="param-tag">sender_agent_id</span><span class="param-tag">receiver_agent_id</span><span class="param-tag">amount_micro</span><span class="param-tag">stream</span></div></div></div>
      <div class="endpoint-card"><div class="endpoint-header"><span class="method-badge method-post">POST</span><span class="endpoint-path">/api/v1/paymaster/toggle</span></div><div class="endpoint-body"><div class="endpoint-desc">Enable or disable Circle Paymaster for an agent. Set per-agent USDC gas spending limit.</div><div class="endpoint-params"><span class="param-tag">agent_id</span><span class="param-tag">enabled</span><span class="param-tag">gas_usdc_limit</span></div></div></div>
    </div>
    <div class="api-cta-row">
      <button class="btn-outline" onclick="showPage('docs',document.querySelectorAll('.nav-btn')[7])">View Full API Docs →</button>
      <a href="https://github.com/shamimgalaxy" target="_blank" class="btn-outline" style="text-decoration:none">🐙 GitHub: shamimgalaxy</a>
    </div>
  </section>

  <!-- Developer Profile -->
  <div class="dev-section">
    <div class="dev-inner">
      <div class="dev-avatar-col">
        <div class="dev-avatar"><div class="dev-avatar-initials">SA</div><div class="dev-avatar-sub">BUILDER</div></div>
        <div class="dev-badge-pill">🏆 Circle Grantee</div>
        <div class="dev-mini-stats">
          <div class="dev-mini-stat"><div class="dev-mini-n" id="prof-agents">—</div><div class="dev-mini-l">Agents Built</div></div>
          <div class="dev-mini-stat"><div class="dev-mini-n" id="prof-payments">—</div><div class="dev-mini-l">Payments Sent</div></div>
          <div class="dev-mini-stat"><div class="dev-mini-n">4</div><div class="dev-mini-l">Milestones</div></div>
        </div>
      </div>
      <div>
        <div class="dev-eyebrow">Developer Profile</div>
        <div class="dev-name">Shamim Ahmed</div>
        <div class="dev-title">Laravel Engineer · Web3 Builder · Circle Developer Grantee</div>
        <p class="dev-bio">Building at the intersection of AI autonomy and on-chain finance. ArcOnAgent is a Circle Developer Grant project — infrastructure for autonomous AI agents to participate as first-class economic actors on Arc and Base blockchain. Real wallets, real spending policies, real USDC settlement — now with nanopayments and Circle Paymaster gas abstraction.</p>
        <div class="dev-chips">
          <span class="dev-chip">Laravel 12</span><span class="dev-chip">PHP 8.5</span><span class="dev-chip">Circle Wallets API</span><span class="dev-chip">Base Network</span><span class="dev-chip">Arc Ecosystem</span><span class="dev-chip">USDC / CCTP</span><span class="dev-chip">RSA-OAEP</span><span class="dev-chip">Laravel Reverb</span><span class="dev-chip">Sanctum</span><span class="dev-chip">MySQL</span><span class="dev-chip" style="color:var(--nano);border-color:rgba(0,229,160,0.3)">Nanopayments</span><span class="dev-chip" style="color:var(--paymaster);border-color:rgba(168,85,247,0.3)">Paymaster</span>
        </div>
        <div class="dev-links">
          <a href="https://circle.questbook.app/" target="_blank" class="dev-link primary">🏆 Circle Grant Application</a>
          <a href="https://github.com/shamimgalaxy" target="_blank" class="dev-link github">🐙 github.com/shamimgalaxy</a>
          <a href="https://arc.network" target="_blank" class="dev-link">⬡ Arc Network</a>
          <button class="dev-link" onclick="showPage('docs',document.querySelectorAll('.nav-btn')[7])">📄 API Docs</button>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA -->
  <section class="cta-section">
    <div class="cta-badge"><span class="hero-eyebrow-dot" style="background:var(--cyan);box-shadow:0 0 6px var(--cyan)"></span>START NOW</div>
    <h2>Deploy Your First Agent</h2>
    <p>Register an agent, provision a USDC wallet, configure spending policies and payment triggers, and start settling on-chain through Arc — in minutes.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <button class="btn-primary" onclick="showPage('agents',document.querySelectorAll('.nav-btn')[2])">🤖 Register Agent →</button>
      <button class="btn-outline" onclick="showPage('send',document.querySelectorAll('.nav-btn')[3])">Send Payment</button>
      <button class="btn-outline" style="border-color:rgba(0,229,160,0.3);color:var(--nano)" onclick="showPage('nano',document.querySelectorAll('.nav-btn')[9])">🔬 Nanopayments</button>
    </div>
  </section>

  <div class="glowing-divider"></div>

  <!-- Footer -->
  <footer>
    <div class="footer-inner">
      <a href="#" class="brand" style="font-size:13px" onclick="showPage('home',document.querySelectorAll('.nav-btn')[0]);return false;">
        <div class="brand-icon" style="width:22px;height:22px;font-size:11px">⬡</div>
        ARCON<span class="hl">AGENT</span>
      </a>
      <div class="footer-links">
        <button onclick="showPage('dashboard',document.querySelectorAll('.nav-btn')[1])" class="footer-link" style="border:none;background:none;cursor:pointer">Dashboard</button>
        <button onclick="showPage('agents',document.querySelectorAll('.nav-btn')[2])"    class="footer-link" style="border:none;background:none;cursor:pointer">Agents</button>
        <button onclick="showPage('payments',document.querySelectorAll('.nav-btn')[4])"  class="footer-link" style="border:none;background:none;cursor:pointer">Payments</button>
        <button onclick="showPage('triggers',document.querySelectorAll('.nav-btn')[5])"  class="footer-link" style="border:none;background:none;cursor:pointer">Triggers</button>
        <button onclick="showPage('audit',document.querySelectorAll('.nav-btn')[6])"     class="footer-link" style="border:none;background:none;cursor:pointer">Audit Logs</button>
        <button onclick="showPage('docs',document.querySelectorAll('.nav-btn')[7])"      class="footer-link" style="border:none;background:none;cursor:pointer">API Docs</button>
        <button onclick="showPage('nano',document.querySelectorAll('.nav-btn')[9])"      class="footer-link" style="border:none;background:none;cursor:pointer;color:var(--nano)">M4 Nano</button>
      </div>
      <div class="footer-copy">ARCONAGENT · BASE-SEPOLIA · CIRCLE CDP · M4 NANO+PAYMASTER</div>
    </div>
  </footer>

</div><!-- /page-home -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: DASHBOARD
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-dashboard" class="page">
  <div class="container">
    <div class="stats-row">
      <div class="stat-card sc-blue">  <div class="stat-lbl">Total Payments</div> <div class="stat-val" id="ds-total">—</div>    <div class="stat-accent"></div></div>
      <div class="stat-card sc-green"> <div class="stat-lbl">Confirmed</div>       <div class="stat-val" id="ds-confirmed">—</div> <div class="stat-accent"></div></div>
      <div class="stat-card sc-amber"> <div class="stat-lbl">USDC Volume</div>     <div class="stat-val" id="ds-volume">—</div>    <div class="stat-accent"></div></div>
      <div class="stat-card sc-purple"><div class="stat-lbl">Active Agents</div>   <div class="stat-val" id="ds-agents">—</div>    <div class="stat-accent"></div></div>
      <div class="stat-card sc-red">   <div class="stat-lbl">Active Triggers</div> <div class="stat-val" id="ds-triggers">—</div>  <div class="stat-accent"></div></div>
      <div class="stat-card" style="border-color:rgba(0,229,160,0.25);background:var(--nano-bg)">
        <div class="stat-lbl">Nanopayments</div>
        <div class="stat-val" id="ds-nano" style="color:var(--nano)">—</div>
        <div class="stat-accent" style="background:var(--nano)"></div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 300px;gap:1rem;" class="dashboard-grid">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Live Payment Feed</span>
          <span class="panel-meta">Arc · Base-Sepolia · USDC</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>From</th><th>To</th><th>Amount</th><th>Status</th><th>Time</th></tr></thead>
            <tbody id="dash-payments-body">
              <tr><td colspan="6" class="loading"><span class="spinner"></span>Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Agents</span>
          <button onclick="loadDashboard()" style="font-size:11px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace;">↻</button>
        </div>
        <div id="dash-agents-list">
          <div class="loading"><span class="spinner"></span>Loading...</div>
        </div>
      </div>
    </div>
  </div>
</div><!-- /page-dashboard -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: AGENTS
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-agents" class="page">
  <div class="container">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Register New Agent</span>
        <span class="panel-meta">Circle Wallet auto-provisioned on Base-Sepolia</span>
      </div>
      <div class="panel-body">
        <div class="form-grid">
          <div class="form-group">
            <label for="reg-name">Agent Name</label>
            <input type="text" class="form-control" id="reg-name" placeholder="e.g. Agent Gamma">
          </div>
          <div class="form-group">
            <label for="reg-email">Email</label>
            <input type="email" class="form-control" id="reg-email" placeholder="gamma@arconagent.com">
          </div>
        </div>
        <div style="margin-top:1rem;display:flex;align-items:center;gap:12px;">
          <button class="btn-submit" onclick="registerAgent()">🤖 Register Agent</button>
          <span id="reg-status" style="font-size:12px;color:var(--text3)"></span>
        </div>
      </div>
    </div>
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Registered Agents</span>
        <span id="agents-count" class="panel-meta">Loading...</span>
      </div>
      <div class="panel-body">
        <div class="agent-grid" id="agents-grid">
          <div class="loading"><span class="spinner"></span>Loading agents...</div>
        </div>
      </div>
    </div>
  </div>
</div><!-- /page-agents -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: SEND PAYMENT
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-send" class="page">
  <div class="container" style="max-width:680px">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Send USDC Payment</span>
        <span class="panel-meta">Base-Sepolia · Arc Network</span>
      </div>
      <div class="panel-body">
        <div class="form-grid">
          <div class="form-group">
            <label for="send-sender">Sender Agent</label>
            <select class="form-control" id="send-sender"><option value="">— Select Agent —</option></select>
          </div>
          <div class="form-group">
            <label for="send-receiver">Receiver Agent</label>
            <select class="form-control" id="send-receiver"><option value="">— Select Agent —</option></select>
          </div>
          <div class="form-group">
            <label for="send-amount">Amount (USDC)</label>
            <input type="number" class="form-control" id="send-amount" placeholder="0.000000" step="0.000001" min="0.000001">
          </div>
          <div class="form-group">
            <label for="send-note">Note (optional)</label>
            <input type="text" class="form-control" id="send-note" placeholder="Payment note...">
          </div>
        </div>
        <div style="margin-top:1.25rem;display:flex;gap:12px;align-items:center;">
          <button class="btn-submit" id="send-btn" onclick="submitPayment()">⚡ Send Payment</button>
          <span id="send-status" style="font-size:12px;color:var(--text3)"></span>
        </div>
      </div>
    </div>
    <div class="panel" id="send-result" style="display:none">
      <div class="panel-header"><span class="panel-title">Transaction Result</span></div>
      <div class="panel-body" id="send-result-body"></div>
    </div>
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Batch Send</span>
        <span class="panel-meta">Multi-agent USDC transfer</span>
      </div>
      <div class="panel-body">
        <div class="form-group" style="margin-bottom:1rem">
          <label for="batch-sender">Sender Agent</label>
          <select class="form-control" id="batch-sender"><option value="">— Select Agent —</option></select>
        </div>
        <div id="batch-items">
          <div class="batch-item" style="display:grid;grid-template-columns:1fr 110px 1fr;gap:0.75rem;margin-bottom:0.75rem;align-items:end;">
            <div class="form-group"><label>Receiver</label><select class="form-control batch-recv"><option value="">— Agent —</option></select></div>
            <div class="form-group"><label>Amount</label><input type="number" class="form-control batch-amt" placeholder="USDC" step="0.000001" min="0.000001"></div>
            <div class="form-group"><label>Note</label><input type="text" class="form-control batch-note" placeholder="Optional"></div>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:0.75rem;flex-wrap:wrap;">
          <button class="btn-secondary" onclick="addBatchItem()">+ Add Recipient</button>
          <button class="btn-submit" onclick="submitBatch()" style="font-size:12px;padding:9px 18px;">⚡ Send Batch</button>
        </div>
      </div>
    </div>
  </div>
</div><!-- /page-send -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: PAYMENTS LIST
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-payments" class="page">
  <div class="container">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">All Payments</span>
        <button onclick="loadPayments()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace;">↻ Refresh</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>#</th><th>TX ID</th><th>From</th><th>To</th><th>Amount</th><th>Status</th><th>Blockchain</th><th>Time</th></tr>
          </thead>
          <tbody id="payments-body">
            <tr><td colspan="8" class="loading"><span class="spinner"></span>Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div><!-- /page-payments -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: TRIGGERS
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-triggers" class="page">
  <div class="container">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Create Trigger</span>
        <span class="panel-meta">balance_threshold · scheduled · task_event</span>
      </div>
      <div class="panel-body">
        <div class="form-grid" style="margin-bottom:1rem">
          <div class="form-group">
            <label for="tr-agent">Agent</label>
            <select class="form-control" id="tr-agent" onchange="onTriggerAgentChange()"><option value="">— Select Agent —</option></select>
          </div>
          <div class="form-group">
            <label for="tr-name">Trigger Name</label>
            <input type="text" class="form-control" id="tr-name" placeholder="e.g. Low Balance Alert">
          </div>
          <div class="form-group">
            <label for="tr-type">Trigger Type</label>
            <select class="form-control" id="tr-type" onchange="onTriggerTypeChange()">
              <option value="">— Select Type —</option>
              <option value="balance_threshold">Balance Threshold</option>
              <option value="scheduled">Scheduled</option>
              <option value="task_event">Task Event</option>
            </select>
          </div>
          <div class="form-group" style="justify-content:flex-end">
            <label style="visibility:hidden">Active</label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 0">
              <input type="checkbox" id="tr-active" checked style="width:16px;height:16px;accent-color:var(--blue)">
              <span style="font-size:13px;color:var(--text2)">Active on creation</span>
            </label>
          </div>
        </div>
        <div class="trigger-fields" id="tf-balance">
          <div class="form-group"><label for="tr-threshold-amount">Threshold Amount (USDC)</label><input type="number" class="form-control" id="tr-threshold-amount" placeholder="0.000000" step="0.000001" min="0"></div>
          <div class="form-group"><label for="tr-threshold-dir">Direction</label><select class="form-control" id="tr-threshold-dir"><option value="below">Below threshold</option><option value="above">Above threshold</option></select></div>
        </div>
        <div class="trigger-fields" id="tf-scheduled" style="grid-template-columns:1fr">
          <div class="form-group"><label for="tr-interval">Interval (hours)</label><input type="number" class="form-control" id="tr-interval" placeholder="e.g. 24" min="1" step="1"></div>
        </div>
        <div class="trigger-fields" id="tf-event" style="grid-template-columns:1fr">
          <div class="form-group"><label for="tr-event-name">Event Name</label><input type="text" class="form-control" id="tr-event-name" placeholder="e.g. task.completed"></div>
        </div>
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
          <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text3);margin-bottom:0.85rem">Payment Details</div>
          <div class="form-grid">
            <div class="form-group"><label for="tr-recv-agent">Receiver Agent (optional)</label><select class="form-control" id="tr-recv-agent"><option value="">— None / Use address —</option></select></div>
            <div class="form-group"><label for="tr-recv-addr">Receiver Address (fallback)</label><input type="text" class="form-control" id="tr-recv-addr" placeholder="0x..."></div>
            <div class="form-group"><label for="tr-amount">Amount (USDC)</label><input type="number" class="form-control" id="tr-amount" placeholder="0.000000" step="0.000001" min="0"></div>
            <div class="form-group"><label for="tr-currency">Currency</label><input type="text" class="form-control" id="tr-currency" value="USDC"></div>
            <div class="form-group" style="grid-column:1/-1"><label for="tr-note">Note (optional)</label><input type="text" class="form-control" id="tr-note" placeholder="Trigger payment note..."></div>
          </div>
        </div>
        <div style="margin-top:1.25rem;display:flex;gap:10px;align-items:center">
          <button class="btn-submit" id="tr-create-btn" onclick="createTrigger()">🎯 Create Trigger</button>
          <span id="tr-create-status" style="font-size:12px;color:var(--text3)"></span>
        </div>
      </div>
    </div>
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Agent Triggers</span>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
          <div class="agent-selector" style="margin:0">
            <label>Agent:</label>
            <select class="form-control" id="tr-filter-agent" style="min-width:160px;padding:6px 10px;font-size:12px" onchange="loadTriggersForAgent()"><option value="">— Select Agent —</option></select>
          </div>
          <select class="form-control" id="tr-filter-type" style="min-width:140px;padding:6px 10px;font-size:12px" onchange="loadTriggersForAgent()">
            <option value="">All Types</option>
            <option value="balance_threshold">Balance Threshold</option>
            <option value="scheduled">Scheduled</option>
            <option value="task_event">Task Event</option>
          </select>
          <label style="font-size:12px;color:var(--text2);display:flex;align-items:center;gap:5px;cursor:pointer">
            <input type="checkbox" id="tr-filter-active" style="accent-color:var(--blue)" onchange="loadTriggersForAgent()">
            Active only
          </label>
          <button onclick="loadTriggersForAgent()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace">↻</button>
        </div>
      </div>
      <div class="panel-body" id="triggers-list">
        <div class="trigger-empty">Select an agent above to view its triggers.</div>
      </div>
    </div>
  </div>
</div><!-- /page-triggers -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: AUDIT LOGS
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-audit" class="page">
  <div class="container">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Audit Logs</span>
        <button onclick="loadAuditLogs()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace">↻ Refresh</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>#</th><th>Agent</th><th>Event</th><th>Status</th><th>Amount</th><th>Rule</th><th>Source</th><th>Time</th></tr>
          </thead>
          <tbody id="audit-body">
            <tr><td colspan="8" class="loading"><span class="spinner"></span>Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div><!-- /page-audit -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: API DOCS
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-docs" class="page">
  <div class="container">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">ArcOnAgent REST API — v1</span>
        <span class="panel-meta">Base URL: /api/v1</span>
      </div>
      <div class="panel-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:1.25rem">
          <span style="font-size:12px;color:var(--text2);background:var(--bg2);border:1px solid var(--border);padding:5px 14px;border-radius:8px;font-family:'JetBrains Mono',monospace">Auth: Bearer token (Sanctum)</span>
          <span style="font-size:12px;color:var(--text2);background:var(--bg2);border:1px solid var(--border);padding:5px 14px;border-radius:8px;font-family:'JetBrains Mono',monospace">Rate limit: 60 req/min</span>
          <span style="font-size:12px;color:var(--text2);background:var(--bg2);border:1px solid var(--border);padding:5px 14px;border-radius:8px;font-family:'JetBrains Mono',monospace">Response: JSON</span>
          <span style="font-size:12px;color:var(--green);background:rgba(0,229,160,0.08);border:1px solid rgba(0,229,160,0.2);padding:5px 14px;border-radius:8px;font-family:'JetBrains Mono',monospace">n8n Plugin: Milestone 2</span>
          <span style="font-size:12px;color:var(--nano);background:rgba(0,229,160,0.08);border:1px solid rgba(0,229,160,0.2);padding:5px 14px;border-radius:8px;font-family:'JetBrains Mono',monospace">🔬 Nano + Paymaster: M4</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Auth</th></tr></thead>
            <tbody>
              <tr><td colspan="4" style="background:rgba(79,124,255,0.04);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--text3);text-transform:uppercase;padding:8px 1rem">Auth</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/auth/login</td><td class="fw">Obtain API token</td><td class="mono">—</td></tr>
              <tr><td colspan="4" style="background:rgba(79,124,255,0.04);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--text3);text-transform:uppercase;padding:8px 1rem">Agents</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/agents</td><td class="fw">List all agents</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/agents/register</td><td class="fw">Register agent + provision wallet</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/agents/{id}</td><td class="fw">Get agent details + wallet</td><td class="mono">Bearer</td></tr>
              <tr><td colspan="4" style="background:rgba(168,85,247,0.04);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--text3);text-transform:uppercase;padding:8px 1rem">Triggers</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/agents/{id}/triggers</td><td class="fw">List triggers — filter by type, active_only</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/agents/{id}/triggers</td><td class="fw">Create trigger</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-put">PUT</span></td><td class="mono">/agents/{id}/triggers/{tid}</td><td class="fw">Update trigger</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-delete">DELETE</span></td><td class="mono">/agents/{id}/triggers/{tid}</td><td class="fw">Delete trigger</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-patch">PATCH</span></td><td class="mono">/agents/{id}/triggers/{tid}/toggle</td><td class="fw">Toggle is_active</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/agents/{id}/triggers/{tid}/fire</td><td class="fw">Manually fire trigger</td><td class="mono">Bearer</td></tr>
              <tr><td colspan="4" style="background:rgba(0,229,160,0.04);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--text3);text-transform:uppercase;padding:8px 1rem">Payments</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/payments/send</td><td class="fw">Send USDC between agents</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/payments/batch</td><td class="fw">Batch USDC transfers</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/payments</td><td class="fw">List all payments</td><td class="mono">Bearer</td></tr>
              <tr><td colspan="4" style="background:rgba(0,212,255,0.04);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--text3);text-transform:uppercase;padding:8px 1rem">Audit</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/audit-logs</td><td class="fw">Full audit trail</td><td class="mono">Bearer</td></tr>
              <!-- M4 Nano + Paymaster -->
              <tr><td colspan="4" style="background:rgba(0,229,160,0.06);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--nano);text-transform:uppercase;padding:8px 1rem">🔬 Nanopayments (M4)</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/nano/send</td><td class="fw">Send sub-cent nanopayment (supports streaming)</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/nano</td><td class="fw">List all nanopayments</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/nano/stats</td><td class="fw">Nano payment stats + totals</td><td class="mono">Bearer</td></tr>
              <tr><td colspan="4" style="background:rgba(168,85,247,0.06);font-size:10px;font-weight:700;letter-spacing:1.5px;color:var(--paymaster);text-transform:uppercase;padding:8px 1rem">⛽ Paymaster (M4)</td></tr>
              <tr><td><span class="method-badge method-post">POST</span></td><td class="mono">/paymaster/toggle</td><td class="fw">Enable/disable Paymaster for agent + set USDC gas limit</td><td class="mono">Bearer</td></tr>
              <tr><td><span class="method-badge method-get">GET</span></td><td class="mono">/paymaster/status/{agent_id}</td><td class="fw">Get Paymaster status + gas usage for agent</td><td class="mono">Bearer</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Trigger payloads -->
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Trigger Request Payloads</span>
        <span class="panel-meta">POST /api/v1/agents/{id}/triggers</span>
      </div>
      <div class="panel-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--amber);margin-bottom:0.5rem">Balance Threshold</div>
            <div class="code-block">{
  "name": "Low balance alert",
  "trigger_type": "balance_threshold",
  "threshold_amount": 10.0,
  "threshold_direction": "below",
  "receiver_agent_id": 2,
  "amount": 50.0,
  "currency": "USDC",
  "is_active": true
}</div>
          </div>
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--purple);margin-bottom:0.5rem">Scheduled</div>
            <div class="code-block">{
  "name": "Daily payment",
  "trigger_type": "scheduled",
  "interval_hours": 24,
  "receiver_agent_id": 3,
  "amount": 5.0,
  "currency": "USDC",
  "note": "Daily subscription",
  "is_active": true
}</div>
          </div>
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--cyan);margin-bottom:0.5rem">Task Event</div>
            <div class="code-block">{
  "name": "Task completed pay",
  "trigger_type": "task_event",
  "event_name": "task.completed",
  "receiver_address": "0xAbC...",
  "amount": 2.5,
  "currency": "USDC",
  "is_active": true
}</div>
          </div>
        </div>
        <div style="margin-top:1rem" class="info-box">
          <div class="info-box-title">Receiver Resolution</div>
          <p style="font-size:12px;color:var(--text2);margin-top:4px;line-height:1.8">The fire endpoint calls <code style="font-size:11px;color:var(--blue2)">resolveReceiverAddress()</code> — prefers <code style="font-size:11px;color:var(--blue2)">receiver_address</code>, falls back to <code style="font-size:11px;color:var(--blue2)">receiverAgent.circle_wallet_address</code>. Scheduled triggers expose <code style="font-size:11px;color:var(--blue2)">isDue()</code> — returns true when last_fired_at + interval_hours ≤ now().</p>
        </div>
      </div>
    </div>

    <!-- M4 Payloads -->
    <div class="panel" style="border-color:rgba(0,229,160,0.2)">
      <div class="panel-header">
        <span class="panel-title" style="color:var(--nano)">🔬 M4 Nano + Paymaster Payloads</span>
        <span class="panel-meta">POST /api/v1/nano/send · POST /api/v1/paymaster/toggle</span>
      </div>
      <div class="panel-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--nano);margin-bottom:0.5rem">Nanopayment Send</div>
            <div class="code-block">{
  "sender_agent_id": 1,
  "receiver_agent_id": 2,
  "amount_micro": 500,
  "currency": "USDC",
  "stream": false,
  "note": "Per-token billing"
}

// amount_micro: integer
// 1 micro-USDC = 0.000001 USDC
// stream: true enables streaming mode</div>
          </div>
          <div>
            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--paymaster);margin-bottom:0.5rem">Paymaster Toggle</div>
            <div class="code-block">{
  "agent_id": 1,
  "enabled": true,
  "gas_usdc_limit": 5.00
}

// gas_usdc_limit: max USDC
//   agent can spend on gas
// enabled: true = Circle
//   Paymaster pays gas in USDC
//   (no ETH required)</div>
          </div>
        </div>
      </div>
    </div>

    <!-- cURL quick start -->
    <div class="panel">
      <div class="panel-header"><span class="panel-title">Quick Start — cURL Examples</span></div>
      <div class="panel-body">
        <div class="code-block"># 1. Get API token
curl -X POST /api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your@email.com","password":"secret"}'

# 2. Register an agent (auto-provisions Circle wallet)
curl -X POST /api/v1/agents/register \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Agent Alpha","email":"alpha@arconagent.com"}'

# 3. Send a nanopayment (500 micro-USDC = $0.0005)
curl -X POST /api/v1/nano/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"sender_agent_id":1,"receiver_agent_id":2,"amount_micro":500,"note":"Per-token fee"}'

# 4. Enable Paymaster for agent (USDC-paid gas, no ETH)
curl -X POST /api/v1/paymaster/toggle \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"agent_id":1,"enabled":true,"gas_usdc_limit":5.00}'

# 5. Check Paymaster status
curl -X GET /api/v1/paymaster/status/1 \
  -H "Authorization: Bearer YOUR_TOKEN"

# 6. Send USDC between agents
curl -X POST /api/v1/payments/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"sender_agent_id":1,"receiver_agent_id":2,"amount":5.00}'</div>
      </div>
    </div>
  </div>
</div><!-- /page-docs -->


<!-- ═══════════════════════════════════════════════════════════════════
     PAGE: M4 NANOPAYMENTS + PAYMASTER
     ═══════════════════════════════════════════════════════════════════ -->
<div id="page-nano" class="page">
  <div class="container">

    <!-- Page header -->
    <div style="margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border)">
      <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--nano);margin-bottom:6px">MILESTONE 04 · UPGRADE LAYER</div>
      <div style="font-size:22px;font-weight:800;color:var(--text)">Nanopayments + Circle Paymaster</div>
      <div style="font-size:13px;color:var(--text3);margin-top:4px">Sub-cent micropayments · Gas abstraction via USDC · Per-agent controls</div>
    </div>

    <!-- Stat cards -->
    <div class="nano-hero">
      <div class="nano-stat-card">
        <div class="stat-lbl">Total Nanopayments</div>
        <div class="stat-val" id="nano-stat-total">—</div>
        <div style="font-size:11px;color:var(--text3);margin-top:4px;font-family:'JetBrains Mono',monospace">transactions sent</div>
      </div>
      <div class="nano-stat-card">
        <div class="stat-lbl">Nano Volume (micro-USDC)</div>
        <div class="stat-val" id="nano-stat-volume">—</div>
        <div style="font-size:11px;color:var(--text3);margin-top:4px;font-family:'JetBrains Mono',monospace">cumulative micro-USDC</div>
      </div>
      <div class="pm-stat-card">
        <div class="stat-lbl">Paymaster Enabled Agents</div>
        <div class="stat-val" id="nano-stat-pm-agents">—</div>
        <div style="font-size:11px;color:var(--text3);margin-top:4px;font-family:'JetBrains Mono',monospace">agents with gas abstraction</div>
      </div>
      <div class="pm-stat-card">
        <div class="stat-lbl">Total Gas Saved (USDC)</div>
        <div class="stat-val" id="nano-stat-gas-saved">—</div>
        <div style="font-size:11px;color:var(--text3);margin-top:4px;font-family:'JetBrains Mono',monospace">no ETH required</div>
      </div>
    </div>

    <!-- Two column: Send Nano + Paymaster Controls -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem" class="dashboard-grid">

      <!-- Send Nanopayment -->
      <div class="panel" style="border-color:rgba(0,229,160,0.2)">
        <div class="panel-header">
          <span class="panel-title" style="color:var(--nano)">🔬 Send Nanopayment</span>
          <span class="panel-meta">Sub-cent USDC · Base-Sepolia</span>
        </div>
        <div class="panel-body">
          <div class="form-group" style="margin-bottom:0.85rem">
            <label for="nano-sender">Sender Agent</label>
            <select class="form-control" id="nano-sender"><option value="">— Select Agent —</option></select>
          </div>
          <div class="form-group" style="margin-bottom:0.85rem">
            <label for="nano-receiver">Receiver Agent</label>
            <select class="form-control" id="nano-receiver"><option value="">— Select Agent —</option></select>
          </div>
          <div class="form-group" style="margin-bottom:0.85rem">
            <label for="nano-amount-micro">Amount (micro-USDC)</label>
            <input type="number" class="form-control" id="nano-amount-micro" placeholder="e.g. 500 = $0.0005" min="1" step="1">
            <div style="font-size:10px;color:var(--text3);margin-top:4px;font-family:'JetBrains Mono',monospace" id="nano-amount-display">— USDC</div>
          </div>
          <div class="form-group" style="margin-bottom:0.85rem">
            <label for="nano-note">Note (optional)</label>
            <input type="text" class="form-control" id="nano-note" placeholder="e.g. Per-token billing">
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:1rem">
            <label class="toggle-wrap">
              <span class="toggle-label">Streaming mode</span>
              <label class="toggle-switch">
                <input type="checkbox" id="nano-stream">
                <span class="toggle-slider"></span>
              </label>
            </label>
            <span style="font-size:11px;color:var(--text3)">Continuous per-second billing</span>
          </div>
          <div style="display:flex;gap:10px;align-items:center">
            <button class="btn-nano" onclick="submitNanoPayment()">🔬 Send Nano</button>
            <span id="nano-send-status" style="font-size:12px;color:var(--text3)"></span>
          </div>
          <div class="nano-info-box">
            <div class="nano-info-box-title">Micro-USDC Unit</div>
            <p>1 micro-USDC = 0.000001 USDC. Enter integer units — 500 = $0.0005. Ideal for per-token, per-second, or per-task agent billing with zero fees on sub-cent amounts.</p>
          </div>
        </div>
      </div>

      <!-- Paymaster Controls -->
      <div class="panel" style="border-color:rgba(168,85,247,0.25)">
        <div class="panel-header">
          <span class="panel-title" style="color:var(--paymaster)">⛽ Paymaster Controls</span>
          <span class="panel-meta">Circle Paymaster · USDC Gas</span>
        </div>
        <div class="panel-body">
          <div class="form-group" style="margin-bottom:0.85rem">
            <label for="pm-agent">Agent</label>
            <select class="form-control" id="pm-agent" onchange="loadPaymasterStatus()"><option value="">— Select Agent —</option></select>
          </div>

          <!-- Status display -->
          <div id="pm-status-box" style="display:none;margin-bottom:1rem;padding:0.85rem;background:var(--pm-bg);border:1px solid rgba(168,85,247,0.2);border-radius:10px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text3)">Current Status</span>
              <span id="pm-status-badge"></span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
              <div style="font-size:11px;color:var(--text3)">Gas USDC Limit</div>
              <div style="font-size:11px;color:var(--paymaster);font-family:'JetBrains Mono',monospace;text-align:right" id="pm-current-limit">—</div>
              <div style="font-size:11px;color:var(--text3)">Gas Used (USDC)</div>
              <div style="font-size:11px;color:var(--text2);font-family:'JetBrains Mono',monospace;text-align:right" id="pm-gas-used">—</div>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:0.85rem">
            <label for="pm-gas-limit">Gas USDC Limit</label>
            <input type="number" class="form-control" id="pm-gas-limit" placeholder="e.g. 5.00" step="0.01" min="0.01">
            <div style="font-size:10px;color:var(--text3);margin-top:4px">Max USDC this agent can spend on gas. 0 = unlimited.</div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:1rem">
            <button class="btn-paymaster" onclick="togglePaymaster(true)">⛽ Enable Paymaster</button>
            <button class="btn-secondary" onclick="togglePaymaster(false)" style="font-size:12px;padding:7px 14px">Disable</button>
            <span id="pm-toggle-status" style="font-size:12px;color:var(--text3);align-self:center"></span>
          </div>
          <div class="pm-info-box">
            <div class="pm-info-box-title">Circle Paymaster</div>
            <p>When enabled, Circle Paymaster pays gas fees on behalf of the agent. Gas is deducted in USDC — agents need zero ETH to transact on Base. Set a per-agent USDC gas limit to cap spending.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Nano Payment Feed -->
    <div class="panel" style="border-color:rgba(0,229,160,0.15)">
      <div class="panel-header">
        <span class="panel-title" style="color:var(--nano)">Nanopayment Feed</span>
        <div style="display:flex;gap:8px;align-items:center">
          <span class="panel-meta">Live · Base-Sepolia</span>
          <button onclick="loadNanoPayments()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace">↻ Refresh</button>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>#</th><th>From</th><th>To</th><th>Micro-USDC</th><th>USDC Value</th><th>Stream</th><th>Status</th><th>Time</th></tr>
          </thead>
          <tbody id="nano-payments-body">
            <tr><td colspan="8" class="loading"><span class="spinner"></span>Loading nanopayments...</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Paymaster Agent List -->
    <div class="panel" style="border-color:rgba(168,85,247,0.2)">
      <div class="panel-header">
        <span class="panel-title" style="color:var(--paymaster)">Paymaster Agent Status</span>
        <button onclick="loadPaymasterAgents()" style="font-size:12px;color:var(--text3);background:none;border:none;cursor:pointer;font-family:'JetBrains Mono',monospace">↻ Refresh</button>
      </div>
      <div id="pm-agents-list">
        <div class="loading"><span class="spinner"></span>Loading agents...</div>
      </div>
    </div>

  </div>
</div><!-- /page-nano -->


<!-- ═══════════════════════════════════════════════════════
     TOAST NOTIFICATION
     ═══════════════════════════════════════════════════════ -->
<div id="toast"></div>


<!-- ═══════════════════════════════════════════════════════════════════
     JAVASCRIPT
     ═══════════════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
<script>
/* ─── CONFIG ──────────────────────────────────────────────────────────────── */
const API = '/api';
let agents = [];

/* ─── PAGE ROUTER ─────────────────────────────────────────────────────────── */
function showPage(name, btn) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  if (btn) btn.classList.add('active');
  window.scrollTo(0, 0);
  if (name === 'dashboard') loadDashboard();
  if (name === 'send')      loadAgentsIntoSelects();
  if (name === 'agents')    loadAgents();
  if (name === 'triggers')  initTriggersPage();
  if (name === 'payments')  loadPayments();
  if (name === 'audit')     loadAuditLogs();
  if (name === 'nano')      initNanoPage();
}

/* ─── API HELPER ──────────────────────────────────────────────────────────── */
async function apiFetch(path, opts = {}) {
  const { headers: extraHeaders = {}, ...restOpts } = opts;
  const res = await fetch(API + path, {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      ...extraHeaders,
    },
    ...restOpts,
  });
  let data;
  try { data = await res.json(); } catch (_) { data = { _parseError: true }; }
  if (typeof data === 'object' && data !== null) { data._status = res.status; data._ok = res.ok; }
  return data;
}

/* ─── UI HELPERS ──────────────────────────────────────────────────────────── */
function toast(msg, type = 'success') {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = msg;
  el.className = 'show ' + type;
  clearTimeout(el._timer);
  el._timer = setTimeout(() => { el.className = ''; }, 4500);
}

function badge(s) {
  const map = {
    confirmed:'confirmed', submitted:'submitted', pending:'pending',
    failed:'failed', active:'active', allowed:'allowed', blocked:'blocked',
    inactive:'inactive', balance_threshold:'balance_threshold',
    scheduled:'scheduled', task_event:'task_event',
    nano:'nano', streaming:'streaming', paymaster:'paymaster',
  };
  const cls   = map[String(s).toLowerCase()] || 'pending';
  const label = String(s).replace(/_/g,' ');
  return `<span class="badge badge-${cls}">${label.charAt(0).toUpperCase() + label.slice(1)}</span>`;
}

function shortTx(id) {
  if (!id) return '<span style="color:var(--text3)">—</span>';
  return `<span class="mono">${String(id).substring(0,8)}…</span>`;
}

function timeAgo(d) {
  if (!d) return '—';
  const diff = Math.floor((Date.now() - new Date(d)) / 1000);
  if (diff < 60)    return diff + 's ago';
  if (diff < 3600)  return Math.floor(diff/60) + 'm ago';
  if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
  return Math.floor(diff/86400) + 'd ago';
}

function escHtml(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ─── TICKER + HERO STATS ─────────────────────────────────────────────────── */
async function loadTickerStats() {
  try {
    const [ag, pay] = await Promise.all([apiFetch('/agents'), apiFetch('/payments')]);
    const ags  = ag.data  || ag;
    const pays = pay.data || pay;
    const vol  = Array.isArray(pays) ? pays.reduce((s,p) => s + parseFloat(p.amount||0), 0) : 0;

    const setAll = (ids, val) => ids.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = val; });
    setAll(['ticker-agents','ticker-agents2'],     (Array.isArray(ags)  ? ags.length  : '—') + ' agents');
    setAll(['ticker-payments','ticker-payments2'], (Array.isArray(pays) ? pays.length : '—') + ' txns');
    setAll(['ticker-volume','ticker-volume2'],     vol.toFixed(2) + ' USDC');

    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('hs-agents',   Array.isArray(ags)  ? ags.length  : '—');
    set('hs-payments', Array.isArray(pays) ? pays.length : '—');
    set('hs-volume',   vol.toFixed(2));
    set('prof-agents',   Array.isArray(ags)  ? ags.length  : '—');
    set('prof-payments', Array.isArray(pays) ? pays.length : '—');
  } catch(e) {}
}

async function loadTickerTriggers() {
  try {
    const ag  = await apiFetch('/agents');
    const ags = ag.data || ag;
    if (!Array.isArray(ags) || !ags.length) return;
    let activeTriggers = 0;
    await Promise.all(ags.map(async a => {
      try {
        const res  = await apiFetch(`/agents/${a.id}/triggers?active_only=true`);
        const list = res.data || res;
        if (Array.isArray(list)) activeTriggers += list.length;
      } catch(_) {}
    }));
    const setAll = (ids, val) => ids.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = val; });
    setAll(['ticker-triggers','ticker-triggers2'], activeTriggers + ' active');
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('hs-triggers', activeTriggers);
    set('ds-triggers', activeTriggers);
  } catch(e) {}
}

async function loadTickerNano() {
  try {
    const res  = await apiFetch('/v1/nano/stats');
    const data = res.data || res;
    const total = data.total || 0;
    const setAll = (ids, val) => ids.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = val; });
    setAll(['ticker-nano','ticker-nano2'], total + ' nano');
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('hs-nano', total);
    set('ds-nano', total);
  } catch(e) {
    // API not yet seeded — show 0
    const setAll = (ids, val) => ids.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = val; });
    setAll(['ticker-nano','ticker-nano2'], '0 nano');
  }
}

async function loadSepoliaBlock() {
  try {
    const res  = await fetch('https://sepolia.base.org', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({jsonrpc:'2.0',method:'eth_blockNumber',params:[],id:1}) });
    const data = await res.json();
    const block = '#' + parseInt(data.result, 16).toLocaleString();
    ['ticker-block','ticker-block2'].forEach(id => { const el = document.getElementById(id); if(el) el.textContent = block; });
  } catch(e) {}
}

/* ─── DASHBOARD ───────────────────────────────────────────────────────────── */
async function loadDashboard() {
  try {
    const [payRes, agRes] = await Promise.all([apiFetch('/payments'), apiFetch('/agents')]);
    const pays = payRes.data || payRes;
    const ags  = agRes.data  || agRes;
    const confirmed = Array.isArray(pays) ? pays.filter(p => p.status === 'confirmed').length : 0;
    const volume    = Array.isArray(pays) ? pays.reduce((s,p) => s + parseFloat(p.amount||0), 0) : 0;

    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('ds-total',     Array.isArray(pays) ? pays.length : '—');
    set('ds-confirmed', confirmed);
    set('ds-volume',    volume.toFixed(2));
    set('ds-agents',    Array.isArray(ags)  ? ags.length  : '—');

    const tbody = document.getElementById('dash-payments-body');
    if (!Array.isArray(pays) || !pays.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="empty">No payments yet</td></tr>';
    } else {
      tbody.innerHTML = pays.slice(0,20).map(p => `
        <tr>
          <td class="mono">${String(p.id).padStart(4,'0')}</td>
          <td class="fw">${escHtml(p.sender_agent?.name || p.senderAgent?.name || '—')}</td>
          <td class="fw">${escHtml(p.receiver_agent?.name || p.receiverAgent?.name || '—')}</td>
          <td class="green-val">${parseFloat(p.amount).toFixed(6)}</td>
          <td>${badge(p.status)}</td>
          <td class="mono">${timeAgo(p.created_at)}</td>
        </tr>`).join('');
    }

    const agList = document.getElementById('dash-agents-list');
    if (agList) {
      agList.innerHTML = Array.isArray(ags) ? ags.map(a => `
        <div style="display:flex;align-items:center;gap:10px;padding:0.75rem 1.25rem;border-bottom:1px solid var(--border)">
          <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,rgba(79,124,255,0.2),rgba(168,85,247,0.2));border:1px solid rgba(79,124,255,0.2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--blue2);font-family:'JetBrains Mono',monospace">${(a.name||'?')[0]}</div>
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:13px;color:var(--text)">${escHtml(a.name)}</div>
            <div style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--text3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${a.circle_wallet_address||'No wallet'}</div>
          </div>
          <div style="display:flex;gap:4px;align-items:center">
            ${a.paymaster_enabled ? '<span class="badge badge-paymaster" style="font-size:9px;padding:2px 6px">⛽ PM</span>' : ''}
            <span class="badge badge-active">${a.status||'Active'}</span>
          </div>
        </div>`).join('') : '<div class="empty">No agents</div>';
    }
    loadTickerTriggers();
    loadTickerNano();
  } catch(e) {
    const tbody = document.getElementById('dash-payments-body');
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="empty">Error loading data</td></tr>';
  }
}

/* ─── AGENTS ──────────────────────────────────────────────────────────────── */
async function loadAgents() {
  const grid = document.getElementById('agents-grid');
  if (!grid) return;
  grid.innerHTML = '<div class="loading"><span class="spinner"></span>Loading agents...</div>';
  try {
    const res = await apiFetch('/agents');
    agents = res.data || res;
    const countEl = document.getElementById('agents-count');
    if (countEl) countEl.textContent = (Array.isArray(agents) ? agents.length : 0) + ' registered';
    if (!Array.isArray(agents) || !agents.length) { grid.innerHTML = '<div class="empty">No agents registered</div>'; return; }
    grid.innerHTML = agents.map(a => `
      <div class="agent-card">
        <div class="agent-card-top">
          <div class="agent-avatar">${(a.name||'?')[0]}</div>
          <div>
            <div class="agent-card-name">${escHtml(a.name)}</div>
            <div class="agent-card-email">${escHtml(a.email)}</div>
          </div>
        </div>
        <div class="agent-card-addr">${a.circle_wallet_address||'<span style="color:var(--amber)">Wallet provisioning...</span>'}</div>
        <div class="agent-card-footer">
          <span class="mono" style="color:var(--text3)">${a.blockchain||'Base-Sepolia'}</span>
          <div style="display:flex;gap:6px;align-items:center">
            ${a.paymaster_enabled ? '<span class="badge badge-paymaster" style="font-size:9px;padding:2px 6px">⛽ PM</span>' : ''}
            <button class="btn-purple" onclick="goToAgentTriggers(${a.id})">🎯 Triggers</button>
            <span class="badge badge-active">Active</span>
          </div>
        </div>
      </div>`).join('');
  } catch(e) {
    grid.innerHTML = '<div class="empty">Error loading agents</div>';
  }
}

function goToAgentTriggers(agentId) {
  showPage('triggers', document.querySelectorAll('.nav-btn')[5]);
  setTimeout(() => {
    const sel = document.getElementById('tr-filter-agent');
    if (sel) { sel.value = agentId; loadTriggersForAgent(); }
  }, 200);
}

async function registerAgent() {
  const name   = document.getElementById('reg-name')?.value.trim();
  const email  = document.getElementById('reg-email')?.value.trim();
  const status = document.getElementById('reg-status');
  if (!name || !email) { toast('Name and email required', 'error'); return; }
  if (status) status.textContent = 'Registering...';
  try {
    const res = await apiFetch('/agents/register', { method:'POST', body: JSON.stringify({ name, email }) });
    if (res.agent || res.id || res._ok) {
      toast('Agent registered!');
      document.getElementById('reg-name').value = '';
      document.getElementById('reg-email').value = '';
      if (status) status.textContent = '';
      loadAgents(); loadAgentsIntoSelects();
    } else {
      toast(res.message || res.error || 'Registration failed', 'error');
      if (status) status.textContent = '';
    }
  } catch(e) {
    toast('Error registering agent', 'error');
    if (status) status.textContent = '';
  }
}

async function loadAgentsIntoSelects() {
  try {
    const res = await apiFetch('/agents');
    agents = res.data || res;
    if (!Array.isArray(agents)) return;
    const opts = agents.map(a => `<option value="${a.id}">${escHtml(a.name)}</option>`).join('');
    ['send-sender','send-receiver','batch-sender'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = '<option value="">— Select Agent —</option>' + opts;
    });
    document.querySelectorAll('.batch-recv').forEach(el => { el.innerHTML = '<option value="">— Agent —</option>' + opts; });
  } catch(e) {}
}

/* ─── TRIGGERS ────────────────────────────────────────────────────────────── */
async function initTriggersPage() {
  try {
    const res = await apiFetch('/agents');
    agents = res.data || res;
    if (!Array.isArray(agents)) return;
    const opts = agents.map(a => `<option value="${a.id}">${escHtml(a.name)}</option>`).join('');
    ['tr-agent','tr-filter-agent','tr-recv-agent'].forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      if (id === 'tr-recv-agent')    el.innerHTML = '<option value="">— None / Use address —</option>' + opts;
      else if (id === 'tr-filter-agent') el.innerHTML = '<option value="">— Select Agent —</option>' + opts;
      else                               el.innerHTML = '<option value="">— Select Agent —</option>' + opts;
    });
  } catch(e) {}
}

function onTriggerTypeChange() {
  const type = document.getElementById('tr-type')?.value;
  document.querySelectorAll('.trigger-fields').forEach(el => el.classList.remove('visible'));
  if (type === 'balance_threshold') document.getElementById('tf-balance')?.classList.add('visible');
  if (type === 'scheduled')         document.getElementById('tf-scheduled')?.classList.add('visible');
  if (type === 'task_event')        document.getElementById('tf-event')?.classList.add('visible');
}

function onTriggerAgentChange() {
  const agentId   = document.getElementById('tr-agent')?.value;
  const filterSel = document.getElementById('tr-filter-agent');
  if (filterSel && agentId) { filterSel.value = agentId; loadTriggersForAgent(); }
}

async function createTrigger() {
  const agentId = document.getElementById('tr-agent')?.value;
  const name    = document.getElementById('tr-name')?.value.trim();
  const type    = document.getElementById('tr-type')?.value;
  if (!agentId) { toast('Select an agent', 'error'); return; }
  if (!name)    { toast('Trigger name is required', 'error'); return; }
  if (!type)    { toast('Select a trigger type', 'error'); return; }

  const payload = {
    name, trigger_type: type,
    is_active:         document.getElementById('tr-active')?.checked,
    amount:            parseFloat(document.getElementById('tr-amount')?.value) || 0,
    currency:          document.getElementById('tr-currency')?.value || 'USDC',
    note:              document.getElementById('tr-note')?.value || undefined,
    receiver_agent_id: document.getElementById('tr-recv-agent')?.value || undefined,
    receiver_address:  document.getElementById('tr-recv-addr')?.value  || undefined,
  };

  if (type === 'balance_threshold') {
    payload.threshold_amount    = parseFloat(document.getElementById('tr-threshold-amount')?.value) || 0;
    payload.threshold_direction = document.getElementById('tr-threshold-dir')?.value;
  }
  if (type === 'scheduled') {
    payload.interval_hours = parseInt(document.getElementById('tr-interval')?.value) || null;
    if (!payload.interval_hours) { toast('Interval (hours) required for scheduled triggers', 'error'); return; }
  }
  if (type === 'task_event') {
    payload.event_name = document.getElementById('tr-event-name')?.value.trim() || null;
    if (!payload.event_name) { toast('Event name required for task event triggers', 'error'); return; }
  }

  const btn    = document.getElementById('tr-create-btn');
  const status = document.getElementById('tr-create-status');
  if (btn) btn.disabled = true;
  if (status) status.textContent = 'Creating...';

  try {
    const res = await apiFetch(`/agents/${agentId}/triggers`, { method:'POST', body: JSON.stringify(payload) });
    if (res._ok || res.id || res.trigger_type) {
      toast('Trigger created!');
      ['tr-name','tr-threshold-amount','tr-interval','tr-event-name','tr-recv-addr','tr-amount','tr-note'].forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
      document.getElementById('tr-type').value = '';
      document.getElementById('tr-currency').value = 'USDC';
      document.getElementById('tr-active').checked = true;
      document.querySelectorAll('.trigger-fields').forEach(el => el.classList.remove('visible'));
      const filterSel = document.getElementById('tr-filter-agent');
      if (filterSel) { filterSel.value = agentId; loadTriggersForAgent(); }
    } else {
      toast(res.message || res.error || JSON.stringify(res.errors || 'Creation failed'), 'error');
    }
  } catch(e) { toast('Error creating trigger: ' + e.message, 'error'); }

  if (btn) btn.disabled = false;
  if (status) status.textContent = '';
}

async function loadTriggersForAgent() {
  const agentId    = document.getElementById('tr-filter-agent')?.value;
  const typeFilter = document.getElementById('tr-filter-type')?.value;
  const activeOnly = document.getElementById('tr-filter-active')?.checked;
  const container  = document.getElementById('triggers-list');
  if (!container) return;
  if (!agentId) { container.innerHTML = '<div class="trigger-empty">Select an agent above to view its triggers.</div>'; return; }
  container.innerHTML = '<div class="loading"><span class="spinner"></span>Loading triggers...</div>';
  try {
    let qs = '';
    if (typeFilter) qs += `type=${encodeURIComponent(typeFilter)}&`;
    if (activeOnly) qs += 'active_only=true&';
    const res  = await apiFetch(`/agents/${agentId}/triggers?${qs}`);
    const list = res.data || res;
    if (!Array.isArray(list) || !list.length) { container.innerHTML = '<div class="trigger-empty">No triggers found. Create one above.</div>'; return; }
    container.innerHTML = list.map(t => renderTriggerCard(t, agentId)).join('');
  } catch(e) {
    container.innerHTML = `<div class="trigger-empty" style="color:var(--red)">Error: ${escHtml(e.message)}</div>`;
  }
}

function renderTriggerCard(t, agentId) {
  const isActive = t.is_active;
  let typeDetail = '';
  if (t.trigger_type === 'balance_threshold') typeDetail = `<span><span class="key">threshold:</span> ${t.threshold_direction} ${parseFloat(t.threshold_amount||0).toFixed(2)} USDC</span>`;
  else if (t.trigger_type === 'scheduled')    typeDetail = `<span><span class="key">every:</span> ${t.interval_hours}h</span>`;
  else if (t.trigger_type === 'task_event')   typeDetail = `<span><span class="key">event:</span> ${escHtml(t.event_name||'—')}</span>`;
  const receiver = t.receiver_address || (t.receiver_agent ? t.receiver_agent.name : '—');
  return `
    <div class="trigger-card${isActive ? '' : ' inactive'}" id="trigger-card-${t.id}">
      <div class="trigger-card-top">
        <div class="trigger-card-name">${escHtml(t.name)}</div>
        ${badge(t.trigger_type)} ${badge(isActive ? 'active' : 'inactive')}
      </div>
      <div class="trigger-card-detail">
        ${typeDetail}
        <span><span class="key">amount:</span> ${parseFloat(t.amount||0).toFixed(6)} ${t.currency||'USDC'}</span>
        <span><span class="key">receiver:</span> ${escHtml(receiver)}</span>
        <span><span class="key">fired:</span> ${t.fired_count||0}×</span>
        <span><span class="key">last fired:</span> ${timeAgo(t.last_fired_at)}</span>
        ${t.note ? `<span><span class="key">note:</span> ${escHtml(t.note)}</span>` : ''}
      </div>
      <div class="trigger-actions">
        <button class="btn-amber"     onclick="fireTrigger(${agentId},${t.id})">⚡ Fire</button>
        <button class="btn-secondary" onclick="toggleTrigger(${agentId},${t.id},${isActive})">${isActive ? '⏸ Deactivate' : '▶ Activate'}</button>
        <button class="btn-danger"    onclick="deleteTrigger(${agentId},${t.id})">🗑 Delete</button>
      </div>
    </div>`;
}

async function fireTrigger(agentId, triggerId) {
  try {
    const res = await apiFetch(`/agents/${agentId}/triggers/${triggerId}/fire`, { method:'POST' });
    if (res.message && res.receiver_address) { toast(`Fired! → ${res.receiver_address.substring(0,12)}… · ${parseFloat(res.amount).toFixed(4)} ${res.currency}`); loadTriggersForAgent(); }
    else toast(res.message || 'Fire failed', 'error');
  } catch(e) { toast('Error firing trigger: ' + e.message, 'error'); }
}

async function toggleTrigger(agentId, triggerId) {
  try {
    const res = await apiFetch(`/agents/${agentId}/triggers/${triggerId}/toggle`, { method:'PATCH' });
    if (res.is_active !== undefined) { toast(res.message || (res.is_active ? 'Trigger activated' : 'Trigger deactivated')); loadTriggersForAgent(); }
    else toast(res.message || 'Toggle failed', 'error');
  } catch(e) { toast('Error toggling trigger: ' + e.message, 'error'); }
}

async function deleteTrigger(agentId, triggerId) {
  if (!confirm('Delete this trigger? This cannot be undone.')) return;
  try {
    const res = await apiFetch(`/agents/${agentId}/triggers/${triggerId}`, { method:'DELETE' });
    if (res.message || res._ok) {
      toast('Trigger deleted');
      const card = document.getElementById('trigger-card-' + triggerId);
      if (card) card.remove();
      const container = document.getElementById('triggers-list');
      if (container && !container.querySelector('.trigger-card')) container.innerHTML = '<div class="trigger-empty">No triggers found. Create one above.</div>';
    } else toast(res.message || 'Delete failed', 'error');
  } catch(e) { toast('Error deleting trigger: ' + e.message, 'error'); }
}

/* ─── SEND PAYMENT ────────────────────────────────────────────────────────── */
async function submitPayment() {
  const sender   = document.getElementById('send-sender')?.value;
  const receiver = document.getElementById('send-receiver')?.value;
  const amount   = document.getElementById('send-amount')?.value;
  const note     = document.getElementById('send-note')?.value;
  const btn      = document.getElementById('send-btn');
  const status   = document.getElementById('send-status');
  const rd       = document.getElementById('send-result');
  const rb       = document.getElementById('send-result-body');

  if (!sender || !receiver || !amount) { toast('Fill in all required fields', 'error'); return; }
  if (sender === receiver)             { toast('Sender and receiver must be different', 'error'); return; }
  if (btn)    btn.disabled = true;
  if (status) status.innerHTML = '<span class="spinner"></span>Processing...';
  if (rd)     rd.style.display = 'none';

  try {
    const res = await apiFetch('/payments/send', { method:'POST', body: JSON.stringify({ sender_agent_id: parseInt(sender), receiver_agent_id: parseInt(receiver), amount: parseFloat(amount), note: note || undefined }) });
    if (rd) rd.style.display = 'block';
    if (res.payment) {
      const p = res.payment;
      toast('Payment submitted successfully!');
      rb.innerHTML = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div><div style="font-size:10px;font-weight:700;color:var(--text3);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px">Payment ID</div><div class="mono" style="font-size:18px;color:var(--blue2)">#${p.id||'—'}</div></div>
        <div><div style="font-size:10px;font-weight:700;color:var(--text3);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px">Status</div><div style="margin-top:6px">${badge(p.status||'submitted')}</div></div>
        <div><div style="font-size:10px;font-weight:700;color:var(--text3);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px">Amount</div><div class="green-val" style="font-size:22px">${parseFloat(p.amount||amount).toFixed(6)} USDC</div></div>
        <div><div style="font-size:10px;font-weight:700;color:var(--text3);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px">Circle TX ID</div><div class="mono">${p.circle_transaction_id||'Pending...'}</div></div>
      </div>`;
    } else if (res.error && String(res.code||'').startsWith('POLICY_')) {
      toast('Payment blocked by spending policy', 'error');
      rb.innerHTML = `<div class="policy-box"><div class="policy-box-title">⛔ Policy Violation</div><div class="policy-box-row"><span>${escHtml(res.error)}</span></div>${res.limit!==undefined?`<div class="policy-box-row"><span>Policy limit</span><span>${res.limit} USDC</span></div>`:''}</div>`;
    } else if (res.error) {
      toast(res.error, 'error');
      rb.innerHTML = `<div class="error-box"><div class="error-box-title">Payment Failed</div><div class="error-box-code">${res.code||'UNKNOWN'}</div><div style="font-size:13px;color:var(--text2)">${escHtml(res.error)}</div>${res.circle_detail?`<div class="error-box-detail">${JSON.stringify(res.circle_detail,null,2)}</div>`:''}</div>`;
    } else {
      toast('Unexpected response', 'error');
      rb.innerHTML = `<div class="error-box"><div class="error-box-title">Unexpected Response</div><div class="error-box-detail">${JSON.stringify(res,null,2)}</div></div>`;
    }
  } catch(e) {
    if (rd) rd.style.display = 'block';
    toast('Network error: ' + e.message, 'error');
    if (rb) rb.innerHTML = `<div class="error-box"><div class="error-box-title">Network Error</div><div style="font-size:13px;color:var(--text2)">${e.message}</div></div>`;
  }
  if (btn)    btn.disabled = false;
  if (status) status.textContent = '';
}

/* ─── BATCH SEND ──────────────────────────────────────────────────────────── */
function addBatchItem() {
  const opts = agents.map(a => `<option value="${a.id}">${escHtml(a.name)}</option>`).join('');
  const item = document.createElement('div');
  item.className = 'batch-item';
  item.style.cssText = 'display:grid;grid-template-columns:1fr 110px 1fr 30px;gap:0.75rem;margin-bottom:0.75rem;align-items:end';
  item.innerHTML = `
    <div class="form-group"><label>Receiver</label><select class="form-control batch-recv"><option value="">— Agent —</option>${opts}</select></div>
    <div class="form-group"><label>Amount</label><input type="number" class="form-control batch-amt" placeholder="USDC" step="0.000001" min="0.000001"></div>
    <div class="form-group"><label>Note</label><input type="text" class="form-control batch-note" placeholder="Optional"></div>
    <div class="form-group"><label>&nbsp;</label><button onclick="this.closest('.batch-item').remove()" style="background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.25);color:var(--red);border-radius:8px;padding:10px;cursor:pointer;font-size:13px;width:100%">✕</button></div>`;
  document.getElementById('batch-items')?.appendChild(item);
}

async function submitBatch() {
  const sender = document.getElementById('batch-sender')?.value;
  if (!sender) { toast('Select a sender agent', 'error'); return; }
  const payments = [];
  document.querySelectorAll('.batch-item').forEach(item => {
    const recv = item.querySelector('.batch-recv')?.value;
    const amt  = item.querySelector('.batch-amt')?.value;
    const note = item.querySelector('.batch-note')?.value;
    if (recv && amt) payments.push({ receiver_agent_id: parseInt(recv), amount: parseFloat(amt), note: note || undefined });
  });
  if (!payments.length) { toast('Add at least one recipient', 'error'); return; }
  try {
    const res    = await apiFetch('/payments/batch', { method:'POST', body: JSON.stringify({ sender_agent_id: parseInt(sender), payments }) });
    const count  = res.results?.length || payments.length;
    const failed = res.results?.filter(r => r.status >= 400).length || 0;
    toast(failed ? `Batch: ${count-failed} sent, ${failed} failed` : `Batch sent — ${count} payments submitted`, failed ? 'error' : 'success');
  } catch(e) { toast('Batch send failed: ' + e.message, 'error'); }
}

/* ─── PAYMENTS LIST ───────────────────────────────────────────────────────── */
async function loadPayments() {
  const tbody = document.getElementById('payments-body');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="8" class="loading"><span class="spinner"></span>Loading...</td></tr>';
  try {
    const res  = await apiFetch('/payments');
    const pays = res.data || res;
    if (!Array.isArray(pays) || !pays.length) { tbody.innerHTML = '<tr><td colspan="8" class="empty">No payments yet</td></tr>'; return; }
    tbody.innerHTML = pays.map(p => `
      <tr>
        <td class="mono">${String(p.id).padStart(4,'0')}</td>
        <td>${shortTx(p.circle_transaction_id)}</td>
        <td class="fw">${escHtml(p.sender_agent?.name || p.senderAgent?.name || String(p.sender_agent_id))}</td>
        <td class="fw">${escHtml(p.receiver_agent?.name || p.receiverAgent?.name || String(p.receiver_agent_id))}</td>
        <td class="green-val">${parseFloat(p.amount).toFixed(6)}</td>
        <td>${badge(p.status)}</td>
        <td class="mono">${p.blockchain||'Arc/Base'}</td>
        <td class="mono">${timeAgo(p.created_at)}</td>
      </tr>`).join('');
  } catch(e) { tbody.innerHTML = '<tr><td colspan="8" class="empty">Error loading payments</td></tr>'; }
}

/* ─── AUDIT LOGS ──────────────────────────────────────────────────────────── */
async function loadAuditLogs() {
  const tbody = document.getElementById('audit-body');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="8" class="loading"><span class="spinner"></span>Loading...</td></tr>';
  try {
    const res  = await apiFetch('/audit-logs');
    const logs = res.data || res;
    if (!Array.isArray(logs) || !logs.length) { tbody.innerHTML = '<tr><td colspan="8" class="empty">No audit logs</td></tr>'; return; }
    tbody.innerHTML = logs.map(l => `
      <tr>
        <td class="mono">${String(l.id).padStart(4,'0')}</td>
        <td class="fw">${escHtml(l.agent_name||'—')}</td>
        <td class="mono" style="color:var(--blue2)">${escHtml(l.event_type||'—')}</td>
        <td>${badge(l.event_status||'pending')}</td>
        <td class="green-val">${l.amount ? parseFloat(l.amount).toFixed(6) : '—'}</td>
        <td class="mono" style="color:var(--text3)">${escHtml(l.policy_rule||'—')}</td>
        <td class="mono">${escHtml(l.source||'—')}</td>
        <td class="mono">${timeAgo(l.created_at)}</td>
      </tr>`).join('');
  } catch(e) { tbody.innerHTML = '<tr><td colspan="8" class="empty">Error loading audit logs</td></tr>'; }
}

/* ─── M4: NANO PAGE INIT ──────────────────────────────────────────────────── */
async function initNanoPage() {
  // Load agents into nano selects
  try {
    const res = await apiFetch('/agents');
    agents = res.data || res;
    if (!Array.isArray(agents)) return;
    const opts = agents.map(a => `<option value="${a.id}">${escHtml(a.name)}</option>`).join('');
    ['nano-sender','nano-receiver','pm-agent'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = '<option value="">— Select Agent —</option>' + opts;
    });
  } catch(e) {}

  // Load stats + feed
  loadNanoStats();
  loadNanoPayments();
  loadPaymasterAgents();
}

/* ─── M4: NANO AMOUNT DISPLAY ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  const microInput = document.getElementById('nano-amount-micro');
  const display    = document.getElementById('nano-amount-display');
  if (microInput && display) {
    microInput.addEventListener('input', () => {
      const v = parseInt(microInput.value) || 0;
      display.textContent = (v / 1000000).toFixed(6) + ' USDC (' + v + ' micro)';
    });
  }
});

/* ─── M4: NANO STATS ──────────────────────────────────────────────────────── */
async function loadNanoStats() {
  try {
    const res  = await apiFetch('/v1/nano/stats');
    const data = res.data || res;
    const set  = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('nano-stat-total',  data.total       || 0);
    set('nano-stat-volume', data.total_micro || 0);
    set('nano-stat-pm-agents', data.paymaster_agents || 0);
    set('nano-stat-gas-saved', data.gas_saved_usdc   ? parseFloat(data.gas_saved_usdc).toFixed(4) : '0.0000');
    // Update ticker
    const setAll = (ids, val) => ids.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = val; });
    setAll(['ticker-nano','ticker-nano2'], (data.total || 0) + ' nano');
    set('hs-nano', data.total || 0);
    set('ds-nano', data.total || 0);
  } catch(e) {}
}

/* ─── M4: NANO PAYMENTS FEED ──────────────────────────────────────────────── */
async function loadNanoPayments() {
  const tbody = document.getElementById('nano-payments-body');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="8" class="loading"><span class="spinner"></span>Loading...</td></tr>';
  try {
    const res  = await apiFetch('/v1/nano');
    const pays = res.data || res;
    if (!Array.isArray(pays) || !pays.length) {
      tbody.innerHTML = '<tr><td colspan="8" class="empty">No nanopayments yet. Send your first one above.</td></tr>';
      return;
    }
    tbody.innerHTML = pays.map(p => `
      <tr>
        <td class="mono">${String(p.id).padStart(4,'0')}</td>
        <td class="fw">${escHtml(p.sender_agent?.name || '—')}</td>
        <td class="fw">${escHtml(p.receiver_agent?.name || '—')}</td>
        <td class="micro-val">${p.amount_micro || 0}</td>
        <td class="micro-val">${((p.amount_micro||0)/1000000).toFixed(6)}</td>
        <td>${p.stream ? badge('streaming') : badge('nano')}</td>
        <td>${badge(p.status || 'confirmed')}</td>
        <td class="mono">${timeAgo(p.created_at)}</td>
      </tr>`).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="8" class="empty">No nanopayments yet.</td></tr>';
  }
}

/* ─── M4: SEND NANOPAYMENT ────────────────────────────────────────────────── */
async function submitNanoPayment() {
  const sender    = document.getElementById('nano-sender')?.value;
  const receiver  = document.getElementById('nano-receiver')?.value;
  const micro     = parseInt(document.getElementById('nano-amount-micro')?.value);
  const note      = document.getElementById('nano-note')?.value;
  const stream    = document.getElementById('nano-stream')?.checked;
  const statusEl  = document.getElementById('nano-send-status');

  if (!sender)               { toast('Select a sender agent', 'error');   return; }
  if (!receiver)             { toast('Select a receiver agent', 'error'); return; }
  if (!micro || micro < 1)   { toast('Amount must be at least 1 micro-USDC', 'error'); return; }
  if (sender === receiver)   { toast('Sender and receiver must differ', 'error'); return; }

  if (statusEl) statusEl.innerHTML = '<span class="spinner"></span>Sending...';

  try {
    const res = await apiFetch('/v1/nano/send', {
      method: 'POST',
      body: JSON.stringify({
        sender_agent_id:   parseInt(sender),
        receiver_agent_id: parseInt(receiver),
        amount_micro:      micro,
        currency:          'USDC',
        stream:            stream,
        note:              note || undefined,
      })
    });

    if (res.nanopayment || res.id || res._ok) {
      const usdcVal = (micro / 1000000).toFixed(6);
      toast(`Nanopayment sent! ${micro} micro-USDC (${usdcVal} USDC)${stream ? ' · streaming' : ''}`);
      document.getElementById('nano-amount-micro').value = '';
      document.getElementById('nano-note').value = '';
      document.getElementById('nano-amount-display').textContent = '— USDC';
      loadNanoPayments();
      loadNanoStats();
    } else {
      toast(res.message || res.error || 'Nanopayment failed', 'error');
    }
  } catch(e) { toast('Error: ' + e.message, 'error'); }

  if (statusEl) statusEl.textContent = '';
}

/* ─── M4: PAYMASTER STATUS ────────────────────────────────────────────────── */
async function loadPaymasterStatus() {
  const agentId = document.getElementById('pm-agent')?.value;
  const box     = document.getElementById('pm-status-box');
  if (!agentId || !box) { if (box) box.style.display = 'none'; return; }
  try {
    const res  = await apiFetch(`/v1/paymaster/status/${agentId}`);
    const data = res.data || res;
    box.style.display = 'block';

    const badgeEl = document.getElementById('pm-status-badge');
    if (badgeEl) badgeEl.innerHTML = data.paymaster_enabled ? badge('paymaster') : badge('inactive');

    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('pm-current-limit', data.gas_usdc_limit ? parseFloat(data.gas_usdc_limit).toFixed(2) + ' USDC' : 'Unlimited');
    set('pm-gas-used',      data.gas_used_usdc  ? parseFloat(data.gas_used_usdc).toFixed(4)  + ' USDC' : '0.0000 USDC');

    // Pre-fill limit
    const limitInput = document.getElementById('pm-gas-limit');
    if (limitInput && data.gas_usdc_limit) limitInput.value = parseFloat(data.gas_usdc_limit).toFixed(2);
  } catch(e) {
    if (box) box.style.display = 'none';
  }
}

/* ─── M4: TOGGLE PAYMASTER ────────────────────────────────────────────────── */
async function togglePaymaster(enable) {
  const agentId  = document.getElementById('pm-agent')?.value;
  const limit    = parseFloat(document.getElementById('pm-gas-limit')?.value) || 0;
  const statusEl = document.getElementById('pm-toggle-status');

  if (!agentId) { toast('Select an agent first', 'error'); return; }
  if (statusEl) statusEl.innerHTML = '<span class="spinner"></span>';

  try {
    const res = await apiFetch('/v1/paymaster/toggle', {
      method: 'POST',
      body: JSON.stringify({ agent_id: parseInt(agentId), enabled: enable, gas_usdc_limit: limit })
    });

    if (res._ok || res.agent || res.message) {
      toast(enable ? `Paymaster enabled (limit: ${limit || '∞'} USDC)` : 'Paymaster disabled');
      loadPaymasterStatus();
      loadPaymasterAgents();
    } else {
      toast(res.message || res.error || 'Toggle failed', 'error');
    }
  } catch(e) { toast('Error: ' + e.message, 'error'); }

  if (statusEl) statusEl.textContent = '';
}

/* ─── M4: PAYMASTER AGENT LIST ────────────────────────────────────────────── */
async function loadPaymasterAgents() {
  const container = document.getElementById('pm-agents-list');
  if (!container) return;
  container.innerHTML = '<div class="loading"><span class="spinner"></span>Loading...</div>';
  try {
    const res = await apiFetch('/agents');
    const ags = res.data || res;
    if (!Array.isArray(ags) || !ags.length) { container.innerHTML = '<div class="empty">No agents found.</div>'; return; }

    // Count paymaster-enabled for stats
    const pmCount = ags.filter(a => a.paymaster_enabled).length;
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('nano-stat-pm-agents', pmCount);

    container.innerHTML = ags.map(a => `
      <div class="pm-agent-card">
        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,rgba(168,85,247,0.15),rgba(79,124,255,0.15));border:1px solid rgba(168,85,247,0.2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--paymaster);font-family:'JetBrains Mono',monospace;flex-shrink:0">${(a.name||'?')[0]}</div>
        <div class="pm-agent-info">
          <div class="pm-agent-name">${escHtml(a.name)}</div>
          <div class="pm-agent-addr">${a.circle_wallet_address || 'No wallet'}</div>
        </div>
        <div style="text-align:right;flex-shrink:0">
          <div style="font-size:10px;color:var(--text3);margin-bottom:3px">Gas Limit</div>
          <div style="font-size:12px;color:var(--paymaster);font-family:'JetBrains Mono',monospace">${a.gas_usdc_limit ? parseFloat(a.gas_usdc_limit).toFixed(2) + ' USDC' : '—'}</div>
        </div>
        <div style="flex-shrink:0">
          ${a.paymaster_enabled
            ? '<span class="badge badge-paymaster">⛽ Enabled</span>'
            : '<span class="badge badge-inactive">Disabled</span>'}
        </div>
        <button class="btn-paymaster" style="flex-shrink:0;font-size:11px;padding:5px 10px"
          onclick="quickTogglePaymaster(${a.id},${!a.paymaster_enabled})">
          ${a.paymaster_enabled ? 'Disable' : 'Enable'}
        </button>
      </div>`).join('');
  } catch(e) {
    container.innerHTML = '<div class="empty">Error loading agents.</div>';
  }
}

async function quickTogglePaymaster(agentId, enable) {
  try {
    const res = await apiFetch('/v1/paymaster/toggle', {
      method: 'POST',
      body: JSON.stringify({ agent_id: agentId, enabled: enable })
    });
    if (res._ok || res.message) {
      toast(enable ? 'Paymaster enabled' : 'Paymaster disabled');
      loadPaymasterAgents();
    } else toast(res.message || 'Toggle failed', 'error');
  } catch(e) { toast('Error: ' + e.message, 'error'); }
}

/* ─── WEBSOCKET (Pusher/Reverb) ───────────────────────────────────────────── */
try {
  const pusher = new Pusher(
    typeof PUSHER_KEY !== 'undefined' ? PUSHER_KEY : '',
    { cluster: typeof PUSHER_CLUSTER !== 'undefined' ? PUSHER_CLUSTER : 'mt1' }
  );
  pusher.subscribe('payments').bind('payment.updated', function(data) {
    const tbody = document.getElementById('dash-payments-body');
    if (tbody && document.getElementById('page-dashboard').classList.contains('active')) {
      const row = document.createElement('tr');
      row.classList.add('new-row');
      row.innerHTML = `
        <td class="mono">${String(data.id).padStart(4,'0')}</td>
        <td class="fw">${data.sender_agent_id}</td>
        <td class="fw">${data.receiver_agent_id}</td>
        <td class="green-val">${parseFloat(data.amount).toFixed(6)}</td>
        <td>${badge(data.status)}</td>
        <td class="mono">just now</td>`;
      tbody.insertBefore(row, tbody.firstChild);
    }
    toast('New payment: ' + parseFloat(data.amount).toFixed(2) + ' USDC');
    loadTickerStats();
  });

  // M4: Listen for nanopayments
  pusher.subscribe('nanopayments').bind('nano.sent', function(data) {
    const tbody = document.getElementById('nano-payments-body');
    if (tbody && document.getElementById('page-nano').classList.contains('active')) {
      const row = document.createElement('tr');
      row.classList.add('new-row');
      row.innerHTML = `
        <td class="mono">${String(data.id||0).padStart(4,'0')}</td>
        <td class="fw">${escHtml(data.sender_name || String(data.sender_agent_id))}</td>
        <td class="fw">${escHtml(data.receiver_name || String(data.receiver_agent_id))}</td>
        <td class="micro-val">${data.amount_micro||0}</td>
        <td class="micro-val">${((data.amount_micro||0)/1000000).toFixed(6)}</td>
        <td>${data.stream ? badge('streaming') : badge('nano')}</td>
        <td>${badge(data.status||'confirmed')}</td>
        <td class="mono">just now</td>`;
      tbody.insertBefore(row, tbody.firstChild);
    }
    toast('Nanopayment: ' + (data.amount_micro||0) + ' micro-USDC');
    loadNanoStats();
  });
} catch(e) { /* Pusher unavailable in standalone mode */ }

/* ─── INIT ────────────────────────────────────────────────────────────────── */
loadTickerStats();
loadTickerTriggers();
loadTickerNano();
loadSepoliaBlock();
</script>
</body>
</html>