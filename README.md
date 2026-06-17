# ImmobiVoice AI — Vocal Real Estate Manager

> **Fase attuale:** Fase 2 completata ✅

Sistema di gestione per agenzie immobiliari a controllo vocale, basato su Web Speech API + OpenAI GPT-4o-mini.

---

## Design System

| Token | Valore |
|---|---|
| Colore primario | `#CC0000` (rosso) |
| Colore scuro | `#1A1A1A` (quasi nero) |
| Background | `#F5F5F7` (bianco neutro) |
| Accent hover | `#FFF5F5` |
| Font | Segoe UI, Roboto |

---

## Struttura attuale

| File | Ruolo |
|---|---|
| `appointment_api.php` | API unificata CRUD appuntamenti (`?action=list\|edit\|delete`) |
| `extract_appointment.php` | Estrazione AI da trascrizione vocale (crea appuntamento) |
| `create_apts.html` | Creazione appuntamento via voce |
| `manage_apts.html` | Lista, modifica, cancellazione appuntamenti |
| `calendar.html` | Vista calendario appuntamenti |
| `dashboard.html` | Hub di navigazione centrale |
| `transcriptions.json` | Storage appuntamenti (flat-file JSON) |

---

## Roadmap

- [x] **Fase 1** — API unificata appuntamenti (`appointment_api.php`)
- [x] **Fase 2** — Rebranding ImmobiVoice AI (tema bianco/rosso/nero su tutte le pagine)
- [ ] **Fase 3** — Modulo Clienti (voce + CRUD)
- [ ] **Fase 4** — Modulo Proprietà in vendita (voce + CRUD)

---

## Stack tecnico

- Frontend: Vanilla HTML / CSS / JS
- Backend: PHP (flat-file JSON, nessun DB)
- Riconoscimento vocale: Web Speech API (browser-nativo)
- AI extraction: OpenAI GPT-4o-mini
- Lingua default riconoscimento: `it-IT`

---

## Note di sviluppo

- Tutti i path sono **relativi** (no leading slash)
- Solo i file **modificati o aggiunti** vengono consegnati per fase
- README aggiornato ad ogni fase
