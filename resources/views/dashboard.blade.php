<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArcOnAgent — Command Center</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #020408;
            --bg2:       #060d14;
            --bg3:       #0a1520;
            --border:    #0f3a5a;
            --border2:   #1a5a8a;
            --cyan:      #00d4ff;
            --cyan2:     #00a8cc;
            --green:     #00ff88;
            --green2:    #00cc6a;
            --yellow:    #ffd700;
            --red:       #ff3a5c;
            --orange:    #ff8c00;
            --text:      #c8e4f0;
            --text2:     #6a9ab8;
            --text3:     #2a5a7a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Rajdhani', sans-serif;
            font-size: 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,212,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,212,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* Glow orbs */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            left: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0,212,255,0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Navbar ── */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(2,4,8,0.95);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
            padding: 0 2rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            font-family: 'Orbitron', monospace;
            font-size: 18px;
            font-weight: 900;
            color: var(--cyan);
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(0,212,255,0.5);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand .bolt {
            color: var(--yellow);
            text-shadow: 0 0 15px rgba(255,215,0,0.6);
            animation: flicker 3s infinite;
        }

        @keyframes flicker {
            0%,95%,100% { opacity: 1; }
            96% { opacity: 0.4; }
            97% { opacity: 1; }
            98% { opacity: 0.6; }
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-tag {
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--text3);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .live-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--green);
            letter-spacing: 2px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--green);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%,100% { opacity: 1; box-shadow: 0 0 8px var(--green); }
            50%      { opacity: 0.5; box-shadow: 0 0 3px var(--green); }
        }

        /* ── Layout ── */
        .main {
            position: relative;
            z-index: 1;
            padding: 1.5rem 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* ── Stat Cards ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 1.2rem 1.5rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
        }

        .stat-card.cyan::before  { background: linear-gradient(90deg, transparent, var(--cyan), transparent); }
        .stat-card.green::before { background: linear-gradient(90deg, transparent, var(--green), transparent); }
        .stat-card.yellow::before{ background: linear-gradient(90deg, transparent, var(--yellow), transparent); }
        .stat-card.blue::before  { background: linear-gradient(90deg, transparent, #4499ff, transparent); }

        .stat-card:hover { border-color: var(--border2); }

        .stat-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text3);
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-family: 'Orbitron', monospace;
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
        }

        .stat-card.cyan  .stat-value { color: var(--cyan);   text-shadow: 0 0 20px rgba(0,212,255,0.4); }
        .stat-card.green .stat-value { color: var(--green);  text-shadow: 0 0 20px rgba(0,255,136,0.4); }
        .stat-card.yellow.stat-value { color: var(--yellow); }
        .stat-card.yellow .stat-value{ color: var(--yellow); text-shadow: 0 0 20px rgba(255,215,0,0.4); }
        .stat-card.blue  .stat-value { color: #4499ff;       text-shadow: 0 0 20px rgba(68,153,255,0.4); }

        .stat-corner {
            position: absolute;
            bottom: 8px; right: 12px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 40px;
            opacity: 0.04;
            font-weight: 900;
            line-height: 1;
        }

        /* ── Grid ── */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1rem;
        }

        /* ── Panel ── */
        .panel {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
        }

        .panel-header {
            background: var(--bg3);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-title {
            font-family: 'Orbitron', monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--cyan);
        }

        .panel-meta {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            color: var(--text3);
            letter-spacing: 1px;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: rgba(0,212,255,0.03);
        }

        th {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text3);
            padding: 0.65rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        td {
            padding: 0.65rem 1rem;
            border-bottom: 1px solid rgba(15,58,90,0.5);
            vertical-align: middle;
            font-size: 14px;
        }

        tbody tr {
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: rgba(0,212,255,0.03);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .id-cell {
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px;
            color: var(--text3);
        }

        .agent-name {
            font-weight: 600;
            color: var(--text);
            letter-spacing: 0.5px;
        }

        .amount-cell {
            font-family: 'Share Tech Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            color: var(--green);
        }

        .time-cell {
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--text3);
        }

        /* ── Badges ── */
        .badge {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 2px;
            border: 1px solid;
            display: inline-block;
        }

        .badge-confirmed  { color: var(--green);  border-color: var(--green);  background: rgba(0,255,136,0.08); }
        .badge-submitted  { color: var(--yellow); border-color: var(--yellow); background: rgba(255,215,0,0.08); }
        .badge-pending    { color: var(--text2);  border-color: var(--text3);  background: rgba(106,154,184,0.08); }
        .badge-failed     { color: var(--red);    border-color: var(--red);    background: rgba(255,58,92,0.08); }

        /* ── Agent Cards ── */
        .agent-card {
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid rgba(15,58,90,0.5);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.15s;
        }

        .agent-card:last-child { border-bottom: none; }
        .agent-card:hover { background: rgba(0,212,255,0.03); }

        .agent-icon {
            width: 36px;
            height: 36px;
            border: 1px solid var(--border2);
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            background: var(--bg3);
            flex-shrink: 0;
        }

        .agent-info { flex: 1; min-width: 0; }

        .agent-info-name {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: var(--text);
            letter-spacing: 0.5px;
        }

        .agent-info-addr {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            color: var(--text3);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .agent-status {
            font-family: 'Share Tech Mono', monospace;
            font-size: 9px;
            letter-spacing: 1.5px;
            padding: 2px 6px;
            border: 1px solid var(--green2);
            color: var(--green);
            border-radius: 2px;
            background: rgba(0,255,136,0.06);
            flex-shrink: 0;
        }

        /* ── Scan line effect ── */
        .panel:hover::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: var(--cyan);
            opacity: 0.2;
            animation: scan 2s linear infinite;
            pointer-events: none;
        }

        @keyframes scan {
            0%   { top: 0; }
            100% { top: 100%; }
        }

        .new-row {
            animation: rowhl 2s ease forwards;
        }

        @keyframes rowhl {
            0%   { background: rgba(0,212,255,0.1); }
            100% { background: transparent; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }
    </style>
</head>
<body>

<nav>
    <div class="nav-brand">
        <span class="bolt">⚡</span> ARCONAGENT
    </div>
    <div class="nav-right">
        <span class="nav-tag">AI AGENT PAYMENT INFRASTRUCTURE</span>
        <div class="live-indicator">
            <span class="live-dot"></span> LIVE
        </div>
    </div>
</nav>

<div class="main">

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card cyan">
            <div class="stat-label">Total Payments</div>
            <div class="stat-value" id="total-payments">{{ $payments->count() }}</div>
            <div class="stat-corner">TX</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Confirmed</div>
            <div class="stat-value" id="confirmed-payments">{{ $payments->where('status','confirmed')->count() }}</div>
            <div class="stat-corner">OK</div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-label">Total Volume (USDC)</div>
            <div class="stat-value" id="total-volume">{{ number_format($payments->sum('amount'), 2) }}</div>
            <div class="stat-corner">$</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-label">Active Agents</div>
            <div class="stat-value">{{ $agents->count() }}</div>
            <div class="stat-corner">AI</div>
        </div>
    </div>

    <div class="content-grid">

        {{-- Payments Panel --}}
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">// Live Payment Feed</span>
                <span class="panel-meta">BASE-SEPOLIA · USDC</span>
            </div>
            <div style="overflow-x:auto;">
                <table id="payments-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="payments-body">
                        @foreach($payments as $payment)
                        <tr>
                            <td class="id-cell">{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td><span class="agent-name">{{ $payment->senderAgent->name ?? 'N/A' }}</span></td>
                            <td><span class="agent-name">{{ $payment->receiverAgent->name ?? 'N/A' }}</span></td>
                            <td><span class="amount-cell">{{ number_format($payment->amount, 6) }}</span></td>
                            <td><span class="badge badge-{{ $payment->status }}">{{ strtoupper($payment->status) }}</span></td>
                            <td class="time-cell">{{ $payment->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Agents Panel --}}
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">// Registered Agents</span>
                <span class="panel-meta">{{ $agents->count() }} ONLINE</span>
            </div>
            @foreach($agents as $agent)
            <div class="agent-card">
                <div class="agent-icon">🤖</div>
                <div class="agent-info">
                    <div class="agent-info-name">{{ $agent->name }}</div>
                    <div class="agent-info-addr">{{ $agent->circle_wallet_address }}</div>
                </div>
                <span class="agent-status">{{ strtoupper($agent->status) }}</span>
            </div>
            @endforeach
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
<script>
    const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
    });

    const channel = pusher.subscribe('payments');

    channel.bind('payment.updated', function(data) {
        const tbody = document.getElementById('payments-body');
        const row = document.createElement('tr');
        row.classList.add('new-row');
        row.innerHTML = `
            <td class="id-cell">${String(data.id).padStart(4,'0')}</td>
            <td><span class="agent-name">${data.sender_agent_id}</span></td>
            <td><span class="agent-name">${data.receiver_agent_id}</span></td>
            <td><span class="amount-cell">${parseFloat(data.amount).toFixed(6)}</span></td>
            <td><span class="badge badge-${data.status}">${data.status.toUpperCase()}</span></td>
            <td class="time-cell">just now</td>
        `;
        tbody.insertBefore(row, tbody.firstChild);

        const total = document.getElementById('total-payments');
        total.textContent = parseInt(total.textContent) + 1;
    });
</script>
</body>
</html>