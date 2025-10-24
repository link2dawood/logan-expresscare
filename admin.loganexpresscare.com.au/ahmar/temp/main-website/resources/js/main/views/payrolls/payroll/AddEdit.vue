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
                            <h3>&nbsp;</h3>
                        </a-card>
                    </a-col>
                    <a-col :span="12">
                        <a-card>
                            <a-statistic
                                :title="$t('payroll.net_salary')"
                                :value="formatAmountCurrency(netSalary)"
                                :value-style="{ color: '#3f8600' }"
                            />
                            <h3 style="color: rgba(0, 0, 0, 0.45)">
                                {{ $t("payroll.net_salary") }} = (
                                {{ $t("payroll.gross_earnings") }} -
                                {{ $t("payroll.total_deductions") }} +
                                {{ $t("payroll.reimbursements") }})
                            </h3>
                        </a-card>
                    </a-col>
                </a-row>
            </div>
            <a-row :gutter="[16, 16]" style="margin-top: 20px">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-row :gutter="16" :class="['input-gap', 'heading']">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            {{ $t("payroll.reimbursements") }}
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                    {{ $t("payroll.expense_claim") }}
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                    <a-input-number
                                        v-model:value="expenseAmount"
                                        :min="0"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t('payroll.expense_claim')]
                                            )
                                        "
                                        :disabled="false"
                                        style="width: 100%"
                                    />
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>

            <a-row :gutter="16" style="margin: 20px 0px 20px 0px">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-row :gutter="16" :class="['input-gap', 'heading']">
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            style="padding: 0px"
                            >{{ $t("payroll.earnings") }}
                        </a-col>
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            style="padding: 0px; margin-left: -21px"
                            >{{ $t("payroll.amount") }}
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-row :gutter="16" :class="['input-gap', 'heading']">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12"
                            >{{ $t("payroll.deductions") }}
                        </a-col>
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            style="padding: 0px; margin-left: -14px"
                            >{{ $t("payroll.amount") }}
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>

            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-row :gutter="16" :class="['input-gap']">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                    {{ $t("payroll.basic_salary") }}
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                    <a-input-number
                                        v-model:value="basicSalary"
                                        :min="0"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t('payroll.basic_salary')]
                                            )
                                        "
                                        :disabled="false"
                                        style="width: 100%"
                                    />
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>
                    <a-row :gutter="[16, 16]" class="input-gap">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <div
                                v-for="(earning, index) in predefinedEarnings"
                                :key="'predefined-earning-' + index"
                            >
                                <a-row :gutter="[16, 16]" class="input-gap">
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        {{ earning.name }}
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <a-input-number
                                            :value="calculateMonthly(earning)"
                                            @input="
                                                (value) =>
                                                    updateMonthlyValue(
                                                        value,
                                                        earning
                                                    )
                                            "
                                            :min="0"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [
                                                        $t(
                                                            'payroll.monthly_value'
                                                        ),
                                                    ]
                                                )
                                            "
                                            :disabled="
                                                earning.value_type !==
                                                'variable'
                                            "
                                            style="width: 100%"
                                        />
                                    </a-col>
                                </a-row>
                            </div>
                        </a-col>
                    </a-row>
                    <a-row
                        :gutter="16"
                        class="input-gap"
                        v-if="Number(calculateSpecialAllowance) !== 0"
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-row :gutter="16" class="input-gap">
                                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                    {{ $t("payroll.special_allowances") }}
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                    <a-input-number
                                        v-model:value="
                                            calculateSpecialAllowance
                                        "
                                        :min="0"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'payroll.special_allowances'
                                                    ),
                                                ]
                                            )
                                        "
                                        :disabled="true"
                                        style="width: 100%"
                                    />
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>
                    <a-row
                        :gutter="16"
                        class="input-gap"
                        v-if="Number(calculateSpecialAllowance) !== 0"
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <div
                                v-for="(earning, index) in earnings"
                                :key="'earning-' + index"
                            >
                                <a-row :gutter="16" class="input-gap">
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <div class="input-wrapper">
                                            <a-input
                                                v-model:value="earning.name"
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [$t('payroll.earnings')]
                                                    )
                                                "
                                                :status="
                                                    rules[
                                                        `custom_earnings.${index}.name`
                                                    ]
                                                        ? 'error'
                                                        : null
                                                "
                                            />
                                            <div
                                                v-if="
                                                    rules[
                                                        `custom_earnings.${index}.name`
                                                    ]
                                                "
                                                class="error-message"
                                            >
                                                {{
                                                    rules[
                                                        `custom_earnings.${index}.name`
                                                    ].message
                                                }}
                                            </div>
                                        </div>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <div class="input-wrapper">
                                            <a-input-number
                                                v-model:value="earning.monthly"
                                                :min="0"
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [$t('payroll.earnings')]
                                                    )
                                                "
                                                style="width: 100%"
                                                :status="
                                                    rules[
                                                        `custom_earnings.${index}.monthly`
                                                    ]
                                                        ? 'error'
                                                        : null
                                                "
                                            />
                                            <div
                                                v-if="
                                                    rules[
                                                        `custom_earnings.${index}.monthly`
                                                    ]
                                                "
                                                class="error-message"
                                            >
                                                {{
                                                    rules[
                                                        `custom_earnings.${index}.monthly`
                                                    ].message
                                                }}
                                            </div>
                                        </div>
                                    </a-col>
                                    <a-col :span="2">
                                        <a-button
                                            type="danger"
                                            @click="removeEarning(index)"
                                            style="width: 100%"
                                        >
                                            <MinusSquareOutlined />
                                        </a-button>
                                    </a-col>
                                </a-row>
                                <div
                                    v-if="Number(calculateSpecialAllowance) < 0"
                                    class="error-message"
                                >
                                    {{ $t("payroll.special_allowances_error") }}
                                </div>
                            </div>
                            <a-button
                                type="primary"
                                @click="addEarning"
                                v-if="Number(calculateSpecialAllowance) !== 0"
                            >
                                <template #icon> <PlusOutlined /> </template
                                >{{ $t("payroll.adds") }}
                            </a-button>
                            <hr style="margin-top: 15px" /> </a-col
                    ></a-row>
                    <a-row :gutter="16" class="input-gap">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <div
                                v-for="(
                                    additionalEarning, index
                                ) in additionalEarnings"
                                :key="'additional-earning-' + index"
                            >
                                <a-row :gutter="16" class="input-gap">
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <div class="input-wrapper">
                                            <a-input
                                                v-model:value="
                                                    additionalEarning.name
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'payroll.additional_earnings'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                :status="
                                                    rules[
                                                        `additional_earnings.${index}.name`
                                                    ]
                                                        ? 'error'
                                                        : null
                                                "
                                            />
                                            <div
                                                v-if="
                                                    rules[
                                                        `additional_earnings.${index}.name`
                                                    ]
                                                "
                                                class="error-message"
                                            >
                                                {{
                                                    rules[
                                                        `additional_earnings.${index}.name`
                                                    ].message
                                                }}
                                            </div>
                                        </div>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <div class="input-wrapper">
                                            <a-input-number
                                                v-model:value="
                                                    additionalEarning.monthly
                                                "
                                                :min="0"
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'payroll.additional_earnings'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                style="width: 100%"
                                                :status="
                                                    rules[
                                                        `additional_earnings.${index}.monthly`
                                                    ]
                                                        ? 'error'
                                                        : null
                                                "
                                            />
                                            <div
                                                v-if="
                                                    rules[
                                                        `additional_earnings.${index}.monthly`
                                                    ]
                                                "
                                                class="error-message"
                                            >
                                                {{
                                                    rules[
                                                        `additional_earnings.${index}.monthly`
                                                    ].message
                                                }}
                                            </div>
                                        </div>
                                    </a-col>
                                    <a-col :span="2">
                                        <a-button
                                            type="danger"
                                            @click="
                                                removeAdditionalEarning(index)
                                            "
                                            style="width: 100%"
                                        >
                                            <MinusSquareOutlined />
                                        </a-button>
                                    </a-col>
                                </a-row>
                            </div>
                            <a-button
                                type="primary"
                                @click="addAdditionalEarning"
                            >
                                <template #icon> <PlusOutlined /> </template
                                >{{ $t("payroll.add_additional_earnings") }}
                            </a-button>
                            <hr style="margin-top: 15px" />
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <div
                                v-for="(
                                    deduction, index
                                ) in predefinedDeductions"
                                :key="'predefined-deduction-' + index"
                            >
                                <a-row :gutter="16" class="input-gap">
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11"
                                        >{{ deduction.name }}
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <a-input-number
                                            :value="calculateMonthly(deduction)"
                                            @input="
                                                (value) =>
                                                    updateMonthlyValue(
                                                        value,
                                                        deduction
                                                    )
                                            "
                                            :min="0"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('payroll.deductions')]
                                                )
                                            "
                                            style="width: 100%"
                                            :disabled="
                                                deduction.value_type !==
                                                'variable'
                                            "
                                        />
                                    </a-col>
                                </a-row></div></a-col
                    ></a-row>
                    <a-row :gutter="16" class="input-gap">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <div
                                v-for="(deduction, index) in deductions"
                                :key="'deduction-' + index"
                            >
                                <a-row :gutter="16" class="input-gap">
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <div class="input-wrapper">
                                            <a-input
                                                v-model:value="deduction.name"
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'payroll.deductions'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                :status="
                                                    rules[
                                                        `custom_deductions.${index}.name`
                                                    ]
                                                        ? 'error'
                                                        : null
                                                "
                                            />
                                            <div
                                                v-if="
                                                    rules[
                                                        `custom_deductions.${index}.name`
                                                    ]
                                                "
                                                class="error-message"
                                            >
                                                {{
                                                    rules[
                                                        `custom_deductions.${index}.name`
                                                    ].message
                                                }}
                                            </div>
                                        </div>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="11" :lg="11">
                                        <div class="input-wrapper">
                                            <a-input-number
                                                v-model:value="
                                                    deduction.monthly
                                                "
                                                :min="0"
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'payroll.deductions'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                style="width: 100%"
                                                :status="
                                                    rules[
                                                        `custom_deductions.${index}.monthly`
                                                    ]
                                                        ? 'error'
                                                        : null
                                                "
                                            />
                                            <div
                                                v-if="
                                                    rules[
                                                        `custom_deductions.${index}.monthly`
                                                    ]
                                                "
                                                class="error-message"
                                            >
                                                {{
                                                    rules[
                                                        `custom_deductions.${index}.monthly`
                                                    ].message
                                                }}
                                            </div>
                                        </div>
                                    </a-col>
                                    <a-col :span="1">
                                        <a-button
                                            type="danger"
                                            @click="removeDeduction(index)"
                                        >
                                            <MinusSquareOutlined />
                                        </a-button>
                                    </a-col>
                                </a-row>
                            </div>
                            <a-button type="primary" @click="addDeduction">
                                <template #icon> <PlusOutlined /> </template
                                >{{ $t("payroll.adds") }}
                            </a-button>
                        </a-col>
                    </a-row>
                </a-col></a-row
            >
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
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
                <a-col :xs="24" :sm="24" :md="16" :lg="16">
                    <a-row
                        :gutter="16"
                        v-if="formData.payroll_status == 'paid'"
                    >
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('payroll.payment_date')"
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
                                <a-date-picker
                                    v-model:value="formData.payment_date"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('payroll.account')"
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
                                    <AccountAddButton
                                        @onAddSuccess="accountAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
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
                    {{
                        addEditType == "add"
                            ? $t("common.create")
                            : $t("common.update")
                    }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>

<script>
import {
    defineComponent,
    onMounted,
    ref,
    watch,
    computed,
    nextTick,
} from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    ArrowUpOutlined,
    ArrowDownOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import StaffMemberAddButton from "../../staff-members/users/StaffAddButton.vue";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../common/composable/hrmManagement.js";
import { map, filter, sumBy, find } from "lodash-es";
import AccountAddButton from "../../../views/accountings/accounts/AddButton.vue";
import { notification } from "ant-design-vue";

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
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        StaffMemberAddButton,
        ArrowUpOutlined,
        ArrowDownOutlined,
        MinusSquareOutlined,
        FormItemHeading,
        AccountAddButton,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { getMonthNameByNumber, formatMinutes } = hrmManagement();
        const { appSetting, permsArray, formatAmountCurrency } = common();
        const activeKey = ref("summary");
        const { t } = useI18n();

        onMounted(() => {});
        const predefinedEarnings = ref([]);
        const predefinedDeductions = ref([]);
        const earnings = ref([]);
        const deductions = ref([]);
        const additionalEarnings = ref([]);
        const basicSalary = ref();
        const ctcAmountMonthly = ref();
        const monthlyCtc = ref();
        const accounts = ref({});
        const accountUrl = "accounts";
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
        const expenseAmount = ref();

        onMounted(() => {
            basicSalary.value = parseFloat(
                (props.data.user?.basic_salary || 0).toFixed(2)
            );
            const accountsPromise = axiosAdmin.get(accountUrl);
            Promise.all([accountsPromise]).then(([accountResponse]) => {
                accounts.value = accountResponse.data;
            });
        });

        const accountAdded = (xid) => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
                emit("addListSuccess", { type: "account_id", id: xid });
            });
        };

        const calculateMonthly = (component) => {
            if (!component) return 0;
            const {
                value_type = "fixed",
                monthly = 0,
                value = 0,
                name = "",
                type = "",
            } = component;

            const {
                ctc_value = 0,
                annual_ctc = 0,
                calculation_type = "ctc_percent",
                basic_salary = 0,
            } = props.data?.user || {};

            let calculatedValue = 0;

            //this is calculate according to the annual_ctc
            ctcAmountMonthly.value = parseFloat((annual_ctc / 12).toFixed(2));

            const { total_days, present_days, holiday_count, paid_leaves } =
                props.data;

            //this is calculate according to the present_days
            monthlyCtc.value = parseFloat(
                ((ctcAmountMonthly.value * present_days) / total_days).toFixed(
                    2
                )
            );

            switch (value_type) {
                case "variable":
                case "fixed":
                    calculatedValue = Number(monthly) || 0;
                    break;

                case "basic_percent":
                    calculatedValue =
                        parseFloat(
                            ((basic_salary * Number(monthly)) / 100).toFixed(2)
                        ) || 0;
                    break;

                case "ctc_percent":
                    calculatedValue = parseFloat(
                        ((monthlyCtc.value * Number(monthly)) / 100).toFixed(2)
                    );
                    break;

                default:
                    calculatedValue = Number(monthly) || 0;
                    break;
            }

            return parseFloat(calculatedValue.toFixed(2));
        };

        const updateMonthlyValue = (value, component) => {
            if (!component) return;

            const sanitizedValue = parseFloat((Number(value) || 0).toFixed(2));

            component.monthly = sanitizedValue;
            component.monthly_value = parseFloat(
                calculateMonthly(component).toFixed(2)
            );

            updateNetSalary();
        };

        // all add component functions
        const addEarning = () => {
            earnings.value.push({ monthly: 0 });
        };

        const addAdditionalEarning = () => {
            additionalEarnings.value.push({ monthly: 0 });
        };

        const addDeduction = () => {
            deductions.value.push({ monthly: 0 });
        };
        // all remove component functions
        const removeEarning = (index) => {
            earnings.value.splice(index, 1);
        };

        const removeAdditionalEarning = (index) => {
            additionalEarnings.value.splice(index, 1);
        };

        const removeDeduction = (index) => {
            deductions.value.splice(index, 1);
        };

        // sum of all earnings and deductions values

        const totalPredefinedEarnings = computed(() => {
            return parseFloat(
                sumBy(predefinedEarnings.value, (earning) => {
                    return parseFloat(earning.monthly_value) || 0;
                }).toFixed(2)
            );
        });

        const totalCustomEarnings = computed(() => {
            return parseFloat(
                sumBy(earnings.value, (earning) => {
                    return parseFloat(earning.monthly) || 0;
                }).toFixed(2)
            );
        });

        const totalAdditionalEarnings = computed(() => {
            return parseFloat(
                sumBy(additionalEarnings.value, (additional) => {
                    return parseFloat(additional.monthly) || 0;
                }).toFixed(2)
            );
        });

        const totalDeductions = computed(() => {
            const totalPredefinedDeductions = parseFloat(
                sumBy(predefinedDeductions.value, (deduction) => {
                    return parseFloat(deduction.monthly_value) || 0;
                }).toFixed(2)
            );
            const totalCustomDeductions = parseFloat(
                sumBy(deductions.value, (deduction) => {
                    return parseFloat(deduction.monthly) || 0;
                }).toFixed(2)
            );

            return parseFloat(
                (totalPredefinedDeductions + totalCustomDeductions).toFixed(2)
            );
        });

        const calculateSpecialAllowance = computed(() => {
            ctcAmountMonthly.value = parseFloat(
                (props.data.user?.annual_ctc / 12 || 0).toFixed(2)
            );

            return parseFloat(
                (
                    parseFloat(ctcAmountMonthly.value) -
                    parseFloat(basicSalary.value) -
                    parseFloat(totalPredefinedEarnings.value) -
                    parseFloat(totalCustomEarnings.value)
                ).toFixed(2)
            );
        });

        watch(calculateSpecialAllowance, (newValue) => {
            if (Number(newValue) === 0) {
                earnings.value = [];
            }
        });

        // Calculate Net Salary

        const calculateNetSalary = () => {
            const basicSalaryValue = parseFloat(basicSalary.value) || 0;
            const totalSpecialAllowance =
                parseFloat(calculateSpecialAllowance.value) || 0;
            const totalPredefinedEarningsValue =
                parseFloat(totalPredefinedEarnings.value) || 0;
            const totalCustomEarningsValue =
                parseFloat(totalCustomEarnings.value) || 0;
            const totalAdditionalEarningsValue =
                parseFloat(totalAdditionalEarnings.value) || 0;
            const totalDeductionsValue = parseFloat(totalDeductions.value) || 0;

            return parseFloat(
                (
                    basicSalaryValue +
                    totalSpecialAllowance +
                    totalPredefinedEarningsValue +
                    totalCustomEarningsValue +
                    totalAdditionalEarningsValue -
                    totalDeductionsValue +
                    (expenseAmount.value || 0)
                ).toFixed(2)
            );
        };

        const netSalary = computed(() => {
            return calculateNetSalary();
        });

        // Update net salary on changes
        const updateNetSalary = () => {
            netSalary.value = calculateNetSalary();
        };
        const onSubmit = () => {
            if (calculateSpecialAllowance.value < 0) {
                notification.error({
                    message: t("common.error"),
                    description: t("payroll.special_allowances_calculation"),
                });
                return;
            }

            // Clear previous validation errors
            rules.value = {};

            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...props.formData,
                    payrolls: [props.data.xid],
                    custom_earnings: earnings.value,
                    custom_deductions: deductions.value,
                    additional_earnings: additionalEarnings.value,
                    predefined_earnings: predefinedEarnings.value,
                    predefined_deductions: predefinedDeductions.value,
                    basic_salary: basicSalary.value,
                    net_salary: netSalary.value,
                    expense_amount: expenseAmount.value,
                },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
                error: (error) => {
                    if (error.error?.details) {
                        const errorData = error.error.details;
                        Object.keys(errorData).forEach((key) => {
                            const templateKey = key.replace(/\\./g, ".");
                            rules.value[templateKey] = {
                                required: true,
                                message: errorData[key][0],
                            };
                        });
                    } else {
                    }
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            () => {
                additionalEarnings.value = [];
                earnings.value = [];
                deductions.value = [];
                addEarning.value = [];
                addDeduction.value = [];
                predefinedDeductions.value = [];
                predefinedEarnings.value = [];
                props.formData.payroll_status = "generated";

                expenseAmount.value = props.data.expense_amount;

                basicSalary.value = parseFloat(
                    (props.data.basic_salary || 0).toFixed(2)
                );
                const components = props.data.payroll_components || [];

                components.forEach((item) => {
                    switch (item.type) {
                        case "earnings":
                            predefinedEarnings.value.push({
                                ...item,
                                monthly: item.amount,
                                monthly_value: parseFloat(
                                    calculateMonthly({
                                        ...item,
                                        monthly: item.amount,
                                    }).toFixed(2)
                                ),
                            });
                            break;
                        case "deductions":
                            predefinedDeductions.value.push({
                                ...item,
                                monthly: item.amount,
                                monthly_value: parseFloat(
                                    calculateMonthly({
                                        ...item,
                                        monthly: item.amount,
                                    }).toFixed(2)
                                ),
                            });
                            break;

                        case "pre_payments":
                            predefinedDeductions.value.push({
                                ...item,
                                monthly: item.amount,
                                monthly_value: parseFloat(
                                    calculateMonthly({
                                        ...item,
                                        monthly: item.amount,
                                    }).toFixed(2)
                                ),
                            });
                            break;
                        case "custom_earning":
                            earnings.value.push({
                                ...item,
                                monthly: item.amount,
                            });
                            break;
                        case "custom_deduction":
                            deductions.value.push({
                                ...item,
                                monthly: item.amount,
                            });
                            break;
                        case "additional_earning":
                            additionalEarnings.value.push({
                                ...item,
                                monthly: item.amount,
                            });
                            break;
                        default:
                            break;
                    }
                });

                nextTick(() => {
                    updateNetSalary();
                });

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
            },
            { immediate: true }
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
            predefinedEarnings,
            predefinedDeductions,
            earnings,
            additionalEarnings,
            deductions,
            addEarning,
            addAdditionalEarning,
            addDeduction,
            removeEarning,
            removeAdditionalEarning,
            removeDeduction,
            calculateSpecialAllowance,
            netSalary,
            calculateMonthly,
            updateMonthlyValue,
            basicSalary,
            totalPredefinedEarnings,
            summaryColumns,
            summaryData,
            leaveHoliday,
            expenseAmount,
            accountAdded,
            accounts,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "70%",
        };
    },
});
</script>
<style scoped>
.input-gap {
    margin-bottom: 15px;
    padding: 0px;
}
.heading {
    font-weight: bold;
    text-align: left;
    padding: 0px;
}
.input-wrapper {
    display: flex;
    flex-direction: column;
    width: 100%;
}
.error-message {
    color: #ff4d4f;
    font-size: 12px;
    margin-top: 4px;
    line-height: 1.5;
    display: block;
    position: relative;
    z-index: 1;
}
</style>
