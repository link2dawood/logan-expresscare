<template>
    <a-drawer
        :open="visible"
        :width="drawerWidth"
        :closable="true"
        :centered="true"
        :footer-style="{ textAlign: 'right' }"
        :title="pageTitle"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('employee_custom_field.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('employee_custom_field.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('employee_custom_field.visible_to_employee')"
                        name="visible_to_employee"
                        :help="
                            rules.visible_to_employee
                                ? rules.visible_to_employee.message
                                : null
                        "
                        :validateStatus="
                            rules.visible_to_employee ? 'error' : null
                        "
                    >
                        <a-switch
                            v-model:checked="formData.visible_to_employee"
                            :checkedValue="1"
                            :unCheckedValue="0"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <FormItemHeading>
                {{ $t("employee_custom_field.employee_fields") }}
            </FormItemHeading>

            <template v-if="formFields.length > 0">
                <a-row
                    v-for="(formField, index) in formFields"
                    :key="`form_fields_${index}`"
                    :gutter="16"
                >
                    <a-col :span="7">
                        <a-form-item>
                            <a-input
                                v-model:value="formField.name"
                                :placeholder="$t('employee_custom_field.name')"
                            />
                        </a-form-item>
                    </a-col>

                    <a-col :span="formField.type !== 'select' ? 15 : 7">
                        <a-form-item>
                            <a-select
                                v-model:value="formField.type"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('employee_custom_field.type'),
                                    ])
                                "
                                @change="changeType(formField)"
                            >
                                <a-select-option value="text">
                                    {{ $t("employee_custom_field.text") }}
                                </a-select-option>
                                <a-select-option value="large_text">
                                    {{ $t("employee_custom_field.large_text") }}
                                </a-select-option>
                                <a-select-option value="date">
                                    {{ $t("employee_custom_field.date") }}
                                </a-select-option>
                                <a-select-option value="select">
                                    {{ $t("employee_custom_field.select") }}
                                </a-select-option>
                                <a-select-option value="file">
                                    {{ $t("employee_custom_field.file") }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>

                    <a-col :span="8" v-if="formField.type === 'select'">
                        <a-form-item>
                            <a-input
                                v-model:value="formField.default_value"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('employee_custom_field.text'),
                                    ])
                                "
                            />
                        </a-form-item>
                    </a-col>

                    <a-col :span="2" class="mt-5">
                        <MinusCircleOutlined
                            @click="removeFormField(formField)"
                        />
                    </a-col>
                    <!-- <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item :label="$t('employee_custom_field.is_required')">
                            <a-switch
                                v-model:checked="formField.is_required"
                                :checkedValue="1"
                                :unCheckedValue="0"
                            />
                        </a-form-item>
                    </a-col> -->

                    <!-- <a-divider v-if="index !== formFields.length - 1" /> -->
                </a-row>
            </template>

            <a-row :gutter="16" v-if="formFields.type !== null">
                <a-col :xs="24">
                    <a-form-item>
                        <a-button
                            type="dashed"
                            block
                            @click="addFormField"
                            :disabled="addFormButtonStatus"
                        >
                            <PlusOutlined />
                            {{
                                $t("employee_custom_field.add_employee_fields")
                            }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>

        <template #footer>
            <a-space
                ><a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon><SaveOutlined /></template>
                    {{
                        addEditType === "add"
                            ? $t("common.create")
                            : $t("common.update")
                    }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button></a-space
            >
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusCircleOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { some, filter } from "lodash-es";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import { cloneDeep, orderBy } from "lodash-es";

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
        FormItemHeading,
        DateRangePicker,
        UploadFile,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const formFields = ref([]);

        const addFormField = () => {
            formFields.value.push({
                name: "",
                type: "text",
                is_required: 0,
                default_value: "",
            });
        };

        const removed = ref([]);

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { name: "" }) ||
                    some(formFields.value, { name: null }) ||
                    some(formFields.value, { type: "" }) ||
                    some(formFields.value, { type: null })
                    // some(formFields.value, { default_value: "" }) ||
                    // some(formFields.value, { default_value: null })
                );
            }
        });

        const onSubmit = () => {
            const allFormFields = filter(
                formFields.value,
                (field) =>
                    field.name &&
                    field.type &&
                    (field.type !== "" || field.default_value !== "")
            );

            const newFormData = {
                ...props.formData,
                all_form_fields: allFormFields,
                removed_fields: removed.value,
            };

            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const changeType = (record) => {
            if (record.type != "select") {
                record.default_value = "";
            }
            return record;
        };

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.xid && !removed.value.includes(item.xid)) {
                removed.value.push(item.xid);
            }
        };

        const onClose = () => {
            rules.value = {};
            formFields.value = [];
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal && props.addEditType === "edit") {
                    formFields.value = cloneDeep(props.formData.employee_field);
                } else {
                    formFields.value = [];
                }
            },
            { immediate: true }
        );

        return {
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            loading,
            rules,
            onClose,
            onSubmit,
            addFormField,
            addFormButtonStatus,
            removeFormField,
            formFields,
            changeType,
        };
    },
});
</script>
