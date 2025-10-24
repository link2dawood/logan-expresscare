<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        :maskClosable="false"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :span="24">
                    <a-form-item
                        :label="$t('expense.status')"
                        name="status"
                        :help="rules.status ? rules.status.message : null"
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
                                {{ $t("common.approved") }}
                            </a-radio-button>
                            <a-radio-button value="rejected" v-if="tabVisible">
                                {{ $t("common.rejected") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <template v-if="newFormData.status == 'approved'">
                <a-row :gutter="16">
                    <a-col :span="24">
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
                                v-model:value="newFormData.payment_status"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('expense.payment_status'),
                                    ])
                                "
                            >
                                <a-select-option :value="1">
                                    {{ $t("expense.right_now") }}
                                </a-select-option>
                                <a-select-option :value="0">{{
                                    $t("expense.decuct_from_payroll")
                                }}</a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="16" v-if="newFormData.payment_status == '0'">
                    <a-col :span="24">
                        <a-form-item
                            :label="$t('pre_payment.month')"
                            name="month"
                            :help="rules.month ? rules.month.message : null"
                            :validateStatus="rules.month ? 'error' : null"
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
                <a-row :gutter="16" v-if="newFormData.payment_status == '1'">
                    <a-col :span="24">
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
                <a-row :gutter="16">
                    <a-col :span="24" v-if="newFormData.payment_status == '1'">
                        <a-form-item
                            :label="$t('expense.account')"
                            name="account_id"
                            :help="
                                rules.account_id
                                    ? rules.account_id.message
                                    : null
                            "
                            :validateStatus="rules.account_id ? 'error' : null"
                            class="required"
                        >
                            <span style="display: flex">
                                <a-select
                                    v-model:value="newFormData.account_id"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('expense.account'),
                                        ])
                                    "
                                    :allowClear="true"
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
                </a-row>
            </template>
        </a-form>
        <template #footer>
            <a-button
                key="submit"
                type="primary"
                :loading="loading"
                @click="onSubmit"
            >
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import AccountAddButton from "../../../views/accountings/accounts/AddButton.vue";

export default defineComponent({
    props: {
        data: {
            type: Object,
            default: {},
        },
        visible: {
            type: Boolean,
            default: false,
        },
        pageTitle: {
            type: String,
            default: "",
        },
        tabVisible: {
            type: Boolean,
            default: true,
        },
    },

    components: {
        SaveOutlined,
        AccountAddButton,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { formatDateTime, dayjs } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const accounts = ref({});
        const accountUrl = "accounts";
        const monthYear = ref(dayjs());
        const newFormData = ref({});

        onMounted(() => {
            const accountsPromise = axiosAdmin.get(accountUrl);

            Promise.all([accountsPromise]).then(([accountResponse]) => {
                accounts.value = accountResponse.data;
            });
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: `update-expense-status/${props.data.xid}`,
                data: {
                    ...newFormData.value,
                    payroll_year: monthYear.value.format("YYYY"),
                    payroll_month: monthYear.value.format("MM"),
                },
                successMessage: t("complaint.complaint_status_updated"),
                success: (response) => {
                    rules.value = {};

                    if (response && response.status == "success") {
                        emit("close");
                        emit("setUrlData");
                    }
                },
            });
        };

        const accountAdded = () => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    newFormData.value = {
                        status: "approved",
                        payment_status: 1,
                        payment_date: dayjs()
                            .utc()
                            .format("YYYY-MM-DDTHH:mm:ssZ"),
                        account_id: undefined,
                    };
                }
            }
        );

        return {
            rules,
            loading,
            formatDateTime,
            onClose,
            onSubmit,
            accounts,
            accountAdded,
            monthYear,

            newFormData,
        };
    },
});
</script>
