<?php

return [
    'plugin' => [
        'name' => 'Opportunities',
        'description' => 'Tenders and job vacancies with closing dates and documents.',
    ],
    'permissions' => [
        'manage_tenders' => 'Manage tenders',
        'manage_vacancies' => 'Manage vacancies',
    ],
    'tenders' => [
        'menu' => 'Tenders',
        'singular' => 'Tender',
        'plural' => 'Tenders',
    ],
    'vacancies' => [
        'menu' => 'Vacancies',
        'singular' => 'Vacancy',
        'plural' => 'Vacancies',
    ],
    'status' => [
        'open' => 'Open',
        'closed' => 'Closed',
        'draft' => 'Not published',
    ],
    'employment_types' => [
        'full-time' => 'Full time',
        'part-time' => 'Part time',
        'contract' => 'Contract',
        'internship' => 'Internship',
        'attachment' => 'Attachment',
        'volunteer' => 'Volunteer',
    ],
];
