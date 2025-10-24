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
                    <a-descriptions-item :label="$t('leave.user')">
                        {{ data.user ? data.user.name : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('leave.leave_type')">
                        {{ data.leave_type ? data.leave_type.name : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('leave.start_date')">
                        {{ formatDateTime(data.start_date) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('leave.end_date')">
                        {{ formatDateTime(data.start_date) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('leave.is_half_day')">
                        {{
                            data.is_half_day
                                ? $t("common.yes")
                                : $t("common.no")
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('leave.status')">
                        <a-tag :color="leaveRequestColors[data.status]">
                            {{ $t(`common.${data.status}`) }}
                        </a-tag>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('leave.reason')">
                        <div
                            v-if="data && data.reason"
                            style="white-space: pre-wrap"
                        >
                            {{ data.reason }}
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
import common from "../../../../../common/composable/common";

export default defineComponent({
    components: {
        DownloadOutlined,
    },
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime, formatAmountCurrency, leaveRequestColors } =
            common();

        const onClose = () => {
            emit("close");
        };

        return {
            leaveRequestColors,
            formatDateTime,
            formatAmountCurrency,
            onClose,
        };
    },
});
</script>
