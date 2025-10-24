<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.all_holidays`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.holidays`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.all_holidays`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
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
                                        $t('common.name'),
                                    ])
                                "
                            />
                        </a-input-group>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
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
                <a-tabs
                    v-model:activeKey="extraFilters.holiday_type"
                    @change="setUrlData"
                >
                    <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                    <a-tab-pane key="holiday" :tab="`${$t('common.holiday')}`" />
                    <a-tab-pane key="weekend" :tab="`${$t('common.weekends')}`" />
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
                            <template v-if="column.dataIndex === 'name'">
                                <span v-if="record.is_half_day == 1" style="color: green">
                                    {{ record.name
                                    }}{{ " (" + $t("holiday.half_holiday") + ")" }}
                                </span>
                                <span v-else>
                                    {{ record.name }}
                                </span>
                            </template>
                            <template v-if="column.dataIndex === 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import hrmManagement from "../../../../common/composable/hrmManagement";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import { useI18n } from "vue-i18n";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,

        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const { columns, filterableColumns } = fields();
        const datatableVariables = datatable();
        const { permsArray, appSetting, formatDate, dayjs } = common();
        const { monthArrays } = hrmManagement();
        const extraFilters = ref({
            holiday_type: "all",
            year: dayjs(),
            month: undefined,
        });

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `holidays?fields=id,xid,name,year,date,month,is_weekend,is_half_day,half_holiday`,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
            };

            datatableVariables.table.filterableColumns = filterableColumns;
            datatableVariables.table.sorter = { field: "date", order: "desc" };

            datatableVariables.fetch({
                page: 1,
            });
        };

        return {
            ...datatableVariables,
            columns,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            setUrlData,
            monthArrays,
            extraFilters,
        };
    },
};
</script>
