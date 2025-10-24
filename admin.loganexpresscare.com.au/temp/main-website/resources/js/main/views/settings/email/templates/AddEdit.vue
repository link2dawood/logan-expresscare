<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('email_template.subject')"
                        name="subject"
                        :help="
                            rules['credentials.title'] &&
                            rules['credentials.title']['message']
                                ? rules['credentials.title']['message']
                                : null
                        "
                        :validateStatus="
                            rules['credentials.title'] && rules['credentials.title']
                                ? 'error'
                                : null
                        "
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.credentials.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('email_template.subject'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <EmailBody
                ref="textEditor"
                :emailTypeKey="emailTypeKey"
                :rules="rules"
                @contentUpdated="
                    (formDescription) => (formData.credentials.content = formDescription)
                "
                editorHeight="200px"
            />
        </a-form>
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
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, onMounted, nextTick, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import EmailBody from "./EmailBody.vue";

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
        EmailBody,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const textEditor = ref(null);
        const emailTypeKey = ref(props.data.name_key);

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

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                if (newVal) {
                    await nextTick();

                    emailTypeKey.value = props.data.name_key;

                    // Always it will be edit mode
                    textEditor.value.setHTML(props.formData.credentials.content || "");
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,

            textEditor,
            emailTypeKey,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

<style lang="less">
.quill-editor {
    .ql-align-center img {
        margin-left: auto;
        margin-right: auto;
    }

    .ql-align-right img {
        margin-left: auto;
        margin-right: 0%;
    }
}

.quill-editor {
    .ql-editor {
        width: 100%;
    }

    .ql-align-center iframe {
        display: block;
        margin: 0 auto;
    }

    .ql-align-right iframe {
        display: block;
        margin-left: auto;
        margin-right: 0;
    }
    iframe {
        height: 100%;
        width: 300px;
    }
}
</style>
