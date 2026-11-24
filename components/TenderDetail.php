<?php

namespace ImpulseTechnologies\Opportunities\Components;

use ImpulseTechnologies\Opportunities\Models\Tender;

class TenderDetail extends OpportunityDetail
{
    public function componentDetails(): array
    {
        return ['name' => 'Tender detail', 'description' => 'Shows one published tender, found by the slug in the URL.'];
    }

    protected function modelClass(): string
    {
        return Tender::class;
    }
}
