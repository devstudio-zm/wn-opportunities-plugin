<?php

namespace ImpulseTechnologies\Opportunities\Components;

use ImpulseTechnologies\Opportunities\Models\Tender;

class TenderList extends OpportunityList
{
    public function componentDetails(): array
    {
        return ['name' => 'Tender list', 'description' => 'Lists published tenders: open, closed or both, with an optional category filter.'];
    }

    protected function modelClass(): string
    {
        return Tender::class;
    }

    protected function filterColumn(): ?string
    {
        return 'category';
    }

    protected function filterOptions(): array
    {
        return Tender::CATEGORIES;
    }
}
