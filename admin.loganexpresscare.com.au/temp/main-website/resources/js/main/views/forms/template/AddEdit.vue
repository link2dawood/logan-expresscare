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
                        :label="$t('form.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="newFormData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('form.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('form.status')"
                        name="status"
                        :help="rules.status ? rules.status.message : null"
                        :validateStatus="rules.status ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="newFormData.status"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.active") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.inactive") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>

            <FormItemHeading>
                {{ $t("form.form_fields") }}
            </FormItemHeading>

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
                                :placeholder="$t('form.field_name')"
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="6">
                        <a-form-item :name="['form_fields', index, 'name']">
                            <a-select
                                v-model:value="formField.type"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('form.field_type'),
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
                            {{ $t("form.add_form_field") }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>
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
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import {
    PlusOutlined,
    MinusCircleOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import { some, filter } from "lodash-es";
import apiAdmin from "../../../../common/composable/apiAdmin";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";

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
        MinusCircleOutlined,
        LoadingOutlined,
        SaveOutlined,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const newFormData = ref({
            name: "",
            form_fields: [],
            status: 1,
        });
        const formFields = ref([]);
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

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

        const onSubmit = () => {
            const allFormFields = filter(formFields.value, (newFormField) => {
                return newFormField.name != "";
            });
            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...newFormData.value,
                    form_fields: allFormFields,
                },
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

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return some(formFields.value, { name: "" });
            }
        });

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.addEditType == "edit" && newVal) {
                    newFormData.value = {
                        name: props.data.name,
                        status: props.data.status,
                        _method: "PUT",
                    };
                    formFields.value = props.data.form_fields;
                } else {
                    newFormData.value = {
                        name: "",
                        status: 1,
                    };
                    formFields.value = [];
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            newFormData,
            formFields,

            removeFormField,
            addFormField,
            addFormButtonStatus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
