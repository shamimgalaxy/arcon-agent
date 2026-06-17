# ArcOnAgent 

> **Autonomous AI Agent Payment Infrastructure on Circle Wallets**

ArcOnAgent is an open REST API and Laravel-based infrastructure layer that enables AI agents to autonomously trigger, schedule, and execute USDC payments via Circle Smart Contract Wallets — across 19+ blockchain networks.

🌐 **Live Demo:** https://arcon-agent-production.up.railway.app/
📹 **Demo Video:** https://www.loom.com/share/ec72cff53d3543a99025efacb2058129

---

## 🧠 The Problem

AI agents can reason and act — but they **cannot move money autonomously**.

Today's payment APIs are built for humans: OAuth flows, browser redirects, and manual approvals. When an AI agent needs to pay for a service, reward a user, or settle a transaction — it hits a wall.

**ArcOnAgent removes that wall.**

---

## ✅ The Solution

Every AI agent gets its own **Circle Smart Contract Wallet** (EOA/SCA auto-selected). Agents can initiate, schedule, and complete USDC payments autonomously — with every transaction logged on-chain.

---

## 🚀 Features

### ✅ Milestone 1 — Core Agent Wallet Infrastructure
- AI agent registration with automatic Circle SCA wallet provisioning
- USDC payment initiation between agents via Circle Wallets API
- Circle webhook listener for real-time payment status updates
- Real-time dashboard showing live payment feed and agent list
- MySQL schema: `agents`, `payments` tables with full relational structure

### ✅ Milestone 2 — Spending Policy Engine + Audit Logs
- `PolicyService`: per-agent spending limits, daily caps, recipient allowlists
- Multi-agent payment coordinator: single API call dispatches payments to N agents
- Immutable audit log: every financial action recorded with Circle TX ID
- `GET /api/v1/audit-logs` endpoint with filtering
- Policy enforcement: blocks over-limit, rate-limited, and blacklisted transfers

### ✅ Milestone 3 — Public API + n8n + CCTP Cross-Chain
- Sanctum-authenticated REST API (V1): agents, payments, audit logs
- n8n webhook endpoint (`POST /api/n8n/payment`) for no-code agent payment automation
- CCTP cross-chain transfer: agents move USDC from Base → Ethereum/Arbitrum via Circle's burn-and-mint protocol
- Cross-chain transfer UI, coordination history, multi-chain agent stats
- Arc mainnet deployment ready

### 🔄 Milestone 4 — Nanopayments (In Progress)
- Gas-free micro-transactions as low as 0.000001 USDC (1 micro-USDC)
- Dedicated nanopayments engine with `amount_micro` precision
- Streaming payments support for continuous agent-to-agent value flow
- Full audit trail under `nanopayment` source

---

## 🔗 Circle Products Integrated

| Product | Status |
|---|---|
| Circle Programmable Wallets API | ✅ Live |
| Circle Smart Contract Wallets (EOA + SCA) | ✅ Live |
| USDC Transfers on BASE-SEPOLIA | ✅ Live |
| Circle Webhooks | ✅ Live |
| CCTP Cross-Chain Transfer Protocol | ✅ Live |
| Circle Paymaster (Gas abstraction) | ✅ Live |

---

## 📊 On-Chain Proof

| Item | Detail |
|---|---|
| Network | BASE-SEPOLIA |
| Agent Wallets Provisioned | 5 (mix of EOA + SCA) |
| Transactions Recorded | 18 |
| Successful On-Chain Transfer | `ff4c2d09-16e9-5b42-9906-7e2aae48b055` |
| Autonomous Trigger Configured | balance_threshold → auto-pay 0.1 USDC |
| Policy Violations Blocked | ✅ (15 USDC blocked, limit: 10 USDC) |

### Agent Smart Contract Addresses (BASE-SEPOLIA)
```
Agent Alpha:    0xa88ce63dd3521b7b6e06bc73ffc18cac5bf04f50
Agent Beta:     0x33e9687216c0b495db6887c2ded735db0ee34eee
Agent SCA One:  0xb8bf63eff0e6a6a081e3566f87d1729aff627d53
Agent SCA One v2: 0xfdec8c226ac4b648f50b9c2bdb58de247b6b497b
Agent SCA Two:  0xee4429031a38c4c3f488ed5345fa99b3e5b6350f
```

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.2, Laravel 12
- **Blockchain:** Circle Programmable Wallets API, USDC, CCTP
- **Database:** MySQL
- **Real-time:** Pusher WebSockets
- **Auth:** Laravel Sanctum
- **Queue:** Laravel Jobs & Scheduled Commands
- **Deployment:** Railway
- **Frontend:** Blade, Tailwind CSS, Alpine.js

---

## 📡 API Endpoints

```
POST   /api/v1/agents              # Register a new AI agent + provision Circle wallet
GET    /api/v1/agents              # List all agents with wallet addresses
POST   /api/v1/payments            # Initiate USDC payment between agents
GET    /api/v1/payments            # List all payments with Circle TX IDs
GET    /api/v1/audit-logs          # Full immutable audit trail with filtering
POST   /api/v1/agents/{id}/triggers # Configure autonomous payment trigger
GET    /api/blockchains            # List all 19 supported blockchain networks
POST   /api/n8n/payment            # n8n webhook for no-code automation
```

---

## ⚙️ Installation

```bash
# Clone the repository
git clone https://github.com/shamimgalaxy/arcon-agent.git
cd arcon-agent

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Add your Circle API credentials to .env
# CIRCLE_API_KEY=your_circle_api_key
# CIRCLE_ENTITY_SECRET=your_entity_secret

# Run migrations
php artisan migrate

# Start the application
php artisan serve
npm run dev
```

---

## 🌐 Supported Networks (19)

ARC-TESTNET (default), BASE-SEPOLIA, Ethereum, Polygon, Solana, Base, Avalanche, Arbitrum, Optimism, and 10+ more via Circle's multi-chain infrastructure.

---

## 🗺️ Roadmap

- [x] Circle SCA wallet provisioning per agent
- [x] USDC agent-to-agent payments
- [x] Spending policy engine
- [x] Immutable audit logs
- [x] CCTP cross-chain transfers
- [x] n8n webhook integration
- [x] Nanopayments engine
- [ ] Autonomous trigger engine (ProcessAgentTriggers job)
- [ ] OpenAPI/Swagger documentation
- [ ] n8n community node (marketplace)
- [ ] Base Mainnet deployment
- [ ] Public developer portal

---

## 👨‍💻 Builder

**Shamim Ahmed** — Full Stack Laravel Developer, Dhaka, Bangladesh

- 🌐 [arcon-agent-production.up.railway.app](https://arcon-agent-production.up.railway.app/)
- 💼 [linkedin.com/in/shamimgalaxy](https://linkedin.com/in/shamimgalaxy)
- 🐙 [github.com/shamimgalaxy](https://github.com/shamimgalaxy)
- 📧 shamimgalaxy@gmail.com

---

## 📄 License

MIT License — open source and free to use.

---

> Built with ❤️ on Circle's infrastructure. Arc-first by design.
