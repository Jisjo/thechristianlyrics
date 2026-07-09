# thechristianlyrics.com — Complete Reference
**Updated:** 09 Jul 2026 (Session 13) · *Single canonical file — overwrite in place; Git history is the version trail.*

**Session 13 summary:** Built merged `mkk.php` (audit + fix + JSON + live HTML dashboard, key-guarded) and pushed to GitHub. Fixed Song 83 TOC ids + Song 160 video. Ran the **first true category-wide audit**. Confirmed real coverage = **226/504 songs published (45%)**. Resolved **4 duplicate song pairs** (redirects set by Jisjo). Fixed **Malayalam corruption** in songs 21 & 22. **Attempted FK on the final 5 (June-2026 batch) via MCP — verified via `mkk.php?fk=1` that the writes did NOT persist (RankMath FK is not REST/MCP-writable). Still 5 FK pending; must use PHP or the RankMath editor.** Consolidated docs to two fixed-name GitHub files.

---

## 1–9. [UNCHANGED from S10 reference doc]
(All sections 1–9 from the S10 reference remain unchanged — core project setup, standards, and history.)

## SESSION UPDATE — 07 Jul 2026 (Session 12) [RETAINED]
- Finalized all 31 FKs (LIST A+B); `mkk-fix-v3.php` (hardcoded FK + meta + alt) run live OK **via PHP `update_post_meta`** (this is why those FKs stuck).
- Alt text reconciled to "first 3 words of FK" on all 31 (14 via fix-v3, 17 via media API).
- GitHub: `scripts/mkk-s12-data.md`, `scripts/mkk-fix-v3.php`.
- **NOTE:** the S12 snapshot's "79–99 meta 21/21 ✅" was wrong — those posts lack meta (S13 audit).

---

## SESSION UPDATE — 09 Jul 2026 (Session 13)

### A. Tooling — `mkk.php` (merged, on GitHub `scripts/`)
- Key-guarded (`?key=mkk-7hq2p9x4`). Modes: `/mkk.php` = **audit text** · `?mode=html` = **live dashboard** · `?mode=json` = **JSON feed** (Claude `web_fetch`) · `?fk=1` = **full FK/meta/title dump** (the ONLY reliable FK verification) · `?mode=fix&live=1` = apply hardcoded fixes. Latest commit `e79a8ca`.
- `web_fetch` note: Claude can only fetch a URL pasted into chat (or in the startup prompt).

### B. Structural fixes applied
- **Song 83 (3166):** TOC ids → `m`/`m-1`. ✅  **Song 160 (10068):** YouTube → `bsd90DN7k6E`. ✅
- Manual due: 3166 TOC "Attempt Recovery" + Save; 10068 open + Save.

### C. ⚠️ Corrections to the S12 pending list
- "125,160,163,83" were **song numbers, not post IDs** (9502, 10068, 10075, 3166). Junk markup **is** real at scale. Song 26 (1581) has a Manglish heading. True Manglish-less: 143, 149, 186, 189, 249, 295.

### D. Live category-28 audit (current)
| Metric | Value |
|---|---|
| Unique songs published | **226 / 504 (45%)** |
| FK missing | **5** (MCP write did NOT persist — see J) |
| META missing | 192 (+1 over-160) |
| No featured image | 47 |
| Featured ALT empty | 0 ✅ |
| Non-standard TOC ids | 96 |
| No Manglish section | 6 |
| Junk markup | 85 |
| Fully clean songs | 12 |

### E. Coverage & the 278 unpublished songs
- 226 live; 278 have no published post. **Decision (locked):** finish SEO on the 226 first.

### F. Duplicates resolved — 4 pairs
| Song | Kept | Trashed | Redirect (301) by Jisjo |
|---|---|---|---|
| 21 | 11762 | 1545 | `/21-bhoovaasikal-sarvvarume/` → `/bhoovasikal-sarvarume/` |
| 22 | 11767 | 1552 | `/22-sarvvamaanushare-parannu/` → `/sarva-manushare-paranu/` |
| 223 | 6550 | 1917 | `/en-daivame-nadathukenne/` → `/en-daivame-nadathukenne-nee/` |
| 233 | 6585 | 1925 | `/enyeshu-en-priyan/` → `/en-yeshu-en-priyan-enikkullon/` |
- Redirects **done.** ✅ Category now **226 posts, 0 duplicate numbers.**

### G. ⚠️ June 2026 batch — Malayalam corruption
- **11762 (21):** `඙` for `ങ` → fixed. **11767 (22):** chillu → `ൿ` → rebuilt. Eyeball pending (22): പരനു, പാടിത്തൻ, നാമവന്നാടും, വാതിൽക്കകത്തുവരിൻ.
- **Likely same corruption: 11770, 11772, 11773 (226, 227, 230)** — audit before relying on them.

### H. Deliverables this session
- `scripts/mkk.php` (audit + html + json + fix, key-guarded). Two canonical docs in `docs/`.

### I. Key Learnings — Session 13
1. **Audit live before trusting any doc.**
2. **Song number ≠ post ID.**
3. **`delete_post` (no force) = recoverable Trash.**
4. **RankMath Redirections NOT via WP MCP** — RankMath UI or PHP.
5. **June 2026 batch carries Malayalam corruption** (`ൿ` for chillu; `඙` for `ങ`).
6. **GitHub is the single canonical home** — fixed-name files, no dated copies.
7. `rank_math_title` unset = template default, not an issue.
8. **`rank_math_focus_keyword` is NOT writable via MCP/REST.** `update_post` returns 200 but WordPress silently drops the key — **verified 09 Jul via `mkk.php?fk=1`: all 5 stayed `(none)` after MCP writes.** The S12 FKs stuck because they were set via **PHP `update_post_meta`**. To set FK: PHP or the RankMath editor. Also NOT readable via MCP — only `mkk.php?fk=1` shows the truth. (This corrects an earlier same-session note that wrongly claimed MCP works.)
9. **`mkk.php?mode=json`/`?mode=html`** = live per-post done/pending; trust over any doc.

### J. Focus keywords — 5 posts STILL PENDING (MCP attempt failed 09 Jul)
Proposed FKs (ready to apply via **PHP** or **manual RankMath editor** — MCP proven not to persist):
| Song | Post | Proposed focus keyword | Status |
|---|---|---|---|
| 21 | 11762 | Bhoovasikal Sarvarume Santhosham | ❌ none |
| 22 | 11767 | Sarva Manushare Paranu | ❌ none |
| 226 | 11770 | Daivahitham Anusarikkunnath Sarvva | ❌ none |
| 227 | 11772 | Ninnishttam Deva Aayidatte | ❌ none |
| 230 | 11773 | Kurishedutthen Yeshuvine Anugamikkum | ❌ none |
- These 5 also need **featured images** (Jisjo); 226/227/230 also need the corruption check.

---

## QUICK STATUS SNAPSHOT (09 Jul 2026 — Session 13)
| Metric | Count |
|---|---|
| Total hymnal | 504 songs |
| Published (unique songs) | **226 / 504 (45%)** |
| Duplicate song numbers | 0 ✅ |
| Focus keyword (FK) | 221 / 226 (**5 pending** — 21,22,226,227,230) |
| Featured ALT | 226 / 226 ✅ |
| META missing | 192 (+1 over-160) |
| Non-standard TOC ids | 96 |
| Junk markup | 85 |
| No featured image | 47 |
| No Manglish section | 6 |
| Fully clean songs | 12 |
| OCR extracted | 150/504 |

---

## PENDING / BACKLOG (true state — S13)
1. **META — 192 missing + 1 over-160 (song 132).** PHP template batch.
2. **Non-standard TOC ids — 96.** PHP.
3. **Junk markup — 85.** PHP once pattern confirmed.
4. **Featured images — 47** (incl. the 5 FK posts). Jisjo.
5. **No Manglish — 6** (143,149,186,189,249,295) + confirm Song 26. MCP/content.
6. **June 2026 corruption check** — 11770, 11772, 11773. MCP.
7. **FK missing — 5** (21,22,226,227,230). **MCP does NOT persist FK → PHP or RankMath editor.**

**Carry-over:** `rank_math_title` manual (low priority); TOC "Attempt Recovery" + Save on 3166/10068; Song 26 Manglish re-confirm.
**Content creation backlog:** 278 unpublished songs (later).

---

## NEXT SESSION (S14) PRIORITIES
1. **FK for the 5** — via PHP `update_post_meta` (add to `mkk.php` fix) or manual.
2. **META batch** — PHP template script for 192 missing + trim song 132.
3. **TOC-id normalization** — extend `mkk.php` fix to the 96.
4. **Junk cleanup** — inspect the 85, confirm pattern, script strip.
5. **June 2026 corruption** — 11770/11772/11773. **Featured images** — 47 (Jisjo).
6. Songs 79–99 (7) + 51–78 (27); OCR from 151.

### Jisjo Manual Actions
1. Featured images for 21, 22, 226, 227, 230 (+ 42 others).
2. Eyeball Song 22 (11767) flagged Malayalam words.
3. TOC "Attempt Recovery" + Save on 3166; open + Save 10068.
4. Retire old Project doc copies (GitHub `docs/` is canonical).

---

## STARTUP PROMPT — Session 14
```
Startup — thechristianlyrics.com MKK SEO Session 14
Read docs/CLAUDE_INSTRUCTIONS.md and docs/thechristianlyrics_Complete_Reference.md
from GitHub repo Jisjo/thechristianlyrics. Also read scripts/mkk.php.
Summarise state and resume.

Live status (paste so Claude can read it):
https://thechristianlyrics.com/mkk.php?mode=json&key=mkk-7hq2p9x4
FK truth: https://thechristianlyrics.com/mkk.php?fk=1&key=mkk-7hq2p9x4

Context:
* 226/504 published. FK 5 pending (21,22,226,227,230). ALT 100%.
* Pending: META 192(+1 long), TOC 96, junk 85, no image 47, no Manglish 6.
* rank_math_focus_keyword is NOT writable via MCP (returns 200 but drops the value);
  set via PHP update_post_meta or the RankMath editor. Verify only via mkk.php?fk=1.
* rank_math_title stays unset (template default). Duplicates resolved; redirects live.
* June 2026 corruption (ൿ for chillu, ඙ for ങ): 21 & 22 fixed; 226/227/230 to check.
* Fix split: meta/TOC/junk/FK → PHP; images → Jisjo; Manglish/corruption → MCP.
```
