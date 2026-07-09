# CLAUDE_INSTRUCTIONS.md
**Project:** thechristianlyrics.com — Marthoma Kristheeya Keerthanangal SEO
**Last Updated:** 09 Jul 2026 (Session 13)

---

## 0. SOURCE OF TRUTH — WHERE TO CHECK / UPDATE / HOW
*(Read this first. As of S13, GitHub is the canonical home — not the Project.)*

**WHERE TO CHECK (every session start):**
1. `CLAUDE_INSTRUCTIONS.md` — GitHub repo `Jisjo/thechristianlyrics`, path `docs/CLAUDE_INSTRUCTIONS.md` (this file).
2. **Latest reference doc** — GitHub `docs/thechristianlyrics_Complete_Reference_DDMMMYYYY_S##.md` (highest date / session number wins).
3. `scripts/mkk.php` — the current audit + fix tool.
4. `scripts/mkk-s12-data.md` — hardcoded FK/meta data for LIST A+B.
- Fetch via GitHub MCP `get_file_contents`. Do **not** rely on Project copies — they may be stale.

**WHERE TO UPDATE:**
- All state, scripts, and docs live in GitHub `Jisjo/thechristianlyrics` (branch `main`). Claude can read **and write** here directly.
- The Project (claude.ai) is read-only to Claude and needs manual re-upload — treat it as a backup only, or retire it.

**HOW TO UPDATE:**
- Reference doc: update throughout the session as tasks complete; at end (or when the chat gets long/compressed), generate a **new dated file** (`_DDMMMYYYY_S##.md`), keep the same section structure, **update only what changed — never delete existing content**, and push to GitHub `docs/` via `push_files`. No need to ask Jisjo to save it.
- This instructions file: when a new working rule, learning, or "never do" is found, update it and push to GitHub `docs/`.
- **For current status/numbers, always defer to the latest reference doc** — this file holds rules, not live counts.

**Live-data rule:** trust the **live site audit** (`mkk.php`) over any doc's pending list. Docs go stale; the S12 pending list was materially wrong (proven in S13).

---

## WORKFLOW RULES

### #1 — Every New Conversation
Read `CLAUDE_INSTRUCTIONS.md` and the latest `Complete_Reference_*.md` **from GitHub `docs/`** first (also `scripts/mkk.php` + `scripts/mkk-s12-data.md`). Summarise current state in 3–4 lines. Ask what Jisjo wants to work on. Never ask him to repeat what is already in the reference document.

### #2 — Living Reference Doc
Update the reference doc automatically throughout the conversation as tasks complete and decisions are made — no need to ask. Before the conversation ends, gets long/compressed, or at EOD — generate the new dated doc and **push it to GitHub `docs/`** (Claude writes it directly). Present the file to Jisjo as well.

### #3 — When Generating Reference Doc
Read `CLAUDE_INSTRUCTIONS.md` and the latest reference doc (GitHub) first. Follow same section structure. Update only what changed — never remove existing content. Always use today's date in the filename. Push to GitHub `docs/`.

### #4 — Living Document
When new working style, technical learnings, or "never do" items are discovered — update `CLAUDE_INSTRUCTIONS.md` and push the new version to GitHub `docs/`.

### #5 — Confirmation before action (see full rule below)
Always show the plan first and wait for Jisjo's explicit agreement before executing anything (creates, updates, deletes, tag/slug changes, bulk pushes). Read-only audits may run without confirmation.

---

## TOOLING — `mkk.php`
Single server-side file (GitHub `scripts/mkk.php`). Place in WP root, open in browser:
- `/mkk.php` — **audit, read-only** (issues-only report + summary). Safe anytime, writes nothing.
- `/mkk.php?fk=1` — full FK / meta / title dump per post (use to verify FK+meta, which are NOT REST-readable).
- `/mkk.php?mode=fix` — fix **dry run** (shows changes, writes nothing).
- `/mkk.php?mode=fix&live=1` — **apply** fixes.
- Fixes are hardcoded per post and touch ASCII tokens only (no Malayalam handling → no encoding risk). Extend the `$FIXES` map for new mechanical fixes.
- Default is always read-only; writes require `mode=fix&live=1`. Delete from WP root after a live run.

**Bulk-fix principle:** fix one *issue* across many posts with one hardcoded server-side script — never loop MCP fetch+push (each round-trip ~30KB context). Use MCP per-post only for judgement edits.

---

## NEW POST CREATION RULES
*(Marthoma Kristheeya Keerthanangal posts only)*

- Check for existing post first — search "NNN." before creating.
- Title: `[number]. [Malayalam] - [Word1 Word2 Word3/4]` — no "Lyrics" suffix.
- **Table rows — exactly 4, in this order:**
  1. Scripture → Bible verse, BibleGateway link
  2. Song's Chords → Chordify link, text: "Guitar, Ukulele, Piano, Mandolin"
  3. Lyricist → hyperlinked to WP tag (not plain text)
  4. Category → Marthoma Kristheeya Keerthanangal (linked, no `?swcfpc=1`)
  - ❌ No Album row · ❌ No other category rows (e.g., "പ്രഭാത കീർത്തനങ്ങൾ")
- Post structure: Table → TOC → Listen H2 → YouTube → Intro para → Malayalam H2+lyrics → Manglish H2+lyrics.
- Tags: alphabetical tag + lyricist tag — always both together, never one without the other.
- `comment_status: open`, `ping_status: closed`.
- Focus keyword: first 3–4 Manglish opening words — set in `create_post`.
- Meta desc ≤160 chars — include focus keyword + soft CTA word (e.g. "Read").
- Clean syllable-break hyphens from lyrics (both Malayalam and Manglish).
- No placeholder or internal text in post content ever.

---

## QC CHECKLIST
*Reverification that all creation rules are correctly in place. Also watch for old patterns in existing posts.*

**Table** — Exactly 4 rows: Scripture | Song's Chords | Lyricist | Category · ❌ remove Album / wrong Category rows · no `?swcfpc=1` in any URL · Lyricist linked to tag · Chords URL verified · no lyricist credit at bottom.

**Structure** — TOC block present (no `<h2>Table of Contents</h2>` inside it) · TOC anchor links match H2 IDs (`#listen-song`, `#m`, `#m-1`) · Listen H2 correct text + anchor (NOT generic "Here") · YouTube correct song, Gutenberg embed · Intro paragraph 2 sentences, verse linked, FK in first 10%.

**Lyrics** — all verses present · no chillu encoding errors · no syllable-break hyphens · verse numbers present · no bottom lyricist credit · `has-text-align-center` on verse paragraphs · Manglish count matches Malayalam.

**SEO** — meta desc set ≤160 + FK + soft CTA · FK set · RankMath score ≥80 · flag any SEO improvement to Jisjo.

**Post Settings** — slug: no number prefix, no "Lyrics" · tags: alphabetical + lyricist both · category 28 · `comment_status: open`, `ping_status: closed`.

**Featured Image** — set (`featured_media` ≠ 0) · alt text = focus keyword (first 3 words).

---

## YOUTUBE SEARCH RULE
1. Search: **[Manglish song title] Maramon Convention**
2. No result → **[first Manglish word] Maramon Convention**
3. Still not found → ask Jisjo.
- Priority when choosing: DSMC RELEASE/MEDIA > *Maramon Convention – Topic* > other. (The `youtube_search` MCP can reach Topic-channel videos that web search cannot.)
- St. James MTC Choir London channel is dead — never use.

---

## CHORDS RULE
1. Search chordify.net for the exact song → link directly (text: "Guitar, Ukulele, Piano, Mandolin").
2. Not found → Chordify search URL `https://chordify.net/search/[Song+Title]`.
3. External chord URLs from other sites also acceptable for SEO.

---

## RANKMATH META
- `rank_math_description` → set via API/PHP ✅ (≤160 chars). NOT exposed in REST batch reads.
- `rank_math_focus_keyword` → set via API/PHP ✅. NOT exposed in REST batch reads. **The ≤4-char rule keys on the 3rd word**, not the 2nd.
- `rank_math_title` → ❌ do NOT set — global `%title% Lyrics` template handles it. **Unset is normal (template default) — not an "issue"; exclude from pending counts.**
- **To verify FK/meta:** RankMath editor, or `mkk.php?fk=1` (read-only echo). Not readable via REST.
- **Redirects:** RankMath Redirections is **NOT exposed via the WP MCP** — set in RankMath UI (301) or via PHP. Jisjo handles.
- Alt text IS writable via REST (`claudeus_wp_media__update` → `alt_text`) — no PHP needed.

---

## GUTENBERG / TOC RULES
- TOC block: Claude sends it in content, Jisjo clicks "Recover"/"Attempt Recovery" in editor after any content change, then Save.
- NEVER put `<h2>Table of Contents</h2>` inside the TOC block HTML — causes double heading.
- H2 IDs must be `listen-song`, `m`, `m-1` (new posts); older posts may use longer IDs. TOC-block JSON `link`, nav `href`, and `<h2>` anchor/id must all match.
- TOC key in block JSON is `headings` (not `headers`).
- **Malayalam Unicode:** never write via shell heredoc (corruption). Use MCP `update_post` (handles Unicode natively) or Python file ops.

---

## FEATURED IMAGE WORKFLOW
- Template: `/mnt/user-data/uploads/Add_a_heading__4_-converted-from-png.webp` (regenerate locally each session — 403 on direct fetch). Always use the project template, never a custom background.
- Fonts: ML `/home/claude/NotoSansMalayalam-Bold.ttf` 34–36 · EN `FreeSansBold.ttf` 22 · site `FreeSans.ttf` 16.
- Output `/mnt/user-data/outputs/[Song-Name].webp`. Jisjo uploads manually (MCP upload times out).
- Claude detects media ID via `get_media` → sets `alt_text` + `featured_media` via API. Alt = focus keyword (first 3 words).

---

## RESPONSE FORMAT RULES
- Always include post URL in every update summary.
- After each post: confirm RankMath fields set (description + FK via API/PHP).
- Remind Jisjo of manual actions still needed (TOC recovery, image upload).
- Reference posts by song name AND number (e.g. "83. Papathin van vishathe"), never post ID alone.
- Never repeat what's already confirmed in the reference doc.

---

## CONFIRMATION RULE
**Always show the plan first and wait for Jisjo's explicit agreement before executing anything.**
- Clarifying question → answer it first, then wait for confirmation. Don't answer a question AND execute in the same response.
- Stop and ask if anything is unclear during execution. Proceed only after explicit "yes/ok/go".
- **Destructive/bulk writes (delete, trash, mass update, tag/slug change):** name the exact targets and effect, then get explicit go.
- Read-only audits (`mkk.php` default) may run without confirmation.

---

## KEY MISTAKES TO NEVER REPEAT
1. ❌ `<h2>Table of Contents</h2>` inside TOC block HTML — double heading.
2. ❌ Searching YouTube by "Maramon Convention - Topic" as a query — use Manglish title + "Maramon Convention" (but the Topic *channel* is a valid source once found).
3. ❌ Not including post URL in summaries.
4. ❌ Updating a post via API after Jisjo manually set RankMath fields in editor.
5. ❌ Answering a clarifying question AND executing in the same response.
6. ❌ `?swcfpc=1` in any URL.
7. ❌ Adding "Lyrics" to the English post title.
8. ❌ Meta descriptions over 160 characters.
9. ❌ Leaving syllable-break hyphens in lyrics.
10. ❌ Album row in new posts.
11. ❌ Setting `rank_math_title` (global template handles it) — and don't flag "unset" as an issue.
12. ❌ Setting tags via API with only one tag — include ALL (alphabetical + lyricist).
13. ❌ Adding a Related Posts block — RankMath Related Posts not enabled.
14. ❌ **Confusing song number with post ID.** Docs write `song (post_id)`; e.g. "125,160,163,83" were song numbers (post IDs 9502/10068/10075/3166). Resolve to the post ID before any API call.
15. ❌ **Trusting a doc's pending list without a live audit.** The S12 list was materially wrong. Audit via `mkk.php` first.
16. ❌ **Assuming the June-2026 post batch is clean.** It carries Malayalam corruption (`ൿ` U+0D7F in place of chillu `ൻ`/`ന്ന`; `඙` U+0D99 in place of `ങ`). Audit any post from that batch. Known-fixed: 11762, 11767. Still to check: 11770, 11772, 11773.
17. ❌ Writing Malayalam via shell heredoc — use MCP `update_post` or Python file ops.
18. ❌ Hard-deleting posts. `claudeus_wp_content__delete_post` (no `force`) moves to Trash (recoverable ~30 days) — good; never force-delete duplicates.
19. ❌ Deduping without a redirect. When trashing a duplicate, set a 301 (RankMath UI) old-slug → keeper-slug.

---

## SITE INFO
- Site: https://thechristianlyrics.com · WP Admin: /wp-admin
- Category ID 28: Marthoma Kristheeya Keerthanangal
- WP MCP alias: `default_test`
- GitHub: `Jisjo/thechristianlyrics` (branch `main`) — canonical home for docs + scripts
- Jisjo: jisjokbz.j@gmail.com
