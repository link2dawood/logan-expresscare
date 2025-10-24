<template>
    <a-modal
        :width="drawerWidth"
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row
                :gutter="16"
                v-for="(formField, index) in formFields"
                :key="formField.id"
                style="display: flex; align-items: center"
            >
                <a-col :xs="24" :sm="24" :md="7" :lg="7">
                    <a-form-item
                        :label="$t('user.name')"
                        :name="['form_fields', index, 'name']"
                        :help="
                            onlyValidateErrors[index] && onlyValidateErrors[index].name
                                ? onlyValidateErrors[index].name.message
                                : null
                        "
                        :validateStatus="
                            onlyValidateErrors[index] && onlyValidateErrors[index].name
                                ? 'error'
                                : null
                        "
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.name')])
                            "
                        />
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="7" :lg="7">
                    <a-form-item
                        :label="$t('user.email')"
                        :name="['form_fields', index, 'email']"
                        :help="
                            onlyValidateErrors[index] && onlyValidateErrors[index].email
                                ? onlyValidateErrors[index].email.message
                                : null
                        "
                        :validateStatus="
                            onlyValidateErrors[index] && onlyValidateErrors[index].email
                                ? 'error'
                                : null
                        "
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.email"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.email')])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="7" :lg="7">
                    <a-form-item
                        :label="$t('user.password')"
                        :name="['form_fields', index, 'password']"
                        :help="
                            onlyValidateErrors[index] &&
                            onlyValidateErrors[index].password
                                ? onlyValidateErrors[index].password.message
                                : null
                        "
                        :validateStatus="
                            onlyValidateErrors[index] &&
                            onlyValidateErrors[index].password
                                ? 'error'
                                : null
                        "
                        class="required"
                    >
                        <a-input-password
                            v-model:value="formField.password"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.password'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :span="1" style="margin-top: 6px">
                    <MinusSquareOutlined @click="removeFormField(formField)" />
                </a-col>
            </a-row>
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-form-item>
                    <a-button
                        type="dashed"
                        block
                        @click="addFormField"
                        :disabled="addFormButtonStatus"
                    >
                        <PlusOutlined />

                        {{ $t("user.add_field") }}
                    </a-button>
                </a-form-item>
            </a-col>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, watch, onMounted, computed } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { forEach, some } from "lodash-es";
import common from "../../../../common/composable/common";

export default defineComponent({
    props: ["data", "visible", "url", "addEditType", "pageTitle", "successMessage"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        MinusSquareOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const remvoedUsers = ref([]);
        const { appSetting, dayjs } = common();
        const formData = ref({});
        const formFields = ref([{ name: "", email: "", password: "" }]);
        const employeeId = ref("");
        const joiningDate = ref("");
        const onlyValidateErrors = ref({});

        onMounted(() => {
            employeeNumber();
        });

        const addFormField = () => {
            formFields.value.push({
                name: "",
                email: "",
                password: "",
            });
        };

        const employeeNumber = () => {
            employeeId.value =
                appSetting.value.employee_id_prefix +
                "-" +
                appSetting.value.employee_id_start;
        };

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                remvoedUsers.value.push(item.id);
            }
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { name: "" }) ||
                    some(formFields.value, { email: "" }) ||
                    some(formFields.value, { password: "" })
                );
            }
        });

        const onSubmit = () => {
            onlyValidateErrors.value = {};

            forEach(formFields.value, (formField) => {
                let index = formFields.value.indexOf(formField);
                formData.value = formField;
                addEditRequestAdmin({
                    url: props.url,
                    data: {
                        ...formData.value,
                        status: "active",
                        gender: "female",
                        joining_date: joiningDate.value,
                        employee_number: employeeId.value,
                        user_type: "staff_members",
                        allow_login: 1,
                    },
                    successMessage: props.successMessage,
                    success: (res) => {
                        emit("addEditSuccess", res.xid);
                        onlyValidateErrors.value = {};
                    },
                    error: (err) => {
                        var errorRules = {};
                        var keys = Object.keys(err.error.details);
                        for (var i = 0; i < keys.length; i++) {
                            // Escape dot that comes with error in array fields
                            var key = keys[i].replace(".", "\\.");

                            errorRules[key] = {
                                required: true,
                                message: err.error.details[keys[i]][0],
                            };
                        }
                        onlyValidateErrors.value[index] = errorRules;
                    },
                });
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                onlyValidateErrors.value = {};
                joiningDate.value = dayjs().format("YYYY-MM-DD");
                employeeNumber();
                formFields.value = [{ name: "", email: "", password: "" }];
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            addFormField,
            removeFormField,
            formFields,
            addFormButtonStatus,
            onlyValidateErrors,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
