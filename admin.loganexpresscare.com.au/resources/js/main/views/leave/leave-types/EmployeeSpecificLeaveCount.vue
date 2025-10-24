<template>
    <a-modal
        :open="visible"
        :width="drawerWidth"
        :closable="false"
        :centered="true"
        :footer-style="{ textAlign: 'right' }"
        :title="pageTitle"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <template v-if="formFields && formFields.length > 0">
                <a-row
                    v-for="(formField, index) in formFields"
                    :key="`form_fields_${index}`"
                    :gutter="16"
                >
                    <a-col :xs="6" :sm="6" :md="11" :lg="11">
                        <a-form-item
                            :validateStatus="
                                inputErrors[index] && inputErrors[index].user_id
                                    ? 'error'
                                    : ''
                            "
                            :help="
                                inputErrors[index] && inputErrors[index].user_id
                                    ? $t('leave_type.user_required')
                                    : ''
                            "
                        >
                            <a-select
                                v-model:value="formField.user_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave_type.user_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                                :filterOption="
                                    (input, option) =>
                                        option.title
                                            .toLowerCase()
                                            .includes(input.toLowerCase())
                                "
                            >
                                <a-select-option
                                    v-for="allStaffMember in allStaffMembers"
                                    :key="allStaffMember.xid"
                                    :value="allStaffMember.xid"
                                    :title="allStaffMember.name"
                                    :disabled="
                                        formFields.some(
                                            (f) =>
                                                f.user_id === allStaffMember.xid
                                        ) &&
                                        formField.user_id !== allStaffMember.xid
                                    "
                                >
                                    <user-list-display
                                        :user="allStaffMember"
                                        whereToShow="select"
                                    />
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>

                    <a-col :xs="6" :sm="6" :md="11" :lg="11">
                        <a-form-item
                            :validateStatus="
                                inputErrors[index] &&
                                inputErrors[index].total_leaves
                                    ? 'error'
                                    : ''
                            "
                            :help="
                                inputErrors[index] &&
                                inputErrors[index].total_leaves
                                    ? $t('leave_type.counts_required')
                                    : ''
                            "
                        >
                            <a-input
                                v-model:value="formField.total_leaves"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('leave_type.total_count'),
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
                </a-row>
            </template>

            <a-row :gutter="16">
                <a-col :xs="24">
                    <a-form-item>
                        <a-button
                            type="dashed"
                            block
                            @click="addFormField"
                            :disabled="addFormButtonStatus"
                        >
                            <PlusOutlined />
                            {{ $t("leave_type.add_employees") }}
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
                    {{ $t("common.save") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button></a-space
            >
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, computed, watch, onMounted } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusCircleOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { some, cloneDeep } from "lodash-es";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

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
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const allStaffMembers = ref([]);
        const staffMembersUrl = "users?limit=10000";

        const formFields = ref([
            {
                user_id: undefined,
                total_leaves: 0,
            },
        ]);
        const removed = ref([]);
        const leaveTypeId = ref(props.data ? props.data.xid : null);
        const inputErrors = ref([]);

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allStaffMembers.value = staffMemberResponse.data;
                setLeaveTypeIdInFormFields();
            });
        });

        const validateFormFields = () => {
            inputErrors.value = formFields.value.map((field) => {
                return {
                    user_id: !field.user_id,
                    total_leaves:
                        !field.total_leaves || field.total_leaves <= 0,
                };
            });
            return !inputErrors.value.some(
                (err) => err.user_id || err.total_leaves
            );
        };

        const onSubmit = () => {
            if (!validateFormFields()) {
                return;
            }

            const newFormData = {
                all_form_fields: formFields.value,
                removed_fields: removed.value,
            };

            axiosAdmin
                .post("employee-specific-leave", newFormData)
                .then((response) => {
                    emit("close");
                });
        };

        const addFormField = () => {
            formFields.value.push({
                user_id: undefined,
                total_leaves: 0,
                leave_type_id: leaveTypeId.value,
            });
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { user_id: "" }) ||
                    some(formFields.value, { user_id: null }) ||
                    some(formFields.value, { total_leaves: "" }) ||
                    some(formFields.value, { total_leaves: null })
                );
            }
        });

        const setLeaveTypeIdInFormFields = () => {
            formFields.value.forEach((field) => {
                field.leave_type_id = leaveTypeId.value;
            });
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
            emit("close");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal && props.addEditType === "edit") {
                    let dataArray = [];
                    if (
                        props.data &&
                        Array.isArray(
                            props.data.employee_specific_leave_count
                        ) &&
                        props.data.employee_specific_leave_count.length > 0
                    ) {
                        dataArray =
                            props.data.employee_specific_leave_count.map(
                                (item) => ({
                                    user_id:
                                        item.x_user_id || item.xid || undefined,
                                    total_leaves: item.total_leaves,
                                    leave_type_id:
                                        item.x_leave_type_id ||
                                        (props.data && props.data.xid
                                            ? props.data.xid
                                            : null),
                                    xid: item.xid,
                                })
                            );
                    } else {
                        dataArray = [
                            {
                                user_id: undefined,
                                total_leaves: 0,
                            },
                        ];
                    }
                    formFields.value = cloneDeep(dataArray);
                    leaveTypeId.value =
                        props.data && props.data.xid ? props.data.xid : null;
                    setLeaveTypeIdInFormFields();
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
            allStaffMembers,
            inputErrors,
        };
    },
});
</script>
