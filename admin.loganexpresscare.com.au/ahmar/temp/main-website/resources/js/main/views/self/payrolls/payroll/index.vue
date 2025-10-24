<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.payrolls`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-row align="middle" justify="space-between">
                <a-col>
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
                            {{ $t(`menu.payrolls`) }}
                        </a-breadcrumb-item>
                    </a-breadcrumb>
                </a-col>
                <a-col>
                    <a-switch
                        v-model:checked="salaryVisibleAll"
                        :checked-children="$t('common.visible')"
                        :un-checked-children="$t('common.hidden')"
                        :style="{ top: '-20px', marginRight: '2px' }"
                    />
                </a-col>
            </a-row>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
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
        <View
            :addEditType="addEditType"
            :visible="addEditVisible"
            :url="addEditUrl"
            @addEditSuccess="addEditSuccess"
            @closed="onCloseAddEdit"
            :formData="formData"
            :data="viewData"
            :pageTitle="$t('payroll.view_salary')"
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
                            <template v-if="column.dataIndex == 'payment_date'">
                                {{
                                    record.payment_date != null
                                        ? formatDate(record.payment_date)
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex == 'month'">
                                {{ getMonthNameByNumber(record.month) }}
                                {{ record.year }}
                            </template>
                            <template v-if="column.dataIndex == 'net_salary'">
                                <SalaryVisibility
                                    :salary="record.net_salary"
                                    :visibleAll="salaryVisibleAll"
                                />
                            </template>
                            <template v-if="column.dataIndex == 'status'">
                                <div v-if="record.status == 'generated'">
                                    <a-tag color="yellow">
                                        {{ $t(`payroll.generated`) }}
                                    </a-tag>
                                </div>
                                <div v-if="record.status == 'paid'">
                                    <a-tag color="green">
                                        {{ $t(`common.paid`) }}
                                    </a-tag>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="() => editItem(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <PdfDownload
                                        :title="$t('payroll.payroll_slip')"
                                        :fileName="`${
                                            record.user?.name
                                        }-${getMonthNameByNumber(record.month)}-${
                                            record.year
                                        }`"
                                        :url="`payroll-pdf/${record.xid}`"
                                        :payload="{
                                            title: `${$t('payroll.payroll_slip')} ${
                                                record.year
                                            }`,
                                            year: record.year,
                                            lang: selectedLang,
                                        }"
                                    />
                                </a-space>
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
import { EyeOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import common from "../../../../../common/composable/common";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import crud from "../../../../../common/composable/crud";
import View from "./View.vue";
import SalaryVisibility from "../../../../components/SalaryVisibility.vue";
import PdfDownload from "../../../../components/pdf/PdfDownload.vue";

export default {
    components: {
        EyeOutlined,
        AdminPageHeader,
        View,
        SalaryVisibility,
        PdfDownload,
    },
    setup() {
        const { url, columns, filterableColumns, hashableColumns, initData } = fields();
        const crudVariables = crud();
        const {
            permsArray,
            appSetting,
            formatAmountCurrency,
            formatDate,
            dayjs,
        } = common();
        const { getMonthNameByNumber, monthArrays } = hrmManagement();
        const extraFilters = ref({
            month: dayjs().format("MM"),
            year: dayjs(),
        });
        const salaryVisibleAll = ref(false);

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.initData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];

            crudVariables.tableUrl.value = {
                extraFilters: {
                    month: extraFilters.value.month,
                    year: extraFilters.value.year.format("YYYY"),
                },
                url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            ...crudVariables,
            columns,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            setUrlData,
            monthArrays,
            extraFilters,
            getMonthNameByNumber,
            formatAmountCurrency,
            salaryVisibleAll,
        };
    },
};
</script>
