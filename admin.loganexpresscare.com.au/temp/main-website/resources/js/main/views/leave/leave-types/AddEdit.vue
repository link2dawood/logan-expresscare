<template>
    <a-drawer
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
        :width="drawerWidth"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="18" :sm="18" :md="18" :lg="18">
                    <a-form-item
                        :label="$t('leave_type.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="6" :sm="6" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('leave_type.is_paid')"
                        name="is_paid"
                        :help="rules.is_paid ? rules.is_paid.message : null"
                        :validateStatus="rules.is_paid ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_paid"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="6" :sm="6" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave_type.count_type')"
                        name="count_type"
                        :help="
                            rules.count_type ? rules.count_type.message : null
                        "
                        :validateStatus="rules.count_type ? 'error' : null"
                    >
                        <a-select
                            v-model:value="formData.count_type"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.count_type'),
                                ])
                            "
                            style="width: 100%"
                        >
                            <a-select-option value="same_for_all">
                                {{ $t("leave_type.same_for_all") }}
                            </a-select-option>
                            <a-select-option value="employee_specific">
                                {{ $t("leave_type.employee_specific") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :xs="6" :sm="6" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave_type.total_leaves')"
                        name="total_leaves"
                        :help="
                            rules.total_leaves
                                ? rules.total_leaves.message
                                : null
                        "
                        :validateStatus="rules.total_leaves ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="formData.total_leaves"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.total_leaves'),
                                ])
                            "
                            style="width: 100%"
                            :disabled="
                                formData.count_type === 'employee_specific'
                            "
                            v-if="
                                formData.count_type === 'same_for_all' ||
                                addEditType === 'add'
                            "
                        />
                        <a-typography-link
                            v-if="
                                formData.count_type === 'employee_specific' &&
                                addEditType === 'edit'
                            "
                            @click="modalOpened"
                            >{{
                                $t("leave_type.edit_count")
                            }}</a-typography-link
                        >
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col
                    :xs="6"
                    :sm="6"
                    :md="24"
                    :lg="24"
                    v-if="formData.is_paid == 1"
                >
                    <a-form-item
                        :label="$t('leave_type.max_leaves_per_month')"
                        name="max_leaves_per_month"
                        :help="
                            rules.max_leaves_per_month
                                ? rules.max_leaves_per_month.message
                                : null
                        "
                        :validateStatus="
                            rules.max_leaves_per_month ? 'error' : null
                        "
                    >
                        <a-input-number
                            v-model:value="formData.max_leaves_per_month"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.max_leaves_per_month'),
                                ])
                            "
                            style="width: 100%"
                        />
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
    <EmployeeSpecificLeaveCount
        :visible="modalOpen"
        :title="$t('leave_type.employee_specific_leave_count')"
        @close="modalClosed"
        :data="modalData"
        :addEditType="addEditType"
    />
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import EmployeeSpecificLeaveCount from "./EmployeeSpecificLeaveCount.vue";

export default defineComponent({
    emits: ["addEditSuccess", "closed"],
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
        EmployeeSpecificLeaveCount,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray } = common();
        const modalOpen = ref(false);
        const modalData = ref({});

        const modalOpened = () => {
            modalOpen.value = true;
            modalData.value = props.data;
        };
        const modalClosed = () => {
            modalOpen.value = false;
            modalData.value = {};
        };

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

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,
            modalOpen,
            modalData,
            modalOpened,
            modalClosed,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
