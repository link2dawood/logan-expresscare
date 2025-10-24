<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="18" :sm="18" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('hrm_dashboard.reason')"
                        name="reason"
                        :help="rules.reason ? rules.reason.message : null"
                        :validateStatus="rules.reason ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.reason"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('hrm_dashboard.reason'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button
                key="submit"
                type="primary"
                :loading="loading"
                @click="onSubmit"
            >
                <template #icon>
                    <SaveOutlined />
                </template>
                {{
                    addEditType == "add"
                        ? $t("common.create")
                        : $t("common.update")
                }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import common from "../../../common/composable/common";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: [
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading } = apiAdmin();
        const { t } = useI18n();
        const { appSetting, disabledDate, permsArray } = common();
        const formData = ref({});
        const rules = ref({});

        const onSubmit = () => {
            if (!formData.value.reason || formData.value.reason.trim() === "") {
                rules.value.reason = {
                    message: t("hrm_dashboard.reason_required"),
                };
                return;
            }

            rules.value = {};
            emit("submitReason", formData.value.reason);
            emit("closed");
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal) {
                    formData.value.reason = "";
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,
            formData,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
