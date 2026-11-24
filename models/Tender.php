<?php

namespace ImpulseTechnologies\Opportunities\Models;

use ImpulseTechnologies\Opportunities\Traits\Opportunity;
use Winter\Storm\Database\Model;

/**
 * Tender - a procurement notice: what is being bought, by when bids close,
 * how to submit, and the bidding documents.
 */
class Tender extends Model
{
    use Opportunity;

    public $table = 'impulsetechnologies_opportunities_tenders';

    const CATEGORIES = ['goods' => 'Goods', 'works' => 'Works', 'services' => 'Services', 'consultancy' => 'Consultancy', 'disposal' => 'Disposal of assets', 'other' => 'Other'];

    public $slugs = ['slug' => 'title'];

    public $attachMany = [
        'documents' => [\System\Models\File::class, 'public' => true, 'order' => 'sort_order'],
    ];

    protected $casts = ['is_published' => 'boolean'];

    protected $dates = ['closing_at', 'published_at'];

    public $rules = [
        'title' => 'required|max:255',
        'slug' => 'required|max:255|unique:impulsetechnologies_opportunities_tenders',
        'contact_email' => 'nullable|email',
        'closing_at' => 'nullable|date',
    ];

    protected $fillable = ['title', 'slug', 'reference', 'category', 'summary', 'description', 'submission_details', 'contact_email', 'closing_at', 'published_at', 'is_published'];

    protected $nullable = ['reference', 'category', 'summary', 'description', 'submission_details', 'contact_email', 'closing_at', 'published_at'];

    public function getCategoryOptions(): array
    {
        return self::CATEGORIES;
    }

    public function getCategoryLabelAttribute(): ?string
    {
        return $this->category ? (self::CATEGORIES[$this->category] ?? $this->category) : null;
    }
}
