<?php

namespace ImpulseTechnologies\Opportunities\Traits;

use Carbon\Carbon;
use Cms\Classes\Page;

/**
 * What tenders and vacancies share: publishing, a closing date, documents and
 * a front-end URL. A record is visible when published and its publish date
 * (if any) has passed; it is open until its closing date and time. Each model
 * declares its own $slugs, $attachMany['documents'], $casts and $dates.
 */
trait Opportunity
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\Sluggable;
    use \Winter\Storm\Database\Traits\Nullable;

    /** @var string|null Front-end URL, set by the components. */
    public $url;

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', Carbon::now()));
    }

    /** Open: no closing date, or it has not passed. */
    public function scopeIsOpen($query)
    {
        return $query->where(fn ($q) => $q->whereNull('closing_at')->orWhere('closing_at', '>=', Carbon::now()));
    }

    public function scopeIsClosed($query)
    {
        return $query->whereNotNull('closing_at')->where('closing_at', '<', Carbon::now());
    }

    public function getIsOpenAttribute(): bool
    {
        return !$this->closing_at || $this->closing_at->gte(Carbon::now());
    }

    /** "open", "closed" or "draft"; for labels and CSS hooks. */
    public function getStatusAttribute(): string
    {
        if (!$this->is_published) {
            return 'draft';
        }

        return $this->is_open ? 'open' : 'closed';
    }

    public function getStatusLabelAttribute(): string
    {
        return trans('impulsetechnologies.opportunities::lang.status.' . $this->status);
    }

    /** Whole days left until closing, or null when closed or undated. */
    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->closing_at || !$this->is_open) {
            return null;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($this->closing_at->copy()->startOfDay());
    }

    public function setUrl(string $pageName, $controller, string $param = 'slug'): string
    {
        return $this->url = $controller->pageUrl($pageName, [$param => $this->slug]) ?: Page::url($pageName, [$param => $this->slug]);
    }
}
