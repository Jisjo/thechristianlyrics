# CLAUDE_INSTRUCTIONS.md
**Project:** thechristianlyrics.com — Marthoma Kristheeya Keerthanangal SEO
**Last Updated:** 09 Jul 2026 (Session 13)

---

## 0. SOURCE OF TRUTH — WHERE TO CHECK / UPDATE / HOW
*(Read this first. GitHub is the canonical home — not the Project. Two fixed-name docs, no dated copies.)*

**WHERE TO CHECK (every session start):**
1. `docs/CLAUDE_INSTRUCTIONS.md` — this file (rules + the per-post standard).
2. `docs/thechristianlyrics_Complete_Reference.md` — single canonical reference doc (current state).
3. `scripts/mkk.php` — audit + fix + live tracker tool.
4. `scripts/mkk-s12-data.md` — hardcoded FK/meta data for LIST A+B.
- All in GitHub repo `Jisjo/thechristianlyrics` (branch `main`). Do **not** rely on Project copies — stale.

**WHERE / HOW TO UPDATE (single-file model — no dated copies):**
- Overwrite the two canonical docs in place and push. Put date + session in the header and commit message. Never create dated filenames — Git history is the trail.
- **For current numbers, defer to the reference doc + live `mkk.php`;** this file holds rules, not live counts.

**Live-data rule:** trust the live site audit (`mkk.php`) over any doc's pending list.

---

## WORKFLOW RULES
1. **Every new chat:** read the two docs + `mkk.php` from GitHub; summarise state in 3–4 lines; resume. Never make Jisjo repeat what's in the reference doc.
2. **Living reference doc:** update it through the chat as tasks complete; before the chat ends/compresses, overwrite it in place and push; present it.
3. **Updating docs:** same section structure; update only what changed; never delete existing content; overwrite in place.
4. **Living rules:** new rule/learning/"never do" → overwrite this file and push.
5. **Confirmation:** show the plan and get an explicit "go" before any create/update/delete/tag/slug/bulk write. Read-only audits run without asking.

---

## TOOLING — `mkk.php`
Server-side file in WP root (GitHub `scripts/mkk.php`), key-guarded (`?key=mkk-7hq2p9x4`):
- `/mkk.php?key=K` — audit text (read-only) · `?mode=html` — live dashboard · `?mode=json` — JSON feed (Claude `web_fetch`, must be pasted into chat) · `?fk=1` — full FK/meta/title dump (**only reliable FK/meta check**) · `?mode=fix&live=1` — apply hardcoded fixes.
- Fixes touch ASCII tokens only (no Malayalam). **Bulk principle:** fix one issue across many posts with one PHP run — never MCP fetch+push loops (~30KB each). MCP per-post only for judgement/content edits.

---

## THE PER-POST STANDARD
*(ONE definition of a complete, correct post. Applies equally to creating a new post and to verifying/correcting an existing one. Every item is checked on every post — there is no separate "QC" vs "creation" list.)*

### A. Content accuracy *(per-post; visual/judgement)*
- **A1. Malayalam lyrics match the hymnal image** `/mnt/project/[song-number].png` — every verse, character-accurate. Claude compares and flags suspected typos; Jisjo confirms; Claude fixes via MCP.
- **A2. No encoding corruption** — no `ൿ` (U+0D7F) standing in for chillu `ൻ`/`ന്ന`; no `඙` (U+0D99) for `ങ` (June-2026 batch risk).
- **A3. Manglish** present and matches Malayalam line-by-line; phonetic (not literal); same word spelled consistently across posts.
- **A4. Verses** all present + numbered; verse paragraphs `has-text-align-center`; no syllable-break hyphens in either script; Manglish verse count = Malayalam verse count.

### B. Structure *(pure Gutenberg — never Elementor)*
Order: **Info table → TOC → Listen H2 → YouTube → Intro paragraph → Malayalam H2 → Manglish H2.**
- **B1. Info table = exactly 4 rows:** Scripture (BibleGateway link) · Song's Chords (Chordify link, text "Guitar, Ukulele, Piano, Mandolin") · Lyricist (linked to WP tag) · Category (Marthoma Kristheeya Keerthanangal, linked). ❌ no Album row · ❌ no extra category rows · ❌ no `?swcfpc=1`.
- **B2. TOC block** (rank-math) with ids `#m` / `#m-1` / `#listen-song`; nav `href` + block JSON `link` + `<h2>` id all match; **never** `<h2>Table of Contents</h2>` inside the block. (JSON key is `headings`.)
- **B3. Listen H2** = "Listen Song [Title]" — real text, never generic "Here".
- **B4. YouTube** = correct song, Gutenberg embed (youtu.be). Sourcing order below.
- **B5. Intro paragraph** — 2 sentences, focus keyword in the first ~10%, Scripture verse linked.
- **B6.** Malayalam H2 (id `m`) + Manglish H2 (id `m-1`).
- **B7. No junk markup** — no `swcfpc`, no stray `<meta http-equiv>`, no "Maglish" typo.
- **B8. No Elementor** — clear all four `_elementor_data` / `_elementor_edit_mode` / `_elementor_template_type` / `_elementor_page_settings` on every update; never add them on new posts.

### C. SEO metadata
- **C1. Focus keyword** = first 3 Manglish opening words of the song (4 if the 3rd word ≤4 chars). **Set via the RankMath editor or PHP `update_post_meta` — NOT via MCP** (`update_post` returns 200 but drops it). Verify via `mkk.php?fk=1`.
- **C2. Meta description** ≤160 chars (verify length every time). **LIVE template (canonical):**
  - with lyricist: `[Song Name] lyrics from Marthoma Kristheeya Keerthanangal by [Lyricist]. Read Malayalam & Manglish lyrics and listen online.`
  - without lyricist: `[Song Name] lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.`
  - `[Song Name]` = the Manglish title (Title Case). **No song number. Never abbreviate "MKK".** Rewrite any legacy/messy meta to this template.
- **C3. `rank_math_title`** — leave UNSET (global `%title% Lyrics` template). Not an issue.
- **C4. Featured image** set (`featured_media` ≠ 0) + alt text = focus keyword (first 3 words). Use the project template `templete.webp` (700×280). Jisjo uploads.
- **C5. Slug** — clean; no number prefix; no "Lyrics"; no stray chars.
- **C6.** `comment_status: open` (UGC/engagement SEO) · `ping_status: closed`.

### D. Tags
- **D1.** Both an alphabetical index tag AND the lyricist tag — never one without the other.
- **D2. Lyricist tag description** — 2–3 sentences (who, era, Marthoma connection, name in Malayalam + Manglish). Set once per lyricist; gives the tag archive its own searchable content.

### E. Schema *(automatic — no per-post work)*
- Site-wide Code Snippets `snippet-v2.php` emits a JSON-LD `@graph`: **MusicComposition + VideoObject**, language by category (cat 28 → `ml` + `ml-Latn`). It reads **featured image + lyricist tag + YouTube embed + category** — so schema self-corrects once B/C/D are right.
- RankMath global default post schema = **Article** (indexable base) — confirm once in Titles & Meta.
- Do NOT add per-post schema or a second schema plugin (avoid duplicate/conflicting markup).

### F. Fix routing (how each item gets done)
- **PHP bulk (`mkk.php` fix)** — meta (C2), TOC ids (B2), junk (B7), comment/ping (C6): mechanical, high volume.
- **MCP per-post** — lyrics/Manglish/corruption (A), intro/structure/table (B1/B3/B5), tags (D). Judgement/content.
- **Jisjo (manual)** — featured images (C4), TOC "Attempt Recovery" + Save after content changes, FK entry in RankMath editor (or Claude via PHP).
- **Automatic** — schema (E) once the rest is correct.

---

## YOUTUBE SOURCING
1. **[Manglish song title] Maramon Convention** → 2. **[first Manglish word] Maramon Convention** → 3. ask Jisjo.
- Priority: DSMC RELEASE/MEDIA > *Maramon Convention – Topic* > other. Use the `youtube_search` MCP (reaches Topic-channel videos web search can't). St. James MTC Choir London channel is dead — never use.

## CHORDS
chordify.net exact song → link directly; else `https://chordify.net/search/[Song+Title]`; external chord URLs also fine. Link text always "Guitar, Ukulele, Piano, Mandolin".

## RANKMATH MECHANICS
- `rank_math_description` — writable via PHP/MCP; NOT returned in REST reads. Verify via `mkk.php?fk=1`.
- `rank_math_focus_keyword` — **NOT writable via MCP** (returns 200, drops silently); use RankMath editor or PHP `update_post_meta`. NOT readable via REST — verify via `mkk.php?fk=1`.
- `rank_math_title` — leave unset (template default).
- **Redirects** — RankMath Redirections NOT exposed via WP MCP; RankMath UI (301) or PHP; Jisjo handles.
- Alt text IS writable via REST (`claudeus_wp_media__update` → `alt_text`).

## GUTENBERG / TOC
- Claude sends the TOC block in content; **Jisjo clicks "Attempt Recovery" + Save** after any content change.
- **Malayalam Unicode:** never via shell heredoc (corruption). Use MCP `update_post` (native Unicode) or Python file ops.

## FEATURED IMAGE
- Template `/mnt/user-data/uploads/…webp` (regenerate locally each session; 403 on direct fetch). Always the project template, never custom. Fonts: ML `NotoSansMalayalam-Bold.ttf` 34–36 · EN `FreeSansBold.ttf` 22 · site `FreeSans.ttf` 16. Output to `/mnt/user-data/outputs/`. Jisjo uploads; Claude sets `alt_text` + `featured_media` via API.

## RESPONSE FORMAT
- Include the post URL in every update summary. Reference posts by song name AND number, never post ID alone. Remind Jisjo of manual actions (TOC recovery, image upload). Don't repeat what's already in the reference doc.

## CONFIRMATION RULE
Show the plan first; wait for explicit "go" before any create/update/delete/tag/slug/bulk write. Answer a clarifying question first, then wait — don't answer AND execute in one turn. Name exact targets + effect for destructive/bulk writes. Read-only audits run without asking.

---

## KEY MISTAKES TO NEVER REPEAT
1. ❌ `<h2>Table of Contents</h2>` inside the TOC block.
2. ❌ "Maramon Convention - Topic" as a *search query* (the Topic *channel* is fine once found).
3. ❌ Omitting the post URL in summaries.
4. ❌ Updating a post via API after Jisjo set RankMath fields in the editor (reload first).
5. ❌ Answering a clarifying question AND executing in the same turn.
6. ❌ `?swcfpc=1` in any URL.
7. ❌ "Lyrics" in the English title.
8. ❌ Meta over 160 chars, or with a song number, or abbreviating "MKK".
9. ❌ Syllable-break hyphens in lyrics.
10. ❌ Album row / extra category rows in the table.
11. ❌ Setting `rank_math_title` — and don't flag "unset" as an issue.
12. ❌ Tags via API with only one tag — include ALL (alphabetical + lyricist).
13. ❌ Related Posts block — not enabled.
14. ❌ **Confusing song number with post ID.** Resolve to the post ID before API calls.
15. ❌ **Trusting a doc's pending list without a live audit** — audit via `mkk.php`.
16. ❌ **Assuming the June-2026 batch is clean** — Malayalam corruption (`ൿ` for chillu; `඙` for `ങ`). To check: 11770, 11772, 11773.
17. ❌ Writing Malayalam via shell heredoc — use MCP `update_post` or Python.
18. ❌ Hard-deleting posts — `delete_post` (no `force`) = recoverable Trash.
19. ❌ Deduping without a 301 redirect old-slug → keeper-slug.
20. ❌ Creating dated doc copies — overwrite the single canonical file.
21. ❌ **Setting `rank_math_focus_keyword` via MCP** — it silently fails; use RankMath editor or PHP, then verify via `mkk.php?fk=1`.
22. ❌ Reporting a write "done" from a 200 response — verify in the authoritative source (`mkk.php?fk=1` for FK/meta) first.
23. ❌ Adding per-post schema or a 2nd schema plugin — schema is automatic via `snippet-v2.php`; keep it single.

---

## SITE INFO
- Site: https://thechristianlyrics.com · WP Admin: /wp-admin
- Category ID 28: Marthoma Kristheeya Keerthanangal
- WP MCP alias: `default_test`
- Schema: Code Snippets `snippet-v2.php` (MusicComposition + VideoObject) + RankMath Article default.
- GitHub: `Jisjo/thechristianlyrics` (branch `main`) — canonical home; docs in `docs/`, scripts in `scripts/`.
- Jisjo: jisjokbz.j@gmail.com
