<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{
            paddingBottom: '80px',
            backgroundColor: isFullMode || activeKey == 'preview' ? '#f0f2f5' : 'inherit',
        }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <template #extra>
            <a-space>
                <a-switch v-model:checked="isFullMode" /> Preview Mode
                <span
                    v-if="
                        permsArray.includes('font_settings') ||
                        permsArray.includes('admin')
                    "
                >
                    <PdfFontSettings btnType="text">
                        <template #icon>
                            <SettingOutlined />
                        </template>
                    </PdfFontSettings>
                </span>
            </a-space>
        </template>
        <a-form layout="vertical">
            <a-tabs v-show="!isFullMode" v-model:activeKey="activeKey">
                <a-tab-pane key="details" :tab="`${$t('generate.details')}`" />
                <a-tab-pane key="preview" :tab="`${$t('generate.preview')}`" />
            </a-tabs>

            <div>
                <a-row :gutter="16">
                    <a-col
                        v-show="activeKey == 'details' || isFullMode"
                        :xs="24"
                        :sm="24"
                        :md="12"
                        :lg="isFullMode ? 12 : 24"
                    >
                        <a-card
                            class="card-height"
                            style="box-shadow: none"
                            :bodyStyle="{ padding: isFullMode ? '24px' : '0px' }"
                            :bordered="isFullMode"
                            :title="isFullMode ? 'Generate' : null"
                        >
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('generate.user_id')"
                                        name="user_id"
                                        :help="
                                            rules.user_id ? rules.user_id.message : null
                                        "
                                        :validateStatus="rules.user_id ? 'error' : null"
                                        class="required"
                                    >
                                        <span style="display: flex">
                                            <a-select
                                                v-model:value="formData.user_id"
                                                :placeholder="
                                                    $t('common.select_default_text', [
                                                        $t('generate.user_id'),
                                                    ])
                                                "
                                                :allowClear="true"
                                                show-search
                                                optionFilterProp="title"
                                            >
                                                <a-select-option
                                                    v-for="user in users"
                                                    :key="user.xid"
                                                    :value="user.xid"
                                                    :title="user.name"
                                                    ><user-list-display
                                                        :user="user"
                                                        whereToShow="select"
                                                    />
                                                </a-select-option>
                                            </a-select>
                                            <StaffAddButton @onAddSuccess="userAdded" />
                                        </span>
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('generate.letter_head_template')"
                                        name="letterhead_template_id"
                                        :help="
                                            rules.letterhead_template_id
                                                ? rules.letterhead_template_id.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.letterhead_template_id ? 'error' : null
                                        "
                                        class="required"
                                    >
                                        <span style="display: flex">
                                            <a-select
                                                v-model:value="
                                                    formData.letterhead_template_id
                                                "
                                                :placeholder="
                                                    $t('common.select_default_text', [
                                                        $t(
                                                            'generate.letter_head_template'
                                                        ),
                                                    ])
                                                "
                                                :allowClear="true"
                                                @change="
                                                    setDescription(
                                                        formData.letterhead_template_id
                                                    )
                                                "
                                            >
                                                <a-select-option
                                                    v-for="template in allTemplates"
                                                    :key="template.xid"
                                                    :value="template.xid"
                                                >
                                                    {{ template.title }}
                                                </a-select-option>
                                            </a-select>
                                            <LetterheadTemplateAddButton
                                                @onAddSuccess="letterheadTemplateAdded"
                                            />
                                        </span>
                                    </a-form-item>
                                </a-col>
                            </a-row>

                            <a-row :gutter="16">
                                <a-col
                                    :xs="24"
                                    :sm="24"
                                    :md="24"
                                    :lg="24"
                                    v-if="formData.letterhead_template_id != undefined"
                                >
                                    <a-form-item
                                        :label="$t('appreciation.letterhead_title')"
                                        name="title"
                                        :help="rules.title ? rules.title.message : null"
                                        :validateStatus="rules.title ? 'error' : null"
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.title"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('appreciation.letterhead_title'),
                                                ])
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>

                            <div v-show="formData.letterhead_template_id != undefined">
                                <LetterDescription
                                    ref="textEditor"
                                    descriptionLableTitle="description"
                                    :letterheadTemplateId="
                                        formData.letterhead_template_id
                                    "
                                    :userId="formData.user_id"
                                    :users="users"
                                    :rules="rules"
                                    :letterheadTemplates="allTemplates"
                                    @contentUpdated="
                                        (formDescription) =>
                                            (descriptionContent = formDescription)
                                    "
                                    :required="
                                        formData && formData.letterhead_template_id
                                    "
                                    :renderListItem="['employee_keys', 'other_keys']"
                                />
                            </div>
                        </a-card>
                    </a-col>
                    <a-col
                        v-show="activeKey == 'preview' || isFullMode"
                        :xs="24"
                        :sm="24"
                        :md="12"
                        :lg="isFullMode ? 12 : 24"
                    >
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-card
                                    class="card-height-preview"
                                    :bodyStyle="{ padding: '0px' }"
                                >
                                    <template #extra>
                                        <a-space>
                                            <PdfDownload
                                                :title="$t('common.download')"
                                                :payload="{
                                                    show_header_footer: 'no',
                                                    title: formData.title,
                                                    html: htmlContent,
                                                }"
                                            />
                                            <PdfDownload
                                                :title="$t('common.print')"
                                                :payload="{
                                                    show_header_footer: 'no',
                                                    title: formData.title,
                                                    html: htmlContent,
                                                }"
                                                :isPrint="true"
                                            >
                                                <template #icon>
                                                    <PrinterOutlined />
                                                </template>
                                            </PdfDownload>
                                        </a-space>
                                    </template>
                                    <div
                                        id="letter"
                                        class="para"
                                        v-html="htmlContent"
                                    ></div>
                                </a-card>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </div>
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
import { defineComponent, onMounted, ref, computed, watch, nextTick } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
    InfoCircleOutlined,
    PrinterOutlined,
    DownloadOutlined,
    SettingOutlined,
} from "@ant-design/icons-vue";
import { QuillEditor } from "@vueup/vue-quill";
import { some, forEach, filter, find } from "lodash-es";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import apiAdmin from "../../../../common/composable/apiAdmin";
import StaffAddButton from "../../staff-members/users/StaffAddButton.vue";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import LetterheadTemplateAddButton from "../../../views/letter-head/template/AddButton.vue";
import LetterDescription from "@/common/components/letters/LetterDescription.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import fields from "./fields";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import common from "../../../../common/composable/common";
import PdfFontSettings from "../../settings/pdf-fonts/PdfFontSettings.vue";

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
        MinusSquareOutlined,
        InfoCircleOutlined,
        PrinterOutlined,
        DownloadOutlined,
        SettingOutlined,

        UserListDisplay,
        StaffAddButton,
        PerfectScrollbar,
        QuillEditor,
        Upload,
        LetterheadTemplateAddButton,
        QuillEditor,
        LetterDescription,
        PdfDownload,
        PdfFontSettings,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { cssStyles, changeStyles, descriptionContent } = fields();
        const { permsArray } = common();
        const users = ref([]);
        const usersUrl = "users";
        const isFullMode = ref(false);
        const activeKey = ref("details");
        const letterheadTemplateUrl = "letter-head-templates?limit=10000";
        const textEditor = ref(null);
        const selectedUser = ref({});

        const htmlContent = ref();
        const allTemplates = ref([]);

        onMounted(() => {
            const userPromise = axiosAdmin.get(usersUrl);
            const letterheadTemplatePromise = axiosAdmin.get(letterheadTemplateUrl);

            Promise.all([userPromise, letterheadTemplatePromise]).then(
                ([userResponse, letterheadTemplateResponse]) => {
                    users.value = userResponse.data;
                    allTemplates.value = letterheadTemplateResponse.data;
                }
            );
            changeStyles();
        });

        const onSubmit = () => {
            var newFormData = {
                ...props.formData,
                description: descriptionContent.value,
            };

            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    htmlContent.value = "";
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const setDescription = (id) => {
            var selectedTemp = find(allTemplates.value, { xid: id });

            if (selectedTemp) {
                props.formData.title = selectedTemp.title;
            }
        };

        const userAdded = (xid) => {
            axiosAdmin.get(usersUrl).then((response) => {
                users.value = response.data;
                emit("addListSuccess", { type: "user_id", id: xid });
            });
        };

        const letterheadTemplateAdded = (xid) => {
            axiosAdmin.get(letterheadTemplateUrl).then((response) => {
                allTemplates.value = response.data;
                emit("addListSuccess", { type: "letterhead_template_id", id: xid });
            });
        };

        const setUserOnDescription = (id) => {
            selectedUser.value = find(users.value, { xid: id });

            if (props.formData.letterhead_template_id) {
                setDescription(props.formData.letterhead_template_id);
            }
        };

        const onClose = () => {
            htmlContent.value = "";
            rules.value = {};
            emit("closed");
        };

        const changeWidthOfDrawer = () => {
            htmlContent.value = htmlContent.value;
        };

        watch(
            () => descriptionContent.value,
            (newVal, oldVal) => {
                htmlContent.value = newVal;
            }
        );

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                changeStyles();
                await nextTick();
                if (props.visible) {
                    isFullMode.value = false;
                    activeKey.value = "details";
                    if (props.addEditType == "edit") {
                        if (props.data) {
                            textEditor.value.setHTML(props.data.description);
                        } else {
                            textEditor.value.setHTML("");
                        }
                    } else {
                        if (textEditor.value) {
                            textEditor.value.setHTML("");
                        }
                    }
                }
            }
        );

        const drawerWidth = computed(() => {
            return window.innerWidth <= 991 ? "90%" : isFullMode.value ? "90%" : "45%";
        });

        return {
            loading,
            rules,
            permsArray,
            onClose,
            onSubmit,

            userAdded,

            users,
            allTemplates,
            setDescription,
            setUserOnDescription,
            cssStyles,
            selectedUser,
            descriptionContent,
            htmlContent,
            isFullMode,
            changeWidthOfDrawer,

            activeKey,
            letterheadTemplateAdded,

            drawerWidth,
            textEditor,
            find,
        };
    },
});
</script>
<style>
.card-height {
    min-height: calc(100vh - 20px);
}

.card-height-preview {
    min-height: calc(100vh - 20px);
    min-width: 793px;
    padding: 0px;
}

.ql-editor {
    box-sizing: border-box;
    line-height: 1.42;
    height: 100%;
    outline: none;
    overflow-y: auto;
    tab-size: 4;
    text-align: left;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.para {
    padding: 20px;
    margin: 20px;
}

.para p {
    white-space: pre-wrap;
    font-size: 14px;
    margin: 0px;
    word-wrap: break-word;
    overflow: hidden;
    line-height: 25px;
}

.para-stroke {
    fill: none;
    stroke: #4b5563;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.para a {
    text-decoration: underline;
    color: #2563eb;
}

.para pre.ql-syntax {
    background-color: #23241f;
    color: #f8f8f2;
    overflow: visible;
}
.para ol {
    list-style-type: decimal !important;
    padding-left: 40px !important;
}

.para ul {
    list-style-type: disc !important;
    padding-left: 40px !important;
}
.para ol li,
.para ul li {
    display: list-item !important;
    list-style-position: inside !important;
}

.para ol li span.ql-ui,
.para ul li span.ql-ui {
    display: none !important;
}

.ql-align-center {
    text-align: center;
}
.ql-align-left {
    text-align: left;
}
.ql-align-right {
    text-align: right;
}
.ql-align-justify {
    text-align: justify;
}

.ql-snow .ql-editor h1 {
    font-size: 2em;
}

.para .ql-size-large {
    font-size: 2em;
}

.para h1 {
    font-size: 2em;
}
.para h2 {
    font-size: 1.5em;
}
.para h3 {
    font-size: 1.17em;
}
.para h4 {
    font-size: 1em;
}
.para h5 {
    font-size: 0.83em;
}
.para h6 {
    font-size: 0.67em;
}

.para strong {
    font-weight: bolder;
}

.para sub {
    bottom: -0.25em;
    position: relative;
    font-size: 75%;
    line-height: 0;
    vertical-align: baseline;
}

.para sup {
    top: -0.5em;
    position: relative;
    font-size: 75%;
    line-height: 0;
    vertical-align: baseline;
}

.para .ql-direction-rtl {
    direction: rtl;
}

.para .ql-size-small {
    font-size: 0.75em;
}
.para .ql-size-large {
    font-size: 1.5em;
}
.para .ql-size-huge {
    font-size: 2.5em;
}

.para table {
    width: 100%;

    border: 1px solid #000;
    border-collapse: collapse;
}

.para td {
    height: 25px;
    border: 1px solid #000;
    padding: 2px;
    border-collapse: collapse;
}

.para .ql-align-right {
    text-align: right;
}

.para .ql-align-left {
    text-align: left;
}

.para .ql-align-center img {
    margin-left: auto;
    margin-right: auto;
}
.para .ql-align-right img {
    margin-left: auto;
    margin-right: 0%;
}
</style>
