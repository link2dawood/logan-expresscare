<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyPolicy;
use App\Models\Location;
use App\Observers\CompanyPolicyObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('company_policies')->delete();

        DB::statement('ALTER TABLE company_policies AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        CompanyPolicy::observe(CompanyPolicyObserver::class);

        $locaion = Location::where('company_id', $company->id)->first();
        $month = Carbon::now()->format('F');
        $year = Carbon::now()->year;

        $companyPolicy = new CompanyPolicy();
        $companyPolicy->company_id = $company->id;
        $companyPolicy->location_id = $locaion->id;
        $companyPolicy->title = 'Office Tour Agreement ' . $month . ' ' . $year;
        $companyPolicy->letter_description = '<p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Subject: Agreement for Office Trip Participation</span></p><p class="ql-align-justify"><br></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Dear Employee,</span></p><p class="ql-align-justify"><br></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">I hope this message finds you well.</span></p><p class="ql-align-justify"><br></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">As you are aware, we are planning an office trip to </span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Pushkar (Rajasthan)</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> from </span><strong style="color: rgb(0, 0, 0); background-color: transparent;">25 June 2024 to 26 June 2024</strong><span style="color: rgb(0, 0, 0); background-color: transparent;">. This trip is intended to be a time for relaxation and team bonding. However, it is important to address some necessary legalities for everyones safety and understanding.</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">By agreeing to participate in this trip, you acknowledge and accept the following terms and conditions:</span></p><ol><li data-list="ordered" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Voluntary Participation:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> Your participation in this trip is entirely voluntary. You are not required to join the trip as part of your employment duties.</span></li><li data-list="ordered" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Personal Responsibility:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> While the company will take reasonable precautions to ensure the safety and well-being of all participants, you understand that your participation in the trip is at your own risk.</span></li><li data-list="ordered" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Limitation of Liability:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> In the event of any unforeseen incidents, including but not limited to accidents, injuries, heart attacks, or death, the company will not be held responsible or liable for any damages, medical expenses, or compensation.</span></li><li data-list="ordered" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Health and Safety:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> You confirm that you are in good health and capable of participating in the trip. If you have any medical conditions that require attention, you agree to inform the company prior to the trip and to take necessary precautions.</span></li><li data-list="ordered" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Insurance:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> We recommend that you have personal health and travel insurance to cover any potential medical needs or emergencies during the trip.</span></li></ol><p class="ql-align-justify"><br></p><p class="ql-align-justify"><br></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Please sign below to acknowledge your understanding and acceptance of these terms. Your agreement is required to confirm your participation in the trip.</span></p><p class="ql-align-justify"><br></p><p><br></p><p class="ql-align-justify"><strong style="color: rgb(0, 0, 0); background-color: transparent;">Acknowledgement and Agreement</strong></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">I, __________________________, have read and understood the terms and conditions outlined above. I voluntarily agree to participate in the office trip to </span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Pushkar (Rajasthan)</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> from </span><strong style="color: rgb(0, 0, 0); background-color: transparent;">25 June 2024 to 26 June 2024</strong><span style="color: rgb(0, 0, 0); background-color: transparent;">, and I acknowledge that the company will not be held responsible for any incidents, injuries, or mishaps that may occur during the trip and company will not be held responsible or liable for any damages, medical expenses, or compensation. If you are receiving this on email please confirm and accept this email and reply to this email.</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Employee Name: _________________________</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Signature: _____________________________</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Date: _________________________________</span></p><p class="ql-align-justify"><br></p><p><br></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Thank you for your understanding and cooperation. If you have any questions or concerns, please feel free to contact me.</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Best regards,</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Rajesh Kumawat</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">CEO &amp; Founder</span></p><p class="ql-align-justify"><span style="color: rgb(0, 0, 0); background-color: transparent;">Codraj Infotech Pvt. Ltd.</span></p><p><br></p>';
        $companyPolicy->method_type = 'create';
        $companyPolicy->description = 'Office Tour Agreement';
        $companyPolicy->save();
    }
}
