<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.assigned_survey`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.feedbacks`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.assigned_survey`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row style="margin-top: 30px; margin-left: 23px">
        <a-col :span="24">
            <a-tabs
                v-model:activeKey="extraFilters.status"
                @change="setUrlData"
            >
                <a-tab-pane key="active" :tab="`${$t('feedback.active')}`" />
                <a-tab-pane key="expired" :tab="`${$t('feedback.expired')}`" />
            </a-tabs>
        </a-col>
    </a-row>

    <admin-page-table-content>
        <a-row style="margin-top: 30px">
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="survies"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'submit_date'">
                                <div v-if="record.submit_date != ''">
                                    {{
                                        dayjs(record.submit_date).format(
                                            "YYYY-MM-DD"
                                        )
                                    }}
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'start_date'">
                                {{
                                    dayjs(record.start_date).format(
                                        "YYYY-MM-DD"
                                    )
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'last_date'">
                                {{
                                    dayjs(record.last_date).format("YYYY-MM-DD")
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'link'">
                                <span
                                    v-if="
                                        remainDays(record.last_date) &&
                                        record.submit_date == ''
                                    "
                                >
                                    <a-button
                                        type="primary"
                                        @click="feedbackOpened(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon
                                            ><LinkOutlined
                                        /></template>
                                    </a-button>
                                </span>
                                <span v-else-if="record.submit_date != ''">
                                    <a-button
                                        v-if="
                                            permsArray.includes(
                                                'feedbacks_view'
                                            ) || permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="viewResponse(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon
                                            ><EyeOutlined
                                        /></template>
                                    </a-button>
                                </span>
                                <span v-else-if="remainDays(record.last_date)"
                                    >-</span
                                >
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <ViewResponse
            :data="recordData"
            :visible="visibleResponse"
            :pageTitle="$t('feedback.survey_info')"
            @close="closed"
        />
        <Feedback
            :users="recordData"
            :visible="feedbackVisible"
            @close="feedbackClosed"
        />
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
    LinkOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";
import fields from "./fields";
import { forEach } from "lodash-es";
import ViewResponse from "./ViewResponse.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import crud from "../../../../common/composable/crud";
import Feedback from "./Feedback.vue";

export default {
    components: {
        EyeOutlined,
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        LinkOutlined,
        ViewResponse,
        AdminPageHeader,
        Feedback,
    },
    setup() {
        const { permsArray, appSetting, dayjs, formatDate } = common();
        const { url, addEditUrl, initData, columns, filterableColumns } =
            fields();
        const { addEditRequestAdmin, loading } = apiAdmin();
        const survies = ref([]);
        const recordData = ref({});
        const visibleResponse = ref(false);
        const feedbackVisible = ref(false);
        const lastDate = ref("");
        const crudVariables = crud();

        const viewResponse = (item) => {
            visibleResponse.value = true;
            recordData.value = item;
        };

        const closed = () => {
            visibleResponse.value = false;
        };
        const feedbackClosed = () => {
            feedbackVisible.value = false;
        };

        const feedbackOpened = (item) => {
            feedbackVisible.value = true;
            recordData.value = item;
        };

        const activeKey = ref("active");

        const extraFilters = ref({
            status: "active",
        });
        onMounted(() => {
            const assignedSurvey = axiosAdmin.get(
                "get-assigned-survey?fields=id,xid,title,description,start_date,last_date,employeeSurvey{id,xid,feedback_id,x_feedback_id,employee_answer,feedback_form_fields,question}"
            );
            Promise.all([assignedSurvey]).then(([assignedSurveyResponse]) => {
                resetData(assignedSurveyResponse.data);
            });
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: "get-assigned-survey?fields=id,xid,title,description,start_date,last_date,employeeSurvey{id,xid,feedback_id,x_feedback_id,employee_answer,feedback_form_fields,question}",
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;
            crudVariables.fetch({
                page: 1,
            });
        };
        const remainDays = (complete_date) => {
            const today = dayjs();
            const targetDate = dayjs(complete_date);
            if (today.isAfter(targetDate)) {
                var differenceInDays = today.diff(targetDate, "day");
            }
            if (differenceInDays >= 1) {
                return false;
            } else {
                return true;
            }
        };

        const resetData = (items) => {
            forEach(items, (item) => {
                var newSurvey = {};
                var submit = "";
                var count = 1;
                forEach(item.employee_survey, (survey) => {
                    if (count == 1) {
                        submit = survey.created_at;
                    }
                    count += 1;
                });
                newSurvey = {
                    ...item,
                    submit_date: submit,
                };
                survies.value.push(newSurvey);
            });
        };

        return {
            permsArray,
            ...crudVariables,
            appSetting,
            columns,
            filterableColumns,
            survies,
            loading,
            dayjs,
            recordData,
            visibleResponse,
            viewResponse,
            closed,
            lastDate,
            activeKey,
            extraFilters,
            setUrlData,
            remainDays,
            feedbackVisible,
            feedbackClosed,
            feedbackOpened,
        };
    },
};
</script>
