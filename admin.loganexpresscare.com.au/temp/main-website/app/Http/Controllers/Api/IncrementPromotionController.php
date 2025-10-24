<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Payrolls;
use App\Http\Controllers\ApiBaseController;
use App\Models\StaffMember;
use App\Http\Requests\Api\IncrementPromotion\IndexRequest;
use App\Http\Requests\Api\IncrementPromotion\StoreRequest;
use App\Http\Requests\Api\IncrementPromotion\UpdateRequest;
use App\Http\Requests\Api\IncrementPromotion\DeleteRequest;
use App\Models\Generate;
use App\Models\IncrementPromotion;

class IncrementPromotionController extends ApiBaseController
{
    protected $model = IncrementPromotion::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // Dates Filters
        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('increments_promotions.date >= ?', [$startDate])
                ->whereRaw('increments_promotions.date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'increments_promotions_view')) {
            $query = $this->applyVisibility($query, 'increments_promotions');

            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('increments_promotions.user_id', $userId);
            }
        } else {
            $query = $query->where('increments_promotions.user_id', $loggedUser->id);
        }

        // status Filters
        if ($request->has('type') && $request->type != "all") {
            $incrementPromotionType = $request->type;
            $query = $query->where('increments_promotions.type', $incrementPromotionType);
        }
        return  $query;
    }

    public function storing(IncrementPromotion $incrementPromotion)
    {
        $request = request();
        $loggedUser = user();

        // Create a new Generate object and set its attributes
        if ($incrementPromotion->letterhead_template_id && $request->letterhead_description != '') {

            $generate = new Generate();
            $generate->letterhead_template_id = $incrementPromotion->letterhead_template_id;
            $generate->description = $request->letterhead_description;
            $generate->user_id = $incrementPromotion->user_id;
            $generate->title = $request->letterhead_title;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->admin_id = $loggedUser->id;
            // Save the Generate object
            $generate->save();

            // Now update the IncrementPromotion with the generate_id
            $incrementPromotion->generate_id = $generate->id;

            // Save the updated IncrementPromotion object
            $incrementPromotion->save();
        }

        // Return the incrementPromotion object
        return $incrementPromotion;
    }

    public function stored(IncrementPromotion $incrementPromotion)
    {
        $loggedUser = user();
        $request = request();
        $userId = $incrementPromotion->user_id;

        if ($request->has('update_basic_salary') && $request->update_basic_salary  && $loggedUser->ability('admin', 'basic_salaries_edit')) {
            $basicSalary = StaffMember::where('id', $userId)
                ->first();

            if ($basicSalary) {
                $basicSalary->basic_salary = $incrementPromotion->net_salary;
                $basicSalary->save();
            }
        }

        if ($request->has('update_designation') && $request->update_designation  && $loggedUser->ability('admin', 'designations_edit')) {
            $staffMember = StaffMember::find($userId);

            $staffMember->designation_id = $incrementPromotion->promoted_designation_id;
            $staffMember->save();
        }

        Payrolls::updateUserSalary($userId, $incrementPromotion->net_salary);

        return $incrementPromotion;
    }

    public function updating(IncrementPromotion $incrementPromotion)
    {
        $request = request();
        $loggedUser = user();

        // Create a new Generate object and set its attributes
        if ($incrementPromotion->letterhead_template_id && $request->letterhead_description != '') {
            // Previous no letter head template selected but now selected
            if ($incrementPromotion->getOriginal('letterhead_template_id') == '' && $incrementPromotion->letterhead_template_id != '' && $request->letterhead_description != '') {
                $generate = new Generate();
                $generate->user_id = $incrementPromotion->user_id;
                $generate->letterhead_template_id = $incrementPromotion->letterhead_template_id;
                $generate->title = $request->letterhead_title;
                $generate->description = $request->letterhead_description;
                $generate->admin_id = $loggedUser->id;
                $generate->save();

                $incrementPromotion->generate_id = $generate->id;
            } else if ($incrementPromotion->letterhead_template_id && $request->letterhead_description != '' && $incrementPromotion->generate_id) {
                // Previous letter head template selected and now new one selected
                $generate = Generate::find($incrementPromotion->generate_id);
                $generate->user_id = $incrementPromotion->user_id;
                $generate->letterhead_template_id = $incrementPromotion->letterhead_template_id;
                $generate->title = $request->letterhead_title;
                $generate->description = $request->letterhead_description;
                $generate->save();
            } else if ($incrementPromotion->getOriginal('letterhead_template_id') != '' && $incrementPromotion->letterhead_template_id == '' && $incrementPromotion->generate_id) {
                // Previous letter head template selected and generate exists but now letterhead templated not selected
                Generate::destroy($incrementPromotion->generate_id);
            }
        }

        // Return the incrementPromotion object
        return $incrementPromotion;
    }


    public function updated(IncrementPromotion $incrementPromotion)
    {
        $userId = $incrementPromotion->user_id;
        Payrolls::updateUserSalary($userId, $incrementPromotion->net_salary);
    }
}
