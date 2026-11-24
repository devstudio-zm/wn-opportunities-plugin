<?php

namespace ImpulseTechnologies\Opportunities\Components;

use ImpulseTechnologies\Opportunities\Models\Vacancy;

class VacancyList extends OpportunityList
{
    public function componentDetails(): array
    {
        return ['name' => 'Vacancy list', 'description' => 'Lists published vacancies: open, closed or both, with an optional employment type filter.'];
    }

    protected function modelClass(): string
    {
        return Vacancy::class;
    }

    protected function filterColumn(): ?string
    {
        return 'employment_type';
    }

    protected function filterOptions(): array
    {
        return (new Vacancy)->getEmploymentTypeOptions();
    }
}
