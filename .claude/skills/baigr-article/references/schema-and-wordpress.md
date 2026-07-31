# Schema (JSON-LD) + WordPress-ready output

## How the user publishes
The blog is WordPress at `baigr.com/blog`. The user pastes the article into a new
post. Make that one paste as clean as possible:
- Body as **valid HTML** using standard tags (`<h2>`, `<h3>`, `<p>`, `<ul>`/`<ol>`,
  `<img>`, `<blockquote>`, `<table>`). WordPress' block editor accepts pasted HTML
  and converts it to blocks. Avoid inline styles and custom classes.
- Put the JSON-LD in its own fenced block so the user can drop it into a **Custom
  HTML block** at the end of the post, or into their SEO plugin's schema field.
- Give the SEO title, meta description, slug, focus keyword, tags/category, and
  featured-image alt as a separate list to fill into the post's fields.

## Images in the body
Embed with the Higgsfield CDN URL and full alt text:
```html
<figure>
  <img src="https://<cdn-url>" alt="قمع إعلانات ميتا لمتجر إلكتروني خليجي" width="800" loading="lazy" />
  <figcaption>مراحل قمع إعلانات ميتا من الوعي إلى الولاء.</figcaption>
</figure>
```
Tell the user they can either keep the CDN URL or download the image and re-upload
it to the WordPress media library (local hosting is slightly better for Core Web
Vitals and guards against the CDN link changing).

## JSON-LD templates

### BlogPosting (always)
```json
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "<SEO title, ≤110 chars>",
  "description": "<meta description>",
  "image": "<hero image CDN URL>",
  "inLanguage": "ar",
  "datePublished": "<YYYY-MM-DD>",
  "dateModified": "<YYYY-MM-DD>",
  "author": { "@type": "Organization", "name": "BAIGR", "url": "https://baigr.com/" },
  "publisher": {
    "@type": "Organization",
    "name": "BAIGR",
    "logo": { "@type": "ImageObject", "url": "https://baigr.com/images/logo.png" }
  },
  "mainEntityOfPage": { "@type": "WebPage", "@id": "https://baigr.com/blog/<slug>/" }
}
```
Use `"author"` as a named person only if a real author exists; otherwise the
Organization is honest. Set both dates; update `dateModified` on every refresh
(freshness is an AEO signal).

### FAQPage (whenever the article has an FAQ — it should)
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "<question exactly as in the article>",
      "acceptedAnswer": { "@type": "Answer", "text": "<the concise 2–4 sentence answer>" }
    }
  ]
}
```
The schema questions/answers must **match the visible on-page FAQ** — never add
schema-only Q&A (that violates Google's structured-data policy).

### HowTo (only for genuine step-by-step tutorials)
```json
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "<task>",
  "step": [
    { "@type": "HowToStep", "name": "<step title>", "text": "<step detail>" }
  ]
}
```

### BreadcrumbList (nice-to-have)
Home → Blog → Article, to reinforce the subfolder hierarchy and earn breadcrumb
rich results.

## Combining schema
Deliver multiple types as an array (or a `@graph`) in one `<script type="application/ld+json">`
block so the user pastes once:
```html
<script type="application/ld+json">
[ { ...BlogPosting... }, { ...FAQPage... } ]
</script>
```

## Final publish checklist to hand the user
1. New post → paste the HTML body.
2. Fill SEO title, meta description, slug, focus keyword, tags/category.
3. Set the featured image (hero) + its alt text.
4. Add a Custom HTML block with the JSON-LD (or paste into the SEO plugin).
5. Review for accuracy and tone, then Publish.
6. In Search Console → URL Inspection → request indexing for the new post URL.
