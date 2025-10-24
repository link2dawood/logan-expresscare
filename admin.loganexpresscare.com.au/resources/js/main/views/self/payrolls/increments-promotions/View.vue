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
                    ><a-descriptions-item
                        :label="$t('increment_promotion.user_id')"
                    >
                        {{ data.user.name }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('increment_promotion.date')"
                    >
                        {{ formatDateTime(data.date_time) }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('increment_promotion.type')"
                    >
                        <div v-if="data.type == 'promotion'">
                            <a-tag color="yellow">
                                {{ $t(`increment_promotion.${"promotion"}`) }}
                            </a-tag>
                        </div>
                        <div v-if="data.type == 'increment'">
                            <a-tag color="green">
                                {{ $t(`increment_promotion.${"increment"}`) }}
                            </a-tag>
                        </div>
                        <div v-if="data.type == 'increment_promotion'">
                            <a-tag color="green">
                                {{
                                    $t(
                                        `increment_promotion.${"increment_promotion"}`
                                    )
                                }}
                            </a-tag>
                        </div>
                        <div v-if="data.type == 'decrement'">
                            <a-tag color="green">
                                {{ $t(`increment_promotion.${"decrement"}`) }}
                            </a-tag>
                        </div>
                        <div v-if="data.type == 'decrement_demotion'">
                            <a-tag color="red">
                                {{
                                    $t(
                                        `increment_promotion.${"decrement_demotion"}`
                                    )
                                }}
                            </a-tag>
                        </div>
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('increment_promotion.net_salary')"
                        v-if="
                            data.type == 'increment' ||
                            data.type == 'increment_promotion' ||
                            data.type == 'decrement' ||
                            data.type == 'decrement_demotion'
                        "
                    >
                        <SalaryVisibility :salary="data.net_salary" />
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('increment_promotion.promoted_designation')"
                        v-if="
                            data.type == 'promotion' ||
                            data.type == 'increment_promotion' ||
                            data.type == 'decrement_demotion'
                        "
                    >
                        {{ data.promoted_designation?.name }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('increment_promotion.current_designation')"
                        v-if="
                            data.type == 'promotion' ||
                            data.type == 'increment_promotion' ||
                            data.type == 'decrement_demotion'
                        "
                    >
                        {{ data.current_designation?.name }}
                    </a-descriptions-item>
                    <a-descriptions-item
                        :label="$t('increment_promotion.description')"
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
import common from "../../../../../common/composable/common";
import SalaryVisibility from "../../../../components/SalaryVisibility.vue";

export default defineComponent({
    components: {
        DownloadOutlined,
        SalaryVisibility,
    },
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime, formatAmountCurrency } = common();

        const onClose = () => {
            emit("close");
        };

        return {
            formatDateTime,
            formatAmountCurrency,
            onClose,
        };
    },
});
</script>
