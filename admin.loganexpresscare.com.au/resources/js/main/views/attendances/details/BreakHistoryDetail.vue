<template>
    <a-drawer
        :title="$t('attendance.break_history')"
        :width="drawerWidth"
        :visible="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="true"
        @close="onClosed"
    >
        <div v-if="data" class="space-y-6">
            <!-- Work and Break Summary -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-lg bg-blue-50 p-4">
                    <h4 class="mb-3 text-lg font-semibold text-blue-700">
                        {{ $t("attendance.work_summary") }}
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"
                                >{{ $t("attendance.total_work_hours") }} :</span
                            >
                            <span class="font-medium text-gray-800">{{
                                data.attendance
                                    ? formatMinutes(data.attendance.shift_duration)
                                    : 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"
                                >{{ $t("attendance.effective_work_time") }} :</span
                            >
                            <span class="font-medium text-gray-800">{{
                                data.attendance
                                    ? formatMinutes(data.attendance.duration)
                                    : 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"
                                >{{ $t("attendance.productivity_rate") }} :</span
                            >
                            <span class="font-medium text-green-600">{{
                                data ? data.productivity + " %" : 0
                            }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-orange-50 p-4">
                    <h4 class="mb-3 text-lg font-semibold text-orange-700">
                        {{ $t("attendance.break_summary") }}
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"
                                >{{ $t("attendance.total_break_time") }} :</span
                            >
                            <span class="font-medium text-gray-800">{{
                                data ? formatMinutes(data.total_break_time) : 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"
                                >{{ $t("attendance.total_breaks") }} :</span
                            >
                            <span class="font-medium text-gray-800">{{
                                data ? data.breaks : 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"
                                >{{ $t("attendance.avg_break_duration") }} :</span
                            >
                            <span class="font-medium text-gray-800">{{
                                data ? formatMinutes(data.avg_break) : 0
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="mb-3 text-lg font-semibold text-gray-800">
                    {{ $t("attendance.break_details") }}
                </h4>
                <div class="overflow-x-auto">
                    <a-table
                        v-if="data && data.attendance"
                        :dataSource="data.attendance.work_duration"
                        :pagination="false"
                        :row-key="(record) => record.xid"
                        :columns="columns"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'start_time'">
                                {{ formatOnlyTime(record.start_time) }}
                            </template>
                            <template v-if="column.dataIndex == 'end_time'">
                                {{ formatOnlyTime(record.end_time) }}
                            </template>
                            <template v-if="column.dataIndex == 'duration'">
                                {{ formatMinutes(record.duration) }}
                            </template>
                            <template v-if="column.dataIndex == 'status'">
                                <a-tag :color="getBreakTypeColor(record.status)">{{
                                    $t(`attendance.${record.status}`)
                                }}</a-tag>
                            </template>
                            <template v-if="column.dataIndex == 'notes'">
                                <a-typography-text underline>{{
                                    record.notes
                                }}</a-typography-text>
                            </template>
                        </template>
                    </a-table>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a-button class="!rounded-button whitespace-nowrap" @click="onClosed()">
                    {{ $t("common.close") }}
                </a-button>
            </div>
        </div>
    </a-drawer>
</template>
<script>
import { onMounted, ref, reactive } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../common/composable/hrmManagement";
import common from "../../../../common/composable/common";
import AttendanceStatus from "../attendance/AttendanceStatus.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default {
    props: ["data", "visible", "pageTitle"],
    emits: ["closed"],
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,

        AttendanceStatus,
        UserInfo,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { getMonthNameByNumber, formatMinutes, monthArrays } = hrmManagement();
        const { dayjs, formatOnlyTime, formatDate, user, permsArray } = common();

        const breakStats = reactive({
            totalWorkHours: "8 hrs 15 min",
            effectiveWorkTime: "7 hrs 0 min",
            productivityRate: "85%",
            totalBreakTime: "1 hr 25 min",
            breakCount: "4",
            avgBreakDuration: "21 min",
            teamAvgWorkHours: "8 hrs 0 min",
            teamAvgBreakTime: "1 hr 0 min",
            teamAvgProductivity: "82%",
        });

        const columns = ref([
            {
                title: t("attendance.start_time"),
                dataIndex: "start_time",
            },
            {
                title: t("attendance.end_time"),
                dataIndex: "end_time",
            },
            {
                title: t("attendance.status"),
                dataIndex: "status",
            },
            {
                title: t("attendance.duration"),
                dataIndex: "duration",
            },
            {
                title: t("attendance.notes"),
                dataIndex: "notes",
            },
        ]);

        onMounted(() => {});

        const onClosed = () => {
            emit("closed");
        };

        const getBreakTypeColor = (type) => {
            const typeColor = {
                started: "blue",
                paused: "orange",
            };
            return typeColor[type] || "default";
        };

        return {
            breakStats,
            getBreakTypeColor,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            columns,
            onClosed,
            formatMinutes,
            formatOnlyTime,
        };
    },
};
</script>
