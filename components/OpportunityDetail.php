<?php

namespace ImpulseTechnologies\Opportunities\Components;

use Cms\Classes\ComponentBase;

/**
 * Shared detail behaviour for TenderDetail and VacancyDetail: the published
 * record whose slug is in the URL, open or closed (so old links still work
 * and show "Closed"); a 404 otherwise. Sets the page title to the record's.
 */
abstract class OpportunityDetail extends ComponentBase
{
    /** @var \Winter\Storm\Database\Model|null */
    public $record;

    abstract protected function modelClass(): string;

    public function defineProperties(): array
    {
        return [
            'slug' => [
                'title' => 'Slug',
                'type' => 'string',
                'default' => '{{ :slug }}',
            ],
            'loadAssets' => [
                'title' => 'Load default styles',
                'description' => 'Turn off when the theme styles the markup or overrides the partials',
                'type' => 'checkbox',
                'default' => true,
            ],
        ];
    }

    public function onRun()
    {
        if ($this->property('loadAssets')) {
            $this->addCss('/plugins/impulsetechnologies/opportunities/assets/css/opportunities.css');
        }

        $this->record = $this->loadRecord();
        if (!$this->record) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        $this->page->title = $this->record->title;
        if ($this->record->summary) {
            $this->page->description = $this->record->summary;
        }
    }

    public function loadRecord()
    {
        $class = $this->modelClass();

        return $class::isPublished()->with('documents')->where('slug', (string) $this->property('slug'))->first();
    }
}
