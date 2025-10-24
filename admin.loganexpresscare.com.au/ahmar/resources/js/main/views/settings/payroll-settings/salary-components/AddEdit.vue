<template>
    <a-drawer
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
        :width="drawerWidth"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('salary_component.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('salary_component.type')"
                        name="type"
                        :help="rules.type ? rules.type.message : null"
                        :validateStatus="rules.type ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.type"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('salary_component.type'),
                                ])
                            "
                        >
                            <a-select-option value="earnings">
                                {{ $t("salary_component.earnings") }}
                            </a-select-option>
                            <a-select-option value="deductions">
                                {{ $t("salary_component.deductions") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('salary_component.value_type')"
                        name="value_type"
                        :help="rules.value_type ? rules.value_type.message : null"
                        :validateStatus="rules.value_type ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.value_type"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('salary_component.value_type'),
                                ])
                            "
                            :disabled="disabled"
                        >
                            <a-select-option value="fixed">
                                {{ $t("salary_component.fixed") }}
                            </a-select-option>
                            <a-select-option value="variable">
                                {{ $t("salary_component.variable") }}
                            </a-select-option>
                            <a-select-option value="basic_percent">
                                {{ $t("salary_component.basic_percent") }}
                            </a-select-option>
                            <a-select-option value="ctc_percent">
                                {{ $t("salary_component.ctc_percent") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('salary_component.monthly')"
                        name="monthly"
                        :help="rules.monthly ? rules.monthly.message : null"
                        :validateStatus="rules.monthly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.monthly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.monthly'),
                                ])
                            "
                            @change="onInputChange(formData.monthly, 'monthly')"
                        />
                    </a-form-item>
                </a-col>
                <a-col
                    style="display: none"
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="
                        formData.value_type == 'fixed' ||
                        formData.value_type == 'variable'
                    "
                >
                    <a-form-item
                        :label="$t('salary_component.semi_monthly')"
                        name="semi_monthly"
                        :help="rules.semi_monthly ? rules.semi_monthly.message : null"
                        :validateStatus="rules.semi_monthly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.semi_monthly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.semi_monthly'),
                                ])
                            "
                            @change="onInputChange(formData.semi_monthly, 'semi_monthly')"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row
                style="display: none"
                :gutter="16"
                v-if="formData.value_type == 'fixed' || formData.value_type == 'variable'"
            >
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('salary_component.weekly')"
                        name="weekly"
                        :help="rules.weekly ? rules.weekly.message : null"
                        :validateStatus="rules.weekly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.weekly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.weekly'),
                                ])
                            "
                            @change="onInputChange(formData.weekly, 'weekly')"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('salary_component.bi_weekly')"
                        name="bi_weekly"
                        :help="rules.bi_weekly ? rules.bi_weekly.message : null"
                        :validateStatus="rules.bi_weekly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.bi_weekly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.bi_weekly'),
                                ])
                            "
                            @change="onInputChange(formData.bi_weekly, 'bi_weekly')"
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
import { defineComponent, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["data", "visible", "url", "addEditType", "pageTitle", "successMessage"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const formData = ref({});
        const disabled = ref(false);
        const monthlyToWeekly = (value) => value / 4.333;
        const monthlyToBiweekly = (value) => value / 2.165;
        const weeklyToMonthly = (value) => value * 4.333;
        const biweeklyToMonthly = (value) => value * 2.165;

        const onInputChange = (changedValue, changedInput) => {
            const value = parseFloat(changedValue) || 0;

            if (value === 0 || changedValue === "") {
                formData.value.monthly = 0;
                formData.value.semi_monthly = 0;
                formData.value.weekly = 0;
                formData.value.bi_weekly = 0;
                return;
            }

            if (changedInput === "monthly") {
                formData.value.monthly = value;
                formData.value.semi_monthly = value / 2;
                formData.value.weekly = parseFloat(monthlyToWeekly(value).toFixed(2));
                formData.value.bi_weekly = parseFloat(
                    monthlyToBiweekly(value).toFixed(2)
                );
            } else if (changedInput === "semi_monthly") {
                formData.value.semi_monthly = value;
                formData.value.monthly = value * 2;
                formData.value.weekly = parseFloat(
                    monthlyToWeekly(formData.value.monthly).toFixed(2)
                );
                formData.value.bi_weekly = parseFloat(
                    monthlyToBiweekly(formData.value.monthly).toFixed(2)
                );
            } else if (changedInput === "weekly") {
                formData.value.weekly = value;
                formData.value.monthly = parseFloat(weeklyToMonthly(value).toFixed(2));
                formData.value.semi_monthly = formData.value.monthly / 2;
                formData.value.bi_weekly = parseFloat(
                    monthlyToBiweekly(formData.value.monthly).toFixed(2)
                );
            } else if (changedInput === "bi_weekly") {
                formData.value.bi_weekly = value;
                formData.value.monthly = parseFloat(biweeklyToMonthly(value).toFixed(2));
                formData.value.semi_monthly = formData.value.monthly / 2;
                formData.value.weekly = parseFloat(
                    monthlyToWeekly(formData.value.monthly).toFixed(2)
                );
            }
        };

        const onSubmit = () => {
            var newFormData = {};

            if (props.addEditType == "add") {
                newFormData = { ...formData.value };
            } else {
                newFormData = { ...formData.value, _method: "put" };
            }
            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
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

        const disabledValueType = () => {
            axiosAdmin.get(`disabled-value/${props.data.xid}`).then((response) => {
                disabled.value = response.data.disabled;
            });
        };
        watch(
            () => props.visible,
            () => {
                disabled.value = false;
                if (props.addEditType == "add") {
                    formData.value = {};
                    formData.value.value_type = "fixed";
                } else if (props.addEditType == "edit") {
                    formData.value = { ...props.data };
                    disabledValueType();
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            formData,
            onInputChange,
            disabled,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
