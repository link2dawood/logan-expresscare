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
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="permsArray.includes('admin') ? 12 : 12"
                    :lg="permsArray.includes('admin') ? 12 : 12"
                >
                    <a-form-item
                        :label="$t('expense.expense_category')"
                        name="expense_category_id"
                        :help="
                            rules.expense_category_id
                                ? rules.expense_category_id.message
                                : null
                        "
                        :validateStatus="rules.expense_category_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="newFormData.expense_category_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('expense.expense_category'),
                                    ])
                                "
                                :allowClear="true"
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
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('expense.amount')"
                        name="amount"
                        :help="rules.amount ? rules.amount.message : null"
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
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('expense.date_time')"
                        name="date_time"
                        :help="rules.date_time ? rules.date_time.message : null"
                        :validateStatus="rules.date_time ? 'error' : null"
                        class="required"
                    >
                        <DateTimePicker
                            :dateTime="newFormData.date_time"
                            @dateTimeChanged="
                                (changedDateTime) =>
                                    (newFormData.date_time = changedDateTime)
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('expense.reference_number')"
                        name="reference_number"
                        :help="
                            rules.reference_number ? rules.reference_number.message : null
                        "
                        :validateStatus="rules.reference_number ? 'error' : null"
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
                                    newFormData.bill_url = file.file_url;
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
        </a-form>
        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon> <SaveOutlined /> </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import common from "../../../../common/composable/common";
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
        UserInfo,
        SaveOutlined,
        UploadFile,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, disabledDate, permsArray, dayjs } = common();
        const expenseCategories = ref([]);
        const expenseCategoryUrl = "self/expense-categories";
        const newFormData = ref({});

        onMounted(() => {
            const expenseCategoriesPromise = axiosAdmin.get(expenseCategoryUrl);

            Promise.all([expenseCategoriesPromise]).then(
                ([expenseCategoriesResponse]) => {
                    expenseCategories.value = expenseCategoriesResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: newFormData.value,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    if (props.addEditType == "add") {
                        newFormData.value = {
                            ...props.formData,
                            date_time: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
                        };
                    } else {
                        newFormData.value = {
                            ...props.formData,
                        };
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

            expenseCategories,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            appSetting,

            newFormData,
        };
    },
});
</script>

<style>
.ant-calendar-picker {
    width: 100%;
}
</style>
