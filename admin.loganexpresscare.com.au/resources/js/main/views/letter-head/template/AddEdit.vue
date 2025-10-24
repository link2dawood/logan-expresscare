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
                        :label="$t('template.title')"
                        name="title"
                        :help="rules.title ? rules.title.message : null"
                        :validateStatus="rules.title ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('template.title'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <LetterDescription
                ref="textEditor"
                :labelTitle="$t('template.description')"
                :rules="rules"
                :required="true"
                :isLetterHeadTemplate="true"
                @contentUpdated="
                    (formDescription) => (formData.description = formDescription)
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
import { defineComponent, ref, watch, computed, onMounted, nextTick } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import apiAdmin from "../../../../common/composable/apiAdmin";
import LetterDescription from "@/common/components/letters/LetterDescription.vue";

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
        InfoCircleOutlined,
        QuillEditor,
        LetterDescription,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const textEditor = ref(null);
        const initialContent = ref("");

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

                    textEditor.value.reSetTemplate();
                    if (props.addEditType == "edit") {
                        textEditor.value.setHTML(props.formData.description);
                    } else {
                        textEditor.value.setHTML("");
                    }
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            textEditor,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
