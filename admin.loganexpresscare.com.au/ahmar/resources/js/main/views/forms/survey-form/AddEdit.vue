<template>
    <a-drawer
        :open="visible"
        :width="drawerWidth"
        :closable="true"
        :centered="true"
        :title="pageTitle"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="basic" :tab="`${$t('user.basic_info')}`">
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('feedback.title')"
                                name="title"
                                :help="rules.title ? rules.title.message : null"
                                :validateStatus="rules.title ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.title"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('feedback.title'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('feedback.last_date')"
                                name="last_date"
                                :help="rules.last_date ? rules.last_date.message : null"
                                :validateStatus="rules.last_date ? 'error' : null"
                                class="required"
                            >
                                <DateTimePicker
                                    :dateTime="formData.last_date"
                                    :disabled-date="undefined"
                                    @dateTimeChanged="
                                        (changedDateTime) =>
                                            (formData.last_date = changedDateTime)
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="8" :lg="8">
                            <a-form-item
                                :label="$t('feedback.visible_to')"
                                name="visible_to"
                                :help="rules.visible_to ? rules.visible_to.message : null"
                                :validateStatus="rules.visible_to ? 'error' : null"
                            >
                                <a-radio-group
                                    v-model:value="visibleToAll"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('feedback.visible_to'),
                                        ])
                                    "
                                    button-style="solid"
                                    size="small"
                                    @change="() => (selectedUser = [])"
                                >
                                    <a-radio-button :key="1" :value="1">
                                        {{ $t("common.all") }}
                                    </a-radio-button>
                                    <a-radio-button :key="0" :value="0">
                                        {{ $t("common.select_employee") }}
                                    </a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="16"
                            :lg="16"
                            v-if="visibleToAll == 0"
                        >
                            <a-form-item
                                :label="$t('feedback.user_id')"
                                name="user_id"
                                :help="rules.user_id ? rules.user_id.message : null"
                                :validateStatus="rules.user_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="selectedUser"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('feedback.user_id'),
                                            ])
                                        "
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        mode="multiple"
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
                                    <StaffAddButton @onAddSuccess="staffMemberAdded" />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('feedback.description')"
                                name="description"
                                :help="
                                    rules.description ? rules.description.message : null
                                "
                                :validateStatus="rules.description ? 'error' : null"
                            >
                                <a-textarea
                                    v-model:value="formData.description"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('feedback.description'),
                                        ])
                                    "
                                    :rows="4"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="form" :tab="`${$t('feedback.form')}`">
                    <a-row :gutter="16" v-if="addEditType == 'add'">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('feedback.form_id')"
                                name="form_id"
                                :help="rules.form_id ? rules.form_id.message : null"
                                :validateStatus="rules.form_id ? 'error' : null"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.form_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('feedback.form_id'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                        @change="setFormFields"
                                        style="width: 100%"
                                    >
                                        <a-select-option
                                            v-for="form in forms"
                                            :key="form.xid"
                                            :value="form.xid"
                                            :data="form.form_fields"
                                            :title="form.name"
                                        >
                                            {{ form.name }}
                                        </a-select-option>
                                    </a-select>
                                    <FormAddButton @onAddSuccess="formAdded" />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <template v-if="formFields.length > 0">
                        <a-row
                            :gutter="16"
                            v-for="(formField, index) in formFields"
                            :key="formField.id"
                        >
                            <a-col :span="16">
                                <a-form-item :name="['form_fields', index, 'name']">
                                    <a-input
                                        v-model:value="formField.name"
                                        :placeholder="$t('feedback.field_name')"
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="6">
                                <a-form-item :name="['form_fields', index, 'name']">
                                    <a-select
                                        v-model:value="formField.type"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('feedback.field_type'),
                                            ])
                                        "
                                    >
                                        <a-select-option value="input">
                                            {{ $t("common.input") }}
                                        </a-select-option>
                                        <a-select-option value="text_area">
                                            {{ $t("common.text_area") }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                            <a-col :span="2">
                                <MinusCircleOutlined
                                    @click="removeFormField(formField)"
                                />
                            </a-col>
                        </a-row>
                    </template>
                    <a-row
                        :gutter="16"
                        v-if="rules && rules.feedback_form_fields"
                        style="color: red; margin-bottom: 15px"
                    >
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <span>{{
                                rules.feedback_form_fields
                                    ? rules.feedback_form_fields.message
                                    : null
                            }}</span>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item>
                                <a-button
                                    type="dashed"
                                    block
                                    @click="addFormField"
                                    :disabled="addFormButtonStatus"
                                >
                                    <PlusOutlined />
                                    {{ $t("feedback.add_form_field") }}
                                </a-button>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="indicator">
                    <template #tab>
                        <span>
                            {{ $t("feedback.indicator_field") }}
                        </span>
                    </template>

                    <a-row :gutter="16" v-if="addEditType == 'add'">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('feedback.indicator_fields')"
                                name="indicator_fields"
                                :help="
                                    rules.indicator_fields
                                        ? rules.indicator_fields.message
                                        : null
                                "
                                :validateStatus="rules.indicator_fields ? 'error' : null"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="selectedIndicatorIds"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('feedback.indicator_fields'),
                                            ])
                                        "
                                        :allowClear="true"
                                        style="width: 100%"
                                        mode="multiple"
                                        @change="setIndicatorFields"
                                    >
                                        <a-select-option
                                            v-for="indicator in indicators"
                                            :key="indicator.xid"
                                            :value="indicator.xid"
                                            :data="indicator.fields"
                                        >
                                            {{ indicator.name }}
                                        </a-select-option>
                                    </a-select>
                                    <IndicatorAddButton @onAddSuccess="indicatorAdded" />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <template v-if="fields.length > 0">
                        <div v-for="field in fields" :key="field.id">
                            <a-row :gutter="16">
                                <a-col :span="24">
                                    <a-typography-title :level="5">
                                        {{ field.name }}
                                    </a-typography-title>
                                </a-col>
                            </a-row>
                            <a-row
                                :gutter="16"
                                v-for="(indicator, index) in field.fields"
                                :key="indicator.id"
                            >
                                <a-col :xs="24" :sm="24" :md="22" :lg="22">
                                    <a-form-item :name="['fields', index, 'name']">
                                        <a-input
                                            v-model:value="indicator.name"
                                            :placeholder="$t('feedback.indicator_fields')"
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col :span="2">
                                    <MinusCircleOutlined
                                        @click="removeIndicatorField(indicator)"
                                    />
                                </a-col>
                            </a-row>
                            <hr class="mb-10" />
                        </div>
                    </template>
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
import { defineComponent, onMounted, ref, watch, computed } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusCircleOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import FormAddButton from "../template/AddButton.vue";
import IndicatorAddButton from "../indicators/AddButton.vue";
import StaffAddButton from "../../staff-members/users/StaffAddButton.vue";
import common from "../../../../common/composable/common";
import { forEach, some, filter, includes, pull, remove } from "lodash-es";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import { find } from "lodash-es";

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
        MinusCircleOutlined,
        Upload,
        FormAddButton,
        StaffAddButton,
        FormItemHeading,
        DateTimePicker,
        IndicatorAddButton,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting } = common();
        const activeKey = ref("basic");
        const users = ref([]);
        const userUrl = "users";
        const forms = ref([]);
        const formUrl = "forms?limits=10000";
        const indicators = ref([]);
        const indicatorUrl = "indicators";
        const visibleToAll = ref(1);
        const selectedUser = ref([]);
        const formFields = ref([]);
        const fields = ref([]);
        const selectedIndicatorIds = ref([]);

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            const formPromise = axiosAdmin.get(formUrl);
            const indicatorPromise = axiosAdmin.get(indicatorUrl);
            Promise.all([userPromise, formPromise, indicatorPromise]).then(
                ([userResponse, formResponse, indicatorResponse]) => {
                    users.value = userResponse.data;
                    forms.value = formResponse.data;
                    indicators.value = indicatorResponse.data;
                }
            );
        });

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }
        };

        const addFormField = () => {
            formFields.value.push({
                name: "",
                type: "input",
                required: 0,
                id: Math.random().toString(36).slice(2),
            });
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return some(formFields.value, { name: "" });
            }
        });

        const setFormFields = (value, option) => {
            if (option.data.length > 0) {
                formFields.value = [];
                forEach(option.data, (field) => {
                    formFields.value.push({
                        name: field.name,
                        type: field.type,
                        required: field.required,
                        id: field.id,
                    });
                });
            }
        };

        const removeIndicatorField = (item) => {
            let index = fields.value.indexOf(item);
            if (index !== -1) {
                fields.value.splice(index, 1);
            } else {
                fields.value.splice(index, 1);
            }
        };

        const setIndicatorFields = (value, option) => {
            fields.value = [];
            if (option.length > 0) {
                forEach(option, (indicator) => {
                    forEach(indicators.value, (indi) => {
                        if (indi.xid == indicator.value) {
                            fields.value.push({
                                id: indicator.value,
                                name: indi.name,

                                fields: indicator.data,
                            });
                        }
                    });
                });
            }
        };

        const onSubmit = () => {
            const allFormFields = filter(formFields.value, (newFormField) => {
                return newFormField.name != "";
            });
            const indicatorFields = filter(fields.value, (newFormField) => {
                return newFormField.id != "";
            });

            var newFormData = {
                ...props.formData,
                user_id: selectedUser.value,
                visible_to: visibleToAll.value,
                feedback_form_fields: allFormFields,
                indicator_fields: indicatorFields,
            };
            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    formFields.value = [];
                    fields.value = [];
                    selectedUser.value = [];
                    visibleToAll.value = 1;
                    selectedIndicatorIds.value = [];
                    emit("addEditSuccess", res.xid);
                },
                error: (err) => {
                    const expenseDetailsTabRequiredFields = [
                        "title",
                        "last_date",
                        "user_id",
                    ];
                    const errorObjectKeyArray = Object.keys(rules.value);

                    if (
                        some(errorObjectKeyArray, (value) =>
                            includes(expenseDetailsTabRequiredFields, value)
                        )
                    ) {
                        activeKey.value = "basic";
                    } else {
                        activeKey.value = "form";
                    }
                },
            });
        };

        const formAdded = (xid) => {
            axiosAdmin.get(formUrl).then((formResponse) => {
                forms.value = formResponse.data;
                emit("addListSuccess", { type: "form_id", id: xid });
            });
        };

        const indicatorAdded = (xid) => {
            axiosAdmin.get(indicatorUrl).then((indicatorResponse) => {
                indicators.value = indicatorResponse.data;
                var newCreated = find(indicators.value, { xid: xid });
                var option = [
                    {
                        key: newCreated.xid,
                        value: newCreated.xid,
                        data: newCreated.fields,
                        disabled: false,
                    },
                ];
                selectedIndicatorIds.value.push(xid);
                setIndicatorFields("", option);
            });
        };

        const staffMemberAdded = (xid) => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
                selectedUser.value.push(xid);
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,

            (newVal, oldVal) => {
                activeKey.value = "basic";
                if (props.addEditType == "add") {
                    formFields.value = [];
                    selectedUser.value = [];
                    visibleToAll.value = 1;
                    fields.value = [];
                    selectedIndicatorIds.value = [];
                } else {
                    formFields.value = props.data.feedback_form_fields;
                    fields.value = props.data.indicator_fields;
                    visibleToAll.value = props.data.visible_to;
                    if (props.data.feedback_user.length > 0) {
                        selectedUser.value = [];
                        forEach(props.data.feedback_user, (employee) => {
                            selectedUser.value.push(employee.user.xid);
                        });
                    }
                }
            }
        );

        return {
            appSetting,
            loading,
            rules,
            activeKey,
            onClose,
            onSubmit,
            users,
            visibleToAll,
            selectedUser,
            forms,
            formAdded,
            staffMemberAdded,
            addFormButtonStatus,
            removeFormField,
            addFormField,
            setFormFields,
            formFields,
            indicators,
            indicatorUrl,
            fields,
            removeIndicatorField,
            setIndicatorFields,
            indicatorAdded,
            selectedIndicatorIds,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
../template/AddButton.vue
