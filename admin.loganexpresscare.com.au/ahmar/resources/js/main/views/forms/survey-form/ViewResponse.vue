<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <div class="bg-white">
            <a-row type="flex">
                <a-col :span="20">
                    <div class="user-details">
                        <a-row :gutter="[16, 16]">
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <FormItemHeading>
                                    {{ $t("feedback.employee_details") }}
                                </FormItemHeading>
                                <a-descriptions
                                    layout="vertical"
                                    :labelStyle="{
                                        fontWeight: 'bold',
                                    }"
                                    :contentStyle="{
                                        fontWeight: 500,
                                        marginBottom: '20px',
                                    }"
                                >
                                    <a-descriptions-item :label="$t('user.name')">
                                        {{ surveyOfEmployee.user.name }}
                                    </a-descriptions-item>
                                    <a-descriptions-item :label="$t('user.email')">
                                        {{ surveyOfEmployee.user.email }}
                                    </a-descriptions-item>
                                    <a-descriptions-item
                                        :label="$t('feedback.submit_date')"
                                    >
                                        {{
                                            surveyOfEmployee
                                                ? formatDate(surveyOfEmployee.submit_date)
                                                : "-"
                                        }}
                                    </a-descriptions-item>
                                </a-descriptions>
                            </a-col>
                        </a-row>
                    </div>

                    <div style="padding: 5px">
                        <a-row type="flex">
                            <a-col :span="20">
                                <a-list
                                    item-layout="horizontal"
                                    :data-source="surveyOfEmployee.data"
                                >
                                    <template #renderItem="{ item, index }">
                                        <a-list-item>
                                            <a-list-item-meta :description="item.reply">
                                                <template #title>
                                                    {{ item.name }}
                                                </template>
                                                <template #avatar>
                                                    {{ index + 1 }}.
                                                </template>
                                            </a-list-item-meta>
                                        </a-list-item>
                                    </template>
                                    <a-divider />
                                </a-list>
                            </a-col>
                        </a-row>
                    </div>
                </a-col>
            </a-row>
        </div>
        <div>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <div v-for="(feed, index) in indicators" :key="feed.id">
                        <FormItemHeading>
                            {{ feed.name }}
                        </FormItemHeading>
                        <div v-for="(indi, index) in feed.fields" :key="indi.id">
                            <a-form layout="horizontal">
                                <a-row>
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <span style="height: 200px"
                                            >{{ indi.name }}
                                        </span>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item name="rating">
                                            <a-rate v-model:value="indi.rating_details" />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-form>
                        </div>
                    </div>
                </a-col>
            </a-row>
        </div>

        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ $t("common.update") }}
                </a-button>
                <a-button key="back" type="primary" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "@/common/composable/common";
import { useI18n } from "vue-i18n";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: {
        SaveOutlined,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading } = apiAdmin();
        const surveyOfEmployee = ref({});
        const { formatDate } = common();
        const indicators = ref([]);
        const indicatorUrl = "indicators?fields=id,xid,name,fields";
        const { t } = useI18n();
        const formData = ref({
            rating_details: "",
            xid: props.data.xid,
        });

        const onSubmit = () => {
            var newFormData = {
                ...formData.value,
                rating_details: indicators.value,
                xid: props.data.xid,
            };
            addEditRequestAdmin({
                url: "create-feedback-user-rating",
                data: newFormData,
                successMessage: t("feedback.rating_updated"),
                success: (response) => {
                    onClose();
                    if (response && response.rating == "success") {
                        emit("updateRatingResponse", response.xid);
                    }
                },
            });
        };

        const onClose = () => {
            emit("close");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                surveyOfEmployee.value = props.data;
                indicators.value = [];

                if (props.data.rating_details == null) {
                    const indicatorPromise = axiosAdmin.get(indicatorUrl);
                    Promise.all([indicatorPromise]).then(([indicatorResponse]) => {
                        indicators.value = indicatorResponse.data;
                    });
                } else {
                    indicators.value = props.data.rating_details;
                }
            }
        );

        return {
            loading,
            onClose,
            surveyOfEmployee,
            formatDate,
            indicators,
            indicatorUrl,
            onSubmit,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
