<template>
    <a-switch
        :checked="status == 1 ? true : false"
        size="small"
        @click="changeStatus"
        :loading="loading"
    />
</template>

<script>
import { defineComponent } from "vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["xid", "status"],
    emits: ["success"],
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading } = apiAdmin();
        const { t } = useI18n();

        const changeStatus = (checked) => {
            const formData = {
                xid: props.xid,
                status: checked ? 1 : 0,
            };

            addEditRequestAdmin({
                url: "update-visible-to-employee",
                data: formData,
                successMessage: t(
                    "employee_custom_field.visible_to_employee_status"
                ),
                success: (res) => {
                    emit("success");
                },
            });
        };

        return {
            changeStatus,
            loading,
        };
    },
});
</script>

<style></style>
