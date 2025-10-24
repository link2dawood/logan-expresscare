<template>
    <a-drawer
        :title="users.title"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <div class="bg-white">
            <a-row type="flex">
                <a-col :span="20">
                    <div v-if="feedbacks && feedbacks.length > 0">
                        <div v-for="feedback in feedbacks" :key="feedback.id">
                            <div>
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
                                    <a-row
                                        :gutter="16"
                                        v-if="feedbackFormField.type == 'input'"
                                    >
                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item
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
                                            </a-form-item>
                                        </a-col>
                                    </a-row>

                                    <a-row :gutter="16" v-else>
                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item
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
                                                <a-textarea
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
                                            </a-form-item>
                                        </a-col>
                                    </a-row>
                                </div>
                            </a-form>
                        </div>
                    </div>
                </a-col>
            </a-row>
        </div>
        <template #footer>
            <a-form-item>
                <a-space>
                    <a-button
                        type="primary"
                        @click="onSubmit"
                        :loading="loading"
                        :disabled="toShowFeedbackForm"
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
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    RightOutlined,
    RightCircleOutlined,
    LeftCircleOutlined,
} from "@ant-design/icons-vue";
import { useRoute, useRouter } from "vue-router";
import apiAdmin from "../../../../common/composable/apiAdmin.js";
import { forEach } from "lodash-es";
import dayjs from "dayjs";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import { message } from "ant-design-vue";
import Common from "../../../../../js/common/composable/common";

export default defineComponent({
    props: ["users", "visible"],
    components: {
        RightOutlined,
        RightCircleOutlined,
        LeftCircleOutlined,
    },
    setup(props, { emit }) {
        const route = useRoute();
        const router = useRouter();
        const feedbacks = ref([]);
        const employeeReply = ref([]);
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const xid = ref("");
        const toShowFeedbackForm = ref(false);
        dayjs.extend(isSameOrAfter);
        const { user } = Common();

        const replyChanged = (record) => {
            employeeReply.value = record;
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
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
        watch(
            () => props.visible,
            (newVal, oldVal) => {
                xid.value = props.users.xid;
                axiosAdmin.get(`employee-survey/${xid.value}`).then((response) => {
                    feedbacks.value = response.data;
                    forEach(feedbacks.value, (feedbac) => {
                        if (dayjs().isAfter(feedbac.last_date, "date")) {
                            toShowFeedbackForm.value = true;
                            message.warning("Submission date is expired !");
                        }
                    });
                });
            }
        );

        return {
            rules,
            loading,
            feedbacks,
            onSubmit,
            replyChanged,
            toShowFeedbackForm,
            user,
            onClose,
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
