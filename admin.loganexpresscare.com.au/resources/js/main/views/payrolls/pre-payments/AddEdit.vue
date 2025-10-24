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
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('pre_payment.user_id')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.user_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('pre_payment.user_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="allStaffMember in allStaffMembers"
                                    :key="allStaffMember.xid"
                                    :value="allStaffMember.xid"
                                    :title="allStaffMember.name"
                                    ><user-list-display
                                        :user="allStaffMember"
                                        whereToShow="select"
                                    />
                                </a-select-option>
                            </a-select>
                            <StaffMemberAddButton @onAddSuccess="staffMemberAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('pre_payment.date_time')"
                        name="date_time"
                        :help="rules.date_time ? rules.date_time.message : null"
                        :validateStatus="rules.date_time ? 'error' : null"
                        class="required"
                    >
                        <DateTimePicker
                            :dateTime="formData.date_time"
                            @dateTimeChanged="
                                (changedDateTime) =>
                                    (formData.date_time = changedDateTime)
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('pre_payment.amount')"
                        name="amount"
                        :help="rules.amount ? rules.amount.message : null"
                        :validateStatus="rules.amount ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="formData.amount"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('pre_payment.amount'),
                                ])
                            "
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
                        :label="$t('pre_payment.deduct_account_number')"
                        name="account_id"
                        :help="rules.account_id ? rules.account_id.message : null"
                        :validateStatus="rules.account_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.account_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('pre_payment.deduct_account_number'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="account in accounts"
                                    :key="account.xid"
                                    :value="account.xid"
                                    :title="account.name"
                                >
                                    {{ account.name }}
                                    {{
                                        account.account_number
                                            ? ` - (${account.account_number})`
                                            : ""
                                    }}
                                </a-select-option>
                            </a-select>
                            <AccountAddButton @onAddSuccess="accountAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('pre_payment.deduct_from_payroll')"
                        name="deduct_from_payroll"
                        :help="
                            rules.deduct_from_payroll
                                ? rules.deduct_from_payroll.message
                                : null
                        "
                        :validateStatus="rules.deduct_from_payroll ? 'error' : null"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.deduct_from_payroll"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('pre_payment.deduct_from_payroll'),
                                    ])
                                "
                            >
                                <a-select-option :value="1">
                                    {{ $t("pre_payment.on_given_payment_month") }}
                                </a-select-option>
                                <a-select-option :value="0">
                                    {{ $t("pre_payment.another_month") }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="formData.deduct_from_payroll == '0'"
                >
                    <a-form-item
                        :label="$t('pre_payment.month')"
                        name="month"
                        :help="rules.month ? rules.month.message : null"
                        :validateStatus="rules.month ? 'error' : null"
                    >
                        <a-date-picker
                            v-model:value="monthYear"
                            :placeholder="
                                $t('common.select_default_text', [$t('payroll.month')])
                            "
                            picker="month"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('pre_payment.notes')"
                        name="notes"
                        :help="rules.notes ? rules.notes.message : null"
                        :validateStatus="rules.notes ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.notes"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('pre_payment.notes'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import StaffMemberAddButton from "../../staff-members/users/StaffAddButton.vue";
import AccountAddButton from "../../accountings/accounts/AddButton.vue";
import dayjs from "dayjs";
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
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        StaffMemberAddButton,
        DateTimePicker,
        AccountAddButton,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray } = common();
        const allStaffMembers = ref([]);
        const staffMembersUrl = "users";
        const monthYear = ref(dayjs());
        const accounts = ref([]);
        const accountUrl = "accounts";

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const accountPromise = axiosAdmin.get(accountUrl);
            Promise.all([staffMemberPromise, accountPromise]).then(
                ([staffMemberResponse, accountResponse]) => {
                    allStaffMembers.value = staffMemberResponse.data;
                    accounts.value = accountResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...props.formData,
                    payroll_year: monthYear.value.format("YYYY"),
                    payroll_month: monthYear.value.format("MM"),
                },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const staffMemberAdded = (xid) => {
            axiosAdmin.get(staffMembersUrl).then((response) => {
                allStaffMembers.value = response.data;
                emit("addListSuccess", { type: "user_id", id: xid });
            });
        };

        const accountAdded = (xid) => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
                emit("addListSuccess", { type: "account_id", id: xid });
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };
        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.visible && props.addEditType == "edit") {
                    monthYear.value = dayjs(
                        `${props.data.payroll_year}-${props.data.payroll_month}-01`
                    );
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,
            staffMemberAdded,
            allStaffMembers,
            monthYear,
            accounts,
            accountAdded,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
