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
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('generate.user_id')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
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
                                @change="setUserOnDescription(formData.user_id)"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                            <StaffAddButton @onAddSuccess="staffMemberAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12" v-if="formData.user_id">
                    <a-form-item
                        :label="$t('generate.letter_head_template')"
                        name="letterhead_template_id"
                        :help="
                            rules.letterhead_template_id
                                ? rules.letterhead_template_id.message
                                : null
                        "
                        :validateStatus="rules.letterhead_template_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.letterhead_template_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('generate.letter_head_template'),
                                    ])
                                "
                                :allowClear="true"
                                @change="setDescription(formData.letterhead_template_id)"
                            >
                                <a-select-option
                                    v-for="template in allTemplates"
                                    :key="template.xid"
                                    :value="template.xid"
                                >
                                    {{ template.title }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('generate.left_space')"
                        name="left_space"
                        :help="rules.left_space ? rules.left_space.message : null"
                        :validateStatus="rules.left_space ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.left_space"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('generate.left_space'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('generate.right_space')"
                        name="right_space"
                        :help="rules.right_space ? rules.right_space.message : null"
                        :validateStatus="rules.right_space ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.right_space"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('generate.right_space'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('generate.top_space')"
                        name="top_space"
                        :help="rules.top_space ? rules.top_space.message : null"
                        :validateStatus="rules.top_space ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.top_space"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('generate.top_space'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('generate.bottom_space')"
                        name="bottom_space"
                        :help="rules.bottom_space ? rules.bottom_space.message : null"
                        :validateStatus="rules.bottom_space ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.bottom_space"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('generate.bottom_space'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('generate.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                    >
                        <QuillEditor
                            ref="textEditor"
                            v-model:content="formData.description"
                            :content="formData.description"
                            contentType="html"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('generate.description'),
                                ])
                            "
                            style="height: 200px"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row
                :gutter="16"
                v-if="resetSelectformOption && resetSelectformOption.length > 0"
                class="mb-20"
            >
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="12"
                    v-for="selectedFormField in resetSelectformOption"
                    :key="selectedFormField.id"
                >
                    <a-button
                        @click="insertFormText(selectedFormField.name)"
                        type="link"
                        style="padding: 0px"
                    >
                        {{ selectedFormField.name }}
                    </a-button>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button key="view" type="primary" danger @click="viewLetter()">
                    {{ $t("common.preview") }}
                </a-button>
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
    <Letter
        :data="formData"
        :visible="letterVisible"
        :pageTitle="letterTitle"
        @closed="closedPreview()"
    />
</template>

<script>
import { defineComponent, ref, watch, computed, onMounted } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "@/common/composable/common";
import StaffAddButton from "../../staff-members/users/StaffAddButton.vue";
import { find, filter, forEach, replace } from "lodash-es";
import Letter from "./Letter.vue";

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
        StaffAddButton,
        Letter,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const templateUrl =
            "letter-head-templates?fields=id,xid,title,description&limit=10000";
        const textEditor = ref(null);
        const allTemplates = ref([]);
        const { selectedLetterHeadFields, dayjs } = common();
        const users = ref([]);
        const userUrl =
            "users?fields=id,xid,user_type,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary";
        const letterVisible = ref(false);
        const letterTitle = ref({});
        const selectedUser = ref({});
        const resetSelectformOption = ref([]);

        onMounted(() => {
            const templatesPromise = axiosAdmin.get(templateUrl);
            const usersPromise = axiosAdmin.get(userUrl);

            Promise.all([templatesPromise, usersPromise]).then(
                ([templatesResponse, usersResponse]) => {
                    allTemplates.value = templatesResponse.data;
                    users.value = usersResponse.data;
                }
            );
        });

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

        const viewLetter = () => {
            letterVisible.value = true;
        };

        const closedPreview = () => {
            letterVisible.value = false;
        };

        const insertFormText = (mergeFieldText) => {
            const selectedPointArray = editor.value.getSelection(true);

            var leadDataInleadDataField = find(resetSelectformOption.value, [
                "name",
                mergeFieldText,
            ]);

            if (
                leadDataInleadDataField != undefined &&
                leadDataInleadDataField.value != undefined &&
                leadDataInleadDataField.value != ""
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

            editor.value.deleteText(selectedPointArray.index, selectedPointArray.length);
            editor.value.insertText(selectedPointArray.index, newFieldTextValue);
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const editor = computed(() => {
            return textEditor.value.getQuill();
        });

        const setDescription = (id) => {
            var selectedTemp = find(allTemplates.value, { xid: id });
            letterTitle.value = selectedTemp.title;
            if (selectedTemp) {
                props.formData.description = selectedTemp.description;
            }

            if (selectedTemp && selectedTemp.description) {
                var bodyMessage = selectedTemp.description;

                forEach(resetSelectformOption.value, (leadDataValue, leadDataKey) => {
                    if (leadDataValue.value != undefined && leadDataValue.value != "") {
                        bodyMessage = replace(
                            bodyMessage,
                            `##${leadDataValue.name}##`,
                            leadDataValue.value
                        );
                    }
                });

                props.formData.description = bodyMessage;

                // Not execute at onMounted
                if (textEditor.value != undefined) {
                    textEditor.value.setHTML(bodyMessage);
                }
            }
        };

        const staffMemberAdded = () => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
            });
        };

        const setUserOnDescription = (id) => {
            selectedUser.value = find(users.value, { xid: id });
            resetSelectformOption.value = [];
            if (selectedUser.value) {
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_NAME",
                    value: selectedUser.value.name,
                });
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_JOINING_DATE",
                    value: selectedUser.value.joining_date,
                });
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_LOCATION",
                    value: selectedUser.value.location
                        ? selectedUser.value.location.name
                        : null,
                });
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_ADDRESS",
                    value: selectedUser.value.address,
                });
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_EXIT_DATE",
                    value: selectedUser.value.exit_date
                        ? selectedUser.value.exit_date
                        : null,
                });
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_DOB",
                    value: selectedUser.value.dob ? selectedUser.value.dob : null,
                });
                resetSelectformOption.value.push({
                    name: "EMPLOYEE_DESIGNATION",
                    value: selectedUser.value.designation
                        ? selectedUser.value.designation.name
                        : null,
                });
                resetSelectformOption.value.push({
                    name: "SIGNATORY",
                    value: selectedUser.value.reporter
                        ? selectedUser.value.reporter.name
                        : null,
                });
                resetSelectformOption.value.push({
                    name: "CURRENT_DATE",
                    value: dayjs().format("YYYY-MM-DD"),
                });
            }
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal == true && textEditor.value) {
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

            insertFormText,
            textEditor,
            allTemplates,
            users,
            selectedLetterHeadFields,
            selectedUser,
            staffMemberAdded,
            setDescription,
            setUserOnDescription,
            resetSelectformOption,
            letterVisible,
            viewLetter,
            closedPreview,
            letterTitle,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
