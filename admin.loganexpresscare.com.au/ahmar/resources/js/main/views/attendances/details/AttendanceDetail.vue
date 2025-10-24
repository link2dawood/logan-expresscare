<template>
    <template v-if="$slots.actions">
        <slot name="actions" :filterData="filterData" :monthArrays="monthArrays"> </slot>
    </template>

    <admin-page-table-content :isPageTableContent="isPageTableContent">
        <template v-if="$slots.card">
            <slot
                name="card"
                :totalPresentDays="totalPresentDays"
                :workingDays="workingDays"
                :totalOfficeTime="totalOfficeTime"
                :formatMinutes="formatMinutes"
                :clockInDuration="clockInDuration"
                :clockInDurationPercentage="clockInDurationPercentage"
                :totalLateDaysPercentage="totalLateDaysPercentage"
                :totalLateDays="totalLateDays"
                :halfDayCount="halfDayCount"
            ></slot>
        </template>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="false"
                        :loading="table.loading"
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                            <template v-if="column.dataIndex == 'status'">
                                <AttendanceStatus :status="record.status" />
                            </template>
                            <template v-if="column.dataIndex == 'clock_in'">
                                {{ record.clock_in ? formatTime(record.clock_in) : "-" }}
                                <span
                                    v-if="record.is_late && record.late_time > 0"
                                    :style="{ color: 'red' }"
                                >
                                    ({{ formatMinutes(record.late_time) }}
                                    {{ $t("attendance.late") }})
                                </span>
                            </template>
                            <template v-if="column.dataIndex == 'clock_out'">
                                {{
                                    record.clock_out ? formatTime(record.clock_out) : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex == 'clocked_time'">
                                <span
                                    style="width: 100%"
                                    v-if="
                                        record.office_time > 0 && record.worked_time > 0
                                    "
                                >
                                    <a-tooltip>
                                        <template #title>
                                            <span v-if="record.late_time > 0">
                                                {{ $t("attendance.late") }} :
                                                {{ formatMinutes(record.late_time) }}
                                                <br />
                                            </span>
                                            {{ $t("attendance.clocked_time") }}
                                            :
                                            {{ formatMinutes(record.worked_time) }}
                                        </template>
                                        <a-progress
                                            strokeColor="#52c41a"
                                            size="small"
                                            :showInfo="false"
                                            :percent="
                                                (record.worked_time /
                                                    record.office_time) *
                                                100
                                            "
                                            :success="{
                                                percent:
                                                    (record.late_time /
                                                        record.office_time) *
                                                    100,
                                                strokeColor: '#ff4d4f',
                                            }"
                                        />
                                    </a-tooltip>
                                    <br />
                                    <a-typography-link
                                        type="primary"
                                        class="work-duration work-duration:hover"
                                        @click="showBreakHistoryModal(record)"
                                    >
                                        {{ formatMinutes(record.worked_time) }}
                                    </a-typography-link>
                                </span>
                                <span v-else> - </span>
                            </template>
                            <template v-if="column.dataIndex == 'other_details'">
                                <span
                                    v-if="
                                        (record.clock_in && record.clock_in_ip) ||
                                        (record.clock_in && record.clock_out_ip)
                                    "
                                >
                                    <span>
                                        <a-typography-text strong>
                                            {{ $t("attendance.clock_in_ip") }} :
                                        </a-typography-text>
                                        {{
                                            record.clock_in && record.clock_in_ip
                                                ? record.clock_in_ip
                                                : ""
                                        }}
                                    </span>
                                    <br />
                                    <span>
                                        <a-typography-text strong>
                                            {{ $t("attendance.clock_out_ip") }}
                                            :
                                        </a-typography-text>
                                        {{
                                            record.clock_in && record.clock_out_ip
                                                ? record.clock_out_ip
                                                : ""
                                        }}
                                    </span>
                                    <br />
                                    <span>
                                        <a-typography-text strong>
                                            {{ $t("attendance.clock_in_location") }}
                                            :
                                        </a-typography-text>
                                        <a-typography-link
                                            type="primary"
                                            class="work-duration work-duration:hover"
                                            @click="
                                                showLocationDetailsModal(
                                                    record,
                                                    'clock_in'
                                                )
                                            "
                                        >
                                            {{ $t("common.view") }}
                                        </a-typography-link>
                                    </span>
                                    <br />
                                    <span>
                                        <a-typography-text strong>
                                            {{ $t("attendance.clock_out_location") }}
                                            :
                                        </a-typography-text>
                                        <a-typography-link
                                            type="primary"
                                            class="work-duration work-duration:hover"
                                            @click="
                                                showLocationDetailsModal(
                                                    record,
                                                    'clock_out'
                                                )
                                            "
                                        >
                                            {{ $t("common.view") }}
                                        </a-typography-link>
                                    </span>
                                </span>
                                <span v-else> - </span>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <BreakHistoryDetail
        :visible="showBreakHistoryModalVisible"
        :data="selectedBreakRecord"
        @closed="closeBreakHistory"
    />

    <LocationDetails
        :locationViewType="locationViewType"
        :visible="locationModalVisible"
        :data="selectedLocationRecord"
        @closed="closeLocationDetailsModal"
    />
</template>

<script>
import { onMounted, ref, reactive } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../common/composable/hrmManagement";
import common from "../../../../common/composable/common";
import attendanceLocationView from "../../../../common/composable/attendanceLocationView";
import AttendanceStatus from "../attendance/AttendanceStatus.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import BreakHistoryDetail from "./BreakHistoryDetail.vue";
import LocationDetails from "./LocationDetails.vue";

export default {
    props: {
        selectable: {
            default: false,
        },
        isPageTableContent: {
            default: true,
        },
        tableSize: {
            default: "large",
        },
        bordered: {
            default: false,
        },
        filters: {
            default: {},
        },
        perPageItems: Number,
    },
    emits: ["onRowSelection"],
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AttendanceStatus,
        UserInfo,
        UserListDisplay,
        BreakHistoryDetail,
        LocationDetails,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { getMonthNameByNumber, formatMinutes, monthArrays } = hrmManagement();
        const { dayjs, formatTime, formatDate, user, permsArray } = common();
        const attendanceLocationViewVariables = attendanceLocationView();

        const columns = ref([
            {
                title: t("attendance.date"),
                dataIndex: "date",
            },
            {
                title: t("attendance.status"),
                dataIndex: "status",
            },
            {
                title: t("attendance.clock_in"),
                dataIndex: "clock_in",
            },
            {
                title: t("attendance.clock_out"),
                dataIndex: "clock_out",
            },
            {
                title: t("attendance.clocked_time"),
                dataIndex: "clocked_time",
            },
            {
                title: t("attendance.other_details"),
                dataIndex: "other_details",
            },
        ]);
        const currentPage = ref(1);

        const table = reactive({
            data: [],
            pagination: {
                pageSize: 10,
                showSizeChanger: true,
            },
            loading: false,
        });

        const officeTime = ref(0);
        const totalPresentDays = ref(0);
        const workingDays = ref(0);
        const clockInDuration = ref(0);
        const totalOfficeTime = ref(0);
        const clockInDurationPercentage = ref(0);
        const totalLateDays = ref(0);
        const totalLateDaysPercentage = ref(0);
        const halfDayCount = ref(0);
        const selectedBreakRecord = ref({});
        const showBreakHistoryModalVisible = ref(false);

        onMounted(() => {
            setUrlData();
        });

        const filterData = () => {
            currentPage.value = 1;
            setUrlData();
        };

        const closeBreakHistory = () => {
            showBreakHistoryModalVisible.value = false;
            selectedBreakRecord.value = {};
        };

        const showBreakHistoryModal = (record) => {
            selectedBreakRecord.value = record;
            showBreakHistoryModalVisible.value = true;
        };

        const setUrlData = () => {
            table.loading = true;
            var filterMonth = props.filters.month;
            var filterYear = props.filters.year.format("YYYY");
            const limit = table.pagination.pageSize;
            const offset = (currentPage.value - 1) * limit;

            var url = `attendances/summary-month?month=${filterMonth}&year=${filterYear}`;

            if (props.filters.user_id) {
                url += `&user_id=${props.filters.user_id}`;
            }

            axiosAdmin.get(url).then((response) => {
                table.data = response.data.data;
                officeTime.value = response.data.office_time;
                workingDays.value = response.data.working_days;
                totalPresentDays.value = response.data.present_days;
                clockInDuration.value = response.data.clock_in_duration;
                totalOfficeTime.value = response.data.total_office_time;
                totalLateDays.value = response.data.total_late_days;
                halfDayCount.value = response.data.half_days;

                clockInDurationPercentage.value = (
                    (clockInDuration.value / totalOfficeTime.value) *
                    100
                ).toFixed(2);

                totalLateDaysPercentage.value = (
                    (totalLateDays.value / workingDays.value) *
                    100
                ).toFixed(2);

                table.loading = false;
            });
        };

        return {
            table,
            permsArray,
            columns,
            setUrlData,

            getMonthNameByNumber,
            filterData,
            formatMinutes,
            monthArrays,
            formatTime,
            formatDate,

            officeTime,
            totalPresentDays,
            workingDays,
            clockInDuration,
            totalOfficeTime,
            clockInDurationPercentage,
            totalLateDays,
            totalLateDaysPercentage,
            halfDayCount,

            showBreakHistoryModal,
            closeBreakHistory,
            selectedBreakRecord,
            showBreakHistoryModalVisible,

            ...attendanceLocationViewVariables,
        };
    },
};
</script>
<style lang="less">
.work-duration {
    cursor: pointer;
    color: #2563eb; /* Tailwind's blue-600 */
}

.work-duration:hover {
    text-decoration: underline;
}
</style>
