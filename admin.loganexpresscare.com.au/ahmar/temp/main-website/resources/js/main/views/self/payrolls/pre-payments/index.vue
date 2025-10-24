<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.pre_payments`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.payrolls`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.pre_payments`) }}
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
                            :allowClear="false"
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
                            <template v-if="column.dataIndex == 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template v-if="column.dataIndex == 'amount'">
                                {{ formatAmountCurrency(record.amount) }}
                            </template>
                            <template v-if="column.dataIndex == 'deduct_month'">
                                {{ getMonthNameByNumber(record.payroll_month) }}
                                {{ record.payroll_year }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="viewPrePaymentData(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <View
            :data="prePaymentData"
            :visible="visible"
            :pageTitle="$t('pre_payment.pre_payment_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import { EyeOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import datatable from "../../../../../common/composable/datatable";
import common from "../../../../../common/composable/common";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";

export default {
    components: {
        EyeOutlined,
        View,
        AdminPageHeader,
    },
    setup() {
        const { columns, filterableColumns, hashableColumns } = fields();
        const datatableVariables = datatable();
        const {
            permsArray,
            appSetting,
            formatAmountCurrency,
            formatDateTime,
            dayjs,
        } = common();
        const { getMonthNameByNumber, monthArrays } = hrmManagement();
        const extraFilters = ref({
            year: dayjs(),
            month: undefined,
        });
        const prePaymentData = ref({});
        const visible = ref(false);

        onMounted(() => {
            setUrlData();
        });

        const viewPrePaymentData = (item) => {
            visible.value = true;
            prePaymentData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `self/pre-payments?fields=id,xid,user_id,x_user_id,user{id,xid,name},amount,notes,payroll_month,payroll_year,date_time`,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
            };

            datatableVariables.table.filterableColumns = filterableColumns;
            datatableVariables.hashable.value = { ...hashableColumns };
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

            setUrlData,
            monthArrays,
            extraFilters,
            getMonthNameByNumber,
            formatAmountCurrency,
            formatDateTime,
            viewPrePaymentData,
            visible,
            closed,
            prePaymentData,
        };
    },
};
</script>
