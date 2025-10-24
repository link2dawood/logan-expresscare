<template>
    <div class="clock-in-out-card">
        <a-card
            :bodyStyle="{
                fontSize: '10px',
                padding: '18px',
                height: '447px',
                position: 'relative',
            }"
        >
            <template #title>
                <ClockCircleOutlined />
                {{ $t("hrm_dashboard.clock_in_out") }}
            </template>
            <template #extra>
                <!-- Department Dropdown and Time Period Dropdown -->
                <a-space>
                    <a-dropdown>
                        <template #overlay>
                            <a-menu>
                                <a-menu-item
                                    key="yesterday"
                                    @click="
                                        selectedFilter = 'yesterday';
                                        dateSelectorClicked('yesterday');
                                    "
                                >
                                    {{ $t("hrm_dashboard.yesterday") }}
                                </a-menu-item>
                                <a-menu-item
                                    key="today"
                                    @click="
                                        selectedFilter = 'today';
                                        dateSelectorClicked('today');
                                    "
                                >
                                    {{ $t("hrm_dashboard.today") }}
                                </a-menu-item>
                                <a-menu-item
                                    key="this_week"
                                    @click="
                                        selectedFilter = 'this_week';
                                        dateSelectorClicked('week');
                                    "
                                >
                                    {{ $t("hrm_dashboard.this_week") }}
                                </a-menu-item>
                                <a-menu-item
                                    key="this_month"
                                    @click="
                                        selectedFilter = 'this_month';
                                        dateSelectorClicked('month');
                                    "
                                >
                                    {{ $t("hrm_dashboard.this_month") }}
                                </a-menu-item>
                                <a-menu-item
                                    key="this_year"
                                    @click="
                                        selectedFilter = 'this_year';
                                        dateSelectorClicked('year');
                                    "
                                >
                                    {{ $t("hrm_dashboard.this_year") }}
                                </a-menu-item>
                            </a-menu>
                        </template>
                        <a-button>
                            <template #icon> <CalendarOutlined /> </template>
                            {{ $t(`hrm_dashboard.${selectedFilter}`) }}
                        </a-button>
                    </a-dropdown>
                </a-space>
            </template>

            <a-result v-if="employees.length <= 0">
                <template #icon>
                    <ExclamationCircleFilled
                        :style="{
                            fontSize: '52px',
                            padding: '0px',
                            marginTop: '35px !important',
                        }"
                    />
                </template>
                <template #extra>
                    <span type="primary" :style="{ fontSize: '28px' }">
                        {{ $t("hrm_dashboard.no_attendace_mark") }}
                    </span>
                </template>
            </a-result>
            <a-row :gutter="[8, 8]" class="mt-30 mb-10" style="display: none">
                <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                    <DateRangePicker
                        ref="serachDateRangePicker"
                        @dateTimeChanged="
                            (changedDateTime) =>
                                (filters.clock_in_date_range = changedDateTime)
                        "
                        :showPreset="false"
                    />
                </a-col>
            </a-row>
            <!-- Attendance Section with Grouped Dates -->
            <div class="attendance-section">
                <div v-for="(employees, date) in groupedEmployees" :key="date">
                    <h2 class="date-header">{{ date }}</h2>
                    <div
                        v-for="employee in employees"
                        :key="employee.xid"
                        class="employee-item"
                    >
                        <a-card
                            class="employee-info-card"
                            :bodyStyle="{ fontSize: '9px', padding: '12px' }"
                            size="small"
                        >
                            <div class="employee-info-container">
                                <!-- Left Side: Avatar and Info -->
                                <div class="left-container">
                                    <a-avatar
                                        :src="employee?.user?.profile_image_url"
                                        size="medium"
                                    />
                                    <div class="employee-info">
                                        <div class="name">
                                            {{ employee?.user?.name }}
                                        </div>
                                        <div class="role">
                                            {{
                                                employee?.user?.designation
                                                    ?.name
                                            }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side: Status Time -->
                                <a-tag class="status-time-tag">
                                    {{
                                        dayjs(
                                            employee.clock_in_time,
                                            "HH:mm:ss"
                                        ).format("hh:mm A")
                                    }}
                                </a-tag>
                            </div>

                            <!-- Employee Details Card -->
                            <a-card
                                v-if="employee"
                                class="employee-details-card"
                                :bodyStyle="{
                                    fontSize: '12px',
                                    padding: '2px',
                                }"
                                size="small"
                            >
                                <div class="details-container">
                                    <div class="left-item">
                                        {{ $t("hrm_dashboard.clock_in") }}:
                                        {{
                                            dayjs(
                                                employee.clock_in_time,
                                                "HH:mm:ss"
                                            ).format("hh:mm A")
                                        }}
                                    </div>
                                    <div class="center-item">
                                        {{ $t("hrm_dashboard.clock_out") }}:
                                        {{
                                            employee.clock_out_time
                                                ? dayjs(
                                                      employee.clock_out_time,
                                                      "HH:mm:ss"
                                                  ).format("hh:mm A")
                                                : "-"
                                        }}
                                    </div>
                                    <div class="right-item">
                                        {{ $t("hrm_dashboard.production") }}:
                                        {{
                                            employee.dynamicProductionTime ||
                                            "0 min"
                                        }}
                                    </div>
                                </div>
                            </a-card>
                        </a-card>
                    </div>
                </div>
            </div>

            <!-- Fixed Footer Button -->
            <div class="footer">
                <a-button type="primary" block @click="viewAllAttendance">
                    {{ $t("hrm_dashboard.view_all_attendance") }}
                </a-button>
            </div>
        </a-card>
    </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import {
    CalendarOutlined,
    ClockCircleOutlined,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import { useRouter } from "vue-router";
import common from "../../../common/composable/common";
import { forEach, map, reduce, find } from "lodash-es";
import DateRangePicker from "../../../common/components/common/calendar/DateRangePicker.vue";

export default {
    props: ["data", "selectedFilte"],
    name: "ClockInOut",
    components: {
        CalendarOutlined,
        ClockCircleOutlined,
        ExclamationCircleFilled,
        DateRangePicker,
    },
    setup(props, { emit }) {
        const { dayjs } = common();
        const employees = ref([]);
        const router = useRouter();
        const intervals = ref({});
        const activeDateSelector = ref("");
        const serachDateRangePicker = ref(null);
        const selectedFilter = ref("today");
        const filters = ref({
            clock_in_date_range: [],
        });

        const fetchClockInData = () => {
            emit("fetchClockInData", {
                type: "clock_in",
                clockInDateRange: filters.value.clock_in_date_range,
            });
        };

        const viewAllAttendance = () => {
            router.push({
                name: "admin.attendance.details",
            });
        };

        const dateSelectorClicked = (selectedType) => {
            let selectedKey;
            let selectedFilterValue;

            if (typeof selectedType === "object" && selectedType !== null) {
                selectedKey = selectedType.key;
                selectedFilterValue = selectedType.selectedFilter;
            } else if (typeof selectedType === "string") {
                selectedKey = selectedType;
            }

            if (selectedFilterValue !== undefined) {
                selectedFilter.value = selectedFilterValue;
            }

            activeDateSelector.value = selectedKey;

            const dateRanges = {
                today: [dayjs(), dayjs()],
                yesterday: [
                    dayjs().subtract(1, "day").startOf("day"),
                    dayjs().subtract(1, "day").endOf("day"),
                ],
                week: [dayjs().subtract(7, "day"), dayjs()],
                month: [dayjs().startOf("month"), dayjs()],
                year: [dayjs().startOf("year"), dayjs()],
            };

            if (dateRanges[selectedKey]) {
                serachDateRangePicker.value.setDatePicker(
                    dateRanges[selectedKey]
                );
                filters.value.clock_in_date_range = map(
                    dateRanges[selectedKey],
                    (date) => date.format("YYYY-MM-DD")
                );
            }
            fetchClockInData();
        };

        const groupedEmployees = computed(() => {
            return reduce(
                employees.value,
                (acc, employee) => {
                    const date = employee.date;
                    if (!acc[date]) acc[date] = [];
                    acc[date].push(employee);
                    return acc;
                },
                {}
            );
        });

        const formatProductionTime = (minutes) => {
            if (minutes < 60) {
                return `${minutes} min`;
            } else {
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return mins > 0 ? `${hours} hr ${mins} min` : `${hours} hr`;
            }
        };

        const getProductionTime = (employee) => {
            const officeClockOutTime = dayjs(
                employee.office_clock_out_time,
                "HH:mm:ss"
            );
            const clockInDate = dayjs(employee.date).format("YYYY-MM-DD");

            const todayDate = dayjs().local().format("YYYY-MM-DD");

            // If it's today, check if current time is past officeClockOutTime
            if (clockInDate === todayDate) {
                const currentTime = dayjs();
                let endTime = employee.clock_out_time
                    ? dayjs(employee.clock_out_time, "HH:mm:ss")
                    : currentTime.isAfter(officeClockOutTime)
                    ? officeClockOutTime
                    : currentTime;

                const startTime = dayjs(employee.clock_in_time, "HH:mm:ss");
                const diffMinutes = endTime.diff(startTime, "minute");
                return formatProductionTime(diffMinutes);
            }
            // If it's a past day and clock-out is null, assume officeClockOutTime as clock-out time
            else if (!employee.clock_out_time) {
                const startTime = dayjs(employee.clock_in_time, "HH:mm:ss");
                const diffMinutes = officeClockOutTime.diff(
                    startTime,
                    "minute"
                );
                return formatProductionTime(diffMinutes);
            }
            // Otherwise, use the actual clock-out time
            else {
                return formatProductionTime(employee.total_duration || 0);
            }
        };

        const employeesWithDynamicTime = computed(() => {
            return map(employees.value, (employee) => ({
                ...employee,
                dynamicProductionTime: getProductionTime(employee),
            }));
        });

        const updateDynamicTimes = () => {
            forEach(employees.value, (employee) => {
                if (!employee.clock_out_time) {
                    if (!intervals.value[employee.xid]) {
                        intervals.value[employee.xid] = setInterval(() => {
                            const updatedEmployee = find(
                                employees.value,
                                (e) => e.xid === employee.xid
                            );
                            if (updatedEmployee) {
                                updatedEmployee.dynamicProductionTime =
                                    getProductionTime(updatedEmployee);
                            }
                        }, 1000);
                    }
                } else {
                    clearInterval(intervals.value[employee.xid]);
                    delete intervals.value[employee.xid];
                }
            });
        };

        onUnmounted(() => {
            Object.values(intervals.value).forEach(clearInterval);
        });

        watch(props, (newVal) => {
            employees.value = map(
                newVal.data.clockInOutTime?.is_late_false,
                (employee) => ({
                    ...employee,
                    dynamicProductionTime: getProductionTime(employee),
                })
            );
            updateDynamicTimes();
        });

        watch(
            () => props.selectedFilte,
            (newVal) => {
                selectedFilter.value = newVal;
            }
        );

        return {
            employees,
            groupedEmployees,
            filters,
            viewAllAttendance,
            selectedFilter,
            dayjs,
            employeesWithDynamicTime,
            activeDateSelector,
            serachDateRangePicker,
            dateSelectorClicked,
        };
    },
};
</script>

<style scoped>
.clock-in-out-card {
    max-width: 100%;
    position: relative;
}

.footer {
    position: absolute;
    bottom: 15px;
    left: 15px;
    right: 15px;
}
.details-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.attendance-section {
    margin-bottom: 10px;
    overflow-y: auto;
    max-height: 382px;
}

.employee-item {
    display: flex;
    flex-direction: column;
    margin-bottom: 8px;
}

.employee-item,
h2 {
    font-weight: bold;
    font-size: 13px;
}

.employee-info-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.left-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.employee-info {
    display: flex;
    flex-direction: column;
}

.employee-info-card {
    width: 100%;
}

.employee-details-card {
    width: 100%;
    margin-top: 10px;
}

.late-section h4 {
    font-size: 15px;
}

.late-employee-item {
    margin-bottom: 16px;
}

.name {
    font-size: 13px;
    font-weight: bold;
}

.role {
    font-size: 11px;
    color: #888;
}

.late-min {
    font-size: 12px;
    background-color: rgb(105, 238, 105);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 8px;
}

.late-time-tag {
    font-size: 12px;
    padding: 4px 6px;
    white-space: nowrap;
    color: rgb(223, 94, 94);
}
.status-time-tag {
    font-size: 12px;
    padding: 4px 10px;
    white-space: nowrap;
    color: white;
    background-color: rgb(101, 204, 101);
}

.late-min {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    background-color: rgb(105, 238, 105);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 8px;
}

.left-item {
    flex: 1;
    padding: 7px;
    text-align: left;
}

.center-item {
    flex: 1;
    padding: 7px;
    text-align: center;
}

.right-item {
    flex: 1;
    padding: 7px;
    text-align: right;
}
</style>
