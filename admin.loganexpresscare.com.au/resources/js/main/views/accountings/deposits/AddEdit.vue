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
                        :label="$t('deposit.account')"
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
                                        $t('deposit.account'),
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
                                </a-select-option>
                            </a-select>
                            <AccountAddButton @onAddSuccess="accountAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('deposit.payer')"
                        name="payer_id"
                        :help="rules.payer_id ? rules.payer_id.message : null"
                        :validateStatus="rules.payer_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.payer_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('deposit.payer'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="payer in payers"
                                    :key="payer.xid"
                                    :value="payer.xid"
                                    :title="payer.name"
                                >
                                    {{ payer.name }}
                                </a-select-option>
                            </a-select>
                            <PayerAddButton @onAddSuccess="payerAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('deposit.amount')"
                        name="amount"
                        :help="rules.amount ? rules.amount.message : null"
                        :validateStatus="rules.amount ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="formData.amount"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('deposit.amount'),
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
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('deposit.date_time')"
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
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('deposit.reference_number')"
                        name="reference_number"
                        :help="
                            rules.reference_number ? rules.reference_number.message : null
                        "
                        :validateStatus="rules.reference_number ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.reference_number"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('deposit.reference_number'),
                                ])
                            "
                            style="width: 100%"
                        >
                        </a-input>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('deposit.file')"
                        name="file"
                        :help="rules.file ? rules.file.message : null"
                        :validateStatus="rules.file ? 'error' : null"
                    >
                        <UploadFile
                            :acceptFormat="'image/*,.pdf'"
                            :formData="formData"
                            folder="accountings"
                            uploadField="file"
                            @onFileUploaded="
                                (file) => {
                                    formData.file = file.file;
                                    formData.file_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('deposit.deposit_category')"
                        name="deposit_category_id"
                        :help="
                            rules.deposit_category_id
                                ? rules.deposit_category_id.message
                                : null
                        "
                        :validateStatus="rules.deposit_category_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.deposit_category_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('deposit.deposit_category'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="deposit_category in depositCategories"
                                    :key="deposit_category.xid"
                                    :value="deposit_category.xid"
                                    :title="deposit_category.name"
                                >
                                    {{ deposit_category.name }}
                                </a-select-option>
                            </a-select>
                            <DepositCategoryAddButton
                                @onAddSuccess="depositCategoryAdded"
                            />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('deposit.notes')"
                        name="notes"
                        :help="rules.notes ? rules.notes.message : null"
                        :validateStatus="rules.notes ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.notes"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('deposit.notes'),
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
import { defineComponent, onMounted, ref } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import AccountAddButton from "../../../views/accountings/accounts/AddButton.vue";
import PayerAddButton from "../../../views/accountings/payers/AddButton.vue";
import DepositCategoryAddButton from "../../../views/accountings/deposit-categories/AddButton.vue";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";

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
        AccountAddButton,
        DateTimePicker,
        PayerAddButton,
        DepositCategoryAddButton,
        UploadFile,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting } = common();
        const accounts = ref({});
        const accountUrl = "accounts";
        const payers = ref([]);
        const payerUrl = "payers";
        const depositCategories = ref({});
        const depositCategoryUrl = "deposit-categories";

        onMounted(() => {
            const accountsPromise = axiosAdmin.get(accountUrl);
            const depositCategoriesPromise = axiosAdmin.get(depositCategoryUrl);
            const payersPromise = axiosAdmin.get(payerUrl);
            Promise.all([accountsPromise, payersPromise, depositCategoriesPromise]).then(
                ([accountResponse, payerResponse, depositCategoryResponse]) => {
                    accounts.value = accountResponse.data;
                    payers.value = payerResponse.data;
                    depositCategories.value = depositCategoryResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const accountAdded = (xid) => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
                emit("addListSuccess", { type: "account_id", id: xid });
            });
        };

        const payerAdded = (xid) => {
            axiosAdmin.get(payerUrl).then((response) => {
                payers.value = response.data;
                emit("addListSuccess", { type: "payer_id", id: xid });
            });
        };
        const depositCategoryAdded = (xid) => {
            axiosAdmin.get(depositCategoryUrl).then((response) => {
                depositCategories.value = response.data;
                emit("addListSuccess", { type: "deposit_category_id", id: xid });
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,
            payerAdded,
            depositCategoryAdded,
            accountAdded,
            accounts,
            payers,
            depositCategories,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
