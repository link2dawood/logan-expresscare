<template>
    <a-form>
        <a-row :gutter="16">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-form-item
                    :label="$t('salary_group.salary_group_id')"
                    name="salary_group_id"
                    :help="
                        rules.salary_group_id
                            ? rules.salary_group_id.message
                            : null
                    "
                    :validateStatus="rules.salary_group_id ? 'error' : null"
                >
                    <span style="display: flex">
                        <a-select
                            v-model:value="formData.salary_group_id"
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('salary_group.salary_group_id'),
                                ])
                            "
                            :allowClear="true"
                            @change="
                                fetchSalaryComponentsAndUsers(
                                    formData.salary_group_id
                                )
                            "
                        >
                            <a-select-option
                                v-for="salaryGroup in salaryGroups"
                                :key="salaryGroup.xid"
                                :value="salaryGroup.xid"
                            >
                                {{ salaryGroup.name }}
                            </a-select-option>
                        </a-select>
                        <SalaryGroupAddButton
                            @onAddSuccess="salaryGroupAdded"
                        />
                    </span>
                </a-form-item>
            </a-col>
        </a-row>
        <a-row :gutter="16" v-if="inputVisible">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <UserInfo :user="user"
            /></a-col>
        </a-row>
        <a-row
            :gutter="16"
            :style="{ marginTop: inputVisible ? '0px' : '18px' }"
        >
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-form-item
                    :label="$t('basic_salary.annual_ctc')"
                    name="annual_ctc"
                    :help="rules.annual_ctc ? rules.annual_ctc.message : null"
                    :validateStatus="rules.annual_ctc ? 'error' : null"
                    :labelCol="{ span: 6 }"
                    :wrapperCol="{ span: 10 }"
                    labelAlign="left"
                >
                    <a-input-number
                        v-model:value="formData.annual_ctc"
                        :placeholder="
                            $t('common.placeholder_default_text', [
                                $t('basic_salary.annual_ctc'),
                            ])
                        "
                        style="width: 100%"
                        @change="calculateSalary"
                    >
                        <template #addonBefore>
                            {{ appSetting.currency.symbol }}
                        </template>
                    </a-input-number>
                </a-form-item>
            </a-col>
            <div :style="{ marginTop: inputVisible ? '0px' : '18px' }">
                {{ $t("basic_salary.cost_to_company_value_for_this_year") }}
            </div>
        </a-row>

        <a-row
            :gutter="16"
            style="
                margin-top: 20px;
                margin-bottom: 20px;
                border-bottom: 1px solid #d9d9d9;
                padding-bottom: 8px;
                display: flex;
                align-items: left;
            "
        >
            <a-col
                :xs="24"
                :sm="12"
                :md="6"
                :lg="6"
                :class="['column-text', 'new-class']"
            >
                {{ $t("basic_salary.salary_component") }}
            </a-col>
            <a-col
                :xs="24"
                :sm="12"
                :md="6"
                :lg="6"
                :class="['column-text', 'new-class']"
            >
                {{ $t("basic_salary.calculation_type") }}
            </a-col>
            <a-col
                :xs="24"
                :sm="12"
                :md="6"
                :lg="6"
                :class="['column-text', 'new-class']"
            >
                {{ $t("basic_salary.monthly_amount") }}
            </a-col>
            <a-col
                :xs="24"
                :sm="12"
                :md="6"
                :lg="6"
                :class="['column-text', 'new-class']"
            >
                {{ $t("basic_salary.annual_amount") }}
            </a-col>
        </a-row>

        <a-row
            :gutter="16"
            style="
                margin-top: 20px;
                padding-bottom: 8px;
                display: flex;
                align-items: left;
            "
        >
            <!-- Salary Component -->
            <a-col :xs="24" :sm="12" :md="6" :lg="6" class="column-text">
                {{ $t("basic_salary.basic_salary") }}
            </a-col>

            <!-- Calculation Type -->
            <a-col :xs="24" :sm="12" :md="6" :lg="6" class="column-text">
                <a-form-item
                    name="ctc_value"
                    :help="rules.ctc_value ? rules.ctc_value.message : null"
                    :validateStatus="rules.ctc_value ? 'error' : null"
                    class="required"
                >
                    <a-input-number
                        v-model:value="formData.ctc_value"
                        :placeholder="
                            $t('common.placeholder_default_text', [
                                $t('basic_salary.ctc_value'),
                            ])
                        "
                        min="0"
                        style="width: 100%"
                        @change="calculateSalary"
                    >
                        <template #addonBefore>
                            {{ appSetting.currency.symbol }}
                        </template>
                        <template #addonAfter>
                            <a-select
                                v-model:value="formData.calculation_type"
                                style="width: 120px"
                                @change="calculateSalary"
                            >
                                <a-select-option value="fixed">{{
                                    $t("basic_salary.fixed")
                                }}</a-select-option>
                                <a-select-option value="%_of_ctc">{{
                                    $t("basic_salary.%_of_ctc")
                                }}</a-select-option>
                            </a-select>
                        </template>
                    </a-input-number>
                </a-form-item>
            </a-col>

            <!-- Monthly Amount -->
            <a-col :xs="24" :sm="12" :md="6" :lg="6" class="column-text">
                <a-input-number
                    v-model:value="monthlySalary"
                    :placeholder="
                        $t('common.placeholder_default_text', [
                            $t('basic_salary.ctc'),
                        ])
                    "
                    min="0"
                    :disabled="true"
                    style="width: 100%"
                >
                    <template #addonBefore>
                        {{ appSetting.currency.symbol }}
                    </template>
                </a-input-number>
            </a-col>

            <!-- Annual Amount -->
            <a-col :xs="24" :sm="12" :md="6" :lg="6" class="column-text">
                <a-input-number
                    v-model:value="annualSalary"
                    :placeholder="
                        $t('common.placeholder_default_text', [
                            $t('basic_salary.ctc'),
                        ])
                    "
                    min="0"
                    :disabled="true"
                    style="width: 100%"
                >
                    <template #addonBefore>
                        {{ appSetting.currency.symbol }}
                    </template>
                </a-input-number>
            </a-col>
        </a-row>
        <a-row :gutter="16" class="new-class">
            <a-col :xs="24" :sm="24" :md="4" :lg="4">{{
                $t("basic_salary.earnings")
            }}</a-col>
        </a-row>
        <a-row :gutter="16">
            <a-col
                :xs="24"
                :sm="24"
                :md="24"
                :lg="24"
                v-if="salaryGroupComponentProps"
            >
                <div
                    v-for="(component, idx) in salaryGroupComponentProps"
                    :key="idx"
                >
                    <!-- Check if the salary component is of type 'earnings' -->
                    <a-row
                        v-if="component.salary_component.type === 'earnings'"
                        :gutter="16"
                        style="margin-top: 14px"
                    >
                        <!-- Salary Component Name -->
                        <a-col :span="6" class="column-text">
                            <span>{{ component.salary_component.name }}</span>
                        </a-col>

                        <!-- Monthly Input for Earnings -->
                        <a-col :span="6">
                            <span>
                                {{
                                    component.salary_component.value_type ===
                                    "fixed"
                                        ? $t("salary_component.fixed")
                                        : component.salary_component
                                              .value_type === "basic_percent"
                                        ? $t("salary_component.basic_percent")
                                        : component.salary_component
                                              .value_type === "ctc_percent"
                                        ? $t("salary_component.ctc_percent")
                                        : $t("salary_component.variable")
                                }}
                            </span>
                        </a-col>
                        <a-col :span="6">
                            <a-input
                                :value="getMonthlyValue(component)"
                                @input="
                                    (event) =>
                                        updateMonthlyValue(
                                            event.target.value,
                                            component.salary_component
                                        )
                                "
                                :disabled="
                                    component.salary_component.value_type !==
                                    'variable'
                                "
                                placeholder="Enter Monthly Value"
                                @change="calculateSalary"
                                style="width: 100%"
                            >
                                <template #addonBefore>
                                    {{ appSetting.currency.symbol }}
                                </template>
                            </a-input>
                        </a-col>

                        <!-- Annual Input (calculated) -->
                        <a-col :span="6">
                            <a-input
                                :value="calculateAnnualValue(component)"
                                :disabled="
                                    component.salary_component.value_type !==
                                    'variable'
                                "
                                placeholder="Annual Value"
                                readonly
                                style="width: 100%"
                            >
                                <template #addonBefore>
                                    {{ appSetting.currency.symbol }}
                                </template>
                            </a-input>
                        </a-col>
                    </a-row>
                </div>
            </a-col>
        </a-row>

        <a-row
            :gutter="16"
            style="margin-top: 10px"
            v-if="Number(specialAllowance) !== 0"
        >
            <a-col :xs="24" :sm="24" :md="6" :lg="6" class="column-text">
                <div>{{ $t("basic_salary.special_allowances") }}</div>
            </a-col>

            <!-- Value Type or Description (Optional) -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6" class="column-text">
                <div>{{ $t("basic_salary.special_allowances") }}</div>
            </a-col>

            <!-- Special Allowance Monthly Input -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6" class="column-text">
                <span style="display: inline-block; width: 100%">
                    {{ appSetting.currency.symbol }}
                    {{ specialAllowance }}</span
                >
            </a-col>

            <!-- Special Allowance Annual Input -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6" class="column-text">
                <span style="display: inline-block; width: 100%">
                    {{ appSetting.currency.symbol }}
                    {{ (specialAllowance * 12).toFixed(2) }}</span
                >
            </a-col>
        </a-row>
        <div v-if="Number(specialAllowance) < 0" class="error-message">
            {{ $t("payroll.special_allowances_error") }}
        </div>

        <a-row :gutter="[16, 24]" style="margin-top: 10px" class="styled-row">
            <!-- Cost to Company Label -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6" class="column-text">
                <div>{{ $t("basic_salary.cost_to_company") }}</div>
            </a-col>
            <a-col :xs="24" :sm="24" :md="6" :lg="6"> </a-col>

            <!-- Monthly Cost to Company Value -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <b style="display: inline-block; width: 100%">
                    {{ appSetting.currency.symbol }}{{ monthlyCostToCompany }}
                </b>
            </a-col>

            <!-- Annual Cost to Company Value -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <b style="display: inline-block; width: 100%">
                    {{ appSetting.currency.symbol }}
                    {{ formData.annual_ctc.toFixed(2) }}
                </b>
            </a-col>
        </a-row>
        <a-row :gutter="16" style="margin-top: 20px" class="new-class">
            <a-col :xs="24" :sm="24" :md="4" :lg="4">{{
                $t("basic_salary.deductions")
            }}</a-col>
        </a-row>
        <a-row :gutter="[16, 24]">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div
                    v-for="(component, idx) in salaryGroupComponentProps"
                    :key="idx"
                >
                    <a-row
                        v-if="component.salary_component.type === 'deductions'"
                        :gutter="16"
                        style="margin-top: 10px"
                    >
                        <a-col :span="6" class="column-text">
                            <span>{{ component.salary_component.name }}</span>
                        </a-col>

                        <a-col :span="6">
                            <span>
                                {{
                                    component.salary_component.value_type ===
                                    "fixed"
                                        ? $t("salary_component.fixed")
                                        : component.salary_component
                                              .value_type === "basic_percent"
                                        ? $t("salary_component.basic_percent")
                                        : component.salary_component
                                              .value_type === "ctc_percent"
                                        ? $t("salary_component.ctc_percent")
                                        : $t("salary_component.variable")
                                }}
                            </span>
                        </a-col>

                        <a-col :span="6">
                            <a-input
                                :value="getMonthlyValue(component)"
                                @input="
                                    (event) =>
                                        updateMonthlyValue(
                                            event.target.value,
                                            component.salary_component
                                        )
                                "
                                :disabled="
                                    component.salary_component.value_type !==
                                    'variable'
                                "
                                placeholder="Enter Monthly Value"
                                @change="calculateSalary"
                                style="width: 100%"
                            >
                                <template #addonBefore>
                                    {{ appSetting.currency.symbol }}
                                </template>
                            </a-input>
                        </a-col>

                        <a-col :span="6">
                            <a-input
                                :value="calculateAnnualValue(component)"
                                :disabled="
                                    component.salary_component.value_type !==
                                    'variable'
                                "
                                placeholder="Annual Value"
                                readonly
                                style="
                                    width: 100%;
                                    border: none;
                                    background: transparent;
                                    color: inherit;
                                    padding: 0;
                                "
                            >
                                <template #addonBefore>
                                    {{ appSetting.currency.symbol }}
                                </template>
                            </a-input>
                        </a-col>
                    </a-row>
                </div>
            </a-col>
        </a-row>
        <a-row :gutter="[16, 24]" style="margin-top: 10px" class="styled-row">
            <!-- Cost to Company Label -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6" class="column-text">
                <div>{{ $t("basic_salary.total_deductions") }}</div>
            </a-col>
            <a-col :xs="24" :sm="24" :md="6" :lg="6"> </a-col>

            <!-- Monthly Cost to Company Value -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <div>
                    {{ appSetting.currency.symbol }}{{ deductions.toFixed(2) }}
                </div>
            </a-col>

            <!-- Annual Cost to Company Value -->
            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <div>
                    {{ appSetting.currency.symbol }}
                    {{ (deductions * 12).toFixed(2) }}
                </div>
            </a-col>
        </a-row>
    </a-form>
</template>
<script>
import { defineComponent, ref, computed, watch, onMounted } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import { forEach, find } from "lodash-es";
import apiAdmin from "../../../../common/composable/apiAdmin";
import SalaryGroupAddButton from "../../settings/payroll-settings/salary-groups/AddButton.vue";

export default defineComponent({
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        user: {
            type: Object,
            default: {},
        },
        inputVisible: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        SaveOutlined,
        UserInfo,
        SalaryGroupAddButton,
    },
    setup(props, { emit }) {
        const { loading, rules } = apiAdmin();
        const { appSetting } = common();

        const selectUsers = ref("");
        const componentIds = ref([]);
        const formData = ref({
            basic_salary: 0,
            monthly_amount: 0,
            annual_amount: 0,
            annual_ctc: 0,
            calculation_type: "%_of_ctc",
            ctc_value: 50,
        });
        const monthlySalary = ref(0);
        const annualSalary = ref(0);
        const earnings = ref(0);
        const deductions = ref(0);
        const monthlyCostToCompany = ref(0);
        const salaryComponents = ref([]);
        const salaryGroups = ref([]);
        const salaryGroupUrl =
            "salary-groups?fields=id,xid,name,salaryGroupComponents{id,xid,x_salary_group_id,x_salary_component_id},salaryGroupComponents:salaryComponent{id,xid,name,type,value_type,bi_weekly,weekly,monthly,semi_monthly},salaryGroupUsers{id,xid,x_user_id,x_salary_group_id},salaryGroupUsers:users{id,xid,name}";

        const salaryGroupComponentProps = ref([]);
        onMounted(() => {
            fetchSalaryGroups();
        });

        const fetchSalaryGroups = () => {
            const salaryGroupPromise = axiosAdmin.get(salaryGroupUrl);

            Promise.all([salaryGroupPromise]).then(([salaryGroupResponse]) => {
                salaryGroups.value = salaryGroupResponse.data;
            });
        };

        const salaryGroupAdded = () => {
            axiosAdmin.get(salaryGroupUrl).then((response) => {
                salaryGroups.value = response.data;
            });
        };

        const fetchSalaryComponentsAndUsers = (salaryGroupId) => {
            if (!salaryGroupId) {
                salaryGroupComponentProps.value = [];
                calculateSalary();
                return;
            }

            axiosAdmin.get(salaryGroupUrl).then((response) => {
                const allSalaryGroups = response.data;

                const selectedGroup = allSalaryGroups.find(
                    (group) => group.xid === salaryGroupId
                );

                if (selectedGroup) {
                    salaryGroupComponentProps.value =
                        selectedGroup.salary_group_components;
                } else {
                    salaryGroupComponentProps.value = [];
                }

                calculateSalary();
            });
        };

        // Computed properties
        const specialAllowance = computed(() =>
            (
                Number(monthlyCostToCompany.value) -
                Number(monthlySalary.value) -
                Number(earnings.value)
            ).toFixed(2)
        );

        const basicSalary = computed(() =>
            (
                Number(monthlySalary.value) +
                Number(specialAllowance.value) +
                Number(earnings.value) -
                Number(deductions.value)
            ).toFixed(2)
        );

        const netSalary = computed(() => {
            return (
                Number(formData.value.basic_salary) +
                Number(specialAllowance.value) +
                Number(earnings.value) -
                Number(deductions.value)
            ).toFixed(2);
        });

        const calculateEarningsAndDeductions = () => {
            earnings.value = 0;
            deductions.value = 0;
            componentIds.value = [];
            salaryComponents.value = [];

            salaryGroupComponentProps.value.forEach(
                ({ salary_component, xid }) => {
                    let amount = 0;

                    switch (salary_component.value_type) {
                        case "fixed":
                        case "variable":
                            amount = Number(salary_component.monthly) || 0;
                            break;

                        case "basic_percent":
                            amount =
                                (monthlySalary.value *
                                    Number(salary_component.monthly)) /
                                    100 || 0;
                            break;

                        case "ctc_percent":
                            amount =
                                (monthlySalary.value *
                                    Number(salary_component.monthly)) /
                                    formData.value.ctc_value || 0;
                            break;

                        default:
                            amount = 0;
                            break;
                    }

                    if (salary_component.type === "earnings") {
                        earnings.value += amount;
                    } else if (salary_component.type === "deductions") {
                        deductions.value += amount;
                    }

                    salaryComponents.value.push({
                        id: salary_component.xid,
                        type: salary_component.type,
                        value_type: salary_component.value_type,
                        monthly_value: amount,
                    });

                    componentIds.value.push(xid);
                }
            );
        };

        const calculateSalary = () => {
            calculateEarningsAndDeductions();

            const { calculation_type, ctc_value, annual_ctc } = formData.value;

            if (calculation_type === "fixed") {
                monthlySalary.value = ctc_value;
                annualSalary.value = ctc_value * 12;
            } else if (calculation_type === "%_of_ctc") {
                const percentage = Number(ctc_value);
                monthlySalary.value = (
                    (annual_ctc * percentage) /
                    100 /
                    12
                ).toFixed(2);
                annualSalary.value = ((annual_ctc * percentage) / 100).toFixed(
                    2
                );
            }

            monthlyCostToCompany.value = parseFloat(
                (annual_ctc / 12).toFixed(2)
            );

            emit("updateSalaryData", {
                ...formData.value,
                xid: props.user.xid,
                basic_salary: monthlySalary.value,
                annual_amount: annualSalary.value,
                monthly_amount: basicSalary.value,
                salary_component_ids: componentIds.value,
                special_allowances: specialAllowance.value,
                salary_components: salaryComponents.value,
                net_salary: netSalary.value,
            });
        };

        const getMonthlyValue = (component) => {
            if (!component) return 0;

            const { value_type, monthly } = component.salary_component;
            const { ctc_value } = formData.value;
            let calculatedValue = 0;

            switch (value_type) {
                case "fixed":
                case "variable":
                    calculatedValue = Number(monthly) || 0;
                    break;
                case "basic_percent":
                    calculatedValue =
                        (monthlySalary.value * Number(monthly)) / 100 || 0;
                    break;
                case "ctc_percent":
                    calculatedValue =
                        (monthlySalary.value * Number(monthly)) / ctc_value ||
                        0;
                    break;
                default:
                    calculatedValue = 0;
            }

            return parseFloat(calculatedValue.toFixed(2));
        };

        const updateMonthlyValue = (value, component) => {
            if (!component) return;

            if (component.value_type === "variable") {
                const sanitizedValue = parseFloat(
                    (Number(value) || 0).toFixed(2)
                );
                component.monthly = sanitizedValue;

                let calculatedValue = 0;
                switch (component.value_type) {
                    case "fixed":
                    case "variable":
                        calculatedValue = sanitizedValue;
                        break;
                    case "basic_percent":
                        calculatedValue = parseFloat(
                            (
                                (monthlySalary.value * sanitizedValue) /
                                100
                            ).toFixed(2)
                        );
                        break;
                    case "ctc_percent":
                        calculatedValue = parseFloat(
                            (
                                (monthlySalary.value * sanitizedValue) /
                                formData.value.ctc_value
                            ).toFixed(2)
                        );
                        break;
                    default:
                        calculatedValue = 0;
                }
                component.monthly_value = calculatedValue;

                const targetComponent = salaryComponents.value.find(
                    (item) => item.id === component.xid
                );

                if (targetComponent) {
                    targetComponent.monthly_value = parseFloat(
                        calculatedValue.toFixed(2)
                    );
                } else {
                    salaryComponents.value.push({
                        id: component.xid,
                        type: component.type,
                        value_type: component.value_type,
                        monthly_value: parseFloat(calculatedValue.toFixed(2)),
                    });
                }

                calculateSalary();
            }
        };

        const calculateAnnualValue = (component) => {
            const monthlyValue = getMonthlyValue(component);
            return parseFloat((monthlyValue * 12).toFixed(2));
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                fetchSalaryGroups();
                if (newVal) {
                    formData.value = {
                        basic_salary: props.user.basic_salary || 0,
                        ctc_value: props.user.ctc_value || 50,
                        calculation_type:
                            props.user.calculation_type || "%_of_ctc",
                        annual_ctc: props.user.annual_ctc || 0,
                        monthly_amount: props.user.monthly_amount || 0,
                        annual_amount: props.user.annual_amount || 0,
                        salary_group_id: props.user.salary_group?.xid,
                    };

                    if (
                        props.user.annual_ctc != 0 &&
                        props.user.annual_ctc != null
                    ) {
                        var allValues = [];

                        forEach(
                            props.user.salary_group?.salary_group_components,
                            (salComponent) => {
                                var findValueObject = find(
                                    props.user.basic_salary_details,
                                    {
                                        x_salary_component_id:
                                            salComponent.x_salary_component_id,
                                    }
                                );

                                if (findValueObject) {
                                    allValues.push({
                                        ...salComponent,
                                        salary_component: {
                                            ...salComponent.salary_component,
                                            monthly:
                                                findValueObject.value_type ===
                                                "variable"
                                                    ? findValueObject.monthly
                                                    : salComponent
                                                          .salary_component
                                                          .monthly,
                                        },
                                    });
                                } else {
                                    allValues.push(salComponent);
                                }
                            }
                        );

                        salaryGroupComponentProps.value = allValues;
                    } else {
                        salaryGroupComponentProps.value =
                            props.user?.salary_group?.salary_group_components ||
                            [];
                    }

                    monthlySalary.value = props.user.monthly_amount || 0;
                    annualSalary.value = props.user.annual_amount || 0;
                    formData.value.salary_group_id =
                        props.user.salary_group?.xid;
                    // Do NOT call fetchSalaryComponentsAndUsers here, because we already set salaryGroupComponentProps.value above
                    // Only call fetchSalaryComponentsAndUsers on salary group change (from dropdown)
                    basicSalary.value = 0;
                    calculateSalary();
                }
            }
        );

        watch(
            [
                () => formData.value.annual_ctc,
                () => formData.value.ctc_value,
                () => earnings.value,
                () => deductions.value,
            ],
            () => {
                calculateSalary();
            }
        );

        return {
            loading,
            getMonthlyValue,
            updateMonthlyValue,
            calculateAnnualValue,
            rules,
            formData,
            appSetting,
            selectUsers,
            monthlySalary,
            annualSalary,
            earnings,
            deductions,
            specialAllowance,
            basicSalary,
            calculateSalary,
            monthlyCostToCompany,
            salaryComponents,
            salaryGroupComponentProps,
            salaryGroups,
            fetchSalaryComponentsAndUsers,
            salaryGroupAdded,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>

<style scoped>
.column-text {
    text-align: left;
}
.styled-row {
    background-color: #f8f9fa;
    height: 55px;
    line-height: 55px;
    border-radius: 4px;
    margin-bottom: 10px;
    text-align: left;
}

.new-class {
    font-weight: bold;
}
.error-message {
    color: red;
    font-size: 14px;
    margin-top: 5px;
}
</style>
