# ImmobiVoice AI — Vocal Real Estate Manager

> **Fase attuale:** Fase 4 completata ✅

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
| `voice_utils.js` | Normalizzatore @ multilingua (client-side, shared) |
| `appointment_api.php` | API CRUD appuntamenti (`?action=list\|edit\|delete`) |
| `extract_appointment.php` | Estrazione AI appuntamento da trascrizione vocale |
| `clients_api.php` | API CRUD clienti (`?action=list\|edit\|delete`) |
| `extract_client.php` | Estrazione AI cliente — prompt multilingua per email |
| `properties_api.php` | API CRUD proprietà (`?action=list\|edit\|delete`) |
| `extract_property.php` | Estrazione AI proprietà da trascrizione vocale |
| `create_apts.html` | Creazione appuntamento via voce |
| `manage_apts.html` | Lista, modifica, cancellazione appuntamenti |
| `create_client.html` | Creazione cliente via voce (voice_utils.js integrato) |
| `manage_clients.html` | Lista, modifica, cancellazione clienti |
| `create_property.html` | Creazione proprietà via voce |
| `manage_properties.html` | Lista, modifica, cancellazione proprietà |
| `calendar.html` | Vista calendario appuntamenti |
| `dashboard.html` | Hub centrale — 7 card + 3 mini-tabelle |
| `transcriptions.json` | Storage appuntamenti (flat-file JSON) |
| `clients.json` | Storage clienti (flat-file JSON) |
| `properties.json` | Storage proprietà (flat-file JSON) |

---

## Fix email multilingua (Fase 4)

**Doppio livello di normalizzazione** per gestire la @ pronunciata in lingue diverse:

### Layer 1 — Client-side (`voice_utils.js`)
Sostituisce i pattern **non ambigui** prima che la trascrizione venga inviata a GPT:
- `chiocciola` → `@` (it)
- `arroba` → `@` (es/pt)
- `arobase` → `@` (fr)
- `klammeraffe` → `@` (de)
- `at sign` → `@` (en)

### Layer 2 — GPT prompt (`extract_client.php`)
Il prompt istruisce GPT a riconoscere i pattern ambigui usando il **contesto**:
- `punto / point / punkt / dot` → `.` (quando nel contesto di un'email)
- `at / et` → `@` (quando nel contesto di un'email)

---

## Esempio voce corretta — Proprietà

```
"Appartamento via Roma 15 Milano, disponibile, 280.000 euro, 80 metri quadri, bilocale luminoso con terrazzo."
```

Campi estratti: `indirizzo · città · prezzo · tipo · stato · superficie · descrizione`
Tipi supportati: `appartamento · villa · negozio · ufficio · garage · terreno · altro`
Stati supportati: `disponibile · venduto · in trattativa · in affitto`

---

## File da caricare sul server (Fase 4)

Nuovi/modificati rispetto alla Fase 3:
- `voice_utils.js` (NUOVO)
- `extract_client.php` (AGGIORNATO — prompt multilingua)
- `create_client.html` (AGGIORNATO — usa voice_utils.js)
- `extract_property.php` (NUOVO)
- `properties_api.php` (NUOVO)
- `create_property.html` (NUOVO)
- `manage_properties.html` (NUOVO)
- `dashboard.html` (AGGIORNATO — 7 card + 3 tabelle)

Creare `properties.json` vuoto (`[]`) nella root del server.

---

## Roadmap

- [x] **Fase 1** — API unificata appuntamenti
- [x] **Fase 2** — Rebranding ImmobiVoice AI
- [x] **Fase 3** — Modulo Clienti (voce + CRUD)
- [x] **Fase 4** — Modulo Proprietà + fix email multilingua
- [ ] **Fase 5** — TBD (es: autenticazione, ricerca/filtri, export CSV)

---

## Stack tecnico

- Frontend: Vanilla HTML/CSS/JS
- Backend: PHP (flat-file JSON, nessun DB)
- Riconoscimento vocale: Web Speech API (browser-nativo)
- AI extraction: OpenAI GPT-4o-mini
- Lingua default riconoscimento: `it-IT`