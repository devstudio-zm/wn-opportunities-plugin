<?php

namespace ImpulseTechnologies\Opportunities\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

/**
 * Vacancies backend: list, create and update.
 */
class Vacancies extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = ['impulsetechnologies.opportunities.manage_vacancies'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('ImpulseTechnologies.Opportunities', 'opportunities', 'vacancies');
    }
}
