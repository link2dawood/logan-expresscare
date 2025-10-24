<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical" id="add_edit_expense_form">
            <a-tabs v-model:activeKey="addEditActiveTab">
                <a-tab-pane
                    key="expense_details"
                    :tab="`${$t('expense.expense_details')}`"
                >
                    <a-row :gutter="16">
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            v-if="
                                permsArray.includes('admin') ||
                                permsArray.includes('manage_company_expense')
                            "
                        >
                            <a-form-item
                                :label="$t('expense.expense_type')"
                                name="expense_type"
                                :help="
                                    rules.expense_type
                                        ? rules.expense_type.message
                                        : null
                                "
                                :validateStatus="
                                    rules.expense_type ? 'error' : null
                                "
                                class="required"
                            >
                                <a-select
                                    v-model:value="newFormData.expense_type"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('expense.expense_type'),
                                        ])
                                    "
                                    @change="setUrlData"
                                    disabled
                                >
                                    <a-select-option value="employee_claims">
                                        {{ $t("expense.employee_claims") }}
                                    </a-select-option>
                                    <a-select-option value="company_claims">
                                        {{ $t("expense.company_claims") }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="
                                permsArray.includes('admin') ||
                                permsArray.includes('manage_company_expense')
                                    ? 12
                                    : 24
                            "
                            :lg="
                                permsArray.includes('admin') ||
                                permsArray.includes('manage_company_expense')
                                    ? 12
                                    : 24
                            "
                        >
                            <a-form-item
                                :label="$t('expense.expense_category')"
                                name="expense_category_id"
                                :help="
                                    rules.expense_category_id
                                        ? rules.expense_category_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.expense_category_id ? 'error' : null
                                "
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="
                                            newFormData.expense_category_id
                                        "
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('expense.expense_category'),
                                            ])
                                        "
                                        :allowClear="
                                            addEditType == 'add' ? true : false
                                        "
                                        optionFilterProp="label"
                                        show-search
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
                                    <ExpenseCategoryAddButton
                                        @onAddSuccess="expenseCategoryAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row
                        :gutter="16"
                        v-show="newFormData.expense_type !== 'company_claims'"
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('expense.created_by_user')"
                                name="user_id"
                                :help="
                                    rules.user_id ? rules.user_id.message : null
                                "
                                :validateStatus="rules.user_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="newFormData.user_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('expense.user'),
                                            ])
                                        "
                                        :allowClear="
                                            addEditType == 'add' ? true : false
                                        "
                                        optionFilterProp="label"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="staffMember in staffMembers"
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
                                    <StaffAddButton
                                        @onAddSuccess="staffMemberAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('expense.amount')"
                                name="amount"
                                :help="
                                    rules.amount ? rules.amount.message : null
                                "
                                :validateStatus="rules.amount ? 'error' : null"
                                class="required"
                            >
                                <a-input-number
                                    v-model:value="newFormData.amount"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('expense.amount'),
                                        ])
                                    "
                                    min="0"
                                    style="width: 100%"
                                >
                                    <template #addonBefore>
                                        {{ appSetting.currency.symbol }}
                                    </template>
                                </a-input-number>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('expense.date_time')"
                                name="date_time"
                                :help="
                                    rules.date_time
                                        ? rules.date_time.message
                                        : null
                                "
                                :validateStatus="
                                    rules.date_time ? 'error' : null
                                "
                                class="required"
                            >
                                <DateTimePicker
                                    :dateTime="newFormData.date_time"
                                    @dateTimeChanged="
                                        (changedDateTime) =>
                                            (newFormData.date_time =
                                                changedDateTime)
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('expense.reference_number')"
                                name="reference_number"
                                :help="
                                    rules.reference_number
                                        ? rules.reference_number.message
                                        : null
                                "
                                :validateStatus="
                                    rules.reference_number ? 'error' : null
                                "
                            >
                                <a-input-number
                                    v-model:value="newFormData.reference_number"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('expense.reference_number'),
                                        ])
                                    "
                                    min="0"
                                    style="width: 100%"
                                >
                                </a-input-number>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('expense.bill')"
                                name="bill"
                                :help="rules.bill ? rules.bill.message : null"
                                :validateStatus="rules.bill ? 'error' : null"
                            >
                                <UploadFile
                                    :acceptFormat="'image/*,.pdf'"
                                    :formData="newFormData"
                                    folder="expenses"
                                    uploadField="bill"
                                    @onFileUploaded="
                                        (file) => {
                                            newFormData.bill = file.file;
                                            newFormData.bill_url =
                                                file.file_url;
                                        }
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('expense.notes')"
                                name="notes"
                                :help="rules.notes ? rules.notes.message : null"
                                :validateStatus="rules.notes ? 'error' : null"
                            >
                                <a-textarea
                                    v-model:value="newFormData.notes"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('expense.notes'),
                                        ])
                                    "
                                    :rows="4"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane
                    key="transaction_type"
                    :tab="`${$t('expense.transaction_type')}`"
                    force-render
                >
                    <template v-if="newFormData.status == 'approved'">
                        <a-row :gutter="16">
                            <a-col
                                :span="24"
                                v-if="
                                    newFormData.expense_type ==
                                    'employee_claims'
                                "
                            >
                                <a-form-item
                                    :label="$t('expense.payment_status')"
                                    name="payment_status"
                                    :help="
                                        rules.payment_status
                                            ? rules.payment_status.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.payment_status ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-select
                                        v-model:value="
                                            newFormData.payment_status
                                        "
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('expense.payment_status'),
                                            ])
                                        "
                                    >
                                        <a-select-option :value="1">
                                            {{ $t("expense.right_now") }}
                                        </a-select-option>
                                        <a-select-option
                                            :value="0"
                                            v-if="
                                                newFormData.expense_type !=
                                                'company_claims'
                                            "
                                        >
                                            {{
                                                $t(
                                                    "expense.decuct_from_payroll"
                                                )
                                            }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row
                            :gutter="16"
                            v-if="
                                newFormData.payment_status == '0' &&
                                newFormData.expense_type != 'company_claims'
                            "
                        >
                            <a-col :span="24">
                                <a-form-item
                                    :label="$t('pre_payment.month')"
                                    name="month"
                                    :help="
                                        rules.month ? rules.month.message : null
                                    "
                                    :validateStatus="
                                        rules.month ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-date-picker
                                        v-model:value="monthYear"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('payroll.month'),
                                            ])
                                        "
                                        picker="month"
                                        style="width: 100%"
                                        :allowClear="false"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row
                            :gutter="16"
                            v-if="newFormData.payment_status == '1'"
                        >
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="$t('expense.account')"
                                    name="account_id"
                                    :help="
                                        rules.account_id
                                            ? rules.account_id.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.account_id ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <span style="display: flex">
                                        <a-select
                                            v-model:value="
                                                newFormData.account_id
                                            "
                                            :placeholder="
                                                $t(
                                                    'common.select_default_text',
                                                    [$t('expense.account')]
                                                )
                                            "
                                            :allowClear="
                                                addEditType == 'add'
                                                    ? true
                                                    : false
                                            "
                                            optionFilterProp="label"
                                            show-search
                                        >
                                            <a-select-option
                                                v-for="account in accounts"
                                                :key="account.xid"
                                                :value="account.xid"
                                                :label="account.name"
                                            >
                                                {{ account.name }}
                                            </a-select-option>
                                        </a-select>
                                        <AccountAddButton
                                            @onAddSuccess="accountAdded"
                                        />
                                    </span>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="$t('expense.payment_date')"
                                    name="payment_date"
                                    :help="
                                        rules.payment_date
                                            ? rules.payment_date.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.payment_date ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <DateTimePicker
                                        :dateTime="newFormData.payment_date"
                                        @dateTimeChanged="
                                            (changedDateTime) =>
                                                (newFormData.payment_date =
                                                    changedDateTime)
                                        "
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row
                            :gutter="16"
                            v-if="newFormData.payment_status == '1'"
                        >
                            <a-col
                                :span="24"
                                v-if="
                                    newFormData.expense_type == 'company_claims'
                                "
                            >
                                <a-form-item
                                    :label="$t('expense.payee')"
                                    name="payee_id"
                                    :help="
                                        rules.payee_id
                                            ? rules.payee_id.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.payee_id ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <span style="display: flex">
                                        <a-select
                                            v-model:value="newFormData.payee_id"
                                            :placeholder="
                                                $t(
                                                    'common.select_default_text',
                                                    [$t('expense.payee')]
                                                )
                                            "
                                            :allowClear="
                                                addEditType == 'add'
                                                    ? true
                                                    : false
                                            "
                                            optionFilterProp="label"
                                            show-search
                                        >
                                            <a-select-option
                                                v-for="payee in payees"
                                                :key="payee.xid"
                                                :value="payee.xid"
                                                :label="payee.name"
                                            >
                                                {{ payee.name }}
                                            </a-select-option>
                                        </a-select>
                                        <PayeeAddButton
                                            @onAddSuccess="payeeAdded"
                                        />
                                    </span>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </template>
                    <a-row :gutter="16">
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('expense.status')"
                                name="status"
                                :help="
                                    rules.status ? rules.status.message : null
                                "
                                :validateStatus="rules.status ? 'error' : null"
                            >
                                <a-radio-group
                                    v-model:value="newFormData.status"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('expense.status'),
                                        ])
                                    "
                                    button-style="solid"
                                    size="small"
                                >
                                    <a-radio-button value="approved">
                                        {{ $t("expense.approved") }}
                                    </a-radio-button>
                                    <a-radio-button value="rejected">
                                        {{ $t("expense.rejected") }}
                                    </a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
            </a-tabs>
        </a-form>
        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon> <SaveOutlined /> </template>
                {{
                    addEditType == "add"
                        ? $t("common.create")
                        : $t("common.update")
                }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import { includes, some } from "lodash-es";
import apiAdmin from "../../../../common/composable/apiAdmin";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import common from "../../../../common/composable/common";
import ExpenseCategoryAddButton from "../expense-categories/AddButton.vue";
import StaffAddButton from "../../../views/staff-members/users/StaffAddButton.vue";
import PayeeAddButton from "../../../views/accountings/payees/AddButton.vue";
import AccountAddButton from "../../../views/accountings/accounts/AddButton.vue";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
        "expenseTypes",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        UserInfo,
        SaveOutlined,

        ExpenseCategoryAddButton,
        StaffAddButton,
        PayeeAddButton,
        AccountAddButton,
        UploadFile,
        DateTimePicker,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules, addEditActiveTab } =
            apiAdmin("expense_details");
        const expenseCategories = ref({});
        const staffMembers = ref({});
        const { appSetting, disabledDate, permsArray, dayjs } = common();
        const expenseCategoryUrl = "expense-categories";
        const staffMemberUrl = "users";
        const payees = ref({});
        const payeeUrl = "payees";
        const accounts = ref({});
        const accountUrl = "accounts";
        const monthYear = ref(dayjs());

        const newFormData = ref({});

        const fetchExpenseCategories = () => {
            return axiosAdmin.get(expenseCategoryUrl, {
                params: { expense_type: props.expenseTypes },
            });
        };

        const fetchAllData = () => {
            const expenseCategoriesPromise = fetchExpenseCategories();
            const staffMembersPromise = axiosAdmin.get(staffMemberUrl);
            const payeesPromise = axiosAdmin.get(payeeUrl);
            const accountsPromise = axiosAdmin.get(accountUrl);

            Promise.all([
                expenseCategoriesPromise,
                staffMembersPromise,
                payeesPromise,
                accountsPromise,
            ]).then(
                ([
                    expenseCategoriesResponse,
                    staffMembersResponse,
                    payeeResponse,
                    accountResponse,
                ]) => {
                    expenseCategories.value = expenseCategoriesResponse.data;
                    staffMembers.value = staffMembersResponse.data;
                    payees.value = payeeResponse.data;
                    accounts.value = accountResponse.data;
                    newFormData.value.expense_type = props.expenseTypes;
                    newFormData.value.payment_status = 1;
                }
            );
        };

        onMounted(() => {
            fetchAllData();
        });

        const setUrlData = (value) => {};

        const onSubmit = () => {
            addEditRequestAdmin({
                id: "add_edit_expense_form",
                url: props.url,
                data: {
                    ...newFormData.value,
                    payroll_year: monthYear.value.format("YYYY"),
                    payroll_month: monthYear.value.format("MM"),
                },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
                error: (err) => {},
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const expenseCategoryAdded = (xid) => {
            axiosAdmin.get(expenseCategoryUrl).then((response) => {
                expenseCategories.value = response.data;
                newFormData.value.expense_category_id = xid;
            });
        };

        const staffMemberAdded = (xid) => {
            axiosAdmin.get(staffMemberUrl).then((response) => {
                staffMembers.value = response.data;
                newFormData.value.user_id = xid;
            });
        };

        const payeeAdded = (xid) => {
            axiosAdmin.get(payeeUrl).then((response) => {
                payees.value = response.data;
                newFormData.value.payee_id = xid;
            });
        };
        const accountAdded = (xid) => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
                newFormData.value.account_id = xid;
            });
        };

        watch(
            () => props.expenseTypes,
            (newVal, oldVal) => {
                fetchExpenseCategories().then((response) => {
                    expenseCategories.value = response.data;
                });
            }
        );

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    addEditActiveTab.value = "expense_details";

                    if (props.addEditType == "add") {
                        newFormData.value = {
                            ...props.formData,
                            date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
                            payment_date: dayjs()
                                .utc()
                                .format("YYYY-MM-DDTHH:mm:ssZ"),
                            expense_type: props.expenseTypes,
                        };
                    } else {
                        newFormData.value = {
                            ...props.formData,
                            expense_type: props.expenseTypes,
                        };

                        monthYear.value = dayjs(
                            `${props.data.payroll_year}-${props.data.payroll_month}-01`
                        );
                    }
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            disabledDate,
            permsArray,
            monthYear,
            expenseCategories,
            staffMembers,
            payeeAdded,
            payees,
            accountAdded,
            accounts,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            appSetting,
            addEditActiveTab,
            expenseCategoryAdded,
            staffMemberAdded,
            newFormData,
            setUrlData,
        };
    },
});
</script>

<style>
.ant-calendar-picker {
    width: 100%;
}
</style>
