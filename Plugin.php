<?php

namespace ImpulseTechnologies\Opportunities;

use Backend\Facades\Backend;
use System\Classes\PluginBase;

/**
 * Opportunities - tenders and job vacancies.
 *
 * Editors publish tenders (procurement notices) and vacancies with a closing
 * date, a description and documents. Four components list and show them;
 * their default markup is plain semantic HTML with `opp-` class names and an
 * optional stylesheet, so any theme can use them as they are or override the
 * partials (partials/<component alias>/default.htm).
 */
class Plugin extends PluginBase
{
    public function pluginDetails(): array
    {
        return [
            'name' => 'impulsetechnologies.opportunities::lang.plugin.name',
            'description' => 'impulsetechnologies.opportunities::lang.plugin.description',
            'author' => 'Impulse Technologies',
            'icon' => 'icon-briefcase',
            'homepage' => 'https://github.com/devstudio-zm/wn-opportunities-plugin',
        ];
    }

    public function registerComponents(): array
    {
        return [
            Components\TenderList::class => 'tenderList',
            Components\TenderDetail::class => 'tenderDetail',
            Components\VacancyList::class => 'vacancyList',
            Components\VacancyDetail::class => 'vacancyDetail',
        ];
    }

    public function registerPermissions(): array
    {
        return [
            'impulsetechnologies.opportunities.manage_tenders' => [
                'tab' => 'impulsetechnologies.opportunities::lang.plugin.name',
                'label' => 'impulsetechnologies.opportunities::lang.permissions.manage_tenders',
            ],
            'impulsetechnologies.opportunities.manage_vacancies' => [
                'tab' => 'impulsetechnologies.opportunities::lang.plugin.name',
                'label' => 'impulsetechnologies.opportunities::lang.permissions.manage_vacancies',
            ],
        ];
    }

    public function registerNavigation(): array
    {
        return [
            'opportunities' => [
                'label' => 'impulsetechnologies.opportunities::lang.plugin.name',
                'url' => Backend::url('impulsetechnologies/opportunities/tenders'),
                'icon' => 'icon-briefcase',
                'permissions' => ['impulsetechnologies.opportunities.*'],
                'order' => 520,
                'sideMenu' => [
                    'tenders' => [
                        'label' => 'impulsetechnologies.opportunities::lang.tenders.menu',
                        'icon' => 'icon-gavel',
                        'url' => Backend::url('impulsetechnologies/opportunities/tenders'),
                        'permissions' => ['impulsetechnologies.opportunities.manage_tenders'],
                    ],
                    'vacancies' => [
                        'label' => 'impulsetechnologies.opportunities::lang.vacancies.menu',
                        'icon' => 'icon-id-badge',
                        'url' => Backend::url('impulsetechnologies/opportunities/vacancies'),
                        'permissions' => ['impulsetechnologies.opportunities.manage_vacancies'],
                    ],
                ],
            ],
        ];
    }
}
