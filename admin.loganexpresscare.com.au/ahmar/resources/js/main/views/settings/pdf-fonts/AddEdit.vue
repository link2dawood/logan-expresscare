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
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('pdf_font.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('pdf_font.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('pdf_font.file')"
                        name="file"
                        :help="rules.file ? rules.file.message : null"
                        :validateStatus="rules.file ? 'error' : null"
                        class="required"
                    >
                        <UploadFile
                            :acceptFormat="'font/ttf'"
                            :formData="formData"
                            folder="fonts"
                            uploadField="file"
                            @onFileUploaded="
                                (file) => {
                                    formData.file = file.file;
                                    formData.file_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                <a-form-item
                    :label="$t('pdf_font.user_kashida')"
                    name="user_kashida"
                    :help="
                        rules.user_kashida ? rules.user_kashida.message : null
                    "
                    :validateStatus="rules.user_kashida ? 'error' : null"
                >
                    <a-radio-group
                        v-model:value="formData.user_kashida"
                        button-style="solid"
                        size="small"
                    >
                        <a-radio-button :value="1">
                            {{ $t("common.yes") }}
                        </a-radio-button>
                        <a-radio-button :value="0">
                            {{ $t("common.no") }}
                        </a-radio-button>
                    </a-radio-group>
                </a-form-item>
            </a-col>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('pdf_font.use_otl')"
                        name="use_otl"
                        :help="rules.use_otl ? rules.use_otl.message : null"
                        :validateStatus="rules.use_otl ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.use_otl"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="255">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
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
import { defineComponent } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import { onMounted } from "vue";

export default defineComponent({
    props: [
        "formData",
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
        UploadFile,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
            onSubmit,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
