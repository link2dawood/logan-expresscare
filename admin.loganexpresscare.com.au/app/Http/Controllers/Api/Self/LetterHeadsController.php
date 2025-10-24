<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Generate\IndexRequest;
use App\Models\Generate;
use App\Models\LetterHeadTemplate;
use Examyou\RestAPI\ApiResponse;

class LetterHeadsController extends ApiBaseController
{
    protected $model = Generate::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();

        $query = $query->where('user_id', $user->id);

        if ($request->has('letterhead_template_id') && ($request->letterhead_template_id != "" || $request->letterhead_template_id != null)) {
            $letterId = $this->getIdFromHash($request->letterhead_template_id);
            $query = $query->where('generates.letterhead_template_id', $letterId);
        };

        if($request->has('year')){
            $query = $query->whereYear('created_at', $request->year);
        }

        if($request->has('month')){
            $query = $query->whereMonth('created_at', $request->month);
        }

        return  $query;
    }

    public function getLetterHeadTemplates()
    {
        $allLeaveTypes = LetterHeadTemplate::select('id', 'title')->get()->toArray();

        return ApiResponse::make('Success', $allLeaveTypes);
    }
}
