<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.attendance_details`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.attendances`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.attendance_details`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]" align="middle">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
                        <a-date-picker
                            v-model:value="filters.year"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            picker="year"
                            @change="filterData"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                        <a-select
                            v-model:value="filters.month"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.month')])
                            "
                            :allowClear="false"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="filterData"
                        >
                            <a-select-option
                                v-for="month in monthArrays"
                                :key="month.name"
                                :value="month.value"
                                :title="month.name"
                            >
                                {{ month.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <div class="mb-30">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="8" :lg="5">
                    <a-card>
                        <a-statistic
                            :title="$t('attendance.present_working_days')"
                            :value="`${totalPresentDays} / ${workingDays} ${$t(
                                'attendance.days'
                            )}`"
                            :value-style="{
                                color:
                                    totalPresentDays >= workingDays
                                        ? '#3f8600'
                                        : '#cf1322',
                            }"
                            style="margin-right: 50px"
                        />
                    </a-card>
                </a-col>
                <a-col :xs="24" :sm="24" :md="8" :lg="5">
                    <a-card>
                        <a-statistic
                            :title="$t('attendance.total_office_time')"
                            :value="formatMinutes(totalOfficeTime)"
                            :value-style="{ color: '#3f8600' }"
                        />
                    </a-card>
                </a-col>
                <a-col :xs="24" :sm="24" :md="8" :lg="6">
                    <a-card>
                        <a-statistic
                            :title="$t('attendance.total_worked_time')"
                            :value="`${formatMinutes(
                                clockInDuration
                            )} (${clockInDurationPercentage}%)`"
                            :value-style="{
                                color:
                                    clockInDuration >= totalOfficeTime
                                        ? '#3f8600'
                                        : '#cf1322',
                            }"
                        />
                    </a-card>
                </a-col>

                <a-col :xs="24" :sm="24" :md="8" :lg="5">
                    <a-card>
                        <a-statistic
                            :title="$t('attendance.late')"
                            :value="
                                totalLateDaysPercentage > 0
                                    ? `${totalLateDays} ${$t(
                                          'attendance.days'
                                      )} (${totalLateDaysPercentage}%)`
                                    : '--'
                            "
                            class="demo-class"
                            :value-style="{ color: '#cf1322' }"
                        />
                    </a-card>
                </a-col>
                <a-col :xs="24" :sm="24" :md="8" :lg="3">
                    <a-card>
                        <a-statistic
                            :title="$t('attendance.half_days')"
                            :value="`${halfDayCount}`"
                            class="demo-class"
                            :value-style="{ color: '#cf1322' }"
                        />
                    </a-card>
                </a-col>
            </a-row>
        </div>

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
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    CheckOutlined,
    CloseOutlined,
    CalendarFilled,
    ArrowRightOutlined,
    CalculatorOutlined,
    CarOutlined,
    ClockCircleOutlined,
    CheckCircleOutlined,
    ExclamationCircleOutlined,
    CloseCircleOutlined,
    CalendarOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import common from "../../../../../common/composable/common";
import attendanceLocationView from "../../../../../common/composable/attendanceLocationView";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import AttendanceStatus from "../../../attendances/attendance/AttendanceStatus.vue";
import BreakHistoryDetail from "@/main/views/attendances/details/BreakHistoryDetail.vue";
import LocationDetails from "@/main/views/attendances/details/LocationDetails.vue";

export default {
    components: {
        // PlusOutlined,
        // EditOutlined,
        // DeleteOutlined,
        // CheckOutlined,
        // CloseOutlined,
        // CalendarFilled,
        // ArrowRightOutlined,
        // CarOutlined,
        // CalculatorOutlined,
        // ClockCircleOutlined,
        // CheckCircleOutlined,
        // ExclamationCircleOutlined,
        // CloseCircleOutlined,
        // CalendarOutlined,

        AdminPageHeader,
        AttendanceStatus,
        BreakHistoryDetail,
        LocationDetails,
    },
    setup() {
        const { t } = useI18n();
        const { getMonthNameByNumber, formatMinutes, monthArrays } = hrmManagement();
        const { dayjs, formatTime, formatDate, user, permsArray } = common();
        const attendanceLocationViewVariables = attendanceLocationView();
        const filters = ref({
            month: dayjs().format("MM"),
            year: dayjs(),
        });
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
        // const currentPage = ref(1);
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
            // currentPage.value = 1;
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
            var filterMonth = filters.value.month;
            var filterYear = filters.value.year.format("YYYY");
            // const limit = table.pagination.pageSize;
            // const offset = (currentPage.value - 1) * limit;

            var url = `self/summary-month?month=${filterMonth}&year=${filterYear}`;

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
            // currentPage,
            columns,
            getMonthNameByNumber,
            filters,
            filterData,
            permsArray,

            officeTime,
            totalPresentDays,
            workingDays,
            clockInDuration,
            totalOfficeTime,
            clockInDurationPercentage,
            totalLateDays,
            totalLateDaysPercentage,
            halfDayCount,

            setUrlData,

            formatTime,
            formatDate,
            formatMinutes,
            monthArrays,

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
