<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\News\IndexRequest;
use App\Http\Requests\Api\News\StoreRequest;
use App\Http\Requests\Api\News\UpdateRequest;
use App\Http\Requests\Api\News\DeleteRequest;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\News;
use App\Models\NewsUser;
use App\Models\User;
use Examyou\RestAPI\ApiResponse;

class NewsController extends ApiBaseController
{
    protected $model = News::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public $newsAlreadyPublished = false;

    public function storing(News $news)
    {
        $request = request();

        if ($request->status == 'published') {
            $news->status = $request->status;
        } else {
            $news->status = $request->status;

            // News is not published yet, so we will not notify users
            $this->newsAlreadyPublished = true;
        }

        return $news;
    }

    public function stored(News $news)
    {
        $this->addAndUpdateNews($news);
    }

    public function updating(News $news)
    {
        $request = request();

        $selectedNews = News::where('id', $news->id)->select('news.status');
        $selectedNews = $selectedNews->first();

        if ($selectedNews->status == 'published' && $request->status == 'draft') {
            throw new ApiException("The news is already published, you can not draft it ");
        } else {
            $news->title = $request->title;
            $news->description = $request->description;
        }

        $this->newsAlreadyPublished = $selectedNews->status == 'published';

        NewsUser::where('news_id', $news->id)->delete();

        return $news;
    }

    public function updated(News $news)
    {

        $this->addAndUpdateNews($news);
    }

    public function addAndUpdateNews(News $news)
    {
        $request = request();

        if (isset($request->user_id)) {
            foreach ($request->user_id as $userId) {

                $id = $this->getIdFromHash($userId);

                $newsUser = new NewsUser();
                $newsUser->news_id = $news->id;
                $newsUser->user_id = $id;
                $newsUser->save();
            }
        }

        if (!$this->newsAlreadyPublished) {
            // Send notification to all users
            $this->sendNewsNotification($news);
        }

        return $news;
    }

    public function sendNewsNotification(News $news)
    {
        $allUserIds = [];

        if ($news->visible_to_all) {
            $allUserIds = User::where('users.user_type', '=', 'staff_members')
                ->where('status', 'active')
                ->pluck('id');
        } else {
            $allUserIds = NewsUser::where('news_id', $news->id)->pluck('user_id');
        }

        foreach ($allUserIds as $userId) {
            $notificationData = [
                'user_id' => $userId,
                'news' => $news,
            ];
            // Notifying to User
            Notify::send('employee_news_published', $notificationData);
        }
    }

    public function publishNews($newsId)
    {
        $id = $this->getIdFromHash($newsId);

        $news = News::find($id);
        $news->status = 'published';
        $news->save();

        // Send notification to all users
        $this->sendNewsNotification($news);

        return ApiResponse::make("Status updated successfully", ['status' => 'success']);
    }
}
