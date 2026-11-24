<?php

namespace ImpulseTechnologies\Opportunities\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

/**
 * Tenders backend: list, create and update.
 */
class Tenders extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = ['impulsetechnologies.opportunities.manage_tenders'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('ImpulseTechnologies.Opportunities', 'opportunities', 'tenders');
    }
}
