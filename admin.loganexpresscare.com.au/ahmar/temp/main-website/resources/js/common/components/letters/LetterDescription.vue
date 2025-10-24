<template>
    <a-row :gutter="16">
        <a-col :xs="24" :sm="24" :md="24" :lg="24">
            <a-form-item
                :label="labelTitle != '' ? labelTitle : $t('generate.description')"
                :name="descriptionLableTitle"
                :help="
                    rules && rules[descriptionLableTitle]
                        ? rules[descriptionLableTitle]['message']
                        : null
                "
                :validateStatus="rules && rules[descriptionLableTitle] ? 'error' : null"
                :class="{ required }"
            >
                <div ref="textEditor" class="quill-editor"></div>
            </a-form-item>
        </a-col>
    </a-row>

    <a-tabs v-model:activeKey="activeKey">
        <a-tab-pane
            v-for="(selectedLetterHeadField, index) in selectedLetterHeadFields"
            :key="'selected_letterhead_field_title_' + index"
            :tab="selectedLetterHeadField.title"
        >
            <a-row :gutter="16" class="mb-20">
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="12"
                    v-for="(
                        selectedFormField, fieldIndex
                    ) in selectedLetterHeadField.values"
                    :key="'letterhead_field_value_' + fieldIndex"
                >
                    <a-button
                        @click="insertFormText(selectedFormField.name)"
                        type="link"
                        style="padding: 0px; font-size: smaller"
                    >
                        {{ selectedFormField.name }}
                    </a-button>
                </a-col>
            </a-row>
        </a-tab-pane>
    </a-tabs>
</template>

<script>
import { defineComponent, onMounted, ref, computed, watch } from "vue";
import Quill from "quill";
import "quill/dist/quill.snow.css"; // Import default theme
import { forEach, find } from "lodash-es";
import hrmManagement from "../../composable/hrmManagement";

export default defineComponent({
    props: {
        labelTitle: {
            default: "",
        },
        editorHeight: {
            default: "auto",
        },
        descriptionLableTitle: {
            default: "description",
        },
        letterheadTemplates: {
            default: [],
        },
        users: {
            default: [],
        },
        appreciation: {
            default: {},
        },
        rules: {
            default: {},
        },
        letterheadTemplateId: {
            default: undefined,
        },
        userId: {
            default: undefined,
        },
        required: {
            default: false,
        },
        isLetterHeadTemplate: {
            default: false,
        },
        renderListItem: {
            default: ["employee_keys", "other_keys"],
        },
    },
    emits: ["contentUpdated"],
    setup(props, { emit }) {
        const {
            letterHeadUser,
            letterHeadAppreciation,
            setLetterHeadTemplateValues,
            selectedLetterHeadFields,
            resetSelectformOption,
            templateDescription,
            reSetListOfKeys,
        } = hrmManagement();

        const textEditor = ref(null);
        const descriptionContent = ref("");
        const letterTitle = ref("");
        const activeKey = ref("selected_letterhead_field_title_0");
        let editor = null;

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
            // call this after setting user and appreciation
            setLetterHeadTemplateValues();

            const selectedPointArray = editor.getSelection(true);
            var leadDataInleadDataField = find(resetSelectformOption.value, [
                "name",
                mergeFieldText,
            ]);
            if (
                leadDataInleadDataField != undefined &&
                leadDataInleadDataField.value != undefined &&
                leadDataInleadDataField.value != "" &&
                !props.isLetterHeadTemplate
            ) {
                var newFieldTextValue =
                    selectedPointArray.length > 0
                        ? `${leadDataInleadDataField.value}`
                        : `${leadDataInleadDataField.value}`;
            } else {
                var newFieldTextValue =
                    selectedPointArray.length > 0
                        ? `##${mergeFieldText}##`
                        : `##${mergeFieldText}##`;
            }

            editor.deleteText(selectedPointArray.index, selectedPointArray.length);
            editor.insertText(selectedPointArray.index, newFieldTextValue);
        };

        const setAllValues = () => {
            letterHeadUser.value = {};

            letterHeadUser.value = find(props.users, { xid: props.userId });

            letterHeadAppreciation.value = props.appreciation;
            setDescription();
        };

        const setDescription = () => {
            var bodyMessage = editor.root.innerHTML;

            // call this after setting user and appreciation
            setLetterHeadTemplateValues();

            forEach(resetSelectformOption.value, (leadDataValue, leadDataKey) => {
                if (leadDataValue.value != undefined && leadDataValue.value != "") {
                    bodyMessage = bodyMessage.replaceAll(
                        `##${leadDataValue.name}##`,
                        leadDataValue.value
                    );
                }
            });

            descriptionContent.value = bodyMessage;

            if (textEditor.value != undefined) {
                editor.root.innerHTML = bodyMessage;
            }
            // Not execute at onMounted
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

        const reSetTemplate = () => {
            if (props.renderListItem.length > 0) {
                reSetListOfKeys(props.renderListItem);
            }
            var selectedTemp = find(props.letterheadTemplates, {
                xid: props.letterheadTemplateId,
            });

            templateDescription.value =
                selectedTemp && selectedTemp.description ? selectedTemp.description : "";

            if (textEditor.value != undefined) {
                editor.root.innerHTML = templateDescription.value;
                setAllValues();
            }
        };

        watch(
            () => props.letterheadTemplateId,
            (newVal, oldVal) => {
                reSetTemplate();
            }
        );

        watch(
            () => props.userId,
            (newVal, oldVal) => {
                reSetTemplate();
            }
        );

        return {
            descriptionContent,
            textEditor,
            selectedLetterHeadFields,
            insertFormText,
            setHTML,
            reSetTemplate,
            letterHeadAppreciation,
            activeKey,
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
