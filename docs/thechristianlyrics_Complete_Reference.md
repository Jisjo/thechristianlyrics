# thechristianlyrics.com — Complete Reference
**Updated:** 09 Jul 2026 (Session 13) · *Single canonical file — overwrite in place; Git history is the version trail.*

**Session 13 summary:** Built merged `mkk.php` (audit + fix + JSON + live HTML dashboard, key-guarded) and pushed to GitHub. Fixed Song 83 TOC ids + Song 160 video. Ran the **first true category-wide audit** — overturned much of the S12 pending list. Confirmed real coverage = **226/504 songs published (45%)**. Resolved **4 duplicate song pairs** (redirects set by Jisjo). Fixed **Malayalam corruption** in songs 21 & 22. **Set focus keywords on the final 5 posts that lacked them (June-2026 batch) via MCP — FK is now 100% across all 226.** Consolidated docs to two fixed-name GitHub files.

---

## 1–9. [UNCHANGED from S10 reference doc]
(All sections 1–9 from the S10 reference remain unchanged — core project setup, standards, and history.)

## SESSION UPDATE — 07 Jul 2026 (Session 12) [RETAINED]
- Finalized all 31 FKs (LIST A+B); `mkk-fix-v3.php` (hardcoded FK + meta + alt) run live OK.
- Alt text reconciled to "first 3 words of FK" on all 31 (14 via fix-v3, 17 via media API).
- GitHub: `scripts/mkk-s12-data.md`, `scripts/mkk-fix-v3.php`.
- **NOTE (superseded by S13 audit):** the S12 snapshot claimed category-wide meta was broadly done and "79–99 FK+meta 21/21 ✅". The S13 live audit shows this is **not** true beyond the 31 LIST A+B posts.

---

## SESSION UPDATE — 09 Jul 2026 (Session 13)

### A. Tooling — `mkk.php` (merged, on GitHub `scripts/`)
- Key-guarded (`?key=mkk-7hq2p9x4`). Modes: `/mkk.php` = **audit text** (read-only) · `?mode=html` = **live HTML dashboard** (read-only, per-post done/pending) · `?mode=json` = **JSON feed** (for Claude `web_fetch`) · `?fk=1` = full FK/meta/title dump · `?mode=fix&live=1` = apply hardcoded fixes.
- Server-side; reads RankMath meta directly (not REST-readable). Latest commit `e79a8ca`.
- `web_fetch` note: Claude can only fetch a URL that is pasted into chat (or in the startup prompt) — put the `?mode=json` link there for live sync.

### B. Structural fixes applied
- **Song 83 (3166):** TOC ids → `m`/`m-1`. ✅
- **Song 160 (10068):** YouTube embed → `bsd90DN7k6E`. ✅
- Manual due: 3166 TOC "Attempt Recovery" + Save; 10068 open + Save.

### C. ⚠️ Corrections to the S12 pending list
- "125,160,163,83" were **song numbers, not post IDs** (9502, 10068, 10075, 3166). 3 already clean; only Song 83 had the TOC-id deviation.
- Junk markup **is** real at scale — just not on the 4 spot-checked.
- Song 26 (1581) has a Manglish heading → S12 "no Manglish" note likely wrong. True Manglish-less: 143, 149, 186, 189, 249, 295.

### D. First true category-wide audit (live, category 28)
| Metric | Value |
|---|---|
| Unique songs published | **226 / 504 (45%)** |
| FK missing | 5 → **0** (set S13 — see J) |
| META missing | 192 (+1 over-160) |
| No featured image | 47 |
| Featured ALT empty | 0 ✅ |
| Non-standard TOC ids | 96 |
| No Manglish section | 6 |
| Junk markup | 85 |
| Fully clean songs | 12 |
*(Live JSON figures as of 09 Jul; slightly lower than the first audit after dedup + fixes.)*

### E. Coverage & the 278 unpublished songs
- 226 live; 278 song numbers have no published post. **Decision (locked):** finish SEO on the 226 first; create the 278 later.

### F. Duplicates resolved — 4 pairs
| Song | Kept (live) | Trashed | Redirect (301) by Jisjo |
|---|---|---|---|
| 21 | 11762 | 1545 | `/21-bhoovaasikal-sarvvarume/` → `/bhoovasikal-sarvarume/` |
| 22 | 11767 | 1552 | `/22-sarvvamaanushare-parannu/` → `/sarva-manushare-paranu/` |
| 223 | 6550 | 1917 | `/en-daivame-nadathukenne/` → `/en-daivame-nadathukenne-nee/` |
| 233 | 6585 | 1925 | `/enyeshu-en-priyan/` → `/en-yeshu-en-priyan-enikkullon/` |
- Trashed via `delete_post` (no force → recoverable). Redirects **done.** ✅ Category now **226 posts, 0 duplicate numbers.**

### G. ⚠️ June 2026 batch — Malayalam corruption
- **11762 (21):** Sinhala `඙` for `ങ` → fixed. **11767 (22):** chillu `‑ൻ`/`‑ന്ന` → `‑ൿ` throughout → rebuilt.
- Eyeball pending (22): പരനു, പാടിത്തൻ, നാമവന്നാടും, വാതിൽക്കകത്തുവരിൻ.
- **Likely same corruption: 11770, 11772, 11773 (226, 227, 230)** — audit before relying on them. (Manglish/FK unaffected; these got FKs in J.)

### H. Deliverables this session
- `scripts/mkk.php` (audit + html + json + fix, key-guarded).
- Two canonical docs consolidated in `docs/` (fixed names, overwrite-in-place).

### I. Key Learnings — Session 13
1. **Audit live before trusting any doc.** S12 pending list was materially wrong.
2. **Song number ≠ post ID.** Resolve to the post ID before API calls.
3. **`delete_post` (no force) = Trash (recoverable).**
4. **RankMath Redirections NOT exposed via WP MCP** — RankMath UI or PHP; Jisjo handles.
5. **June 2026 post batch carries Malayalam corruption** (`ൿ` for chillu; `඙` for `ങ`).
6. **GitHub is the single canonical home** — fixed-name files, no dated copies.
7. `rank_math_title` unset = template default, not an issue.
8. **`rank_math_focus_keyword` IS writable via MCP `update_post` `meta`** (confirmed live S13) — the earlier "not REST-writable" assumption was wrong; it just isn't echoed back in the response `meta`. Verify via `mkk.php?fk=1`. Caveat: if a post is later opened+saved in Gutenberg from a stale editor, the FK can be wiped — reload before saving.
9. **`mkk.php?mode=json` for Claude, `?mode=html` for Jisjo** — live per-post done/pending; trust over any doc's pending list.

### J. Focus keywords completed — 5 posts via MCP (09 Jul)
| Song | Post | Focus keyword |
|---|---|---|
| 21 | 11762 | Bhoovasikal Sarvarume Santhosham |
| 22 | 11767 | Sarva Manushare Paranu |
| 226 | 11770 | Daivahitham Anusarikkunnath Sarvva |
| 227 | 11772 | Ninnishttam Deva Aayidatte |
| 230 | 11773 | Kurishedutthen Yeshuvine Anugamikkum |
- Set via `update_post` `meta.rank_math_focus_keyword` (Elementor fields cleared each time). **FK-missing 5 → 0; FK now 100% across 226.**
- These 5 still need **featured images** (Jisjo) and **226/227/230** still need the corruption check.

---

## QUICK STATUS SNAPSHOT (09 Jul 2026 — Session 13)
| Metric | Count |
|---|---|
| Total hymnal | 504 songs |
| Published (unique songs) | **226 / 504 (45%)** |
| Duplicate song numbers | 0 ✅ |
| **Focus keyword (FK)** | **226 / 226 ✅ (0 pending)** |
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
**Published-post SEO backlog (priority by impact):**
1. **META descriptions — 192 missing + 1 over-160 (song 132).** PHP template batch.
2. **Non-standard TOC ids — 96 posts.** PHP (generalize Song 83 fix).
3. **Junk markup — 85 posts.** PHP once pattern confirmed.
4. **Featured images — 47 posts** (incl. the 5 FK posts). Jisjo manual upload.
5. **No Manglish — 6 posts** (143,149,186,189,249,295) + confirm Song 26. MCP/content.
6. **June 2026 corruption check** — 11770, 11772, 11773. MCP.
7. ~~FK missing~~ — **DONE (0 pending).** ✅

**Carry-over:** `rank_math_title` manual (low priority); TOC "Attempt Recovery" + Save on 3166/10068; Song 26 Manglish re-confirm.

**Content creation backlog:** 278 unpublished songs (after published-SEO complete).

---

## NEXT SESSION (S14) PRIORITIES
1. **META batch** — PHP template script for the 192 missing + trim song 132 (biggest SEO win).
2. **TOC-id normalization** — extend `mkk.php` fix mode to the 96 non-standard posts.
3. **Junk-markup cleanup** — inspect a few of the 85, confirm pattern, script the strip.
4. **June 2026 batch** — audit 11770/11772/11773; fix corruption.
5. **Featured images** — 47 (Jisjo), incl. songs 21/22/226/227/230.
6. **Songs 79–99 rebuilds (7)** + **51–78 (27)**; **OCR** from 151.

### Jisjo Manual Actions
1. Featured images for songs 21, 22, 226, 227, 230 (+ the other 42).
2. Eyeball Song 22 (11767) flagged Malayalam words; if you open the 5 FK posts in Gutenberg, **reload first, then Save** (avoids FK wipe).
3. TOC "Attempt Recovery" + Save on 3166; open + Save 10068 (oEmbed).
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

Priority: META batch for the ~193 missing/over-160 (PHP template script),
then TOC-id normalization on the 96 non-standard posts via mkk.php fix mode.

Context:
* 226/504 songs published. FK = 100% done (0 pending). ALT = 100%.
* Pending: META 192(+1 long), TOC 96, junk 85, no image 47, no Manglish 6.
* Duplicates resolved (21,22,223,233); redirects live.
* June 2026 batch corruption (ൿ for chillu, ඙ for ങ): 21 & 22 fixed;
  226/227/230 (11770/11772/11773) still to check.
* rank_math_focus_keyword IS writable via MCP update_post meta (not echoed back);
  verify via mkk.php?fk=1. rank_math_title stays unset (template default).
* Fix split: meta/TOC/junk → PHP; images → Jisjo; FK/Manglish/corruption → MCP.
```
