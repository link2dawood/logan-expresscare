<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
    >
        <div>
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <FormItemHeading>
                        {{ $t("feedback.feedback_info") }}
                    </FormItemHeading>
                    <a-descriptions
                        :labelStyle="{
                            fontWeight: 'bold',
                        }"
                        :contentStyle="{
                            marginBottom: '15px',
                        }"
                    >
                        <a-descriptions-item :label="$t('feedback.title')">
                            {{ data.title }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('feedback.last_date')">
                            {{ data.last_date ? formatDate(data.last_date) : "-" }}
                        </a-descriptions-item>
                    </a-descriptions>
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
                        <a-descriptions-item
                            :label="$t('feedback.description')"
                            style="white-space: pre-wrap"
                        >
                            {{ data.description }}
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
            </a-row>
        </div>

        <ResponseTable
            ref="responseTable"
            :filters="filters"
            tableSize="middle"
            :bordered="true"
            :selectable="true"
            :responseColumns="responseColumns"
            :isPageTableContent="false"
        >
            <template #tabs="{ setUrlData }">
                <a-row>
                    <a-col :span="24">
                        <a-tabs
                            v-model:activeKey="filters.tab_key"
                            @change="setUrlData()"
                        >
                            <a-tab-pane
                                key="replied"
                                :tab="`${$t('feedback.replied')}`"
                            />
                            <a-tab-pane
                                key="not_replied"
                                :tab="`${$t('feedback.not_replied')}`"
                            />
                        </a-tabs>
                    </a-col>
                </a-row>
            </template>
        </ResponseTable>
        <template #footer>
            <a-space>
                <a-button key="back" type="primary" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, nextTick } from "vue";
import { EyeOutlined } from "@ant-design/icons-vue";
import common from "@/common/composable/common";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import ResponseTable from "./ResponseTable.vue";
import fields from "./fields";

export default defineComponent({
    props: [
        "data",
        "visible",
        "feedbackId",
        "employeeIds",
        "pageTitle",
        "successMessage",
        "tabKey",
    ],
    components: { EyeOutlined, FormItemHeading, ResponseTable },
    setup(props, { emit }) {
        const { formatDate } = common();
        const { responseColumns } = fields();
        const responseTable = ref(null);
        const filters = ref({ feedback_id: undefined, tab_key: "replied" });

        const onClose = () => {
            emit("close");
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                await nextTick();
                if (newVal) {
                    filters.value.feedback_id = props.feedbackId;
                    filters.value.tab_key = props.tabKey;
                    if (responseTable.value) {
                        responseTable.value.setUrlData();
                    }
                }
            }
        );

        return {
            onClose,
            responseTable,
            closed,
            filters,
            formatDate,
            responseColumns,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
