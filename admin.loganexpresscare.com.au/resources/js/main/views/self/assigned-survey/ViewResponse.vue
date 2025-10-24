<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="20" :lg="20">
                <FormItemHeading>
                    {{ $t("assigned_surve.assigned_info") }}
                </FormItemHeading>
                <a-descriptions
                    layout="horizontal"
                    :labelStyle="{
                        fontWeight: 'bold',
                    }"
                >
                    <a-descriptions-item :label="$t('assigned_surve.title')">
                        {{
                            surveyOfEmployee.feedback.title
                                ? surveyOfEmployee.feedback.title
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('assigned_surve.last_date')">
                        {{
                            surveyOfEmployee.feedback.last_date
                                ? formatDate(surveyOfEmployee.feedback.last_date)
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('assigned_surve.submit_date')">
                        {{
                            surveyOfEmployee.submit_date
                                ? formatDate(surveyOfEmployee.submit_date)
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('assigned_surve.rating')">
                        {{
                            surveyOfEmployee.rating
                                ? formatRating(surveyOfEmployee.rating)
                                : "-"
                        }}
                    </a-descriptions-item>
                </a-descriptions>
            </a-col>
        </a-row>

        <div class="mt-20">
            <FormItemHeading>
                {{ surveyOfEmployee.feedback.title }}
            </FormItemHeading>
        </div>

        <div class="bg-white">
            <a-row type="flex">
                <a-col :span="20">
                    <a-list item-layout="horizontal" :data-source="surveyOfEmployee.data">
                        <template #renderItem="{ item, index }">
                            <a-list-item>
                                <a-list-item-meta :description="item.reply">
                                    <template #title>
                                        {{ item.name }}
                                    </template>
                                    <template #avatar> {{ index + 1 }}. </template>
                                </a-list-item-meta>
                            </a-list-item>
                        </template>
                        <a-divider />
                    </a-list>
                </a-col>
            </a-row>
        </div>

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
import { defineComponent, ref, watch } from "vue";
import common from "../../../../common/composable/common";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: { FormItemHeading },
    setup(props, { emit }) {
        const surveyOfEmployee = ref({});
        const { formatDate } = common();

        const formatRating = (item) => {
            return item.toFixed(2);
        };

        const onClose = () => {
            emit("close");
        };

        watch(
            () => props.data,
            (newVal, oldVal) => {
                surveyOfEmployee.value = newVal;
            }
        );

        return {
            onClose,
            surveyOfEmployee,
            formatDate,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            formatRating,
        };
    },
});
</script>
