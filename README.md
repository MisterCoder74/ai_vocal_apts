# ImmobiVoice AI — Vocal Real Estate Manager

> **Fase attuale:** Fase 3 completata ✅

Sistema di gestione per agenzie immobiliari a controllo vocale, basato su Web Speech API + OpenAI GPT-4o-mini.

---

## Design System

| Token | Valore |
|---|---|
| Colore primario | `#CC0000` (rosso) |
| Colore scuro | `#1A1A1A` (quasi nero) |
| Background | `#F5F5F7` (bianco neutro) |
| Font | Segoe UI, Roboto |

---

## Struttura file

| File | Ruolo |
|---|---|
| `appointment_api.php` | API CRUD appuntamenti (`?action=list\|edit\|delete`) |
| `extract_appointment.php` | Estrazione AI appuntamento da trascrizione vocale |
| `clients_api.php` | API CRUD clienti (`?action=list\|edit\|delete`) |
| `extract_client.php` | Estrazione AI cliente da trascrizione vocale |
| `create_apts.html` | Creazione appuntamento via voce |
| `manage_apts.html` | Lista, modifica, cancellazione appuntamenti |
| `create_client.html` | Creazione cliente via voce |
| `manage_clients.html` | Lista, modifica, cancellazione clienti |
| `calendar.html` | Vista calendario appuntamenti |
| `dashboard.html` | Hub centrale (navigazione + preview recenti) |
| `transcriptions.json` | Storage appuntamenti (flat-file JSON) |
| `clients.json` | Storage clienti (flat-file JSON) |

---

## Roadmap

- [x] **Fase 1** — API unificata appuntamenti (`appointment_api.php`)
- [x] **Fase 2** — Rebranding ImmobiVoice AI (tema bianco/rosso/nero)
- [x] **Fase 3** — Modulo Clienti (voce + CRUD)
- [ ] **Fase 4** — Modulo Proprietà in vendita (voce + CRUD)

---

## Stack tecnico

- Frontend: Vanilla HTML / CSS / JS
- Backend: PHP (flat-file JSON, nessun DB)
- Riconoscimento vocale: Web Speech API (browser-nativo)
- AI extraction: OpenAI GPT-4o-mini
- Lingua default riconoscimento: `it-IT`

---

## Note

- Tutti i path sono **relativi** (no leading slash)
- Solo i file **modificati o aggiunti** vengono consegnati per fase
- README aggiornato ad ogni fase
