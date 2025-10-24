<?php

namespace App\Http\Controllers\Api\Self;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\CompanyPolicy\IndexRequest;
use App\Models\CompanyPolicy;

class CompanyPolicyController extends ApiBaseController
{
    protected $model = CompanyPolicy::class;
    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();

        $query = $query->where('location_id', $user->location_id);

        return  $query;
    }
}