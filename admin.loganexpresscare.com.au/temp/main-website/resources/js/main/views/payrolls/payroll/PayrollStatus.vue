<template>
    <a-button type="primary" @click="openModel">
        <SafetyOutlined />

        {{ $t("payroll.payroll_status") }}
    </a-button>
    <a-modal
        :open="visible"
        @close="closeModel"
        :centered="true"
        :title="$t('payroll.payroll_status')"
        @ok="onSubmit"
        :closable="false"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('payroll.payroll_status')"
                        name="payroll_status"
                        :help="
                            rules.payroll_status
                                ? rules.payroll_status.message
                                : null
                        "
                        :validateStatus="rules.payroll_status ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.payroll_status"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('payroll.payroll_status'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option value="generated">
                                {{ $t("payroll.generated") }}
                            </a-select-option>
                            <a-select-option value="paid">
                                {{ $t("payroll.paid") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16" v-if="formData.payroll_status == 'paid'">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('payroll.payment_date')"
                        name="payment_date"
                        :help="
                            rules.payment_date
                                ? rules.payment_date.message
                                : null
                        "
                        :validateStatus="rules.payment_date ? 'error' : null"
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="formData.payment_date"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16" v-if="formData.payroll_status == 'paid'">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('payroll.account')"
                        name="account_id"
                        :help="
                            rules.account_id ? rules.account_id.message : null
                        "
                        :validateStatus="rules.account_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.account_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('payroll.account'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="account in accounts"
                                    :key="account.xid"
                                    :value="account.xid"
                                >
                                    {{ account.name }}
                                </a-select-option>
                            </a-select>
                            <AccountAddButton @onAddSuccess="accountAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
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
                {{
                    addEditType == "add"
                        ? $t("common.create")
                        : $t("common.update")
                }}
            </a-button>
            <a-button key="back" @click="closeModel">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    SafetyOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import AccountAddButton from "../../../views/accountings/accounts/AddButton.vue";

export default defineComponent({
    emits: ["onSuccess"],
    props: ["payrolls", "addEditType"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        SafetyOutlined,
        AccountAddButton,
    },

    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, permsArray } = common();
        const accounts = ref({});
        const accountUrl = "accounts?fields=id,xid,name";
        const visible = ref(false);
        const { t } = useI18n();
        const formData = ref({
            date: undefined,
            payroll_status: undefined,
            account_id: undefined,
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "payrolls/update-status",
                data: {
                    ...formData.value,
                    payrolls: props.payrolls,
                },
                successMessage: t("payroll.status_generated"),
                success: (res) => {
                    emit("onSuccess");
                    visible.value = false;
                    formData.value = {
                        payment_date: undefined,
                        payroll_status: undefined,
                        account_id: undefined,
                    };
                },
            });
        };
        const openModel = () => {
            visible.value = true;
        };

        const closeModel = () => {
            visible.value = false;
            rules.value = {};
            formData.value = {
                payment_date: undefined,
                payroll_status: undefined,
                account_id: undefined,
            };
        };

        onMounted(() => {
            const accountsPromise = axiosAdmin.get(accountUrl);
            Promise.all([accountsPromise]).then(([accountResponse]) => {
                accounts.value = accountResponse.data;
            });
        });

        const accountAdded = () => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
            });
        };

        return {
            loading,
            rules,
            onSubmit,
            appSetting,
            permsArray,
            accountAdded,
            accounts,
            openModel,
            visible,
            formData,
            closeModel,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
