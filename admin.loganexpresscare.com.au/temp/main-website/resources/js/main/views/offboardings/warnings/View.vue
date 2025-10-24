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
                    <a-descriptions-item :label="$t('warning.user')">
                        {{ data.user.name }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('warning.title')">
                        {{ data.title }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('warning.warning_date')">
                        <span v-if="data && data.warning_date">
                            {{ formatDateTime(data.warning_date) }}
                        </span>
                        <span v-else>-</span>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('warning.description')">
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
