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
                        :label="$t('appreciation.profile_image')"
                    >
                        <a-col :xs="24" :sm="24" :md="15" :lg="15">
                            <span v-if="data.profile_image">
                                <a-image
                                    :width="32"
                                    :src="data.profile_image_url"
                            /></span>
                            <span v-else>-</span>
                        </a-col>
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('appreciation.user')">
                        {{ data.user?.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('appreciation.date')">
                        {{ formatDateTime(data.date_time) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('appreciation.award')">
                        {{ data.award?.name }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('appreciation.price_amount')"
                    >
                        {{ formatAmountCurrency(data.price_amount) }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('appreciation.award')">
                        <div v-for="price in data.price_given">
                            {{ price.price_given }}
                        </div>
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('appreciation.description')"
                    >
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
