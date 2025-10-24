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
            <div style="background: #ececec; padding: 20px">
                <a-row :gutter="16">
                    <a-col :span="12">
                        <a-card>
                            <a-statistic
                                :title="$t('payroll.month')"
                                :value="`${getMonthNameByNumber(
                                    formData.month
                                )} ${formData.year}`"
                                :value-style="{ color: '#3f8600' }"
                            />
                        </a-card>
                    </a-col>
                    <a-col :span="12">
                        <a-card>
                            <a-statistic
                                :title="$t('payroll.net_salary')"
                                :value="0"
                                :value-style="{ color: '#3f8600' }"
                            >
                                <template #formatter>
                                    <SalaryVisibility
                                        :salary="data.net_salary"
                                        :visibleAll="salaryVisibleAll"
                                    />
                                </template>
                            </a-statistic>
                        </a-card>
                    </a-col>
                </a-row>
            </div>
            <a-row class="mt-30">
                <a-col :span="12" :offset="6">
                    <a-tabs v-model:activeKey="activeKey">
                        <a-tab-pane
                            key="summary"
                            :tab="`${$t('payroll.summary')}`"
                        >
                            <a-table
                                :dataSource="summaryData"
                                :columns="summaryColumns"
                                :pagination="false"
                                size="middle"
                                :showHeader="false"
                            />
                        </a-tab-pane>
                        <a-tab-pane
                            key="leave"
                            :tab="`${$t('payroll.leaves')} / ${$t(
                                'payroll.holiday'
                            )}`"
                        >
                            <a-table
                                :dataSource="leaveHoliday"
                                :columns="summaryColumns"
                                :pagination="false"
                                size="middle"
                                :showHeader="false"
                            />
                        </a-tab-pane>
                    </a-tabs>
                </a-col>
            </a-row>

            <a-row class="mt-30" :gutter="16">
                <a-col :xs="24" :sm="24" :md="10" :lg="10">
                    <a-table
                        :dataSource="salaryComponentsData"
                        :columns="salaryComponentsColumns"
                        :pagination="false"
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'value'">
                                <a-typography-text
                                    v-if="record.key == 'pre_payment_amount'"
                                    type="danger"
                                >
                                    - {{ formatAmountCurrency(record.value) }}
                                </a-typography-text>
                                <a-typography-text
                                    v-if="record.key === 'salary_amount'"
                                    type="success"
                                >
                                    +
                                    <SalaryVisibility :salary="record.value" />
                                </a-typography-text>

                                <a-typography-text
                                    v-else-if="record.key === 'expense_amount'"
                                    type="success"
                                >
                                    + {{ formatAmountCurrency(record.value) }}
                                </a-typography-text>
                            </template>
                        </template>
                    </a-table>
                </a-col>

                <a-col :xs="24" :sm="24" :md="14" :lg="14"> </a-col>
            </a-row>
        </a-form>
    </a-drawer>
</template>
<script>
import { defineComponent, onMounted, ref, watch, computed } from "vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import common from "../../../../../common/composable/common";
import FormItemHeading from "../../../../../common/components/common/typography/FormItemHeading.vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../../common/composable/hrmManagement.js";
import SalaryVisibility from "../../../../components/SalaryVisibility.vue";

export default defineComponent({
    props: [
        "data",
        "formData",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        FormItemHeading,
        SalaryVisibility,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { getMonthNameByNumber, formatMinutes } = hrmManagement();

        const { appSetting, permsArray, formatAmountCurrency } = common();
        const activeKey = ref("summary");
        const { t } = useI18n();

        onMounted(() => {});

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

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const summaryColumns = [
            {
                title: t("common.title"),
                dataIndex: "title",
            },
            {
                title: t("common.value"),
                dataIndex: "value",
            },
        ];
        const summaryData = ref([]);
        const leaveHoliday = ref([]);

        const salaryComponentsColumns = [
            {
                title: t("payroll.salary_component"),
                dataIndex: "title",
            },
            {
                title: t("common.value"),
                dataIndex: "value",
            },
        ];
        const salaryComponentsData = ref([]);

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                summaryData.value = [
                    {
                        key: "total_days",
                        title: t("payroll.total_days"),
                        value: props.data.total_days,
                    },
                    {
                        key: "working_days",
                        title: t("payroll.working_days"),
                        value: props.data.working_days,
                    },
                    {
                        key: "present_days",
                        title: t("payroll.present_days"),
                        value: props.data.present_days,
                    },
                    {
                        key: "total_office_time",
                        title: t("payroll.total_office_time"),
                        value: formatMinutes(props.data.total_office_time),
                    },
                    {
                        key: "total_worked_time",
                        title: t("payroll.total_worked_time"),
                        value: formatMinutes(props.data.total_worked_time),
                    },
                    {
                        key: "half_days",
                        title: t("payroll.half_days"),
                        value: props.data.half_days,
                    },
                    {
                        key: "late_days",
                        title: t("payroll.late_days"),
                        value: props.data.late_days,
                    },
                    {
                        key: "basic_salary",
                        title: t("payroll.basic_salary"),
                        value: formatAmountCurrency(props.data.basic_salary),
                    },
                ];
                leaveHoliday.value = [
                    {
                        key: "paid_leaves",
                        title: t("payroll.paid_leaves"),
                        value: props.data.paid_leaves,
                    },
                    {
                        key: "unpaid_leaves",
                        title: t("payroll.unpaid_leaves"),
                        value: props.data.unpaid_leaves,
                    },
                    {
                        key: "holiday_count",
                        title: t("payroll.holiday_count"),
                        value: props.data.holiday_count,
                    },
                ];
                salaryComponentsData.value = [
                    {
                        key: "salary_amount",
                        title: t("payroll.salary_amount"),
                        value: props.data.salary_amount,
                    },
                    {
                        key: "pre_payment_amount",
                        title: t("payroll.pre_payment_deduction"),
                        value: props.data.pre_payment_amount,
                    },
                    {
                        key: "expense_amount",
                        title: t("payroll.expense_claim"),
                        value: props.data.expense_amount,
                    },
                ];
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            permsArray,
            activeKey,

            getMonthNameByNumber,
            formatAmountCurrency,

            summaryColumns,
            summaryData,
            leaveHoliday,
            salaryComponentsColumns,
            salaryComponentsData,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "70%",
        };
    },
});
</script>
