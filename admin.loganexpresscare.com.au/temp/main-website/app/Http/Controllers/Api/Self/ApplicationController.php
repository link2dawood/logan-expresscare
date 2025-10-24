<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Application\IndexRequest;
use App\Http\Requests\Api\Self\Application\StoreRequest;
use App\Models\Application;

class ApplicationController extends ApiBaseController
{
    protected $model = Application::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();
        $query = $query->where('submitted_by', $user->id);
        return  $query;
    }

    public function storing(Application $application)
    {
        $loggedUser = user();

        $application->submitted_by = $loggedUser->id;
        $application->opening_id = $application->opening_id;
        $application->stage = "Initial";

        return $application;
    }
}
