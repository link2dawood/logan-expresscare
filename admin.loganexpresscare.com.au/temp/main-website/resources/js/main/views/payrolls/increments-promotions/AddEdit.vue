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
        <a-form layout="vertical" id="add_edit_increment_promotion_form">
            <a-tabs v-model:activeKey="addEditActiveTab">
                <a-tab-pane
                    key="basic_details"
                    :tab="`${$t('increment_promotion.basic_details')}`"
                >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('increment_promotion.type')"
                                name="type"
                                :help="rules.type ? rules.type.message : null"
                                :validateStatus="rules.type ? 'error' : null"
                                class="required"
                            >
                                <a-select
                                    v-model:value="formData.type"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('increment_promotion.type'),
                                        ])
                                    "
                                >
                                    <a-select-option value="increment">
                                        {{
                                            $t("increment_promotion.increment")
                                        }}
                                    </a-select-option>
                                    <a-select-option value="promotion">
                                        {{
                                            $t("increment_promotion.promotion")
                                        }}
                                    </a-select-option>
                                    <a-select-option
                                        value="increment_promotion"
                                    >
                                        {{
                                            $t(
                                                "increment_promotion.increment_promotion"
                                            )
                                        }}
                                    </a-select-option>
                                    <a-select-option value="decrement">
                                        {{
                                            $t("increment_promotion.decrement")
                                        }}
                                    </a-select-option>
                                    <a-select-option value="decrement_demotion">
                                        {{
                                            $t(
                                                "increment_promotion.decrement_demotion"
                                            )
                                        }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('increment_promotion.user_id')"
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
                                                $t(
                                                    'increment_promotion.user_id'
                                                ),
                                            ])
                                        "
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                    >
                                        <a-select-option
                                            v-for="allStaffMember in users"
                                            :key="allStaffMember.xid"
                                            :value="allStaffMember.xid"
                                            :title="allStaffMember.name"
                                            ><user-list-display
                                                :user="allStaffMember"
                                                whereToShow="select"
                                            />
                                        </a-select-option>
                                    </a-select>
                                    <StaffMemberAddButton
                                        @onAddSuccess="staffMemberAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row
                        :gutter="16"
                        v-if="
                            formData.type == 'promotion' ||
                            formData.type == 'increment_promotion' ||
                            formData.type == 'decrement_demotion'
                        "
                    >
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="
                                    $t(
                                        'increment_promotion.current_designation_id'
                                    )
                                "
                                name="current_designation_id"
                                :help="
                                    rules.current_designation_id
                                        ? rules.current_designation_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.current_designation_id
                                        ? 'error'
                                        : null
                                "
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="
                                            formData.current_designation_id
                                        "
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t(
                                                    'increment_promotion.current_designation_id'
                                                ),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="designation in designations"
                                            :key="designation.xid"
                                            :value="designation.xid"
                                            :title="designation.name"
                                        >
                                            {{ designation.name }}
                                        </a-select-option>
                                    </a-select>
                                    <DesignationAddButton
                                        @onAddSuccess="currentDesignationAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="
                                    $t(
                                        'increment_promotion.promoted_designation_id'
                                    )
                                "
                                name="promoted_designation_id"
                                :help="
                                    rules.promoted_designation_id
                                        ? rules.promoted_designation_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.promoted_designation_id
                                        ? 'error'
                                        : null
                                "
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="
                                            formData.promoted_designation_id
                                        "
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t(
                                                    'increment_promotion.promoted_designation_id'
                                                ),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="designation in designations"
                                            :key="designation.xid"
                                            :value="designation.xid"
                                            :title="designation.name"
                                        >
                                            {{ designation.name }}
                                        </a-select-option>
                                    </a-select>
                                    <DesignationAddButton
                                        @onAddSuccess="designationAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            v-if="
                                formData.type == 'increment' ||
                                formData.type == 'increment_promotion' ||
                                formData.type == 'decrement' ||
                                formData.type == 'decrement_demotion'
                            "
                        >
                            <a-form-item
                                :label="$t('increment_promotion.net_salary')"
                                name="net_salary"
                                :help="
                                    rules.net_salary
                                        ? rules.net_salary.message
                                        : null
                                "
                                :validateStatus="
                                    rules.net_salary ? 'error' : null
                                "
                                class="required"
                            >
                                <a-input-number
                                    v-model:value="formData.net_salary"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t(
                                                'increment_promotion.net_salary'
                                            ),
                                        ])
                                    "
                                    style="width: 100%"
                                    :disabled="addEditType === 'edit'"
                                >
                                    <template #addonBefore>
                                        {{ appSetting.currency.symbol }}
                                    </template></a-input-number
                                >
                            </a-form-item>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('increment_promotion.date')"
                                name="date"
                                :help="rules.date ? rules.date.message : null"
                                :validateStatus="rules.date ? 'error' : null"
                                class="required"
                            >
                                <DateTimePicker
                                    :dateTime="formData.date"
                                    @dateTimeChanged="
                                        (changedDateTime) =>
                                            (formData.date = changedDateTime)
                                    "
                                    :disabled="false"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row
                        v-if="
                            addEditType == 'add' &&
                            (formData.type == 'promotion' ||
                                formData.type == 'increment' ||
                                formData.type == 'increment_promotion' ||
                                formData.type == 'decrement' ||
                                (formData.type == 'decrement_demotion' &&
                                    (permsArray.includes(
                                        'basic_salaries_edit'
                                    ) ||
                                        permsArray.includes(
                                            'designations_edit'
                                        ) ||
                                        permsArray.includes('admin'))))
                        "
                        class="mt-5 mb-20"
                        :gutter="16"
                    >
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <label
                                v-if="
                                    formData.type == 'increment' ||
                                    formData.type == 'increment_promotion' ||
                                    formData.type == 'decrement' ||
                                    (formData.type == 'decrement_demotion' &&
                                        (permsArray.includes(
                                            'basic_salaries_edit'
                                        ) ||
                                            permsArray.includes('admin')))
                                "
                                class="form-check form-check-custom me-5 me-lg-20"
                            >
                                <a-checkbox
                                    v-model:checked="
                                        newData.update_basic_salary
                                    "
                                >
                                    {{
                                        $t(
                                            "increment_promotion.update_basic_salary"
                                        )
                                    }}
                                </a-checkbox>
                            </label>

                            <label
                                v-if="
                                    formData.type == 'promotion' ||
                                    formData.type == 'increment_promotion' ||
                                    (formData.type == 'decrement_demotion' &&
                                        (permsArray.includes(
                                            'designations_edit'
                                        ) ||
                                            permsArray.includes('admin')))
                                "
                                class="form-check form-check-custom me-5 me-lg-20"
                            >
                                <a-checkbox
                                    v-model:checked="newData.update_designation"
                                >
                                    {{
                                        $t(
                                            "increment_promotion.update_designation"
                                        )
                                    }}
                                </a-checkbox>
                            </label>
                        </a-col></a-row
                    >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('increment_promotion.description')"
                                name="description"
                                :help="
                                    rules.description
                                        ? rules.description.message
                                        : null
                                "
                                :validateStatus="
                                    rules.description ? 'error' : null
                                "
                                class="required"
                            >
                                <a-textarea
                                    v-model:value="formData.description"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t(
                                                'increment_promotion.description'
                                            ),
                                        ])
                                    "
                                    :rows="4"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane
                    key="letter_head_details"
                    :tab="`${$t('increment_promotion.letter_head_details')}`"
                    force-render
                >
                    <a-row>
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="
                                    $t(
                                        'increment_promotion.letterhead_template_id'
                                    )
                                "
                                name="letterhead_template_id"
                                :help="
                                    rules.letterhead_template_id
                                        ? rules.letterhead_template_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.letterhead_template_id
                                        ? 'error'
                                        : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="
                                            formData.letterhead_template_id
                                        "
                                        @change="
                                            setDescription(
                                                formData.letterhead_template_id
                                            )
                                        "
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t(
                                                    'increment_promotion.letterhead_template_id'
                                                ),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="template in templates"
                                            :key="template.xid"
                                            :value="template.xid"
                                            :title="template.title"
                                        >
                                            {{ template.title }}
                                        </a-select-option>
                                    </a-select>

                                    <TemplateAddButton
                                        @onAddSuccess="templateAdded"
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
                                name="letterhead_title"
                                :help="
                                    rules.letterhead_title
                                        ? rules.letterhead_title.message
                                        : null
                                "
                                :validateStatus="
                                    rules.letterhead_title ? 'error' : null
                                "
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.letterhead_title"
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
                            descriptionLableTitle="letterhead_description"
                            :letterheadTemplateId="
                                formData.letterhead_template_id
                            "
                            :userId="formData.user_id"
                            :users="users"
                            :appreciation="{}"
                            :rules="rules"
                            :letterheadTemplates="templates"
                            @contentUpdated="
                                (formDescription) =>
                                    (formData.letterhead_description =
                                        formDescription)
                            "
                            :required="
                                formData && formData.letterhead_template_id
                            "
                            :renderListItem="['employee_keys', 'other_keys']"
                        />
                    </div>
                </a-tab-pane>
            </a-tabs>
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
                    {{
                        addEditType == "add"
                            ? $t("common.create")
                            : $t("common.update")
                    }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import {
    defineComponent,
    onMounted,
    ref,
    watch,
    computed,
    nextTick,
} from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import StaffMemberAddButton from "../../staff-members/users/StaffAddButton.vue";
import DesignationAddButton from "../../staff-members/designations/AddButton.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import TemplateAddButton from "../../letter-head/template/AddButton.vue";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { QuillEditor } from "@vueup/vue-quill";
import { find } from "lodash-es";
import hrmManagement from "../../../../common/composable/hrmManagement";
import LetterDescription from "@/common/components/letters/LetterDescription.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default defineComponent({
    props: [
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
        "formData",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        LetterDescription,
        StaffMemberAddButton,
        DesignationAddButton,
        DateTimePicker,
        TemplateAddButton,
        QuillEditor,
        FormItemHeading,
        hrmManagement,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules, addEditActiveTab } =
            apiAdmin("basic_details");

        const {
            appSetting,
            disabledDate,
            permsArray,
            selectedForm,
            user,
            dayjs,
        } = common();
        const users = ref([]);
        const { formatShiftTime12Hours } = hrmManagement();
        const staffMembersUrl = "users";
        const designations = ref([]);
        const templates = ref([]);
        const templatesUrl = "letter-head-templates";
        const designationUrl = "designations";
        const newData = ref({
            update_basic_salary: false,
            update_designation: false,
        });
        const resetSelectformOption = ref([]);
        const textEditor = ref(null);

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const designationPromise = axiosAdmin.get(designationUrl);
            const letterHeadTemplatePromise = axiosAdmin.get(templatesUrl);
            Promise.all([
                staffMemberPromise,
                designationPromise,
                letterHeadTemplatePromise,
            ]).then(
                ([
                    staffMemberResponse,
                    designationResponse,
                    letterHeadTemplateResponse,
                ]) => {
                    users.value = staffMemberResponse.data;
                    designations.value = designationResponse.data;
                    templates.value = letterHeadTemplateResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                id: "add_edit_increment_promotion_form",
                url: props.url,
                data: { ...newData.value, ...props.formData },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const staffMemberAdded = (xid) => {
            axiosAdmin.get(staffMembersUrl).then((response) => {
                users.value = response.data;
                emit("addListSuccess", { type: "user_id", id: xid });
            });
        };

        const designationAdded = (xid) => {
            axiosAdmin.get(designationUrl).then((response) => {
                designations.value = response.data;
                emit("addListSuccess", {
                    type: "promoted_designation_id",
                    id: xid,
                });
            });
        };

        const currentDesignationAdded = (xid) => {
            axiosAdmin.get(designationUrl).then((response) => {
                designations.value = response.data;
                emit("addListSuccess", {
                    type: "current_designation_id",
                    id: xid,
                });
            });
        };
        const templateAdded = (xid) => {
            axiosAdmin.get(templatesUrl).then((response) => {
                templates.value = response.data;
                emit("addListSuccess", {
                    type: "letterhead_template_id",
                    id: xid,
                });
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const setDescription = (id) => {
            // Find the selected template based on its ID
            const selectedTemplate = templates.value.find(
                (template) => template.xid === id
            );
            props.formData.letterhead_title = selectedTemplate.title;
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                await nextTick();
                if (props.visible) {
                    addEditActiveTab.value = "basic_details";
                    if (props.addEditType == "edit") {
                        if (
                            props.data &&
                            props.data.generate &&
                            props.data.generate.description
                        ) {
                            textEditor.value.reSetTemplate();
                            textEditor.value.setHTML(
                                props.data.generate.description
                            );
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

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,
            designationAdded,
            currentDesignationAdded,
            staffMemberAdded,
            users,
            designations,
            newData,
            addEditActiveTab,
            templates,
            templateAdded,
            setDescription,
            resetSelectformOption,
            selectedForm,
            textEditor,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
