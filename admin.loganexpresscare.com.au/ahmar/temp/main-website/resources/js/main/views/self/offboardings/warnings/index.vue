<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.warnings`)" class="p-0" />
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
                    {{ $t(`menu.warnings`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                                <a-date-picker
                                    v-model:value="extraFilters.year"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('holiday.year'),
                                        ])
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
                                                $t('warning.title'),
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
                                        <a-typography-link
                                            type="link"
                                            @click="viewWarning(record)"
                                        >
                                            {{ record.title }}
                                        </a-typography-link>
                                    </template>
                                    <template v-if="column.dataIndex === 'user_id'">
                                        {{ record.user?.name }}
                                    </template>
                                    <template v-if="column.dataIndex === 'description'">
                                        {{
                                            record.description ? record.description : "-"
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'warning_date'">
                                        {{ formatDateTime(record.warning_date) }}
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-button
                                            type="primary"
                                            @click="viewWarning(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><EyeOutlined /></template>
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
                <View
                    :data="warningData"
                    :visible="visibleWarning"
                    @close="closed"
                    :pageTitle="$t('warning.warning_details')"
                />
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { onMounted, ref, watch } from "vue";
import { PlusOutlined, EyeOutlined } from "@ant-design/icons-vue";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import hrmManagement from "../../../../../common/composable/hrmManagement";

export default {
    components: {
        PlusOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        View,
    },
    setup() {
        const { permsArray, formatDateTime, dayjs } = common();
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
        const visibleWarning = ref(false);
        const warningData = ref({});
        const extraFilters = ref({
            year: dayjs(),
            month: undefined,
        });

        const viewWarning = (item) => {
            visibleWarning.value = true;
            warningData.value = item;
        };

        const closed = () => {
            visibleWarning.value = false;
        };

        onMounted(() => {
            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "warning";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];

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
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            hashableColumns,
            setUrlData,

            extraFilters,
            formatDateTime,
            visibleWarning,
            warningData,
            viewWarning,
            closed,
            monthArrays,
        };
    },
};
</script>
