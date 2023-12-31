<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class DashboardController extends BaseController
{
    protected string $actionsPath = 'App\Actions\Dashboard';
    protected string $requestsPath = 'App\Http\Requests\Dashboard';
}
