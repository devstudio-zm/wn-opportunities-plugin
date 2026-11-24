<?php

namespace ImpulseTechnologies\Opportunities\Models;

use ImpulseTechnologies\Opportunities\Traits\Opportunity;
use Winter\Storm\Database\Model;

/**
 * Vacancy - a job opening: the role, where and on what terms, by when to
 * apply, how to apply (link, email or instructions) and any documents.
 */
class Vacancy extends Model
{
    use Opportunity;

    public $table = 'impulsetechnologies_opportunities_vacancies';

    public $slugs = ['slug' => 'title'];

    public $attachMany = [
        'documents' => [\System\Models\File::class, 'public' => true, 'order' => 'sort_order'],
    ];

    protected $casts = ['is_published' => 'boolean'];

    protected $dates = ['closing_at', 'published_at'];

    public $rules = [
        'title' => 'required|max:255',
        'slug' => 'required|max:255|unique:impulsetechnologies_opportunities_vacancies',
        'employment_type' => 'nullable|in:full-time,part-time,contract,internship,attachment,volunteer',
        'apply_email' => 'nullable|email',
        'apply_url' => 'nullable|url',
        'closing_at' => 'nullable|date',
    ];

    protected $fillable = ['title', 'slug', 'reference', 'department', 'location', 'employment_type', 'salary', 'summary', 'description', 'how_to_apply', 'apply_url', 'apply_email', 'closing_at', 'published_at', 'is_published'];

    protected $nullable = ['reference', 'department', 'location', 'employment_type', 'salary', 'summary', 'description', 'how_to_apply', 'apply_url', 'apply_email', 'closing_at', 'published_at'];

    public function getEmploymentTypeOptions(): array
    {
        return trans('impulsetechnologies.opportunities::lang.employment_types');
    }

    public function getEmploymentTypeLabelAttribute(): ?string
    {
        return $this->employment_type ? ($this->getEmploymentTypeOptions()[$this->employment_type] ?? $this->employment_type) : null;
    }
}
