<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.expenses`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.expenses`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <a-button type="primary" @click="addItem">
                        <PlusOutlined />
                        {{ $t("expense.add") }}
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
                            @change="reFetchDatatable"
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
                            @change="reFetchDatatable"
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.expense_category_id"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('expense.expense_category'),
                                ])
                            "
                            @change="reFetchDatatable"
                            :allowClear="true"
                            optionFilterProp="label"
                        >
                            <a-select-option
                                v-for="expenseCategory in expenseCategories"
                                :key="expenseCategory.xid"
                                :value="expenseCategory.xid"
                                :label="expenseCategory.name"
                            >
                                {{ expenseCategory.name }}
                            </a-select-option>
                        </a-select>
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
        <a-tabs v-model:activeKey="extraFilters.status" @change="reFetchDatatable">
            <a-tab-pane key="pending" :tab="`${$t('expense.pending')}`" />
            <a-tab-pane key="approved" :tab="`${$t('expense.approved')}`" />
            <a-tab-pane key="rejected" :tab="`${$t('expense.rejected')}`" />
        </a-tabs>
        <a-row class="mt-20">
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, text, record }">
                            <template v-if="column.dataIndex === 'expense_category_id'">
                                {{ record.expense_category.name }}
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                {{ formatAmountCurrency(text) }}
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <a-tag :color="expenseStatusColors[text]">
                                    {{ $t(`common.${text}`) }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template v-if="column.dataIndex === 'reference_number'">
                                <span v-if="record.reference_number">
                                    {{ record.reference_number }}
                                </span>
                                <template v-else> - </template>
                            </template>
                            <template v-if="column.dataIndex === 'notes'">
                                <span v-if="record.notes">
                                    {{ record.notes }}
                                </span>
                                <template v-else> - </template>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    type="primary"
                                    @click="viewExpense(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="record.status === 'pending'"
                                    type="primary"
                                    @click="editItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon>
                                        <EditOutlined />
                                    </template>
                                </a-button>

                                <a-button
                                    v-if="record.status === 'pending'"
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
        <View
            :data="expenseData"
            :visible="visible"
            :pageTitle="$t('expense.expense_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>

<script>
import { onMounted, watch, ref } from "vue";
import {
    PlusOutlined,
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import AddEdit from "./AddEdit.vue";
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";
import dayjs from "dayjs";

export default {
    components: {
        PlusOutlined,
        EyeOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        View,
    },
    setup() {
        const { url, addEditUrl, initData, columns, hashableColumns } = fields();
        const crudVariables = crud();
        const {
            appSetting,
            permsArray,
            formatDateTime,
            formatAmountCurrency,
            expenseStatusColors,
        } = common();
        const { monthArrays } = hrmManagement();
        const extraFilters = ref({
            status: "pending",
            year: dayjs(),
            month: undefined,
        });

        const filters = ref({
            expense_category_id: undefined,
            user_id: undefined,
        });
        const visible = ref(false);
        const expenseData = ref({});
        const expenseCategories = ref([]);
        const expenseCategoryUrl = "self/expense-categories?limit=10000";

        const viewExpense = (item) => {
            visible.value = true;
            expenseData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        onMounted(() => {
            getPreFetchData();

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "expense";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        });

        const getPreFetchData = () => {
            const expenseCategoriesPromise = axiosAdmin.get(expenseCategoryUrl);

            Promise.all([expenseCategoriesPromise]).then(
                ([expenseCategoriesResponse]) => {
                    expenseCategories.value = expenseCategoriesResponse.data;
                    reFetchDatatable();
                }
            );
        };

        const reFetchDatatable = () => {
            crudVariables.tableUrl.value = {
                url,
                filters,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
            };

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            columns,
            appSetting,
            ...crudVariables,
            filters,
            extraFilters,
            reFetchDatatable,
            permsArray,
            formatAmountCurrency,
            formatDateTime,
            visible,
            expenseData,
            viewExpense,
            closed,
            expenseStatusColors,
            expenseCategories,
            monthArrays,
        };
    },
};
</script>
