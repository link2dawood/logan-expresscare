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
                    layout="vertical"
                    :contentStyle="{ fontWeight: 500, marginBottom: '5px' }"
                    :labelStyle="{
                        fontWeight: 'bold',
                    }"
                >
                    <a-descriptions-item
                        v-if="data && data.user && data.user.name"
                        :label="$t('expense.user')"
                    >
                        {{ data.user?.name }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('expense.expense_category')"
                    >
                        {{ data.expense_category?.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('expense.amount')">
                        {{ formatAmountCurrency(data.amount) }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('expense.reference_number')"
                    >
                        <div v-if="data.reference_number">
                            {{ data.reference_number }}
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('expense.date_time')">
                        {{ formatDateTime(data.date_time) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('expense.bill')">
                        <div v-if="data.bill">
                            <a-typography-link
                                :href="data.bill_url"
                                target="_blank"
                            >
                                <a-tag color="success">
                                    <template #icon>
                                        <DownloadOutlined />
                                    </template>
                                    {{ $t("common.download") }}
                                </a-tag>
                            </a-typography-link>
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('expense.payee')">
                        {{ data.payee ? data.payee.name : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('expense.account')">
                        {{ data.account ? data.account.name : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item> </a-descriptions-item>
                    <a-descriptions-item :label="$t('expense.notes')">
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

export default defineComponent({
    components: {
        DownloadOutlined,
    },
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime, formatAmountCurrency } = common();
        const { loading, rules } = apiAdmin();

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
        };
    },
});
</script>
