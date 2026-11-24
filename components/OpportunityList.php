<?php

namespace ImpulseTechnologies\Opportunities\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Support\Facades\Request;

/**
 * Shared list behaviour for TenderList and VacancyList: open, closed or all
 * published records, an optional ?category= / ?type= filter from the query
 * string, pagination with ?page=, and links to a detail page.
 */
abstract class OpportunityList extends ComponentBase
{
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection */
    public $records;

    /** @var string open, closed or all */
    public $status;

    /** @var array<string, string> Filter choices (key => label) that have records. */
    public $filters = [];

    /** @var string|null The filter in use. */
    public $filter;

    abstract protected function modelClass(): string;

    /** Column the query-string filter applies to, or null for none. */
    abstract protected function filterColumn(): ?string;

    /** All filter choices, key => label. */
    abstract protected function filterOptions(): array;

    public function defineProperties(): array
    {
        return [
            'status' => [
                'title' => 'Show',
                'type' => 'dropdown',
                'default' => 'open',
                'options' => ['open' => 'Open only', 'closed' => 'Closed only', 'all' => 'All (open first)'],
            ],
            'perPage' => [
                'title' => 'Per page',
                'description' => '0 lists everything',
                'type' => 'string',
                'default' => '10',
                'validationPattern' => '^[0-9]+$',
            ],
            'detailPage' => [
                'title' => 'Detail page',
                'type' => 'dropdown',
                'default' => '',
            ],
            'queryFilter' => [
                'title' => 'Filter from the query string',
                'description' => 'Read ?filter= and ?page= from the URL',
                'type' => 'checkbox',
                'default' => true,
            ],
            'loadAssets' => [
                'title' => 'Load default styles',
                'description' => 'Turn off when the theme styles the markup or overrides the partials',
                'type' => 'checkbox',
                'default' => true,
            ],
        ];
    }

    public function getDetailPageOptions(): array
    {
        return ['' => '- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        if ($this->property('loadAssets')) {
            $this->addCss('/plugins/impulsetechnologies/opportunities/assets/css/opportunities.css');
        }
        $this->prepareVars();
    }

    public function prepareVars(): void
    {
        $this->status = in_array($this->property('status'), ['open', 'closed', 'all'], true) ? $this->property('status') : 'open';
        $useQuery = (bool) $this->property('queryFilter');

        $column = $this->filterColumn();
        if ($column) {
            $present = $this->baseQuery()->whereNotNull($column)->distinct()->pluck($column)->all();
            $this->filters = array_intersect_key($this->filterOptions(), array_flip($present));
            $filter = $useQuery ? Request::input('filter') : null;
            $this->filter = is_string($filter) && isset($this->filters[$filter]) ? $filter : null;
        }

        $query = $this->baseQuery()->with('documents')
            ->when($this->filter, fn ($q) => $q->where($column, $this->filter));

        // Open first, soonest closing (undated "until filled" last); then closed, newest first.
        $now = now();
        $query->orderByRaw('case when closing_at is null or closing_at >= ? then 0 else 1 end', [$now])
            ->orderByRaw('case when closing_at is null then 1 else 0 end')
            ->orderByRaw('case when closing_at >= ? then closing_at end asc', [$now])
            ->orderBy('closing_at', 'desc');
        $query->orderBy('id', 'desc');

        $perPage = (int) $this->property('perPage');
        if ($perPage > 0) {
            $page = $useQuery ? max(1, (int) Request::input('page', 1)) : 1;
            $this->records = $query->paginate($perPage, ['*'], 'page', $page)
                ->withPath(Request::url())
                ->appends(array_filter(['filter' => $this->filter]));
        } else {
            $this->records = $query->get();
        }

        if ($detailPage = $this->property('detailPage')) {
            foreach ($this->records as $record) {
                $record->setUrl($detailPage, $this->controller);
            }
        }
    }

    protected function baseQuery()
    {
        $class = $this->modelClass();
        $query = $class::isPublished();

        return match ($this->status) {
            'open' => $query->isOpen(),
            'closed' => $query->isClosed(),
            default => $query,
        };
    }
}
