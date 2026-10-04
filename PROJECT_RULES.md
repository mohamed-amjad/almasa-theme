# ALMASA — Project Rules

Source of truth for **شركة الماسة للمقاولات العامة والتوريدات العمومية**  
(Almasa General Contracting & General Supplies).

Theme slug: `almasa`  
Theme author (theme headers only, **never on the public site**): MohamedAmjad — mohamed-amjad.com

Every implementation must follow this file. Do not invent facts, statistics, emails, social accounts, coordinates, or service copy.

---

## 1. Architecture

| Layer | Responsibility |
| --- | --- |
| **WordPress** | Pages, menus, media, Site Logo, CPT entries |
| **Custom theme `almasa`** | HTML shell, Elementor locations, CPT/ACF registration, tokens, a11y, performance |
| **Elementor** | Visual layout, global colors/type, header/footer/theme builder, all normal visible copy and imagery |
| **ACF + WP meta** | Structured/repeatable data (projects, services, locations, contacts, galleries) |
| **Custom CSS/JS** | Motion, advanced layout, RTL polish — never primary content |

**RTL-first.** Locale is Arabic. `dir="rtl"` comes from WordPress language. Do not ship an LTR layout and “flip” it.

**Future English:** keep strings in templates translatable (`almasa` text domain). Do not add English site content now.

---

## 2. Elementor vs ACF

### Elementor owns

Headings, paragraphs, buttons, images, backgrounds, overlays, CTAs, section content, header/footer chrome, landing pages, spacing, typography, colors, visual labels.

### ACF / structured WP owns

- Project metadata, galleries, featured flag, order  
- Service titles (CPT)  
- Locations  
- Contact people + phone numbers  
- Other repeatable structured records  

Do **not** use ACF to replace Elementor text widgets for marketing copy.

### Non-negotiable

Do not hardcode visible marketing content, logos, image URLs, nav labels, phones, or locations in PHP/JS/CSS.

Theme **fallback** templates may print dynamic fields (`the_title()`, `get_post_meta()`, ACF) so the site works before Theme Builder templates exist. That is data output, not hardcoded copy.

---

## 3. WordPress / theme stack

- WordPress + PHP 8.3  
- Theme supports: `title-tag`, `post-thumbnails`, `custom-logo`, `html5`, menus, Elementor locations (`header`, `footer`, `single`, `archive`)  
- Header/footer: `almasa_render_site_part()` (`inc/elementor.php`) prints the Elementor template chosen in Customizer → **الهيدر والفوتر** (theme_mods `almasa_header_template` / `almasa_footer_template`); falls back to `template-parts/header|footer/*.php` when none is chosen or Elementor is off. Skipped while editing an `elementor_library` template.  
- Front page and pages: when built with Elementor (`almasa_uses_elementor_layout()`), only `the_content()` is printed; otherwise the PHP section map in `page.php` / `front-page.php` is the fallback  

### File map

```text
wp-content/themes/almasa/
  style.css          functions.php       screenshot.png
  index.php          front-page.php      page.php
  single.php         archive.php         404.php
  header.php         footer.php
  single-project.php archive-project.php
  inc/               template-parts/     assets/     acf-json/     languages/
  inc/elementor/     class-almasa-widget.php · widgets-sections.php · widgets-layout.php
```

Root `PROJECT_RULES.md` is canonical; the theme copy is for deploy/packaging.

---

## 4. Design system (tokens)

Defined in `assets/css/tokens.css` as CSS variables. Map the same values into Elementor Site Settings (global colors / typography) in a later phase — do not scatter magic numbers.

### Color

| Token | Value | Role |
| --- | --- | --- |
| `--almasa-gold` | `#C9A227` | Primary / brand metal |
| `--almasa-gold-bright` | `#E0C36A` | Hover / highlight |
| `--almasa-navy` | `#011B3D` | **Site brand color** — dark surfaces, header, footer, badges (client-specified) |
| `--almasa-navy-2` | `#072652` | Raised surface on navy |
| `--almasa-navy-deep` | `#00112A` | Deepest navy (patterns, map placeholders) |
| `--almasa-paper` | `#F4F1EA` | Light surfaces |
| `--almasa-text` | `#1C1C1C` | Body on light |
| `--almasa-text-on-dark` | `#F4F1EA` | Body on dark |
| `--almasa-muted` | `#6E6A62` | Secondary text |
| `--almasa-border` | `#D9D2C5` | Lines / hairlines |
| `--almasa-danger` | `#8B1E1E` | Errors only |

Avoid heavy gradients, glassmorphism, oversized radius, and decorative clutter. Visual language: architecture, structure, concrete, steel, editorial photography. Subtle grids and hairlines only.

### Typography

- **Display / headings:** Cairo (700 / 600)  
- **Body / UI:** IBM Plex Sans Arabic (400 / 500)  
- `font-display: swap`  
- Do not use ultra-thin weights  
- Arabic line-height ~1.7 body, ~1.25–1.35 display  

Scale (fluid, clamp on later CSS): Display, H1, H2, H3, Body, Small, Button, Nav.

### Spacing (4px base)

`--space-1` 4px … `--space-2` 8px, `3` 12px, `4` 16px, `5` 24px, `6` 32px, `7` 48px, `8` 64px, `9` 96px, `10` 128px.

Section padding should use this scale via Elementor or tokens — no random spacers.

### Layout

- Container max: **1280px** (`--almasa-container`)  
- Content measure: ~65ch for long Arabic paragraphs  
- Radius: **0–2px** (almost square)  
- Shadow: none or a single hairline; no floating cards  

### Breakpoints

| Name | Width |
| --- | --- |
| xs | 360px |
| sm | 390px / 430px |
| md | 768px |
| lg | 1024px |
| xl | 1280px |
| xxl | 1440px |
| wall | 1920px |

Mobile is a first-class layout, not a shrunk desktop.

---

## 5. Components (architecture only)

Plan these as Elementor structures + optional theme classes (`almasa-*`):

- Header (default / scrolled / mobile)  
- Hero (imagery + type + CTA — content from Elementor)  
- Section header (kicker + title + rule)  
- Service item (number + title)  
- Project teaser (image, title, work type, context)  
- Location block  
- Contact pair (`tel:` links)  
- Footer columns  
- Buttons: solid gold, outline, ghost on dark  

Hover: image scale via `transform`, 200–400ms `opacity`/`transform` only.

---

## 6. Motion

- Premium, slow, structural (reveal, stagger, clip-path, slight ken-burns)  
- Animate `transform` and `opacity` only  
- Honor `prefers-reduced-motion: reduce` (disable non-essential motion)  
- JS does not inject marketing copy  

---

## 7. Content facts (do not expand)

**Company:** شركة الماسة للمقاولات العامة والتوريدات العمومية  
**Offer:** أعمال إنشاءات متكاملة من الخرسانات حتى التشطيب  

**Services (titles only):** أعمال الإنشاءات · أعمال الخرسانات المسلحة · أعمال التشطيبات · أعمال المقاولات المتكاملة  

**Locations**

1. محافظة كفر الشيخ — أبراج الجبالي  
2. محافظة مطروح — العلمين — شارع الغزالة — بجوار فرع فودافون  

**Contacts** (`tel:`)

- المهندس / محمد عبدالمطلب — 01068309062  
- المهندس / عثمان عماد يوسف — 01065084349  

No email. Social links are **editable only** (Customizer → السوشيال ميديا, `inc/social.php`); until any URL is saved, all networks show as non-clickable placeholder icons; once URLs are saved only those networks render, as links — never fill them with guesses. Never invent coordinates — maps on the contact page are Google embeds built from the location title + `address`, or from the optional `map_query` field when an editor fills it.

**About page copy** (vision, mission, "why choose us" items) was seeded as editable draft copy grounded in the facts above; the client may rewrite it from the dashboard. Do not add claims, numbers, or years.

**Projects**

| Title | Work | Context | Media |
| --- | --- | --- | --- |
| محلات تجارية بالحي اللاتيني (was «جامعة العلمين»; renamed per client) | أعمال تشطيبات | مشروع خاص بشركة ريدكون للتعمير | `uploads/2026/09/1` |
| الحي اللاتيني بالعلمين | أعمال تشطيبات | شركة ريدكون، جاما، أوراسكوم | `uploads/2026/09/3` — photos cropped out of the client's branded posters (originals kept in `3/originals/`) |
| تشطيب الفيلات بقرية سول (بجوار رأس الحكمة; was «قرية سول») | أعمال خرسانات مسلحة وتشطيبات | مشروع خاص بشركة ريدكون للتعمير | `uploads/2026/09/2` (client confirmed these 38 photos are Sol, not the Latin Quarter) |

---

## 8. CPT / ACF

### `project`

- Public, archive, REST, Elementor-friendly  
- Rewrite slug: `projects`  
- Supports: title, editor, thumbnail, excerpt, custom-fields  
- Archive: `/projects/`  
- Single: `/projects/{slug}/`  

**Fields (meta keys)**

| Key | Type | Notes |
| --- | --- | --- |
| `work_type` | text | Required when known |
| `associated_company` | text | Context / companies as provided |
| `project_location` | text | Only if confirmed — do not invent |
| `project_subtitle` | text | Optional |
| `featured_project` | true/false | |
| `project_order` | number | Lower first |
| `project_gallery` | attachment ID array | Native metabox; ACF Gallery if Pro |

Description: WordPress editor and/or ACF textarea `project_description` — seed only given context, not filler.

### `service`

Title + `menu_order` + featured image (card background). Optional ACF `service_summary` left empty until real copy exists.

### Image sources (no image URLs in code)

| Where | Source |
| --- | --- |
| Home hero slideshow | Media library → attachment field **عرض في الهيرو** (meta `almasa_hero`), ordered by **ترتيب الهيرو** (`menu_order`). Falls back to project featured images. |
| Service cards | Service featured image |
| Project cards / single hero | Project featured image |
| Gallery (home, مشاريعنا) | Gallery widget **tabs** (Elementor repeater: tab name + images; each tab = a filter button). Defaults to one tab per project from its `project_gallery`, until edited |
| Gallery (archive, single) | `project_gallery` of each project — posters, collages, banners, logos stay out |
| Inner page hero | Page featured image (من نحن، خدماتنا، مشاريعنا، تواصل معنا) |
| "Why choose us" image | "خدماتنا" featured image |
| About section | "من نحن" featured image + first project featured image |
| Contact CTA background | "تواصل معنا" featured image ("الرئيسية" featured image on the contact page itself) |

### `almasa_location`

Title = place name; ACF `address` textarea; optional `map_query` (place name / coordinates copied from Google Maps for an exact pin).

### `almasa_contact`

Title = person name; ACF `phone` (stored digits, output `tel:`).

### `almasa_reason` (لماذا تختارنا)

Not public. Title + excerpt, ordered by `menu_order`. Rendered on "من نحن".

### About page fields

ACF group on the "من نحن" page: `about_vision`, `about_mission` (textarea). Empty fields hide their card.

### `almasa_request` (الطلبات — contact form inbox)

Not public; created only by the contact form (`inc/contact-form.php`), never from the dashboard. Meta: `request_name`, `request_phone`, `request_email`, `request_service`, `request_page`, `_almasa_unread`. List columns + read-only details metabox; unread count bubble on the menu. Each request also triggers `wp_mail()` to `admin_email` (needs working SMTP in production).

Form: nonce + honeypot (`company_site`) + 30s per-IP throttle. Validation rules and Arabic messages live in `almasa_contact_messages()` / `ALMASA_PHONE_PATTERN` and are passed to JS via `wp_localize_script`, so client and server always agree. Works without JS (`admin-post.php` redirect with `?almasa_form=sent|error`).

ACF JSON lives in `acf-json/`. Local field groups in `inc/acf.php`.

**ACF Free:** no Repeater, Gallery, or Options page. Repeatable data = CPTs. Gallery = `project_gallery` metabox (same key if Pro is added later).

---

## 9. Homepage information architecture (later)

Header (fixed, transparent → solid on scroll) → Hero slideshow ("مرحبًا بكم في" eyebrow + company name + CTAs; no bottom bar / scroll cue) → Services marquee → About (images + figures derived from CPT counts + engineers; logo badge on brand navy) → Services **slider** → Projects **slider** (equal 4:3 cards) → Photo gallery (title, filters under it, equal 4:3 tiles, lightbox) → Locations (embedded maps) → Contact CTA → Footer  

Nav (WP menu, not hardcoded): الرئيسية، من نحن، خدماتنا، مشاريعنا، تواصل معنا  

Inner pages (`page.php` maps page title → sections):

| Page | Sections |
| --- | --- |
| من نحن | intro → vision & mission → why choose us → marquee → services slider → contact CTA |
| خدماتنا | services **grid** (2 × 2) → contact CTA (no projects) |
| مشاريعنا | projects grid → gallery → contact CTA |
| تواصل معنا | engineers' phones + social icons (same height as the form) + request form → locations with embedded maps |

There is no separate locations page; locations live on تواصل معنا (maps) and the homepage/footer.

Sliders: native CSS scroll-snap track + `almasa_slider_controls()` (arrows loop, progress bar, autoplay paused on hover/focus/touch/hidden tab/out of view; controls hide when everything fits).  

Section markup lives in theme templates (`template-parts/sections/*`); pages place it through the Almasa Elementor widgets (§ 9a).

---

## 9a. Elementor editing (Free — no Pro)

**Widgets** (category **الماسة**, `inc/elementor/`): one widget per section — hero, page-hero, marquee, intro, services, projects, gallery, locations, contact, contact-form, values, why, header, footer. Each extends `Almasa_Widget` and renders the existing template part via `get_template_part( $template, null, $args )` — one markup source for widget and fallback.

Arg semantics (`almasa_arg()` in `inc/helpers.php`):

- Key **absent** → template's own default (dynamic data / previous copy).  
- Key **present but empty** → element hidden.  
- Controls are **prefilled with the live values** (control `default`): site name/tagline, page title and featured image (`almasa_context_post_id()`), section images, hero slides, page permalinks, vision/mission ACF. Untouched controls keep following the dashboard; clearing a dynamic field returns to the dashboard value, and a separate "إظهار" toggle hides it.  
- Empty link controls fall back to the matching page permalink (`almasa_arg_url()`).  
- Widgets fed by CPTs show a read-only list of the current records with an edit link (`data_note()`).  
- Gallery widget: repeater `tabs` (`tab_title`, `tab_images`) → template arg `tabs` ({ key = repeater `_id`, title, ids }) → `almasa_interleave_gallery_tabs()`. Tabs without a name or images are skipped. Each page's gallery widget has its own tabs.  
- Attachment URLs encode spaces (`inc/media.php`) — some seeded file names contain spaces, which break CSS `url()` previews and `srcset`.  

Structured data (projects, services, gallery, locations, contacts, reasons, vision/mission) always comes from the dashboard CPTs/ACF — widgets only control copy, limits, layout, toggles, and style (colors, typography, padding, backgrounds).

**Pages** (built in Elementor, one full-width container per section):

| Page | Widgets |
| --- | --- |
| الرئيسية | hero → marquee → intro → services → projects → gallery → locations → contact |
| من نحن | page-hero → intro → values → why → marquee → services → contact |
| خدماتنا | page-hero → services (grid) → contact |
| مشاريعنا | page-hero → projects (grid) → gallery → contact |
| تواصل معنا | page-hero → contact-form → locations |

A page whose first widget is hero/page-hero gets body class `almasa-has-hero` (transparent header).

**Header / footer:** saved templates (type *section*) "هيدر الموقع" / "فوتر الموقع" containing the header/footer widgets; selected in Customizer → الهيدر والفوتر.

**Kit (Site Settings):** global colors and fonts mirror § 4 tokens; content width 1280. Elementor default color/typography schemes are disabled so theme CSS wins.

**JS:** `assets/js/theme.js` `init(scope)` is idempotent and re-runs on `frontend/element_ready/widget`, so sliders, reveal, gallery filters, lightbox, and the form work inside the editor.

**Not in Elementor:** single project pages and the project archive stay PHP templates (Theme Builder single/archive requires Pro).

---

## 10. Performance, a11y, SEO, security

- Lazy-load images; prefer modern formats when exporting  
- Minimal JS; no unused libraries  
- Semantic headings; one H1 per view  
- Alt text from media library  
- Keyboard + visible focus; contrast on gold/navy  
- Document title via `title-tag`; Open Graph later via Yoast/Rank Math only if requested  
- Escape all output (`esc_html`, `esc_attr`, `esc_url`). Capability checks on admin metaboxes. No secrets in the theme.  

---

## 11. Coding standards

- Prefix: `almasa_`  
- Text domain: `almasa`  
- WordPress PHP standards  
- Inspect files before editing; no drive-by rewrites  
- Theme author URI stays in `style.css` only  

---

## 12. Workflow

Phases 1–6 (this delivery): rules, theme shell, tokens, CPT/ACF, Elementor compatibility.  

Do not start homepage visual design (phase 7+) until asked.

---

## 13. QA checklist (when building UI)

Desktop 1920 / 1440 / 1366 / 1280 · Tablet 1024 / 768 · Mobile 430 / 390 / 375 / 360  
RTL overflow, Elementor editability, ACF on singles, `tel:` links, reduced motion.
