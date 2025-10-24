<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\PdfFonts\IndexRequest;
use App\Http\Requests\Api\PdfFonts\StoreRequest;
use App\Http\Requests\Api\PdfFonts\UpdateRequest;
use App\Http\Requests\Api\PdfFonts\DeleteRequest;
use App\Models\PdfFonts;

class PdfFontsController extends ApiBaseController
{
	protected $model = PdfFonts::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

}