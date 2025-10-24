<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        :maskClosable="false"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('complaint.status')"
                        name="status"
                        :help="rules.status ? rules.status.message : null"
                        :validateStatus="rules.status ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="data.status"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('resignation.status'),
                                ])
                            "
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button value="approved">
                                {{ $t("common.approved") }}
                            </a-radio-button>
                            <a-radio-button value="rejected">
                                {{ $t("common.rejected") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('complaint.reply_notes')"
                        name="reply_notes"
                        :help="rules.reply_notes ? rules.reply_notes.message : null"
                        :validateStatus="rules.reply_notes ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="data.reply_notes"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('complaint.reply_notes'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";

export default defineComponent({
    components: {
        SaveOutlined,
    },
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "change-status",
                data: props.data,
                successMessage: t("complaint.complaint_status_updated"),
                success: (response) => {
                    if (response && response.status == "success") {
                        emit("close");
                    }
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            props.data.reply_notes = undefined;
            emit("close");
        };

        return {
            rules,
            loading,
            formatDateTime,
            onClose,
            onSubmit,
        };
    },
});
</script>
