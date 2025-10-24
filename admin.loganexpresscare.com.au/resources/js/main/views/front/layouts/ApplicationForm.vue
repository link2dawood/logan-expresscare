<template>
    <header class="bg-gray-800 text-white p-4">
        <div class="max-w-4xl mx-auto flex items-center">
            <img
                src="https://demo.hrmifly.in/images/dark.png"
                alt="Logo"
                class="h-8"
            />
        </div>
    </header>

    <div
        class="relative bg-cover bg-center h-64"
        style="
            background-image: url('https://demo.hrmifly.in/images/login_background.svg');
        "
    >
        <div class="absolute inset-0 bg-black opacity-70"></div>
        <div class="relative flex items-center justify-center h-full">
            <h1 class="text-4xl text-white font-bold">
                {{ formFields.opening?.job_title }}
            </h1>
        </div>
    </div>
    <a-row v-if="message == false">
        <a-col :span="24">
            <a-card :bordered="false" style="height: 96%">
                <div style="text-align: center; font-size: 15px">
                    <a-typography-link
                        @click="
                            () => {
                                $router.push({
                                    name: 'front.job.details',
                                    params: {
                                        id: xid,
                                    },
                                });
                            }
                        "
                    >
                        [&nbsp;{{
                            $t("opening.view_job_details")
                        }}&nbsp;]</a-typography-link
                    >
                </div>
                <main class="max-w-4xl mx-auto p-6 bg-white shadow-md my-8">
                    <a-form layout="vertical">
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item
                                    :label="$t('application.applicant_name')"
                                    name="applicant_name"
                                    :help="
                                        rules.applicant_name
                                            ? rules.applicant_name.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.applicant_name ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-input
                                        v-model:value="formData.applicant_name"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'application.applicant_name'
                                                    ),
                                                ]
                                            )
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="$t('application.contact_email')"
                                    name="contact_email"
                                    :help="
                                        rules.contact_email
                                            ? rules.contact_email.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.contact_email ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-input
                                        v-model:value="formData.contact_email"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'application.contact_email'
                                                    ),
                                                ]
                                            )
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="$t('application.phone_number')"
                                    name="phone_number"
                                    :help="
                                        rules.phone_number
                                            ? rules.phone_number.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.phone_number ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-input
                                        v-model:value="formData.phone_number"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t('application.phone_number')]
                                            )
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="12"
                                v-if="formFields.opening?.gender == true"
                            >
                                <a-form-item
                                    :label="$t('user.gender')"
                                    name="gender"
                                    :help="
                                        rules.gender
                                            ? rules.gender.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.gender ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-select
                                        v-model:value="formData.gender"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.gender'),
                                            ])
                                        "
                                        :allowClear="true"
                                    >
                                        <a-select-option value="male">{{
                                            $t("user.male")
                                        }}</a-select-option>
                                        <a-select-option value="female">{{
                                            $t("user.female")
                                        }}</a-select-option>
                                        <a-select-option value="other">{{
                                            $t("user.other")
                                        }}</a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="12"
                                v-if="formFields.opening?.date_of_birth == true"
                            >
                                <a-form-item
                                    :label="$t('opening.date_of_birth')"
                                    name="date_of_birth"
                                    :help="
                                        rules.date_of_birth
                                            ? rules.date_of_birth.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.date_of_birth ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-date-picker
                                        v-model:value="formData.date_of_birth"
                                        :format="appSetting.date_format"
                                        valueFormat="YYYY-MM-DD"
                                        style="width: 100%"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="12"
                                v-if="formFields.opening?.address == true"
                            >
                                <a-form-item
                                    :label="$t('application.address')"
                                    name="address"
                                    :help="
                                        rules.address
                                            ? rules.address.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.address ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-input
                                        v-model:value="formData.address"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t('application.address')]
                                            )
                                        "
                                        :rows="4"
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="12"
                                v-if="formFields.opening?.profile_image == true"
                            >
                                <a-form-item
                                    :label="$t('application.image')"
                                    name="image"
                                    :help="
                                        rules.image ? rules.image.message : null
                                    "
                                    :validateStatus="
                                        rules.image ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <Upload
                                        :formData="formData"
                                        folder="application"
                                        imageField="image"
                                        @onFileUploaded="
                                            (file) => {
                                                formData.image = file.file;
                                                formData.image_url =
                                                    file.file_url;
                                            }
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <div
                            v-for="questionField in employeeQuestion"
                            :key="questionField.id"
                        >
                            <a-form-item
                                v-if="questionField.type == 'input'"
                                :label="questionField.name"
                                :help="
                                    rules.answer ? rules.answer.message : null
                                "
                                :validateStatus="rules.answer ? 'error' : null"
                            >
                                <a-row :gutter="[30, 30]">
                                    <a-col
                                        :xs="24"
                                        :sm="24"
                                        :md="24"
                                        :lg="24"
                                        :xl="24"
                                        ><a-input
                                            v-model:value="questionField.reply"
                                            @change="
                                                replyChanged(employeeQuestion)
                                            "
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('feedback.answers')]
                                                )
                                            "
                                        />
                                    </a-col>
                                </a-row>
                            </a-form-item>
                            <a-form-item
                                v-else
                                :help="
                                    rules.answer ? rules.answer.message : null
                                "
                                :validateStatus="rules.answer ? 'error' : null"
                                :label="questionField.name"
                            >
                                <a-row :gutter="[30, 30]">
                                    <a-col
                                        :xs="24"
                                        :sm="24"
                                        :md="24"
                                        :lg="24"
                                        :xl="24"
                                        ><a-textarea
                                            v-model:value="questionField.reply"
                                            @change="
                                                replyChanged(opening.questions)
                                            "
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('feedback.answers')]
                                                )
                                            "
                                        />
                                    </a-col>
                                </a-row>
                            </a-form-item>
                        </div>
                        <a-row :gutter="16">
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="24"
                                :lg="24"
                                v-if="formFields.opening?.cover_letter == true"
                            >
                                <a-form-item
                                    :label="$t('application.cover_letter')"
                                    name="cover_letter"
                                    :help="
                                        rules.cover_letter
                                            ? rules.cover_letter.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.cover_letter ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-textarea
                                        v-model:value="formData.cover_letter"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t('application.cover_letter')]
                                            )
                                        "
                                        :rows="4"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <a-row :gutter="16">
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="12"
                                v-if="formFields.opening?.resume == true"
                            >
                                <a-form-item
                                    :label="$t('application.resume')"
                                    name="resume"
                                    :help="
                                        rules.resume
                                            ? rules.resume.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.resume ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <UploadFile
                                        :acceptFormat="'image/*,.pdf'"
                                        :formData="formData"
                                        folder="application"
                                        uploadField="resume"
                                        @onFileUploaded="
                                            (file) => {
                                                formData.resume = file.file;
                                                formData.resume_url =
                                                    file.file_url;
                                            }
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-form>
                </main>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                    style="margin-left: 48%"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ $t("common.submit") }}
                </a-button>
            </a-card>
            <footer class="bg-gray-800 text-white py-6">
                <div class="max-w-4xl mx-auto text-center">
                    <p>{{ $t("opening.contact_us_at") }}</p>
                    <p>
                        {{ $t("opening.email")
                        }}{{ formFields.company?.email }} |
                        {{ $t("opening.phone") }}
                        {{ formFields.company?.phone }}
                    </p>
                </div>
            </footer>
        </a-col>
    </a-row>

    <div v-if="message == true" class="max-w-4xl mx-auto bg-white my-8">
        <a-alert
            style="text-align: center; font-size: 25px; font-weight: bold"
            :message="$t('application.application_status')"
            :description="$t('application.application_submit_sucessfully')"
            type="success"
            show-icon
        />
    </div>
</template>

<script>
import { defineComponent, onMounted, ref } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import common from "../../../../common/composable/common.js";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
        "recordData",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        UploadFile,
        Upload,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, applicationStages } = common();
        const employeeQuestion = ref([]);
        const { t } = useI18n();
        const formData = ref({
            applicant_name: "",
            contact_email: "",
            phone_number: "",
            resume: undefined,
            resume_url: undefined,
            image: undefined,
            image_url: undefined,
            cover_letter: "",
            stage: "initial",
            address: "",
            date_of_birth: undefined,
            gender: "female",
            data_question: "",
        });
        const route = useRoute();
        const xid = ref("");
        const employeeReply = ref([]);
        const formFields = ref({});
        const message = ref(false);

        onMounted(() => {
            setColumnValues();
        });

        const setColumnValues = () => {
            xid.value = route.params.id;

            axiosAdmin
                .get(`job-details/${xid.value}`)
                .then((successResponse) => {
                    formFields.value = successResponse.data;
                    employeeQuestion.value =
                        formFields.value.opening?.questions;
                });
        };

        const replyChanged = (record) => {
            employeeReply.value = record;
        };

        const onSubmit = () => {
            if (employeeQuestion.value.length == 0) {
                employeeQuestion.value = formFields.value.opening?.questions;
            }

            addEditRequestAdmin({
                url: "front-application-form",
                data: {
                    ...formData.value,
                    data_question: employeeQuestion.value,
                    opening_id: xid.value,
                },
                successMessage: t("application.created"),
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                    employeeQuestion.value = [];
                    formData.value = {};
                    setColumnValues();
                    message.value = true;
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,
            message,
            applicationStages,
            replyChanged,
            employeeQuestion,
            formFields,
            formData,
            xid,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
<style scoped>
.nav {
    background-color: #315684;
    color: white;
    padding: 20px;
    height: 70px;
    font-size: 18px;
    vertical-align: top;
}
.nav-item-1 {
    float: left;
    font-size: 25px;
}

.nav-item-2 {
    float: right;
    margin-top: -17px;
}
</style>
