<template>
    <a-modal
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
                        :label="$t('salary_group.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_group.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('salary_group.salary_component_id')"
                        name="salary_component_id"
                        :help="
                            rules.salary_component_id
                                ? rules.salary_component_id.message
                                : null
                        "
                        :validateStatus="
                            rules.salary_component_id ? 'error' : null
                        "
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="salaryComponentIds"
                                mode="multiple"
                                style="width: 100%"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('salary_group.salary_component_id'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="salaryComponent in salaryComponents"
                                    :key="salaryComponent.xid"
                                    :value="salaryComponent.xid"
                                >
                                    {{ salaryComponent.name }}
                                </a-select-option>
                            </a-select>
                            <SalaryComponentAddButton
                                @onAddSuccess="salaryComponentAdded"
                            />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row
                :gutter="16"
                v-for="(formField, index) in formFields"
                :key="index"
                style="display: flex; align-items: center"
                v-show="salaryComponentIds.length > 0"
            >
                <a-col :xs="24" :sm="24" :md="5" :lg="5">
                    <a-form-item
                        :label="$t('salary_component.name')"
                        name="name"
                        :help="
                            validationErrors.name ? validationErrors.name : null
                        "
                        :validateStatus="validationErrors.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.name'),
                                ])
                            "
                            :disabled="!formField.isAdded"
                        />
                        <div
                            v-if="validationErrors[`name-${index}`]"
                            class="error-message"
                        >
                            {{ validationErrors[`name-${index}`] }}
                        </div>
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('salary_component.type')"
                        name="type"
                        :help="
                            validationErrors.type ? validationErrors.type : null
                        "
                        :validateStatus="validationErrors.type ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formField.type"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('salary_component.type'),
                                ])
                            "
                            :disabled="!formField.isAdded"
                        >
                            <a-select-option value="earnings">
                                {{ $t("salary_component.earnings") }}
                            </a-select-option>
                            <a-select-option value="deductions">
                                {{ $t("salary_component.deductions") }}
                            </a-select-option>
                        </a-select>
                        <div
                            v-if="validationErrors[`type-${index}`]"
                            class="error-message"
                        >
                            {{ validationErrors[`type-${index}`] }}
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('salary_component.value_type')"
                        name="value_type"
                        :help="
                            validationErrors.value_type
                                ? validationErrors.value_type
                                : null
                        "
                        :validateStatus="
                            validationErrors.value_type ? 'error' : null
                        "
                        class="required"
                    >
                        <a-select
                            v-model:value="formField.value_type"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('salary_component.value_type'),
                                ])
                            "
                            :disabled="!formField.isAdded"
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
                        <div
                            v-if="validationErrors[`value_type-${index}`]"
                            class="error-message"
                        >
                            {{ validationErrors[`value_type-${index}`] }}
                        </div>
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="5" :lg="5">
                    <a-form-item
                        :label="$t('salary_component.monthly')"
                        name="monthly"
                        :help="
                            validationErrors.monthly
                                ? validationErrors.monthly
                                : null
                        "
                        :validateStatus="
                            validationErrors.monthly ? 'error' : null
                        "
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.monthly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.monthly'),
                                ])
                            "
                            @change="
                                onInputChange(
                                    formField.monthly,
                                    'monthly',
                                    index
                                )
                            "
                            :disabled="!formField.isAdded"
                        />
                        <div
                            v-if="validationErrors[`monthly-${index}`]"
                            class="error-message"
                        >
                            {{ validationErrors[`monthly-${index}`] }}
                        </div>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="
                        (formField.value_type == 'fixed' ||
                            formField.value_type == 'variable') &&
                        hiddenInput
                    "
                >
                    <a-form-item
                        :label="$t('salary_component.semi_monthly')"
                        name="semi_monthly"
                        :help="
                            rules.semi_monthly
                                ? rules.semi_monthly.message
                                : null
                        "
                        :validateStatus="rules.semi_monthly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.semi_monthly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.semi_monthly'),
                                ])
                            "
                            @change="
                                onInputChange(
                                    formField.semi_monthly,
                                    'semi_monthly',
                                    index
                                )
                            "
                            :disabled="!formField.isAdded"
                        />
                    </a-form-item>
                </a-col>

                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="
                        (formField.value_type == 'fixed' ||
                            formField.value_type == 'variable') &&
                        hiddenInput
                    "
                >
                    <a-form-item
                        :label="$t('salary_component.weekly')"
                        name="weekly"
                        :help="rules.weekly ? rules.weekly.message : null"
                        :validateStatus="rules.weekly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.weekly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.weekly'),
                                ])
                            "
                            @change="
                                onInputChange(formField.weekly, 'weekly', index)
                            "
                            :disabled="!formField.isAdded"
                        />
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="
                        (formField.value_type == 'fixed' ||
                            formField.value_type == 'variable') &&
                        hiddenInput
                    "
                >
                    <a-form-item
                        :label="$t('salary_component.bi_weekly')"
                        name="bi_weekly"
                        :help="rules.bi_weekly ? rules.bi_weekly.message : null"
                        :validateStatus="rules.bi_weekly ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.bi_weekly"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('salary_component.bi_weekly'),
                                ])
                            "
                            @change="
                                onInputChange(
                                    formField.bi_weekly,
                                    'bi_weekly',
                                    index
                                )
                            "
                            :disabled="!formField.isAdded"
                        />
                    </a-form-item>
                </a-col>

                <a-col :span="1">
                    <MinusSquareOutlined @click="removeFormField(index)" />
                </a-col>
            </a-row>
            <a-col
                :xs="24"
                :sm="24"
                :md="24"
                :lg="24"
                v-show="salaryComponentIds.length > 0"
            >
                <a-form-item>
                    <a-button
                        type="dashed"
                        block
                        @click="addFormField"
                        :disabled="addFormButtonStatus"
                    >
                        <PlusOutlined />

                        {{ $t("salary_group.add_new_value") }}
                    </a-button>
                </a-form-item>
            </a-col>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('salary_group.user_id')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="userIds"
                                mode="multiple"
                                style="width: 100%"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('salary_group.user_id'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                    :label="user.name"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
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
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, onMounted, watch, computed } from "vue";
import { forEach, some, filter, map, find } from "lodash-es";

import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import SalaryComponentAddButton from "../salary-components/AddButton.vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";

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
        MinusSquareOutlined,
        SalaryComponentAddButton,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const salaryComponents = ref([]);
        const salaryComponentUrl =
            "salary-components?fields=id,xid,name,type,value_type,bi_weekly,weekly,monthly,semi_monthly&limit=10000";
        const salaryComponentIds = ref([]);
        const users = ref([]);
        const userIds = ref([]);
        const hiddenInput = ref(false);
        const validationErrors = ref({});
        const groupId = ref("");
        const { t } = useI18n();
        const formFields = ref([
            {
                name: "",
                monthly: 0,
                weekly: 0,
                bi_weekly: 0,
                semi_monthly: 0,
                value_type: "fixed",
                isAdded: true,
            },
        ]);

        onMounted(() => {
            groupId.value =
                props.addEditType === "add" ? null : props.data?.xid || null;
            fetchSalaryComponentsAndUsers(groupId.value);
        });

        const validateForm = () => {
            validationErrors.value = {};
            let isValid = true;

            formFields.value.forEach((field, index) => {
                if (!field.name) {
                    validationErrors.value[`name-${index}`] = t(
                        "salary_group.validation_name"
                    );
                    isValid = false;
                }

                if (!field.type) {
                    validationErrors.value[`type-${index}`] = t(
                        "salary_group.validation_type"
                    );
                    isValid = false;
                }

                if (!field.value_type) {
                    validationErrors.value[`value_type-${index}`] = t(
                        "salary_group.validation_value_type"
                    );
                    isValid = false;
                }

                if (field.monthly == null || field.monthly === 0) {
                    validationErrors.value[`monthly-${index}`] = t(
                        "salary_group.validation_value_type"
                    );
                    isValid = false;
                }
            });

            return isValid;
        };

        const fetchSalaryComponentsAndUsers = (groupId = null) => {
            const salaryComponentPromise = axiosAdmin.get(salaryComponentUrl);
            const usersPromise = axiosAdmin.get("filter-user", {
                params: { group_id: groupId },
            });

            Promise.all([salaryComponentPromise, usersPromise]).then(
                ([salaryComponentResponse, usersResponse]) => {
                    salaryComponents.value = salaryComponentResponse.data;
                    users.value = usersResponse.data.data;
                }
            );
        };

        // Monthly
        const monthlyToWeekly = (value) => value / 4.333;
        const monthlyToBiweekly = (value) => value / 2.165;
        const monthlyToSemiMonthly = (value) => value / 2;

        // Weekly
        const weeklyToMonthly = (value) => value * 4.333;
        const weeklyToBiweekly = (value) => value * 2;
        const weeklyToSemiMonthly = (value) => (value * 4.333) / 2;

        // Bi-weekly
        const biweeklyToMonthly = (value) => value * 2.165;
        const biweeklyToWeekly = (value) => value / 2;
        const biweeklyToSemiMonthly = (value) => (value * 2.165) / 2;

        // Semi-monthly
        const semiMonthlyToMonthly = (value) => value * 2;
        const semiMonthlyToWeekly = (value) => (value * 2 * 4.333) / 2;
        const semiMonthlyToBiweekly = (value) => (value * 2 * 2.165) / 2;

        const onInputChange = (value, fieldKey, index) => {
            const formField = formFields.value[index];

            if (formField.value_type === "fixed") {
                switch (fieldKey) {
                    case "monthly":
                        formField.weekly = monthlyToWeekly(value).toFixed(2);
                        formField.bi_weekly =
                            monthlyToBiweekly(value).toFixed(2);
                        formField.semi_monthly =
                            monthlyToSemiMonthly(value).toFixed(2);
                        break;

                    case "weekly":
                        formField.monthly = weeklyToMonthly(value).toFixed(2);
                        formField.bi_weekly =
                            weeklyToBiweekly(value).toFixed(2);
                        formField.semi_monthly =
                            weeklyToSemiMonthly(value).toFixed(2);
                        break;

                    case "bi_weekly":
                        formField.monthly = biweeklyToMonthly(value).toFixed(2);
                        formField.weekly = biweeklyToWeekly(value).toFixed(2);
                        formField.semi_monthly =
                            biweeklyToSemiMonthly(value).toFixed(2);
                        break;

                    case "semi_monthly":
                        formField.monthly =
                            semiMonthlyToMonthly(value).toFixed(2);
                        formField.weekly =
                            semiMonthlyToWeekly(value).toFixed(2);
                        formField.bi_weekly =
                            semiMonthlyToBiweekly(value).toFixed(2);
                        break;

                    default:
                        break;
                }
            }
        };
        const resetFormFields = () => {
            formFields.value = [
                {
                    name: "",
                    monthly: 0,
                    weekly: 0,
                    bi_weekly: 0,
                    semi_monthly: 0,
                    value_type: "fixed",
                },
            ];
            userIds.value = [];
            salaryComponentIds.value = [];
        };

        const addFormField = () => {
            formFields.value.push({
                name: "",
                monthly: 0,
                weekly: 0,
                bi_weekly: 0,
                semi_monthly: 0,
                value_type: "fixed",
                isAdded: true,
            });
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { name: "" }) ||
                    some(formFields.value, { name: null })
                );
            }
        });
        const removeFormField = (index) => {
            const formField = formFields.value[index];

            if (!formField.isAdded && formField.xid) {
                salaryComponentIds.value = filter(
                    salaryComponentIds.value,
                    (id) => id !== formField.xid
                );

                formFields.value = filter(
                    formFields.value,
                    (field) => field.xid !== formField.xid
                );
            } else if (formField.isAdded) {
                formFields.value.splice(index, 1);
            }
        };

        const onSubmit = () => {
            const isValid = validateForm();
            let newFormData = {
                ...props.formData,
                salary_component_ids: salaryComponentIds.value,
                user_ids: userIds.value,
                form_Fields: formFields.value,
            };
            if (isValid) {
                addEditRequestAdmin({
                    url: props.url,
                    data: newFormData,
                    successMessage: props.successMessage,
                    success: (res) => {
                        emit("addEditSuccess", res.xid);
                        resetFormFields();
                    },
                });
            }
        };

        const updateGroupAndFetch = () => {
            if (props.addEditType === "add") {
                resetFormFields();
                userIds.value = [];
                salaryComponentIds.value = [];
                groupId.value = null;
            } else if (props.addEditType === "edit") {
                const selectedIds = [];
                const selectedFields = [];
                groupId.value = props.data?.xid || null;

                forEach(
                    props.data.salary_group_components,
                    (salaryGroupComponent) => {
                        selectedIds.push(
                            salaryGroupComponent.x_salary_component_id
                        );
                        selectedFields.push({
                            ...salaryGroupComponent.salary_component,
                            xid: salaryGroupComponent.x_salary_component_id,
                            value: salaryGroupComponent.value || "",
                        });
                    }
                );

                salaryComponentIds.value = [...selectedIds];
                formFields.value = [...selectedFields];

                userIds.value = map(
                    props.data.salary_group_users,
                    (salaryGroupUser) => salaryGroupUser.x_user_id
                );
            }
            fetchSalaryComponentsAndUsers(groupId.value);
        };

        const onClose = () => {
            validationErrors.value = {};
            updateGroupAndFetch();
            emit("closed");
        };

        const salaryComponentAdded = () => {
            axiosAdmin.get(salaryComponentUrl).then((response) => {
                salaryComponents.value = response.data;
            });
        };

        const userAdded = () => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
            });
        };
        watch(
            () => props.visible,

            (newVsisible) => {
                if (newVsisible) {
                    updateGroupAndFetch();
                }
            },
            { immediate: true }
        );

        watch(salaryComponentIds, (newIds) => {
            fetchSalaryComponentsAndUsers(groupId.value);

            const updatedFields = filter(salaryComponents.value, (component) =>
                newIds.includes(component.xid)
            ).map((component) => {
                const existingField = find(
                    formFields.value,
                    (field) => field.xid === component.xid
                );
                return existingField
                    ? existingField
                    : {
                          ...component,
                          value: "",
                          xid: component.xid,
                          isAdded: false,
                      };
            });

            const manuallyAddedFields = filter(
                formFields.value,
                (field) => field.isAdded
            );

            formFields.value = [...updatedFields, ...manuallyAddedFields];
        });

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            salaryComponents,
            salaryComponentAdded,
            salaryComponentIds,
            users,
            userIds,
            userAdded,
            formFields,
            removeFormField,
            addFormField,
            addFormButtonStatus,
            onInputChange,
            hiddenInput,
            validationErrors,
            validateForm,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
<style scoped>
.required .ant-form-item-label > label::after {
    content: "*";
    color: red;
}
.error-message {
    color: red;
    font-size: 12px;
}
</style>
