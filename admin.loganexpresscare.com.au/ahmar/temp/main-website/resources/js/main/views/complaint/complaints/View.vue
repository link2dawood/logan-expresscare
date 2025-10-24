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
                    <a-descriptions-item :label="$t('complaint.title')">
                        {{ data.title }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('complaint.from_staff')">
                        {{ data.from_staff.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('complaint.to_staff')">
                        {{ data.to_staff.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('complaint.date_time')">
                        {{ data ? formatDateTime(data.date_time) : "-" }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('complaint.proff_of_document')">
                        <a-col :xs="24" :sm="24" :md="15" :lg="15">
                            <span v-if="data.proff_of_document">
                                <a-image :width="32" :src="data.proff_of_document_url"
                            /></span>
                            <span v-else>-</span>
                        </a-col>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('complaint.status')">
                        {{ data.status }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('complaint.reply')"
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
