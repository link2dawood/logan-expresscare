<template>
    <div class="bg-white">
        <a-row type="flex" justify="center">
            <a-col :span="20">
                <div v-if="feedbacks && feedbacks.length > 0">
                    <div v-for="feedback in feedbacks" :key="feedback.id">
                        <div style="text-align: center" class="mt-50 mb-40">
                            <a-typography-title
                                :level="3"
                                :style="{ marginBottom: '5px' }"
                            >
                                {{ feedback.title }}
                            </a-typography-title>
                            <a-typography-title
                                v-if="feedback.description != ''"
                                type="secondary"
                                :level="5"
                                :style="{ marginTop: '0px' }"
                            >
                                {{ feedback.description }}
                            </a-typography-title>
                        </div>

                        <a-form layout="vertical" class="mt-30">
                            <div
                                v-for="feedbackFormField in feedback.feedback_form_fields"
                                :key="feedbackFormField.id"
                            >
                                <a-form-item
                                    v-if="feedbackFormField.type == 'input'"
                                    :label="feedbackFormField.name"
                                    :help="
                                        rules.answer
                                            ? rules.answer.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.answer ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-row :gutter="[30, 30]">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="24"
                                            :lg="24"
                                            :xl="24"
                                            ><a-input
                                                v-model:value="
                                                    feedbackFormField.reply
                                                "
                                                @change="replyChanged(feedback)"
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
                                        rules.answer
                                            ? rules.answer.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.answer ? 'error' : null
                                    "
                                    :label="feedbackFormField.name"
                                    class="required"
                                >
                                    <a-row :gutter="[30, 30]">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="24"
                                            :lg="24"
                                            :xl="24"
                                            ><a-textarea
                                                v-model:value="
                                                    feedbackFormField.reply
                                                "
                                                @change="replyChanged(feedback)"
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
                            <a-divider dashed />

                            <a-form-item style="text-align: center">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        class="mt-10"
                                        @click="onSubmit"
                                        :loading="loading"
                                        :disabled="toShowFeedbackForm"
                                    >
                                        {{ $t("common.submit") }}
                                    </a-button>
                                </a-space>
                            </a-form-item>
                        </a-form>
                    </div>
                </div>
            </a-col>
        </a-row>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { LeftCircleOutlined } from "@ant-design/icons-vue";
import { useRoute, useRouter } from "vue-router";
import apiAdmin from "../../../../common/composable/apiAdmin.js";
import { forEach } from "lodash-es";
import dayjs from "dayjs";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import { message } from "ant-design-vue";
import Common from "../../../../common/composable/common.js";

export default defineComponent({
    components: {
        LeftCircleOutlined,
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const feedbacks = ref([]);
        const employeeReply = ref([]);
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const xid = ref();
        const toShowFeedbackForm = ref(false);
        dayjs.extend(isSameOrAfter);
        const { user } = Common();

        onMounted(() => {
            xid.value = route.params.id;
            axiosAdmin.get(`employee-survey/${xid.value}`).then((response) => {
                feedbacks.value = response.data;
                forEach(feedbacks.value, (feedbac) => {
                    if (dayjs().isAfter(feedbac.last_date, "date")) {
                        toShowFeedbackForm.value = true;
                        message.warning("Submission date is expired !");
                        router.push({
                            name: `admin.assigned_survey.index`,
                        });
                    }
                });
            });
        });

        const replyChanged = (record) => {
            employeeReply.value = record;
        };

        const onSubmit = () => {
            if (employeeReply.value.length == 0) {
                forEach(feedbacks.value, (feedback) => {
                    employeeReply.value = feedback;
                });
            }
            addEditRequestAdmin({
                url: "employee-feedback",
                data: employeeReply.value,
                successMessage: "Success",
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                    router.push({
                        name: `admin.assigned_survey.index`,
                    });
                },
            });
        };

        return {
            rules,
            loading,
            feedbacks,
            onSubmit,
            replyChanged,
            toShowFeedbackForm,
            user,
        };
    },
});
</script>

<style lang="less">
.prdoct-card-list {
    margin-bottom: 20px;
    margin-top: 20px;
    border-radius: 10px;
}

.prdoct-card-list-body {
    padding: 10px 0px 20px 0px;
}

.featured-categories .ant-list-item-meta-title {
    margin-top: 6px;
}
</style>
