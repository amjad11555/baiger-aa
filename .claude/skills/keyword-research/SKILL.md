---
name: keyword-research
description: >-
  Find and choose the right keywords/topics to target for the Gulf/MENA Arabic
  market — intent-driven keyword research, topic clustering, and a prioritized
  content plan. Takes a seed topic, business, or URL and returns a ranked keyword
  table (primary + long-tail + question keywords) scored by intent, business value,
  difficulty vs. the site's authority, and opportunity, then maps them into pillar/
  cluster content. USE THIS SKILL whenever the user asks what keywords to target,
  what to write about, how to find search terms, keyword difficulty/volume, topic
  clusters, a content calendar, or "أي كلمات مفتاحية"، "على أي كلمة أكتب"، "أفكار
  مقالات" — and as the keyword step before writing a BAIGR article. Focuses on how
  Arabic/Gulf users actually search (dialect, no diacritics, transliteration).
  Default language Arabic unless the user asks otherwise.
---

# Keyword Research (Gulf / Arabic market)

The goal isn't the biggest search volume — it's the keyword you can **realistically
rank for** that brings **the right buyer**. A new-ish site chasing a giant head term
loses; the same site owning specific long-tail queries wins customers this quarter.
Match intent, weigh it against your authority, and prioritize business value.

## Core principle: win where you can, for who matters
Three questions decide every keyword:
1. **Intent** — does this query match what our page offers, and what stage is the
   searcher at (informational → commercial → transactional)?
2. **Winnability** — can this specific site rank for it soon, given its current
   authority? (New domain ⇒ favor long-tail, low-competition, question queries.)
3. **Value** — will ranking bring a real potential BAIGR client, or just traffic?
A medium-volume, high-intent, winnable keyword beats a huge, generic, unwinnable one
every time.

## Workflow

### 1. Seed
Start from the business, service, or topic. For BAIGR the seeds are things like:
performance ads, Meta/Google/TikTok ads, e-commerce marketing, brand building,
website design, AI marketing automation — for متاجر/شركات in الخليج.

### 2. Expand (use live tools)
Broaden each seed with real sources, not guesses:
- **Google Autocomplete** — type the seed in Arabic, read the suggestions.
- **People Also Ask / Related searches** — harvest questions and adjacent terms
  (WebSearch the seed and read what Google surfaces).
- **Search Console** (once the site has data) — Performance report → Queries: the
  keywords you *already* get impressions for are the fastest wins. This is the single
  best source once available.
- **Competitors** — what the top-ranking pages target in their titles/H2s (see the
  competitor-gap method in `references/methods.md`).
- Free/keyword tools (Keyword Planner, and others) for rough volume/competition.

### 3. Classify each keyword
Tag every candidate with:
- **Intent**: informational / commercial-investigation / transactional / navigational.
- **Funnel stage**: TOFU (awareness) / MOFU (consideration) / BOFU (decision).
- **Type**: head / body / long-tail / question.
- **Rough volume** and **rough difficulty** (or a proxy: how strong are the pages
  currently ranking — big brands ⇒ hard).

### 4. Score & prioritize (Opportunity)
Rank by an opportunity view, not volume alone:
> **Opportunity = (Intent match × Business value × Winnability) — Difficulty**
Favor: high-intent, high-value, winnable long-tail and question keywords first;
queue big head terms as long-term pillar targets. Flag "quick wins" = decent value +
low difficulty (or Search Console queries ranking positions 5–20 that a better page
could lift).

### 5. Cluster into a content plan (pillar + cluster model)
Group keywords by topic into **clusters**: one broad **pillar** page per cluster
targeting the head term, plus several **supporting** posts targeting the long-tail/
question keywords, all interlinking. This builds topical authority — Google rewards
covering a topic comprehensively, not one-off posts. Map each cluster to funnel stage
so the plan pulls readers from awareness to decision.

## Arabic / Gulf search behavior (critical — don't skip)
- **People search in dialect and shorthand**, often without diacritics; target the
  *actual* phrasing, not only formal Arabic (e.g. how a Saudi/Emirati owner types it).
- **Transliteration & code-switching**: many search English/Franco-Arabic terms
  ("Meta ads", "شوبيفاي", "SEO") — capture both script variants where relevant.
- **Local modifiers**: city/country ("السعودية"، "الإمارات"، "الرياض"، "دبي") add
  intent and are far more winnable than global terms.
- Volume tools underreport Arabic — treat numbers as directional; weight real signals
  (autocomplete presence, PAA, Search Console) more heavily.

## Output format
```
## 🌱 Seeds & clusters
<cluster name → pillar keyword → the supporting keywords under it>

## 📊 Prioritized keyword table
| Keyword (as searched) | Intent | Funnel | Type | ~Volume | ~Difficulty | Value | Priority |
| … | … | … | … | … | … | … | 🔥 quick win / ⭐ core / 🕰 long-term |

## 🗺 Content plan
<ordered list: what to publish first and why — lead with quick wins, then pillars.
For each: working title + the primary + secondary keywords + funnel stage>

## 🎯 Start here
<the top 3 recommended pieces to write next, one line each on why>
```
Hand the top recommendation straight to the `baigr-article` skill to write it.

See `references/methods.md` for the competitor-gap method, Search Console mining,
and difficulty estimation without paid tools.
