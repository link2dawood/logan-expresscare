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
                    <a-descriptions-item :label="$t('news.title')">
                        {{ data.title }}
                    </a-descriptions-item>
                </a-descriptions>
            </a-col>
        </a-row>
        <a-row :gutter="16">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-descriptions
                    layout="vertical"
                    :contentStyle="{ fontWeight: 500, marginBottom: '5px' }"
                    :labelStyle="{
                        fontWeight: 'bold',
                    }"
                >
                    <a-descriptions-item :label="$t('news.description')">
                        <div
                            v-if="data && data.description"
                            style="white-space: pre-wrap"
                        >
                            {{ data.description }}
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
        const { formatDateTime, formatAmountCurrency, leaveRequestColors } =
            common();
        const { loading, rules } = apiAdmin();

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        return {
            rules,
            loading,
            leaveRequestColors,
            formatDateTime,
            formatAmountCurrency,
            onClose,
        };
    },
});
</script>
