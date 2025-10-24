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
                    {{ $t(`menu.assigned_survey`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-date-picker
                            v-model:value="extraFilters.year"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            picker="year"
                            @change="setUrlData"
                            style="width: 100%"
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="extraFilters.month"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.month')])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
                        >
                            <a-select-option
                                v-for="month in monthArrays"
                                :key="month.name"
                                :value="month.value"
                                :title="month.name"
                            >
                                {{ month.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs v-model:activeKey="extraFilters.status" @change="setUrlData">
                    <a-tab-pane key="active" :tab="`${$t('feedback.active')}`" />
                    <a-tab-pane key="expired" :tab="`${$t('feedback.expired')}`" />
                </a-tabs>
            </a-col>
        </a-row>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'title'">
                                <span v-if="record.feedback_given == 1">
                                    <a-typography-link
                                        type="link"
                                        @click="viewResponse(record)"
                                    >
                                        {{ record.feedback.title }}
                                    </a-typography-link>
                                </span>
                                <span v-else>
                                    <a-typography-link
                                        type="link"
                                        @click="feedbackOpened(record)"
                                    >
                                        {{ record.feedback.title }}
                                    </a-typography-link>
                                </span>
                            </template>
                            <template v-if="column.dataIndex === 'submit_date'">
                                {{
                                    record.submit_date
                                        ? formatDate(record.submit_date)
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'last_date'">
                                {{ formatDate(record.feedback.last_date) }}
                            </template>
                            <template v-if="column.dataIndex === 'rating'">
                                <div v-if="record.rating">
                                    <a-rate :value="record.rating" allow-half disabled />
                                    {{ formatRating(record.rating) }}
                                </div>
                                <span v-else>-</span>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <span v-if="record.feedback_given == 1">
                                    <a-button
                                        type="primary"
                                        @click="viewResponse(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                </span>

                                <span v-else>
                                    <a-button
                                        type="primary"
                                        @click="feedbackOpened(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><PlayCircleOutlined /></template>
                                    </a-button>
                                </span>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <ViewResponse
            :data="recordData"
            :visible="visibleResponse"
            :pageTitle="$t('assigned_surve.assigned_survey_details')"
            @close="closed"
        />
        <Feedback
            :data="questionData"
            :visible="feedbackVisible"
            @close="feedbackClosed()"
            @addSuccess="feedbackClosed()"
            :pageTitle="$t('assigned_surve.assigned_survey_details')"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref } from "vue";
import { PlayCircleOutlined, EyeOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ViewResponse from "./ViewResponse.vue";
import Feedback from "./Feedback.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";

export default {
    components: {
        PlayCircleOutlined,
        EyeOutlined,

        AdminPageHeader,
        ViewResponse,
        Feedback,
    },
    setup() {
        const { columns, filterableColumns } = fields();
        const { monthArrays } = hrmManagement();
        const datatableVariables = datatable();
        const { formatDate, dayjs } = common();
        const recordData = ref({});
        const visibleResponse = ref(false);
        const questionData = ref({});
        const feedbackVisible = ref(false);
        const extraFilters = ref({
            title: "",
            year: dayjs(),
            month: undefined,
            status: "active",
        });

        const formatRating = (item) => {
            return item.toFixed(2);
        };

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `self/get-assigned-survey?fields=id,xid,rating,allowed,data,submit_date,feedback_given,user_id,x_user_id,user{id,xid,name},feedback_id,x_feedback_id,feedback{id,xid,title,last_date,visible_to,form_id,x_form_id,description,feedback_form_fields}`,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
            };
            datatableVariables.fetch({
                page: 1,
            });
        };

        const viewResponse = (item) => {
            visibleResponse.value = true;
            recordData.value = item;
        };

        const closed = () => {
            visibleResponse.value = false;
        };

        const feedbackClosed = () => {
            feedbackVisible.value = false;
            setUrlData();
        };

        const feedbackOpened = (item) => {
            feedbackVisible.value = true;
            questionData.value = item;
        };

        return {
            ...datatableVariables,
            columns,
            filterableColumns,
            formatDate,
            setUrlData,

            viewResponse,
            closed,
            visibleResponse,
            recordData,
            feedbackVisible,
            questionData,
            feedbackClosed,
            feedbackOpened,
            formatRating,
            extraFilters,
            monthArrays,
        };
    },
};
</script>
