<?php

namespace ImpulseTechnologies\Opportunities\Components;

use ImpulseTechnologies\Opportunities\Models\Vacancy;

class VacancyDetail extends OpportunityDetail
{
    public function componentDetails(): array
    {
        return ['name' => 'Vacancy detail', 'description' => 'Shows one published vacancy, found by the slug in the URL.'];
    }

    protected function modelClass(): string
    {
        return Vacancy::class;
    }
}
