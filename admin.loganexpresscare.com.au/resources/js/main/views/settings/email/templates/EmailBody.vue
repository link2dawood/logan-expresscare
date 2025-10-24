<template>
    <a-row :gutter="16">
        <a-col :xs="24" :sm="24" :md="24" :lg="24">
            <a-form-item
                :label="$t('email_template.body')"
                name="body"
                :help="rules.body ? rules.body.message : null"
                :validateStatus="rules.body ? 'error' : null"
                class="required"
            >
                <div ref="textEditor" class="quill-editor"></div>
            </a-form-item>
        </a-col>
    </a-row>

    <a-row :gutter="16" class="mt-20">
        <a-col :span="24">
            <strong>{{ $t("email_template.available_variable") }}:</strong>
        </a-col>
    </a-row>

    <a-row :gutter="16" class="mb-20">
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="12"
            v-for="variableFieldValue in variableFields"
            :key="'letterhead_field_value_' + variableFieldValue"
        >
            <a-button
                @click="insertFormText(variableFieldValue)"
                type="link"
                style="padding: 0px; font-size: smaller"
            >
                {{ variableFieldValue }}
            </a-button>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, onMounted, ref, computed, watch } from "vue";
import Quill from "quill";
import "quill/dist/quill.snow.css"; // Import default theme
import { get, find } from "lodash-es";
import { useAuthStore } from "../../../../store/authStore";

export default defineComponent({
    props: {
        editorHeight: {
            default: "auto",
        },
        emailTypeKey: {
            default: "",
        },
        rules: {
            default: {},
        },
    },
    emits: ["contentUpdated"],
    setup(props, { emit }) {
        const authStore = useAuthStore();
        const textEditor = ref(null);
        const descriptionContent = ref("");
        const letterTitle = ref("");
        let editor = null;
        const templateDescription = ref("");
        const variableFields = ref([
            "EMPLOYEE_NAME",
            "EMPLOYEE_EMAIL",
            "EMPLOYEE_PASSWORD",
            "EMPLOYEE_ID",
            "EMPLOYEE_JOINING_DATE",
            "EMPLOYEE_DEPARTMENT",
            "EMPLOYEE_DESIGNATION",
            "COMPANY_NAME",
            "LOGIN_URL",
        ]);

        onMounted(() => {
            editor = new Quill(textEditor.value, {
                theme: "snow",
                placeholder: "Write something...",
                modules: {
                    toolbar: [
                        [{ size: ["small", false, "large", "huge"] }], // custom dropdown
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        ["bold", "italic", "underline", "strike"], // toggled buttons
                        ["image", "video"],

                        [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                        [{ script: "sub" }, { script: "super" }], // superscript/subscript

                        [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                        [{ align: [] }],
                    ],
                    table: true,
                },
            });

            // Update content on text change
            editor.on("text-change", () => {
                descriptionContent.value = editor.root.innerHTML;
                emit("contentUpdated", descriptionContent.value);
            });
        });

        const insertFormText = (mergeFieldText) => {
            const selectedPointArray = editor.getSelection(true);

            var newFieldTextValue =
                selectedPointArray.length > 0
                    ? `##${mergeFieldText}##`
                    : `##${mergeFieldText}##`;
            // }

            editor.deleteText(selectedPointArray.index, selectedPointArray.length);
            editor.insertText(selectedPointArray.index, newFieldTextValue);
        };

        const setHTML = (html) => {
            if (html == "") {
                editor.root.innerHTML = html;
                editor.history.clear(); // Resets history to ensure it's truly empty
            } else {
                editor.root.innerHTML = "";
                editor.clipboard.dangerouslyPasteHTML(0, html);
            }
        };

        watch(
            () => props.emailTypeKey,
            (newVal, oldVal) => {
                variableFields.value = get(authStore.emailVariables, newVal, []);
            }
        );

        return {
            descriptionContent,
            textEditor,
            insertFormText,
            setHTML,

            variableFields,
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
