<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Feedback;
use App\Models\FeedbackUser;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyFormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('forms')->delete();
        DB::table('feedbacks')->delete();
        DB::table('indicators')->delete();
        DB::table('feedback_users')->delete();

        DB::statement('ALTER TABLE forms AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE feedbacks AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE indicators AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE feedback_users AUTO_INCREMENT = 1');

        $company = Company::where('is_global', 0)->first();
        $user = User::where('company_id', $company->id)->where('status', 'active')->first();

        $monthName = Carbon::now()->format('F');
        $year = Carbon::now()->format('Y');

        $forms = [
            [
                'name' => 'Monthly Suggestions',
                'feedback_title' => $monthName . ' ' . $year . ' Survey',
                'indicator' =>   [
                    'name' => 'Monthly Form Review Indicator',
                    'company_id' => $company->id,
                    'fields' => [
                        [
                            "name" => "Performance",
                            "id" => Str::substr(Str::uuid()->toString(), 0, 8)
                        ],
                        [
                            "name" => "Nature",
                            "id" => Str::substr(Str::uuid()->toString(), 0, 8)
                        ],
                        [
                            "name" => "Hard Work",
                            "id" => Str::substr(Str::uuid()->toString(), 0, 8)
                        ],
                        [
                            "name" => "Behaviour",
                            "id" => Str::substr(Str::uuid()->toString(), 0, 8)
                        ]
                    ],
                ],
                'form_fields' => [
                    [
                        "name" => "What's your name?",
                        "type" => "input",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 8)
                    ],
                    [
                        "name" => "Are you happy with current work?",
                        "type" => "text_area",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7)
                    ],
                    [
                        "name" => "In last month on which framework you worked?",
                        "type" => "text_area",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7)
                    ],
                    [
                        "name" => "Any suggestion for improving the company growth?",
                        "type" => "text_area",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7)
                    ]
                ],
            ],
            [
                'name' => 'Employee Feedback',
                'feedback_title' => 'Employee Happiness Evaluation ' . $monthName . ' ' . $year,
                'indicator' =>    [
                    'name' => 'Employee Satisfaction Survey',
                    'company_id' => $company->id,
                    'fields' => [
                        ["name" => "Job Satisfaction", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Work Environment", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Management Support", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Career Growth", "id" => Str::substr(Str::uuid()->toString(), 0, 8)]
                    ],
                ],
                'form_fields' => [
                    [
                        "name" => "How satisfied are you with your job?",
                        "type" => "input",
                        "required" => 1,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7),
                    ],
                    [
                        "name" => "Do you feel valued in the company?",
                        "type" => "input",
                        "required" => 1,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7),
                    ],
                    [
                        "name" => "Any suggestions for improving the work culture?",
                        "type" => "text_area",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7)
                    ]
                ],
            ],
            [
                'name' => 'Workplace Safety Survey',
                'feedback_title' => 'Reporting Workplace Hazards and Risks Survey',
                'indicator' =>   [
                    'name' => 'Annual Goal Achievement',
                    'company_id' => $company->id,
                    'fields' => [
                        ["name" => "Objectives Met", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Challenges Overcome", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Innovations", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Leadership", "id" => Str::substr(Str::uuid()->toString(), 0, 8)]
                    ],
                ],
                'form_fields' => [
                    [
                        "name" => "Do you feel safe at work?",
                        "type" => "input",
                        "required" => 1,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7),
                    ],
                    [
                        "name" => "Have you received safety training?",
                        "type" => "input",
                        "required" => 1,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7),
                    ],
                    [
                        "name" => "Any suggestions for safety improvements?",
                        "type" => "text_area",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7)
                    ]
                ],
            ],
            [
                'name' => 'Work-Life Balance Survey',
                'feedback_title' => 'Employee Preferences for Remote Work',
                'indicator' =>   [
                    'name' => 'Quarterly Performance Review',
                    'company_id' => $company->id,
                    'fields' => [
                        ["name" => "Efficiency", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Teamwork", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Communication", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                        ["name" => "Problem-Solving", "id" => Str::substr(Str::uuid()->toString(), 0, 8)]
                    ],
                ],
                'form_fields' => [
                    [
                        "name" => "Do you feel you have a good work-life balance?",
                        "type" => "input",
                        "required" => 1,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7),
                    ],
                    [
                        "name" => "How many hours do you work per week?",
                        "type" => "input",
                        "required" => 1,
                    ],
                    [
                        "name" => "Any suggestions for improving work-life balance?",
                        "type" => "text_area",
                        "required" => 0,
                        "id" => Str::substr(Str::uuid()->toString(), 0, 7)
                    ]
                ],
            ],
        ];

        $allIndicators = [
            [
                'name' => 'Training Effectiveness Assessment',
                'company_id' => $company->id,
                'fields' => [
                    ["name" => "Knowledge Gained", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                    ["name" => "Skills Developed", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                    ["name" => "Training Relevance", "id" => Str::substr(Str::uuid()->toString(), 0, 8)],
                    ["name" => "Engagement", "id" => Str::substr(Str::uuid()->toString(), 0, 8)]
                ],
            ],
        ];

        foreach ($allIndicators as $allIndicator) {
            $indicator = Indicator::create($allIndicator);
        }

        $monthName = Carbon::now()->format('F');
        $year = Carbon::now()->format('Y');
        $lastDate = Carbon::now()->addDays(7);

        $allCompanyActiveMembers = User::where('company_id', $company->id)->where('status', 'active')->pluck('id');

        foreach ($forms as $form) {
            $newForm = new Form();
            $newForm->company_id = $company->id;
            $newForm->form_fields = $form['form_fields'];
            $newForm->name = $form['name'];
            $newForm->created_by = $user->id;
            $newForm->updated_by = $user->id;
            $newForm->save();

            $indicator = new Indicator();
            $indicator->company_id = $company->id;
            $indicator->name = $form['indicator']['name'];
            $indicator->fields = $form['indicator']['fields'];
            $indicator->save();


            $feedback = new Feedback();
            $feedback->company_id = $company->id;
            $feedback->title = $form['feedback_title'];
            $feedback->form_id = $newForm->id;
            $feedback->visible_to = 1;
            $feedback->description = $feedback->title . ' ' . $monthName . ' ' . $year;
            $feedback->last_date = $lastDate;
            $feedback->feedback_form_fields = $newForm->form_fields;
            $feedback->indicator_fields = [
                [
                    "id" => $indicator->id,
                    "name" => $indicator->name,
                    "fields" => $indicator->fields
                ]
            ];
            $feedback->save();


            foreach ($allCompanyActiveMembers as $allCompanyActiveMember) {
                $feedbackUser = new FeedbackUser();
                $feedbackUser->feedback_id = $feedback->id;
                $feedbackUser->user_id = $allCompanyActiveMember;

                $willRatingGiven = fake()->randomElement([0, 1]);

                if ($willRatingGiven) {
                    $ratingData = $this->generateRatingData($newForm, $indicator);

                    $feedbackUser->feedback_given = 1;
                    $feedbackUser->rating = $ratingData['rating'];
                    $feedbackUser->submit_date = Carbon::now();
                    $feedbackUser->data = $ratingData['data'];
                    $feedbackUser->rating_details = $ratingData['rating_details'];
                }

                $feedbackUser->save();
            }
        }
    }

    public function generateRatingData($newForm, $indicator)
    {
        $data = [];
        $ratingDetails = [];
        $formFields = $newForm->form_fields;

        foreach ($formFields as $formField) {
            $data[] = [
                "name" => $formField['name'],
                "type" => $formField['type'],
                "required" => 0,
                "id" => Str::substr(Str::uuid()->toString(), 0, 8),
                "reply" => $formField['type'] == 'input' ? fake()->sentence() : fake()->paragraph(2)
            ];
        }

        $ratingCount = 0;
        foreach ($indicator->fields as $indicatorField) {
            $rating = fake()->randomElement([3, 4, 5]);
            $ratingCount = $ratingCount + $rating;

            $ratingDetails[] = [
                "name" => $indicatorField['name'],
                "id" => Str::substr(Str::uuid()->toString(), 0, 8),
                "rating_details" => $rating
            ];
        }

        return [
            'rating' => $ratingCount / count($indicator->fields),
            'data' => $data,
            'rating_details' => [
                [
                    'name' => $indicator->name,
                    'fields' => $ratingDetails
                ]
            ]
        ];
    }
}
