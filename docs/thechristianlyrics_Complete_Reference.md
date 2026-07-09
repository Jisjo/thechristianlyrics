# thechristianlyrics.com — Complete Reference
**Updated:** 09 Jul 2026 (Session 13) · *Single canonical file — overwrite in place; Git history is the version trail.*

**Session 13 summary:** Built merged `mkk.php` (audit + fix + JSON + live HTML dashboard, key-guarded). Fixed Song 83 TOC ids + Song 160 video. First true category audit. Coverage **226/504 (45%)**. Resolved **4 duplicate pairs** (redirects by Jisjo). Fixed **Malayalam corruption** in 21 & 22. **Confirmed RankMath FK is NOT writable via MCP (verified via `mkk.php?fk=1`); Jisjo set the final 5 FKs manually in the RankMath editor — FK now 100% across 226.** Consolidated docs to two fixed-name GitHub files.

---

## 1–9. [UNCHANGED from S10 reference doc]
(All sections 1–9 from the S10 reference remain unchanged — core project setup, standards, and history.)

## SESSION UPDATE — 07 Jul 2026 (Session 12) [RETAINED]
- Finalized all 31 FKs (LIST A+B) via PHP `update_post_meta` (this is why those stuck).
- Alt text = "first 3 words of FK" on all 31. GitHub: `scripts/mkk-s12-data.md`, `scripts/mkk-fix-v3.php`.
- **NOTE:** S12's "79–99 meta 21/21 ✅" was wrong — those posts lack meta (S13 audit).

---

## SESSION UPDATE — 09 Jul 2026 (Session 13)

### A. Tooling — `mkk.php` (GitHub `scripts/`)
- Key-guarded (`?key=mkk-7hq2p9x4`). Modes: `/mkk.php` audit text · `?mode=html` live dashboard · `?mode=json` JSON feed (Claude `web_fetch`) · `?fk=1` full FK/meta/title dump (**only reliable FK check**) · `?mode=fix&live=1` apply fixes. Commit `e79a8ca`.
- `web_fetch` only works on a URL pasted into chat (or in the startup prompt).

### B. Structural fixes applied
- Song 83 (3166) TOC ids → `m`/`m-1`. ✅  Song 160 (10068) YouTube → `bsd90DN7k6E`. ✅
- Manual due: 3166 TOC "Attempt Recovery" + Save; 10068 open + Save.

### C. ⚠️ Corrections to S12 pending list
- "125,160,163,83" were song numbers, not post IDs. Junk markup **is** real at scale. Song 26 (1581) has Manglish. True Manglish-less: 143, 149, 186, 189, 249, 295.

### D. Live category-28 audit (current)
| Metric | Value |
|---|---|
| Unique songs published | **226 / 504 (45%)** |
| FK missing | **0** (5 set manually by Jisjo — MCP can't; see J) |
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
- 11762 (21) `඙`→`ങ` fixed. 11767 (22) chillu→`ൿ` rebuilt. Eyeball pending (22): പരനു, പാടിത്തൻ, നാമവന്നാടും, വാതിൽക്കകത്തുവരിൻ.
- **Likely same corruption: 11770, 11772, 11773 (226, 227, 230)** — audit before relying on them.

### H. Deliverables this session
- `scripts/mkk.php` (audit + html + json + fix). Two canonical docs in `docs/`.

### I. Key Learnings — Session 13
1. **Audit live before trusting any doc.**
2. **Song number ≠ post ID.**
3. **`delete_post` (no force) = recoverable Trash.**
4. **RankMath Redirections NOT via WP MCP** — RankMath UI or PHP.
5. **June 2026 batch carries Malayalam corruption** (`ൿ` for chillu; `඙` for `ങ`).
6. **GitHub is the single canonical home** — fixed-name files, no dated copies.
7. `rank_math_title` unset = template default, not an issue.
8. **`rank_math_focus_keyword` is NOT writable via MCP/REST.** `update_post` returns 200 but WordPress silently drops the key (verified 09 Jul via `mkk.php?fk=1`: the 5 stayed `(none)` after MCP writes). Set via **PHP `update_post_meta`** or the **RankMath editor** (the 5 were ultimately set manually in the editor). Also not *readable* via MCP — only `mkk.php?fk=1` shows the truth.
9. **`mkk.php?mode=json`/`?mode=html`** = live per-post done/pending; trust over any doc.
10. **Process:** verify a write in the authoritative source before reporting it done — do not infer success from a 200 response or from the user's assumption.

### J. Focus keywords — 5 posts SET MANUALLY (09 Jul) ✅
MCP attempts did not persist (see #8); Jisjo entered these in the RankMath editor:
| Song | Post | Focus keyword |
|---|---|---|
| 21 | 11762 | Bhoovasikal Sarvarume Santhosham |
| 22 | 11767 | Sarva Manushare Paranu |
| 226 | 11770 | Daivahitham Anusarikkunnath Sarvva |
| 227 | 11772 | Ninnishttam Deva Aayidatte |
| 230 | 11773 | Kurishedutthen Yeshuvine Anugamikkum |
- **FK now 100% (226/226).** Verify anytime via `mkk.php?fk=1`.
- These 5 still need **featured images** (Jisjo); 226/227/230 still need the corruption check.

---

## QUICK STATUS SNAPSHOT (09 Jul 2026 — Session 13)
| Metric | Count |
|---|---|
| Total hymnal | 504 songs |
| Published (unique songs) | **226 / 504 (45%)** |
| Duplicate song numbers | 0 ✅ |
| Focus keyword (FK) | **226 / 226 ✅** (5 set manually by Jisjo) |
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
4. **Featured images — 47** (incl. songs 21,22,226,227,230). Jisjo.
5. **No Manglish — 6** (143,149,186,189,249,295) + confirm Song 26. MCP/content.
6. **June 2026 corruption check** — 11770, 11772, 11773. MCP.
7. ~~FK missing~~ — **DONE (226/226, 5 set manually).** ✅

**Carry-over:** `rank_math_title` manual (low priority); TOC "Attempt Recovery" + Save on 3166/10068; Song 26 Manglish re-confirm.
**Content creation backlog:** 278 unpublished songs (later).

---

## NEXT SESSION (S14) PRIORITIES
1. **META batch** — PHP template script for 192 missing + trim song 132 (biggest SEO win).
2. **TOC-id normalization** — extend `mkk.php` fix to the 96.
3. **Junk cleanup** — inspect the 85, confirm pattern, script strip.
4. **June 2026 corruption** — 11770/11772/11773. **Featured images** — 47 (Jisjo).
5. Songs 79–99 (7) + 51–78 (27); OCR from 151.

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
* 226/504 published. FK 100% (5 set manually 09 Jul). ALT 100%.
* Pending: META 192(+1 long), TOC 96, junk 85, no image 47, no Manglish 6.
* rank_math_focus_keyword is NOT writable via MCP (drops silently); use PHP
  update_post_meta or the RankMath editor. Verify only via mkk.php?fk=1.
* rank_math_title stays unset (template default). Duplicates resolved; redirects live.
* June 2026 corruption (ൿ for chillu, ඙ for ങ): 21 & 22 fixed; 226/227/230 to check.
* Fix split: meta/TOC/junk → PHP; images → Jisjo; Manglish/corruption → MCP.
```
