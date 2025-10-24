<template>
    <div>
        <AdminPageHeader>
            <template #header>
                <a-page-header :title="$t(`menu.complaints`)" class="p-0" />
            </template>
            <template #breadcrumb>
                <a-breadcrumb separator="-" style="font-size: 12px">
                    <a-breadcrumb-item>
                        <router-link :to="{ name: 'admin.dashboard.index' }">
                            {{ $t(`menu.dashboard`) }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>
                        {{ $t(`menu.offboardings`) }}
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>
                        {{ $t(`menu.complaints`) }}
                    </a-breadcrumb-item>
                </a-breadcrumb>
            </template>
        </AdminPageHeader>

        <admin-page-filters>
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                    <a-space>
                        <template v-if="extraFilters.reports == 'by_you'">
                            <a-button type="primary" @click="addItem">
                                <PlusOutlined />
                                {{ $t("complaint.add") }}
                            </a-button>
                        </template>
                        <a-button
                            v-if="table.selectedRowKeys.length > 0"
                            type="primary"
                            @click="showSelectedDeleteConfirm"
                            danger
                        >
                            <template #icon><DeleteOutlined /></template>
                            {{ $t("common.delete") }}
                        </a-button>
                    </a-space>
                </a-col>
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
                                :allowClear="false"
                            />
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                            <a-select
                                v-model:value="extraFilters.month"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('holiday.month'),
                                    ])
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
                        <a-col :xs="24" :sm="24" :md="16" :lg="8" :xl="8">
                            <a-input-group compact>
                                <a-input-search
                                    style="width: 100%"
                                    v-model:value="table.searchString"
                                    show-search
                                    @change="onTableSearch"
                                    @search="onTableSearch"
                                    :loading="table.filterLoading"
                                    :placeholder="
                                        $t('common.placeholder_search_text', [
                                            $t('complaint.title'),
                                        ])
                                    "
                                />
                            </a-input-group>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
        </admin-page-filters>

        <admin-page-table-content>
            <AddEdit
                :addEditType="addEditType"
                :visible="addEditVisible"
                :url="addEditUrl"
                @addEditSuccess="addEditSuccess"
                @closed="onCloseAddEdit"
                :formData="formData"
                :data="viewData"
                :pageTitle="pageTitle"
                :successMessage="successMessage"
            />

            <a-row>
                <a-col :span="24">
                    <a-tabs v-model:activeKey="extraFilters.reports" @change="setUrlData">
                        <a-tab-pane
                            key="against_you"
                            :tab="`${$t('complaint.against_you')}`"
                        />
                        <a-tab-pane key="by_you" :tab="`${$t('complaint.by_you')}`" />
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
                            <template #bodyCell="{ column, record, text }">
                                <template v-if="column.dataIndex === 'title'">
                                    <a-typography-link
                                        type="link"
                                        @click="viewComplaint(record)"
                                    >
                                        {{ record.title }}
                                    </a-typography-link>
                                </template>
                                <template v-if="column.dataIndex === 'proff_of_document'">
                                    <span v-if="record.proff_of_document">
                                        <a-image
                                            :width="32"
                                            :src="record.proff_of_document_url"
                                    /></span>
                                    <span v-else>-</span>
                                </template>

                                <template v-if="column.dataIndex === 'to_user_id'">
                                    {{ record.x_to_user_id ? record.to_staff.name : "-" }}
                                </template>
                                <template v-if="column.dataIndex == 'date_time'">
                                    {{ formatDateTime(record.date_time) }}
                                </template>
                                <template v-if="column.dataIndex === 'status'">
                                    <a-tag :color="complaintsColors[text]">
                                        {{ $t(`common.${text}`) }}
                                    </a-tag>
                                </template>
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button
                                        type="primary"
                                        @click="viewComplaint(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            record.status == 'pending' &&
                                            extraFilters.reports == 'by_you'
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EditOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            record.status == 'pending' &&
                                            extraFilters.reports == 'by_you'
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><DeleteOutlined /></template>
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                    </div>
                </a-col>
            </a-row>
        </admin-page-table-content>
    </div>
    <View
        :data="recordComplaint"
        :visible="visibleComplaint"
        @close="closed"
        :pageTitle="$t('complaint.complaint_details')"
    />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import dayjs from "dayjs";
import hrmManagement from "../../../../../common/composable/hrmManagement";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        View,
    },
    setup() {
        const { permsArray, complaintsColors, formatDateTime } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const { monthArrays } = hrmManagement();
        const crudVariables = crud();
        const visibleComplaint = ref(false);
        const recordComplaint = ref({});

        const viewComplaint = (item) => {
            visibleComplaint.value = true;
            recordComplaint.value = item;
        };
        const closed = () => {
            visibleComplaint.value = false;
        };

        const extraFilters = ref({
            reports: "against_you",
            year: dayjs(),
            month: undefined,
        });

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "complaint";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = { ...hashableColumns };
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            complaintsColors,
            setUrlData,
            formatDateTime,
            extraFilters,
            closed,
            viewComplaint,
            recordComplaint,
            visibleComplaint,
            monthArrays,
        };
    },
};
</script>
