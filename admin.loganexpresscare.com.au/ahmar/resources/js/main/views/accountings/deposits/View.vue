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
                >
                    <a-descriptions-item :label="$t('deposit.account')">
                        {{ data.account ? data.account.name : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('deposit.amount')">
                        {{ formatAmountCurrency(data.amount) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('deposit.date_time')">
                        {{ formatDateTime(data.date_time) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('deposit.deposit_category')">
                        {{ data.deposit_category.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('deposit.payer')">
                        {{ data.payer ? data.payer.name : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('deposit.reference_number')">
                        <div v-if="data.reference_number">
                            {{ data.reference_number }}
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('deposit.file')">
                        <div v-if="data.file">
                            <a-typography-link :href="data.file_url" target="_blank">
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
                    <a-descriptions-item :label="$t('deposit.notes')">
                        <div v-if="data && data.notes" style="white-space: pre-wrap">
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
