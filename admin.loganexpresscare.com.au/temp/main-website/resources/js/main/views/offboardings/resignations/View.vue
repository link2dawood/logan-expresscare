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
                    <a-descriptions-item :label="$t('resignation.user')">
                        {{ data.user.name }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('resignation.title')">
                        {{ data.title }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('resignation.resignation_date')">
                        <span v-if="data && data.start_date">
                            {{ formatDateTime(data.start_date) }}
                        </span>
                        <span v-else>-</span>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('resignation.last_working_date')">
                        <span v-if="data && data.end_date">
                            {{ formatDateTime(data.end_date) }}
                        </span>
                        <span v-else>-</span>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('resignation.status')">
                        {{ data.status }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('resignation.description')">
                        <div
                            v-if="data && data.description"
                            style="white-space: pre-wrap"
                        >
                            {{ data.description }}
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('resignation.reply_notes')"
                        v-if="data && data.reply_notes"
                    >
                        <div
                            v-if="data && data.reply_notes"
                            style="white-space: pre-wrap"
                        >
                            {{ data.reply_notes }}
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
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime } = common();
        const { loading, rules } = apiAdmin();

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        return {
            rules,
            loading,
            formatDateTime,
            onClose,
        };
    },
});
</script>
