<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\News\IndexRequest;
use App\Models\News;

class NewsController extends ApiBaseController
{
    protected $model = News::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();

        $query = $query->leftJoin('news_users', 'news_users.news_id', '=', 'news.id')
            ->where(function ($q) use ($user) {
                $q->where('news_users.user_id', $user->id)
                    ->orWhere('visible_to_all', 1);
            })
            ->where('status', 'published');

        return $query;
    }
}
