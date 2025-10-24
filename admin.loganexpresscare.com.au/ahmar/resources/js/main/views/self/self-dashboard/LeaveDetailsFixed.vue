<template>
    <a-card :title="$t('leave.leave_details')" style="max-width: 100%">
        <a-row :gutter="16">
            <a-col span="12" class="leave-item">
                <span class="leave-heading">
                    {{ $t("leave.total_leaves") }}</span
                >
                <div class="leave-value">{{ leaveDetails.totalLeaves }}</div>
            </a-col>
            <a-col span="12" class="leave-item">
                <span class="leave-heading"> {{ $t("common.approved") }}</span>
                <div class="leave-value">{{ leaveDetails.taken }}</div>
            </a-col>
            <a-col span="12" class="leave-item">
                <span class="leave-heading">{{ $t("common.rejected") }}</span>
                <div class="leave-value">{{ leaveDetails.rejected }}</div>
            </a-col>
            <a-col span="12" class="leave-item">
                <span class="leave-heading"> {{ $t("common.pending") }}</span>
                <div class="leave-value">{{ leaveDetails.request }}</div>
            </a-col>
            <a-col span="12" class="leave-item">
                <span class="leave-heading"> {{ $t("leave.paid") }}</span>
                <div class="leave-value">{{ leaveDetails.paid_leaves }}</div>
            </a-col>
            <a-col span="12" class="leave-item">
                <span class="leave-heading"> {{ $t("leave.unpaid") }}</span>
                <div class="leave-value">{{ leaveDetails.lossOfPay }}</div>
            </a-col>
        </a-row>

        <div style="margin-top: 20px; text-align: center">
            <a-button type="primary" block @click="applyNewLeave">
                {{ $t("leave.apply_new_leave") }}
            </a-button>
        </div>
    </a-card>
    <LeaveAddEdit
        :visible="leaveVisible"
        :formData="formData"
        :url="leaveUrl"
        :data="leaveData"
        @closed="leaveModalClose"
        :addEditType="addEditType"
        @addEditSuccess="leaveModalClose"
        :pageTitle="$t(`menu.leaves`)"
        :successMessage="$t(`leave.created`)"
    />
</template>

<script>
import { ref, defineComponent, watch, reactive } from "vue";
import {
    CalendarOutlined,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import LeaveAddEdit from "../leave/leaves/AddEdit.vue";

export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        ExclamationCircleFilled,
        LeaveAddEdit,
    },
    setup(props) {
        const leaveDetails = reactive({
            totalLeaves: 0,
            taken: 0,
            absent: 0,
            request: 0,
            paid_leaves: 0,
            lossOfPay: 0,
            rejected: 0,
        });
        const leaveVisible = ref(false);
        const leaveUrl = "self/leaves";
        const formData = ref({
            leave_type_id: undefined,
            start_date: undefined,
            end_date: undefined,
            is_half_day: 0,
            reason: "",
            date: undefined,
            status: "pending",
        });
        const leaveData = ref({});
        const addEditType = ref("add");

        const applyNewLeave = () => {
            leaveVisible.value = true;
        };
        const leaveModalClose = () => {
            leaveVisible.value = false;
        };

        watch(
            props,
            (newVal, oldVal) => {
                leaveDetails.totalLeaves =
                    newVal.data?.leaves_summary?.total_leaves;
                leaveDetails.taken = newVal.data?.leaves_summary?.approved;
                leaveDetails.request = newVal.data?.leaves_summary?.pending;
                leaveDetails.lossOfPay =
                    newVal.data?.leaves_summary?.unpaid_leaves;
                leaveDetails.rejected = newVal.data?.leaves_summary?.rejected;
            },
            { immediate: true }
        );

        return {
            leaveDetails,
            applyNewLeave,
            leaveModalClose,
            leaveUrl,
            formData,
            leaveVisible,
            leaveData,
            addEditType,
        };
    },
});
</script>

<style scoped>
.leave-item {
    text-align: left;
}

.leave-item:nth-child(n + 3) {
    margin-top: 2px;
}

.leave-value {
    font-size: 16px;
    color: var(--ant-text-color);
}
.leave-heading {
    font-size: 16px;
    color: var(--ant-text-color);
}
</style>
