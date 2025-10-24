<template>
    <a-drawer
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
        :width="drawerWidth"
    >
        <BasicSalary
            ref="basicSalaryRef"
            :inputVisible="true"
            :user="newUser"
            :visible="salaryVisible"
            @updateSalaryData="updateSalaryData"
        />

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
import { defineComponent, ref, watch, nextTick } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import BasicSalary from "./BasicSalary.vue";
import { useI18n } from "vue-i18n";
import { notification } from "ant-design-vue";

export default defineComponent({
    props: ["visible", "user", "addEditType", "pageTitle", "successMessage"],
    components: {
        SaveOutlined,
        BasicSalary,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const newData = ref({});
        const basicSalaryRef = ref(null);
        const salaryVisible = ref(false);
        const newUser = ref({});
        const { t } = useI18n();

        // Form submission
        const onSubmit = () => {
            if (Number(newData.value.special_allowances) < 0) {
                notification.error({
                    message: t("common.error"),
                    description: t("payroll.special_allowances_calculation"),
                });
                return;
            }
            addEditRequestAdmin({
                url: "update-salary",
                data: newData.value,
                successMessage: props.successMessage,
                success: () => emit("modelClose"),
            });
        };

        const updateSalaryData = (updatedData) => {
            Object.assign(newData.value, updatedData);
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                await nextTick();
                newUser.value = {};
                if (props.visible) {
                    if (basicSalaryRef.value) {
                        salaryVisible.value = true;
                        newUser.value = props.user;
                    }
                } else {
                    salaryVisible.value = false;
                }
            }
        );

        return {
            loading,
            rules,
            basicSalaryRef,
            salaryVisible,
            newUser,
            onClose,
            onSubmit,
            updateSalaryData,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>
