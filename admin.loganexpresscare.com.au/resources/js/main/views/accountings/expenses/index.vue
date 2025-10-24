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
                    {{ $t(`menu.finance`) }}
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
                    <template
                        v-if="
                            permsArray.includes(`expenses_create`) ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("expense.add") }}
                        </a-button>
                    </template>
                    <ExpenseEntry :expenseType="extraFilters.expense_type" />
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('expenses_delete') ||
                                permsArray.includes('admin'))
                        "
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="4" :xl="4">
                        <a-select
                            v-model:value="extraFilters.status"
                            @change="statusUpdated"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('expense.status'),
                                ])
                            "
                            style="width: 100%"
                            allowClear
                        >
                            <a-select-option
                                v-if="
                                    extraFilters.expense_type ==
                                    'employee_claims'
                                "
                                value="pending"
                            >
                                {{ $t("expense.pending") }}
                            </a-select-option>
                            <a-select-option value="approved">
                                {{ $t("expense.approved") }}
                            </a-select-option>
                            <a-select-option value="rejected">
                                {{ $t("expense.rejected") }}
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
                                v-for="expenseCategory in preFetchData.expenseCategories"
                                :key="expenseCategory.xid"
                                :value="expenseCategory.xid"
                                :label="expenseCategory.name"
                            >
                                {{ expenseCategory.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.user_id"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('expense.user'),
                                ])
                            "
                            @change="reFetchDatatable"
                            :allowClear="true"
                            optionFilterProp="label"
                        >
                            <a-select-option
                                v-for="staffMember in preFetchData.staffMembers"
                                :key="staffMember.xid"
                                :value="staffMember.xid"
                                :label="staffMember.name"
                            >
                                <user-list-display
                                    :user="staffMember"
                                    whereToShow="select"
                                />
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                        <DateRangePicker
                            @dateTimeChanged="
                                (changedDateTime) => {
                                    extraFilters.dates = changedDateTime;
                                    reFetchDatatable();
                                }
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs
                    v-model:activeKey="extraFilters.expense_type"
                    @change="expenseTypeChanged"
                >
                    <a-tab-pane
                        key="employee_claims"
                        :tab="`${$t('expense.employee_claims')}`"
                    >
                    </a-tab-pane>
                    <a-tab-pane
                        v-if="
                            permsArray.includes('admin') ||
                            permsArray.includes('manage_company_expense')
                        "
                        key="company_claims"
                        :tab="`${$t('expense.company_claims')}`"
                    />
                </a-tabs>
            </a-col>
        </a-row>

        <a-row class="mt-20">
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
                        <template #bodyCell="{ column, text, record }">
                            <template
                                v-if="
                                    column.dataIndex === 'expense_category_id'
                                "
                            >
                                {{ record.expense_category?.name }}
                            </template>
                            <template v-if="column.dataIndex === 'payee_id'">
                                {{ record.payee ? record.payee.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'account_id'">
                                {{ record.account?.name }}
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <a-tag :color="expenseStatusColors[text]">
                                    {{ $t(`common.${text}`) }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                <a-button
                                    type="link"
                                    @click="openUserView(record)"
                                >
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                {{ formatAmountCurrency(text) }}
                            </template>
                            <template v-if="column.dataIndex === 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template
                                v-if="column.dataIndex === 'reference_number'"
                            >
                                <span v-if="record.reference_number">
                                    {{ record.reference_number }}
                                </span>
                                <template v-else> - </template>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        v-if="
                                            permsArray.includes('admin') &&
                                            record.status == 'pending'
                                        "
                                        type="primary"
                                        @click="updateExpenseStatus(record)"
                                    >
                                        <template #icon
                                            ><AppstoreOutlined
                                        /></template>
                                    </a-button>
                                    <a-button
                                        type="primary"
                                        @click="viewExpense(record)"
                                    >
                                        <template #icon
                                            ><EyeOutlined
                                        /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes(
                                                `expenses_edit`
                                            ) || permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                    >
                                        <template #icon
                                            ><EditOutlined
                                        /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes(
                                                `expenses_delete`
                                            ) || permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                    >
                                        <template #icon
                                            ><DeleteOutlined
                                        /></template>
                                    </a-button>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <user-view-page
            :visible="userOpen"
            :userId="userId"
            @closed="closeUser"
        />

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
            :expenseTypes="extraFilters.expense_type"
            :activeTab="extraFilters.expense_type"
            @addListSuccess="reSetFormData"
        />

        <UpdateStatus
            :data="visibleStatusData"
            :visible="visibleStatus"
            @close="closedStatus"
            @setUrlData="reFetchDatatable"
            :pageTitle="$t('expense.update_expense_status')"
        />

        <View
            :data="expenseData"
            :visible="visible"
            :pageTitle="$t('expense.expense_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>

<script>
import { onMounted, watch, ref, computed, reactive } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
    AppstoreOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import AddEdit from "./AddEdit.vue";
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import View from "./View.vue";
import { useI18n } from "vue-i18n";
import UpdateStatus from "./UpdateStatus.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import ExpenseEntry from "./ExpenseEntry.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        AppstoreOutlined,
        AddEdit,
        AdminPageHeader,
        DateRangePicker,
        UserInfo,
        View,
        UpdateStatus,
        UserListDisplay,
        DownloadOutlined,
        ExpenseEntry,
    },
    setup() {
        const {
            url,
            addEditUrl,
            initData,
            employeeClaimsColumns,
            companyClaimsColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const {
            appSetting,
            permsArray,
            formatDate,
            formatAmountCurrency,
            formatDateTime,
            expenseStatusColors,
            selectedLang,
        } = common();
        const extraFilters = ref({
            dates: [],
            expense_type: "employee_claims",
            status: undefined,
        });
        const expenseData = ref({});
        const visible = ref(false);
        const { t } = useI18n();
        const visibleStatus = ref(false);
        const visibleStatusData = ref({});
        const userOpen = ref(false);
        const userId = ref(undefined);
        const preFetchData = reactive({
            expenseCategories: [],
            staffMembers: [],
        });

        const openUserView = (item) => {
            userId.value = item.x_user_id;
            userOpen.value = true;
        };

        const filters = reactive({
            expense_category_id: undefined,
            user_id: undefined,
        });

        const getPreFetchData = () => {
            const expenseCategoriesPromise = axiosAdmin.get(
                "expense-categories",
                {
                    params: { expense_type: extraFilters.value.expense_type },
                }
            );
            const staffMembersPromise = axiosAdmin.get("users");

            Promise.all([expenseCategoriesPromise, staffMembersPromise]).then(
                ([expenseCategoriesResponse, staffMembersResponse]) => {
                    preFetchData.expenseCategories =
                        expenseCategoriesResponse.data;
                    preFetchData.staffMembers = staffMembersResponse.data;
                }
            );
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        const columns = computed(() => {
            return extraFilters.value.expense_type == "employee_claims"
                ? employeeClaimsColumns
                : companyClaimsColumns;
        });

        const viewExpense = (item) => {
            expenseData.value = item;
            visible.value = true;
        };

        onMounted(() => {
            getPreFetchData();

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "expense";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];

            reFetchDatatable();
        });

        const statusUpdated = (newStatus) => {
            extraFilters.value.status = newStatus;
            reFetchDatatable();
        };

        const updateExpenseStatus = (item) => {
            visibleStatusData.value = item;
            visibleStatus.value = true;
        };

        const expenseTypeChanged = (value) => {
            getPreFetchData();
            extraFilters.value = {
                ...extraFilters.value,
                dates: [],
                status: undefined,
            };

            reFetchDatatable();
        };

        const closed = () => {
            visible.value = false;
        };

        const closedStatus = () => {
            reFetchDatatable();
            visibleStatus.value = false;
        };

        const reFetchDatatable = () => {
            crudVariables.tableUrl.value = {
                url,
                filters,
                extraFilters,
            };

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            columns,
            appSetting,
            formatDate,
            ...crudVariables,
            hashableColumns,
            filters,
            extraFilters,
            preFetchData,
            reFetchDatatable,
            permsArray,
            formatAmountCurrency,
            formatDateTime,
            expenseData,
            visible,
            viewExpense,
            closed,
            expenseStatusColors,
            updateExpenseStatus,
            visibleStatus,
            visibleStatusData,
            statusUpdated,
            closedStatus,
            expenseTypeChanged,
            userOpen,
            userId,
            openUserView,
            closeUser,
            selectedLang,
        };
    },
};
</script>
