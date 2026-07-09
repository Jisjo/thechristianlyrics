# thechristianlyrics.com — Complete Reference
**Updated:** 09 Jul 2026 (v15 — Session 13) | **Session 13:** Built merged `mkk.php` (read-only audit + fix modes) and pushed to GitHub. Fixed Song 83 TOC ids + Song 160 video via the fix mode. Ran the **first true category-wide audit** — it overturned much of the S12 pending list (see corrections below). Confirmed real coverage = **226/504 songs published (45%)**. Resolved **4 duplicate song pairs** (trashed 4 old posts, kept the cleaner rebuilds; redirects set by Jisjo). Discovered + fixed **Malayalam corruption in the June 2026 post batch** (songs 21 & 22). Built an interactive per-song tracker. **Reference doc now lives in GitHub** (canonical, Claude-writable, versioned).

---

## 1–9. [UNCHANGED from S10 reference doc]
(All sections 1–9 from `thechristianlyrics_Complete_Reference_06JUL2026_S10.md` remain unchanged.)

## SESSION UPDATE — 07 Jul 2026 (Session 12) [RETAINED]
- Finalized all 31 FKs (LIST A+B); `mkk-fix-v3.php` (hardcoded FK + meta + alt) run live OK.
- Alt text reconciled to "first 3 words of FK" on all 31 (14 via fix-v3, 17 via media API).
- FK/meta NOT REST-readable → verification deferred at S12.
- GitHub: `scripts/mkk-s12-data.md`, `scripts/mkk-fix-v3.php`.
- **NOTE (superseded by S13 audit):** the S12 snapshot claimed category-wide meta was broadly done and "79–99 FK+meta 21/21 ✅". The S13 live audit shows this is **not** true beyond the 31 LIST A+B posts (see below).

---

## SESSION UPDATE — 09 Jul 2026 (Session 13)

### A. Tooling — `mkk.php` (merged, on GitHub)
- Single file replaces `mkk-toc-id-fix.php` / `mkk-structural-fixes.php` / `mkk-audit.php`.
- Modes: `/mkk.php` = **audit, read-only** (issues + summary) · `?fk=1` = full FK/meta/title dump · `?mode=fix` = dry run · `?mode=fix&live=1` = apply.
- Server-side; fixes touch ASCII tokens only (no Malayalam handling). Pushed to `scripts/mkk.php` (commit `342a051`).

### B. Structural fixes applied (via `mkk.php?mode=fix&live=1`)
- **Song 83 (3166):** TOC ids `malayalam-lyrics`/`manglish-lyrics` → **`m`/`m-1`** (4 occurrences each). ✅ audit confirms flag cleared.
- **Song 160 (10068):** YouTube embed swapped `ZrUVd40FQq0` → **`bsd90DN7k6E`** (Vaanalokathezhunnulli, *Maramon Convention – Topic*, 2023, 4:10). ✅ verified live embed.
- Manual still due: 3166 → "Attempt Recovery" on TOC block + Save; 10068 → open + Save (oEmbed refresh).

### C. ⚠️ Corrections to the S12 pending list (verified live)
- **S12 item "Structural cleanup — 4 posts (125,160,163,83)" was largely STALE.** "125,160,163,83" were **song numbers, not post IDs** (post IDs: 9502, 10068, 10075, 3166). Live check: **3 were already clean**; only Song 83 had the TOC-id deviation.
- **swcfpc / broken `<meta http-equiv>` / "Maglish"/"Here"** — NOT present in those 4 posts' stored content. (swcfpc is a cache-plugin render-time param, not stored content; broken `<meta>` would be in `<head>`.)
- **BUT junk markup is real at category scale (87 posts)** — it just wasn't on the 4 spot-checked. Earlier "phantom" call corrected.
- **Song 26 (1581):** audit finds a "Manglish" heading present → the S12 "no Manglish" note is likely wrong (needs eyeball). The genuinely Manglish-less posts are **143, 149, 186, 189, 249, 295**.

### D. First true category-wide audit (live, category 28)
| Metric | Value |
|---|---|
| Post rows scanned | 230 |
| Unique songs published | **226 / 504 (45%)** |
| FK missing | 5 (the near-empty June-2026 dupes) |
| META description missing | **196** |
| META > 160 chars | 1 |
| `rank_math_title` unset | 229 (**template default — low priority, ignore**) |
| No featured image | 47 |
| Featured ALT empty | 0 ✅ (S12 alt work held) |
| Non-standard TOC ids | **98** |
| No Manglish section | 6 (143,149,186,189,249,295) |
| Junk markup (swcfpc/meta/Maglish) | **87** |
| Duplicate song numbers | 4 (21,22,223,233) — **now resolved** |
| Fully clean songs | 12 |
- **Truth vs S12:** FK is broadly done (225/230 set), but **META exists on only ~34 posts**; songs 79–99 have **no** meta (contradicts S12's "21/21 ✅").

### E. Coverage & the 278 unpublished songs
- **226 songs live; 278 song numbers have no published post** (dense above ~150; matches OCR-through-150 state). Likely "never created" (not verified vs draft/trash — one all-status check still open).
- **Decision (locked):** finish SEO on the 226 live posts first; create the 278 later.

### F. Duplicates resolved — 4 pairs
Verified content on all pairs (earlier hasty "keep old" call corrected — the newer rebuilds are the better posts).
| Song | Kept (live) | Trashed | Redirect (301) set by Jisjo |
|---|---|---|---|
| 21 | 11762 | 1545 | `/21-bhoovaasikal-sarvvarume/` → `/bhoovasikal-sarvarume/` |
| 22 | 11767 | 1552 | `/22-sarvvamaanushare-parannu/` → `/sarva-manushare-paranu/` |
| 223 | 6550 | 1917 | `/en-daivame-nadathukenne/` → `/en-daivame-nadathukenne-nee/` |
| 233 | 6585 | 1925 | `/enyeshu-en-priyan/` → `/en-yeshu-en-priyan-enikkullon/` |
- Trashed via `claudeus_wp_content__delete_post` (no `force` param → **recoverable Trash**, ~30 days). Redirects: **completed by Jisjo.** ✅
- Category 28 now **226 posts, no duplicate song numbers.**

### G. ⚠️ June 2026 batch — Malayalam corruption (fixed 21 & 22)
- The 27-Jun-2026 rebuild batch has systematic Malayalam encoding corruption:
  - **11762 (song 21):** Sinhala char `඙` (U+0D99) in place of `ങ`, 2 words. → **fixed** (via MCP `update_post`).
  - **11767 (song 22):** chillu `‑ൻ` / `‑ന്ന` systematically replaced by `‑ൿ` (U+0D7F) throughout. → **rebuilt/fixed** (context-mapped; not a 1:1 swap).
- **Eyeball pending (song 22 judgment words):** പരനു, പാടിത്തൻ, നാമവന്നാടും, വാതിൽക്കകത്തുവരിൻ.
- **Likely same corruption in the rest of the batch: 11770, 11772, 11773 (songs 226, 227, 230)** — audit before relying on them.
- Elementor meta already empty on these new posts.

### H. Deliverables this session
- `scripts/mkk.php` (GitHub, commit `342a051`).
- `mkk_live_tracker.html` — interactive per-song tracker (all 8 checks, filter/search/sort) built from the live audit.
- This reference doc → **GitHub is now the canonical home** (Claude reads + writes; versioned). Project copy optional, kept in sync only if needed.

### I. Key Learnings — Session 13
1. **Audit live before trusting any doc.** The S12 pending list was materially wrong; live category audit is the source of truth.
2. **Song number ≠ post ID.** S12 wrote `song (post_id)`; "125,160,163,83" were song numbers. Always resolve to the parenthetical post ID before API calls.
3. **`delete_post` (no force) = Trash (recoverable).** Safe for dedup.
4. **RankMath Redirections are NOT exposed via the WP MCP** — set in RankMath UI or via PHP; Jisjo handles.
5. **June 2026 post batch carries Malayalam corruption** (`ൿ` for chillu; `඙` for `ങ`). Treat any post from that batch as suspect.
6. **GitHub > Project for a living doc** — Claude can write + version GitHub; Project files are read-only to Claude and need manual re-upload.
7. `rank_math_title` unset on ~all posts = template default, **not** an issue; exclude from "pending" counts.

---

## QUICK STATUS SNAPSHOT (updated 09 Jul 2026 — Session 13)
| Metric | Count |
|---|---|
| Total hymnal | 504 songs |
| Published (unique songs) | **226 / 504 (45%)** |
| Category 28 posts (post dedup) | 226 (was 230) |
| Duplicate song numbers | 0 ✅ (4 resolved) |
| LIST A+B (YouTube/img/Elementor/FK/meta/alt) | 31/31 ✅ each |
| Song 83 TOC ids / Song 160 video | ✅ fixed |
| **Category-wide META missing** | **196** |
| **Category-wide non-standard TOC ids** | **98** |
| **Category-wide junk markup** | **87** |
| No featured image | 47 |
| No Manglish section | 6 (143,149,186,189,249,295) |
| OCR extracted | 150/504 |

---

## PENDING / BACKLOG (true state — as of S13)
**Published-post SEO backlog (priority order by impact):**
1. **META descriptions — 196 posts.** Scriptable template batch (song name + lyricist), same shape as fix-v3.
2. **Non-standard TOC ids — 98 posts.** Scriptable (generalize the Song 83 fix in `mkk.php`).
3. **Junk markup — 87 posts.** Scriptable once the exact junk pattern per post is confirmed.
4. **Featured images — 47 posts.** Jisjo manual upload.
5. **No Manglish — 6 posts** (143,149,186,189,249,295) + confirm Song 26.
6. **June 2026 batch corruption check** — 11770, 11772, 11773 (songs 226,227,230).
7. **FK missing — 5** (near-empty items now largely resolved via dedup; recheck).

**Carry-over from S12:**
- `rank_math_title` — manual only (low priority; template default fine).
- TOC "Attempt Recovery" + Save where content changed (incl. 3166, 10068).
- Song 26 (1581) Manglish — re-confirm need.

**Content creation backlog:** 278 unpublished songs (after published-SEO complete).

---

## NEXT SESSION (S14) PRIORITIES
1. **META batch** — generate hardcoded template script for the 196 missing (biggest SEO win).
2. **TOC-id normalization** — extend `mkk.php` fix mode to the 98 non-standard posts.
3. **Junk-markup cleanup** — inspect a few of the 87, confirm pattern, script the strip.
4. **June 2026 batch** — audit 11770/11772/11773 for Malayalam corruption; fix.
5. **Songs 79–99 content rebuilds (7)** + **51–78 (27)**.
6. **OCR** — continue from 151.

### Jisjo Manual Actions
1. Eyeball Song 22 (11767) flagged Malayalam words; Save 11762 & 11767 in Gutenberg.
2. TOC "Attempt Recovery" + Save on 3166; open + Save 10068 (oEmbed).
3. Confirm reference-doc home = GitHub (then retire Project copies to avoid drift).

---

## STARTUP PROMPT — Session 14
```
Startup — thechristianlyrics.com MKK SEO Session 14
Read CLAUDE_INSTRUCTIONS.md, then read the latest reference doc from GitHub
repo Jisjo/thechristianlyrics (docs/ or scripts/). Also read scripts/mkk.php.
Summarise state and resume.

Priority: META batch for the 196 missing (template script), then TOC-id
normalization on the 98 non-standard posts via mkk.php fix mode.

Context:
* True state from S13 live audit: 226/504 songs published; META missing 196,
  non-standard TOC ids 98, junk markup 87, no image 47, no Manglish 6.
* Duplicates resolved (21,22,223,233); redirects live.
* June 2026 post batch has Malayalam corruption (ൿ for chillu, ඙ for ങ) —
  21 & 22 fixed; 226/227/230 (11770/11772/11773) still to check.
* mkk.php = audit (default read-only) + fix (?mode=fix&live=1). On GitHub.
* FK/meta NOT REST-readable — verify via RankMath editor or mkk.php?fk=1.
* Reference doc now lives in GitHub (canonical).
```
