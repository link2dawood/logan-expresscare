<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
    >
        <a-row :gutter="16">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <FormItemHeading>
                    {{ $t("feedback.survey_info") }}
                </FormItemHeading>
                <a-descriptions
                    :labelStyle="{
                        fontWeight: 'bold',
                    }"
                    :contentStyle="{
                        marginBottom: '15px',
                    }"
                    :column="1"
                    layout="vertical"
                    size="small"
                >
                    <a-descriptions-item :label="$t('feedback.last_date')">
                        {{ feedback.feedback ? formatDate(feedback.last_date) : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('feedback.description')">
                        <div
                            v-if="feedback.feedback.description"
                            style="white-space: pre-wrap"
                        >
                            {{ feedback.feedback.description }}
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>
                </a-descriptions>
            </a-col>
        </a-row>

        <a-row v-if="!toShowFeedbackForm" :gutter="16">
            <a-col :span="24">
                <a-alert
                    :message="$t('common.expired')"
                    :description="$t('feedback.submittion_date_expired')"
                    type="error"
                    show-icon
                >
                    <template #icon>
                        <CalendarOutlined />
                    </template>
                </a-alert>
            </a-col>
        </a-row>

        <a-row v-else>
            <a-col :span="24">
                <a-form v-if="feedback.feedback" layout="vertical" class="mt-30">
                    <div
                        v-for="feedbackFormField in feedback.feedback
                            .feedback_form_fields"
                        :key="feedbackFormField.id"
                    >
                        <a-row :gutter="16" v-if="feedbackFormField.type == 'input'">
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item
                                    :label="feedbackFormField.name"
                                    :help="
                                        rules.reply && !feedbackFormField.reply
                                            ? rules.reply.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.reply && !feedbackFormField.reply
                                            ? 'error'
                                            : null
                                    "
                                    class="required"
                                >
                                    <a-input
                                        v-model:value="feedbackFormField.reply"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('feedback.answers'),
                                            ])
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <a-row :gutter="16" v-else>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item
                                    :help="
                                        rules.reply && !feedbackFormField.reply
                                            ? rules.reply.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.reply && !feedbackFormField.reply
                                            ? 'error'
                                            : null
                                    "
                                    :label="feedbackFormField.name"
                                    class="required"
                                >
                                    <a-textarea
                                        v-model:value="feedbackFormField.reply"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('feedback.answers'),
                                            ])
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </div>
                </a-form>
            </a-col>
        </a-row>
        <template #footer>
            <a-form-item>
                <a-space>
                    <a-button
                        v-if="toShowFeedbackForm"
                        type="primary"
                        @click="onSubmit"
                        :loading="loading"
                    >
                        {{ $t("common.submit") }}
                    </a-button>
                    <a-button key="back" @click="onClose">
                        {{ $t("common.cancel") }}
                    </a-button>
                </a-space>
            </a-form-item>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch } from "vue";
import { CalendarOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin.js";
import dayjs from "dayjs";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import Common from "../../../../../js/common/composable/common";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: {
        CalendarOutlined,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const feedback = ref({});
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const toShowFeedbackForm = ref(true);
        dayjs.extend(isSameOrAfter);
        const { formatDate, user } = Common();

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "self/employee-feedback",
                data: feedback.value,
                successMessage: "Success",
                success: (res) => {
                    emit("addSuccess");
                },
            });
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    feedback.value = props.data;

                    if (
                        feedback.value.feedback &&
                        feedback.value.feedback.last_date &&
                        dayjs().isAfter(feedback.value.feedback.last_date, "date")
                    ) {
                        toShowFeedbackForm.value = false;
                    } else {
                        toShowFeedbackForm.value = true;
                    }
                }
            }
        );

        return {
            rules,
            loading,
            onSubmit,
            feedback,
            toShowFeedbackForm,
            user,
            onClose,
            formatDate,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
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
