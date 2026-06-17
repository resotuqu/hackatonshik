# Organizer UX — Phase 4 backlog (epics)

These items are intentionally out of scope for the current implementation track; treat each as a separate epic with its own discovery, data model, and QA.

## Partially implemented (baseline in main track)

| Epic | What exists today | Still open |
|------|-------------------|------------|
| **A** | `BuildOrganizerHackatonAnalytics` — 14-day application chart, conversion rate on organizer hub | Funnel, retention, export, cached aggregates |
| **B** | `ProcessHackatonFinishedAutomations` — auto certificates + results announcement on `FINISHED` | Per-organizer rules, material archive, template-driven config |
| **D** | `HackatonTemplate` model + presets in create wizard | Public gallery, versioning, localization |

## Epic A — Personal organizer analytics

- Time series of applications (by day/week), conversion from pending to accepted, funnel from view to application.
- Team retention: repeat participants, completion rate of submissions.
- Dashboard charts and export; consider a read-optimized store or cached aggregates.

**Baseline:** organizer hub shows `applicationsByDay` and `conversionRate` via `BuildOrganizerHackatonsHubData` → `BuildOrganizerHackatonAnalytics`.

## Epic B — Post-hackathon automations

- Jobs triggered on `HackatonStatus::FINISHED` (or timeline): auto-issue certificates, publish results announcements, archive materials.
- Configurable rules per organizer or per hackathon template.

**Baseline:** `ProcessHackatonFinishedAutomations` runs when status becomes `FINISHED` (`auto_issue_certificates`, `auto_publish_results_announcement` flags on hackaton).

## Epic C — Team recommendations and matching

- Suggest teams or mentors based on skills, history, or case affinity.
- Likely needs new tables, scoring, and privacy/consent review.

## Epic D — Hackathon templates gallery

- Presets such as «GameJam», «AI Hack», «Corporate» mapping default fields, documents, and timeline.
- Template catalog in DB or config-driven with versioning and localization.

**Baseline:** active templates loaded in hackaton create flow (`HackatonTemplate::active()`).

## Epic E — Advanced cases builder

- Full drag-and-drop field builder with live participant preview, conditional fields, and validation rules beyond current case field types.

---

Use `BuildOrganizerHackatonsHubData` and show-page Actions as integration points when scoping each epic so metrics and lifecycle stay consistent.
