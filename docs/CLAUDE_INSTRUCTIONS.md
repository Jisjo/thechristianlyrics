# CLAUDE_INSTRUCTIONS.md
**Project:** thechristianlyrics.com — Marthoma Kristheeya Keerthanangal SEO
**Last Updated:** 09 Jul 2026 (Session 13)

---

## 0. SOURCE OF TRUTH — WHERE TO CHECK / UPDATE / HOW
*(Read this first. GitHub is the canonical home — not the Project. Two fixed-name docs, no dated copies.)*

**WHERE TO CHECK (every session start):**
1. `docs/CLAUDE_INSTRUCTIONS.md` — this file (rules).
2. `docs/thechristianlyrics_Complete_Reference.md` — **single canonical reference doc** (current state; header shows date + session).
3. `scripts/mkk.php` — the current audit + fix tool.
4. `scripts/mkk-s12-data.md` — hardcoded FK/meta data for LIST A+B.
- All in GitHub repo `Jisjo/thechristianlyrics` (branch `main`). Fetch via GitHub MCP `get_file_contents`. Do **not** rely on Project copies — stale.

**WHERE TO UPDATE:**
- Everything lives in GitHub; Claude reads **and writes** directly (`push_files`).
- The Project (claude.ai) is read-only to Claude — backup only, or retire it.

**HOW TO UPDATE (single-file model — no dated copies):**
- Reference doc: update throughout the session; at end (or when the chat gets long/compressed), **overwrite `docs/thechristianlyrics_Complete_Reference.md` in place** — same section structure, **update only what changed — never delete existing content** — and push. Put date + session in the **file header** and the **commit message**. **Never create dated filenames** — Git history is the version trail. No need to ask Jisjo to save.
- This instructions file: when a new rule/learning/"never do" appears, overwrite it in place and push.
- **For current numbers, defer to the reference doc** — this file holds rules, not live counts.

**Live-data rule:** trust the **live site audit** (`mkk.php`) over any doc's pending list. Docs go stale; the S12 pending list was materially wrong (proven S13).

---

## WORKFLOW RULES

### #1 — Every New Conversation
Read `docs/CLAUDE_INSTRUCTIONS.md` and `docs/thechristianlyrics_Complete_Reference.md` **from GitHub** first (also `scripts/mkk.php` + `scripts/mkk-s12-data.md`). Summarise current state in 3–4 lines. Ask what Jisjo wants to work on. Never ask him to repeat what is already in the reference document.

### #2 — Living Reference Doc
Update the reference doc automatically throughout the conversation as tasks complete and decisions are made — no need to ask. Before the conversation ends, gets long/compressed, or at EOD — **overwrite `docs/thechristianlyrics_Complete_Reference.md` in place and push it to GitHub** (Claude writes it directly). Present the file to Jisjo as well.

### #3 — When Generating/Updating the Reference Doc
Read `docs/CLAUDE_INSTRUCTIONS.md` and the reference doc (GitHub) first. Follow the same section structure. Update only what changed — never remove existing content. **Single canonical file `docs/thechristianlyrics_Complete_Reference.md` — overwrite in place; put date + session in the header and commit message; never create dated copies.** Push to GitHub.

### #4 — Living Document
When new working style, technical learnings, or "never do" items are discovered — overwrite `docs/CLAUDE_INSTRUCTIONS.md` in place and push to GitHub.

### #5 — Confirmation before action (see full rule below)
Always show the plan first and wait for Jisjo's explicit agreement before executing anything (creates, updates, deletes, tag/slug changes, bulk pushes). Read-only audits may run without confirmation.

---

## TOOLING — `mkk.php`
Single server-side file (GitHub `scripts/mkk.php`). Place in WP root, open in browser:
- `/mkk.php` — **audit, read-only** (issues-only report + summary). Safe anytime, writes nothing.
- `/mkk.php?fk=1` — full FK / meta / title dump per post (verify FK+meta — NOT REST-readable).
- `/mkk.php?mode=fix` — fix **dry run** (shows changes, writes nothing).
- `/mkk.php?mode=fix&live=1` — **apply** fixes.
- Fixes hardcoded per post, ASCII tokens only (no Malayalam handling → no encoding risk). Extend the `$FIXES` map for new mechanical fixes. Default is read-only; writes require `mode=fix&live=1`. Delete from WP root after a live run.

**Bulk-fix principle:** fix one *issue* across many posts with one hardcoded server-side script — never loop MCP fetch+push (~30KB/round-trip). MCP per-post only for judgement edits.

---

## NEW POST CREATION RULES
*(Marthoma Kristheeya Keerthanangal posts only)*

- Check for existing post first — search "NNN." before creating.
- Title: `[number]. [Malayalam] - [Word1 Word2 Word3/4]` — no "Lyrics" suffix.
- **Table rows — exactly 4, in this order:** Scripture (BibleGateway) · Song's Chords (Chordify, "Guitar, Ukulele, Piano, Mandolin") · Lyricist (linked to WP tag) · Category (Marthoma Kristheeya Keerthanangal, linked, no `?swcfpc=1`). ❌ No Album row · ❌ no other category rows.
- Structure: Table → TOC → Listen H2 → YouTube → Intro para → Malayalam H2+lyrics → Manglish H2+lyrics.
- Tags: alphabetical tag + lyricist tag — always both together.
- `comment_status: open`, `ping_status: closed`.
- Focus keyword: first 3–4 Manglish opening words — set in `create_post`.
- Meta desc ≤160 chars — include focus keyword + soft CTA word (e.g. "Read").
- Clean syllable-break hyphens from lyrics (Malayalam and Manglish). No placeholder text ever.

---

## QC CHECKLIST
**Table** — 4 rows: Scripture | Song's Chords | Lyricist | Category · ❌ Album / wrong Category rows · no `?swcfpc=1` · Lyricist linked · Chords verified · no bottom lyricist credit.
**Structure** — TOC block present (no `<h2>Table of Contents</h2>` inside) · TOC links match H2 IDs (`#listen-song`,`#m`,`#m-1`) · Listen H2 correct text + anchor (NOT generic "Here") · YouTube correct song, Gutenberg embed · Intro 2 sentences, verse linked, FK in first 10%.
**Lyrics** — all verses present · no chillu encoding errors · no syllable-break hyphens · verse numbers · no bottom credit · `has-text-align-center` · Manglish count matches Malayalam.
**SEO** — meta ≤160 + FK + soft CTA · FK set · RankMath ≥80 · flag improvements.
**Post Settings** — slug no number prefix / no "Lyrics" · tags alphabetical + lyricist · category 28 · `comment_status: open`, `ping_status: closed`.
**Featured Image** — set (`featured_media` ≠ 0) · alt = focus keyword (first 3 words).

---

## YOUTUBE SEARCH RULE
1. **[Manglish song title] Maramon Convention** → 2. **[first Manglish word] Maramon Convention** → 3. ask Jisjo.
- Priority: DSMC RELEASE/MEDIA > *Maramon Convention – Topic* > other. `youtube_search` MCP reaches Topic-channel videos web search can't. St. James MTC Choir London channel is dead — never use.

---

## CHORDS RULE
1. chordify.net exact song → link directly. 2. Not found → `https://chordify.net/search/[Song+Title]`. 3. External chord URLs also acceptable.

---

## RANKMATH META
- `rank_math_description` → API/PHP ✅ (≤160). NOT in REST batch reads.
- `rank_math_focus_keyword` → API/PHP ✅. NOT in REST batch reads. **≤4-char rule keys on the 3rd word.**
- `rank_math_title` → ❌ do NOT set (global `%title% Lyrics` template). **Unset is normal — not an issue; exclude from pending counts.**
- Verify FK/meta via RankMath editor or `mkk.php?fk=1` (not REST-readable).
- **Redirects:** RankMath Redirections NOT exposed via WP MCP — RankMath UI (301) or PHP; Jisjo handles.
- Alt text IS writable via REST (`claudeus_wp_media__update` → `alt_text`).

---

## GUTENBERG / TOC RULES
- TOC block: Claude sends it in content; Jisjo clicks "Recover"/"Attempt Recovery" after any content change, then Save.
- NEVER put `<h2>Table of Contents</h2>` inside the TOC block HTML.
- H2 IDs `listen-song`,`m`,`m-1` (new posts); older may use longer IDs. TOC-block JSON `link`, nav `href`, `<h2>` id must all match.
- TOC block JSON key is `headings` (not `headers`).
- **Malayalam Unicode:** never via shell heredoc (corruption). Use MCP `update_post` (native Unicode) or Python file ops.

---

## FEATURED IMAGE WORKFLOW
- Template `/mnt/user-data/uploads/Add_a_heading__4_-converted-from-png.webp` (regenerate locally; 403 on direct fetch). Always the project template, never custom.
- Fonts: ML `NotoSansMalayalam-Bold.ttf` 34–36 · EN `FreeSansBold.ttf` 22 · site `FreeSans.ttf` 16.
- Output `/mnt/user-data/outputs/[Song-Name].webp`. Jisjo uploads manually. Claude sets `alt_text` + `featured_media` via API. Alt = FK (first 3 words).

---

## RESPONSE FORMAT RULES
- Always include post URL in every update summary. After each post confirm RankMath fields set. Remind Jisjo of manual actions (TOC recovery, image upload). Reference posts by song name AND number, never post ID alone. Never repeat what's already confirmed in the reference doc.

---

## CONFIRMATION RULE
**Always show the plan first and wait for Jisjo's explicit agreement before executing anything.**
- Clarifying question → answer first, then wait. Don't answer a question AND execute in the same response.
- Stop and ask if unclear. Proceed only after explicit "yes/ok/go".
- **Destructive/bulk writes (delete, trash, mass update, tag/slug change):** name exact targets + effect, then get explicit go.
- Read-only audits (`mkk.php` default) may run without confirmation.

---

## KEY MISTAKES TO NEVER REPEAT
1. ❌ `<h2>Table of Contents</h2>` inside TOC block HTML.
2. ❌ "Maramon Convention - Topic" as a *search query* (the Topic *channel* is a valid source once found).
3. ❌ Not including post URL in summaries.
4. ❌ Updating a post via API after Jisjo manually set RankMath fields.
5. ❌ Answering a clarifying question AND executing in the same response.
6. ❌ `?swcfpc=1` in any URL.
7. ❌ "Lyrics" in the English title.
8. ❌ Meta over 160 chars.
9. ❌ Syllable-break hyphens in lyrics.
10. ❌ Album row in new posts.
11. ❌ Setting `rank_math_title` — and don't flag "unset" as an issue.
12. ❌ Tags via API with only one tag — include ALL.
13. ❌ Related Posts block — not enabled.
14. ❌ **Confusing song number with post ID** ("125,160,163,83" = song numbers; post IDs 9502/10068/10075/3166). Resolve to post ID before API calls.
15. ❌ **Trusting a doc's pending list without a live audit** — audit via `mkk.php` first.
16. ❌ **Assuming the June-2026 post batch is clean** — Malayalam corruption (`ൿ` for chillu `ൻ`/`ന്ന`; `඙` for `ങ`). Fixed: 11762, 11767. To check: 11770, 11772, 11773.
17. ❌ Writing Malayalam via shell heredoc — use MCP `update_post` or Python file ops.
18. ❌ Hard-deleting posts — `delete_post` (no `force`) = recoverable Trash; never force-delete duplicates.
19. ❌ Deduping without a 301 redirect (RankMath UI) old-slug → keeper-slug.
20. ❌ Creating dated doc copies — overwrite the single canonical file; Git history is the trail.

---

## SITE INFO
- Site: https://thechristianlyrics.com · WP Admin: /wp-admin
- Category ID 28: Marthoma Kristheeya Keerthanangal
- WP MCP alias: `default_test`
- GitHub: `Jisjo/thechristianlyrics` (branch `main`) — canonical home; docs in `docs/`, scripts in `scripts/`
- Jisjo: jisjokbz.j@gmail.com
