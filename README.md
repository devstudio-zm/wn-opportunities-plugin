# Opportunities for Winter CMS

Publish **tenders** (procurement notices) and **job vacancies** with closing dates, documents and application details. It works with any theme.

- **Backend.** An *Opportunities* menu holds Tenders and Vacancies.
  - Each screen has a list with search and filters (open or closed, category or type, unpublished), plus a tabbed form.
  - Each screen has its own permission: *Manage tenders* and *Manage vacancies*.
- **Closing dates.** Records show as **Open** until their closing date and time, then **Closed**.
  - Records with no closing date stay open ("Open until filled").
  - Use *Publish from* to schedule a record.
- **Documents.** Upload bidding documents, terms of reference, addenda or job descriptions. The file caption is the label shown on the site.
- **Four components** with plain, accessible markup: `tenderList`, `tenderDetail`, `vacancyList` and `vacancyDetail`.

## Install

Add the repository to the site's `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/devstudio-zm/wn-opportunities-plugin" }
]
```

Then:

```sh
composer require impulsetechnologies/wn-opportunities-plugin
php artisan winter:up
```

The package installs to `plugins/impulsetechnologies/opportunities`. It needs Winter CMS 1.2 and PHP 8.0 or later.

## Pages

A tenders page and a tender detail page:

```ini
title = "Tenders"
url = "/tenders"

[tenderList]
status = "open"          ; open | closed | all (open first)
perPage = 10             ; 0 lists everything
detailPage = "tender"    ; the CMS page below
queryFilter = 1          ; read ?filter=<category> and ?page= from the URL
loadAssets = 1           ; add the default stylesheet
==
{% component 'tenderList' %}
```

```ini
title = "Tender"
url = "/tenders/:slug"

[tenderDetail]
slug = "{{ :slug }}"
==
{% component 'tenderDetail' %}
```

Vacancies work the same way with `vacancyList` and `vacancyDetail`. On vacancy lists, `?filter=` takes an employment type such as `full-time` or `internship`.

How the components behave:

- **Detail pages** show published records whether open or closed, so old links keep working and show *Closed*.
- **Unknown or unpublished slugs** return the theme's 404 page.
- **Page title and description** are set from the record's title and summary.
- **Sorting:** open records come first, soonest closing first, with undated records last. Closed records follow, newest first.

## Fitting it to a theme

The components work in three ways, from least to most effort:

1. **Default styles.** Leave `loadAssets` on. The stylesheet (`assets/css/opportunities.css`) inherits the theme's fonts and colours, and its colours are CSS custom properties you can override:

   ```css
   .opp { --opp-accent: #0a5; --opp-rule: #ddd; --opp-open: #0a5; --opp-radius: 0; }
   ```

2. **Your own CSS.** Set `loadAssets = 0` and style the `opp-*` classes: `opp-items`, `opp-item--open`, `opp-status--closed`, `opp-facts`, `opp-documents__link` and so on.

3. **Override the markup.** Copy a component's partials into the theme at `partials/<component alias>/`. For example, `partials/tenderList/default.htm` and `item.htm`.

   Each partial lists the variables it uses:

   | Component | Variables |
   |---|---|
   | Lists | `__SELF__.records` (a paginator, or a collection when `perPage = 0`), `__SELF__.status`, `__SELF__.filters` (key → label, only choices with records), `__SELF__.filter`, and `record.url` on each record when `detailPage` is set |
   | Details | `__SELF__.record` |

   Each record also provides:

   | Records | Attributes |
   |---|---|
   | Both | `status` (`open`/`closed`/`draft`), `status_label`, `is_open`, `days_left`, `closing_at`, `documents` |
   | Tenders | `category_label` |
   | Vacancies | `employment_type_label` |

## Tests

```sh
cd plugins/impulsetechnologies/opportunities
../../../vendor/bin/phpunit
```

## Licence

MIT, © Impulse Technologies.
