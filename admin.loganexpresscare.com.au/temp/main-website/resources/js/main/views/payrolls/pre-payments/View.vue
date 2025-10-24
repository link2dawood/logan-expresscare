<template>
    <a-drawer
        :title="pageTitle"
        :width="720"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <a-row :gutter="16">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-descriptions
                    :labelStyle="{
                        fontWeight: 'bold',
                    }"
                    :contentStyle="{
                        marginBottom: '15px',
                    }"
                    :column="1"
                    layout="vertical"
                    size="small"
                    ><a-descriptions-item :label="$t('pre_payment.user_id')">
                        {{ data.user.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('pre_payment.date_time')">
                        {{ formatDateTime(data.date_time) }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('pre_payment.deduct_from_payroll')"
                    >
                        {{
                            data.deduct_from_payroll
                                ? data.deduct_from_payroll
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('pre_payment.amount')">
                        {{ formatAmountCurrency(data.amount) }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('pre_payment.deduct_month')"
                    >
                        {{ getMonthNameByNumber(data.payroll_month) }}
                        {{ data.payroll_year }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('pre_payment.notes')">
                        <div
                            v-if="data && data.notes"
                            style="white-space: pre-wrap"
                        >
                            {{ data.notes }}
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>
                </a-descriptions>
            </a-col>
        </a-row>

        <template #footer>
            <a-button key="back" type="primary" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent } from "vue";
import { DownloadOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";
import hrmManagement from "../../../../common/composable/hrmManagement";

export default defineComponent({
    components: {
        DownloadOutlined,
    },
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime, formatAmountCurrency } = common();
        const { loading, rules } = apiAdmin();
        const { getMonthNameByNumber } = hrmManagement();

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        return {
            rules,
            loading,
            formatDateTime,
            formatAmountCurrency,
            onClose,
            getMonthNameByNumber,
        };
    },
});
</script>
