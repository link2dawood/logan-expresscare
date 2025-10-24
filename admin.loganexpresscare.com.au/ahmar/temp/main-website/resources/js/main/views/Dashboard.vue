<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.dashboard`)" style="padding: 0px" />
        </template>
    </AdminPageHeader>

    <div class="dashboard-page-content-container">
        <UpdateAppAlert />
    </div>
    <div class="dashboard-page-content-container">
        <div class="mt-30 mb-20">
            <a-row :gutter="[8, 8]" class="mt-30 mb-10" style="display: none">
                <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                    <DateRangePicker
                        ref="serachDateRangePicker"
                        @dateTimeChanged="
                            (changedDateTime) =>
                                (filters.dates = changedDateTime)
                        "
                        :showPreset="false"
                    />
                </a-col>
            </a-row>
            <a-row :gutter="[15, 15]">
                <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                    <a-row>
                        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <a-card class="clock-in">
                                <template #title>
                                    <ClockCircleOutlined />
                                    {{ $t("hrm_dashboard.today_attendance") }}
                                </template>
                                <MarkTodayAttendance />
                            </a-card>
                        </a-col> </a-row
                ></a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                    <a-row
                        :gutter="[15, 15]"
                        style="margin-bottom: 13px; margin-top: 2px"
                    >
                        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                            <StateWidget>
                                <template #image>
                                    <TeamOutlined
                                        style="color: #fff; font-size: 24px"
                                    />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData.stateData">
                                        {{
                                            responseData.stateData
                                                .totalEmployees
                                        }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{ $t("dashboard.total_employees") }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                            <StateWidget>
                                <template #image>
                                    <SmileOutlined
                                        style="color: #fff; font-size: 24px"
                                    />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData.stateData">
                                        {{
                                            responseData.stateData
                                                .totalActiveEmployee
                                        }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{
                                            $t(
                                                "dashboard.total_active_employees"
                                            )
                                        }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col></a-row
                    >
                    <a-row :gutter="[15, 15]">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                            <StateWidget>
                                <template #image>
                                    <TagOutlined
                                        style="color: #fff; font-size: 24px"
                                    />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData.stateData">
                                        {{
                                            responseData.stateData
                                                .totalInactiveEmployees
                                        }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{
                                            $t(
                                                "dashboard.total_inactive_employees"
                                            )
                                        }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                            <StateWidget>
                                <template #image>
                                    <BankOutlined
                                        style="color: #fff; font-size: 24px"
                                    />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData.stateData">
                                        {{
                                            responseData.todaysAttendence
                                                .employee_under_you
                                        }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{ $t("dashboard.employee_under_you") }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col></a-row
                    >
                </a-col>
            </a-row>
        </div>
        <div class="mb-20">
            <a-row :gutter="[15, 15]" class="equal-height mb-20">
                <a-col
                    v-if="willSubscriptionModuleVisible('leaves')"
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8"
                    :xl="8"
                >
                    <PendingLeaves />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <div class="card-wrapper">
                        <a-card>
                            <template #title>
                                <ClockCircleOutlined />
                                {{ $t("hrm_dashboard.today_attendance") }}
                            </template>
                            <template #extra>
                                <a-space>
                                    <a-dropdown>
                                        <template #overlay>
                                            <a-menu>
                                                <a-menu-item
                                                    key="yesterday"
                                                    @click="
                                                        selectedFilter =
                                                            'yesterday';
                                                        dateSelectorClicked(
                                                            'yesterday'
                                                        );
                                                    "
                                                >
                                                    {{
                                                        $t(
                                                            "hrm_dashboard.yesterday"
                                                        )
                                                    }}
                                                </a-menu-item>
                                                <a-menu-item
                                                    key="today"
                                                    @click="
                                                        selectedFilter =
                                                            'today';
                                                        dateSelectorClicked(
                                                            'today'
                                                        );
                                                    "
                                                >
                                                    {{
                                                        $t(
                                                            "hrm_dashboard.today"
                                                        )
                                                    }}
                                                </a-menu-item>
                                                <a-menu-item
                                                    key="this_week"
                                                    @click="
                                                        selectedFilter =
                                                            'this_week';
                                                        dateSelectorClicked(
                                                            'week'
                                                        );
                                                    "
                                                >
                                                    {{
                                                        $t(
                                                            "hrm_dashboard.this_week"
                                                        )
                                                    }}
                                                </a-menu-item>
                                                <a-menu-item
                                                    key="this_month"
                                                    @click="
                                                        selectedFilter =
                                                            'this_month';
                                                        dateSelectorClicked(
                                                            'month'
                                                        );
                                                    "
                                                >
                                                    {{
                                                        $t(
                                                            "hrm_dashboard.this_month"
                                                        )
                                                    }}
                                                </a-menu-item>
                                                <a-menu-item
                                                    key="this_year"
                                                    @click="
                                                        selectedFilter =
                                                            'this_year';
                                                        dateSelectorClicked(
                                                            'year'
                                                        );
                                                    "
                                                >
                                                    {{
                                                        $t(
                                                            "hrm_dashboard.this_year"
                                                        )
                                                    }}
                                                </a-menu-item>
                                            </a-menu>
                                        </template>
                                        <a-button>
                                            <template #icon>
                                                <CalendarOutlined />
                                            </template>
                                            {{
                                                $t(
                                                    `hrm_dashboard.${selectedFilter}`
                                                )
                                            }}
                                        </a-button>
                                    </a-dropdown>
                                </a-space>
                            </template>
                            <DoughChart :data="responseData" />
                        </a-card>
                    </div>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <div class="card-wrapper">
                        <ClockInOut
                            :data="responseData"
                            @fetchClockInData="fetchClockInData"
                        />
                    </div>
                </a-col>
            </a-row>

            <a-row class="mb-20" :gutter="[15, 15]">
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <Birthday
                        :data="responseData"
                        @employeeBirthDayData="employeeBirthDayData"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <WorkAnniversay
                        :data="responseData"
                        @employeeAnniversaryData="employeeAnniversaryData"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <EmployeeByDepartment :data="responseData" />
                </a-col>
            </a-row>
            <a-row :gutter="[15, 15]" class="mb-20">
                <a-col
                    v-if="willSubscriptionModuleVisible('holidays')"
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8"
                    :xl="8"
                >
                    <WeekendHoliday
                        :data="responseData"
                        @fetchYearData="fetchWeekend"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <EmployeeWorkStatus :data="responseData" />
                </a-col>
                <a-col
                    v-if="willSubscriptionModuleVisible('appreciations')"
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8"
                    :xl="8"
                >
                    <Performance
                        :data="responseData"
                        @employeeAppriciationData="employeeAppriciationData"
                    />
                </a-col>
                <a-col
                    v-if="willSubscriptionModuleVisible('payrolls')"
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8"
                    :xl="8"
                >
                    <IncrementPromotion
                        :data="responseData"
                        @employeeIncrementData="employeeIncrementData"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <div class="card-wrapper">
                        <EmployeeStatus
                            :data="responseData"
                            @employeeStatusData="employeeStatusData"
                        />
                    </div>
                </a-col>
            </a-row>
        </div>
    </div>
</template>

<script>
import { onMounted, reactive, ref, watch } from "vue";
import UpdateAppAlert from "./UpdateAppAlert.vue";
import AdminPageHeader from "../../common/layouts/AdminPageHeader.vue";
import {
    ClockCircleOutlined,
    TeamOutlined,
    TagOutlined,
    SmileOutlined,
    BankOutlined,
    CalendarOutlined,
} from "@ant-design/icons-vue";
import { useRouter } from "vue-router";
import common from "../../common/composable/common";
import { map } from "lodash-es";
import MarkTodayAttendance from "./admin-dashboard/MarkTodayAttendance.vue";
import PendingLeaves from "./admin-dashboard/PendingLeaves.vue";
import TodayAttendance from "./admin-dashboard/TodayAttendance.vue";
import StateWidget from "../../common/components/common/card/StateWidget.vue";
import DoughChart from "../../common/components/common/card/DoughChart.vue";
import EmployeeStatus from "./admin-dashboard/EmployeeStatus.vue";
import ClockInOut from "./admin-dashboard/ClockInOut.vue";
import EmployeeByDepartment from "./admin-dashboard/EmployeeByDepartment.vue";
import Birthday from "./admin-dashboard/Birthday.vue";
import DateRangePicker from "../../common/components/common/calendar/DateRangePicker.vue";
import WorkAnniversay from "./admin-dashboard/WorkAnniversay.vue";
import WeekendHoliday from "./admin-dashboard/WeekendHoliday.vue";
import EmployeeWorkStatus from "./admin-dashboard/EmployeeWorkStatus.vue";
import Performance from "./admin-dashboard/performance.vue";
import IncrementPromotion from "./admin-dashboard/IncrementPromotion.vue";

export default {
    components: {
        UpdateAppAlert,
        AdminPageHeader,
        ClockCircleOutlined,
        TeamOutlined,
        BankOutlined,
        TagOutlined,
        SmileOutlined,
        MarkTodayAttendance,
        PendingLeaves,
        TodayAttendance,
        StateWidget,
        DoughChart,
        EmployeeStatus,
        ClockInOut,
        EmployeeByDepartment,
        Birthday,
        DateRangePicker,
        CalendarOutlined,
        WorkAnniversay,
        WeekendHoliday,
        EmployeeWorkStatus,
        IncrementPromotion,
        Performance,
    },
    setup(props) {
        const {
            permsArray,
            dayjs,
            selectedWarehouse,
            user,
            willSubscriptionModuleVisible,
        } = common();
        const router = useRouter();
        const responseData = ref([]);
        const filters = reactive({
            dates: [dayjs().format("YYYY-MM-DD"), dayjs().format("YYYY-MM-DD")],
            year: dayjs().year(),
            userId: undefined,
            filterMonthYear: undefined,
            date: undefined,
            status_date: [
                dayjs().startOf("year").format("YYYY-MM-DD"),
                dayjs().endOf("year").format("YYYY-MM-DD"),
            ],
            type: "all",
            clock_in_dates: [
                dayjs().format("YYYY-MM-DD"),
                dayjs().format("YYYY-MM-DD"),
            ],
        });
        const activeDateSelector = ref("");
        const serachDateRangePicker = ref(null);
        const selectedFilter = ref("today");

        onMounted(() => {
            const dashboardPromise = axiosAdmin.post("dashboard", filters);
            Promise.all([dashboardPromise]).then(([dashboardResponse]) => {
                responseData.value = {
                    ...dashboardResponse.data,
                    ...responseData.value,
                };
            });
        });

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
                filters.dates = map(dateRanges[selectedKey], (date) =>
                    date.format("YYYY-MM-DD")
                );
            }
            filters.type = "attendance";
        };

        const fetchWeekend = (weekendObj) => {
            filters.year = weekendObj.filterMonthYear;
            filters.type = weekendObj.type;
        };

        const fetchClockInData = (clockInObj) => {
            filters.clock_in_dates = clockInObj.clockInDateRange;
            filters.type = clockInObj.type;
        };

        const employeeStatusData = (statusData) => {
            filters.userId = statusData.xid;
            filters.status_date = statusData.status_date;
            filters.type = statusData.type;
        };

        const employeeBirthDayData = (birthdayData) => {
            filters.userId = birthdayData.xid;
            filters.date = birthdayData.date;
            filters.type = birthdayData.type;
        };
        const employeeAnniversaryData = (anniversaryData) => {
            filters.userId = anniversaryData.xid;
            filters.date = anniversaryData.date;
            filters.type = anniversaryData.type;
        };

        const employeeIncrementData = (increamentData) => {
            filters.userId = increamentData.user_id;
            filters.type = increamentData.type;
            filters.filterMonthYear = increamentData.filterMonthYear;
        };

        const employeeAppriciationData = (appreciationData) => {
            filters.userId = appreciationData.user_id;
            filters.type = appreciationData.type;
            filters.filterMonthYear = appreciationData.filterMonthYear;
        };

        watch(
            filters,
            (newVal, oldVal) => {
                axiosAdmin.post("dashboard", newVal).then((response) => {
                    responseData.value = {
                        ...responseData.value,
                        ...response.data,
                    };
                });
            },
            { deep: true }
        );

        return {
            filters,
            permsArray,
            responseData,
            dateSelectorClicked,
            serachDateRangePicker,
            selectedFilter,
            fetchWeekend,
            employeeStatusData,
            fetchClockInData,
            employeeIncrementData,
            employeeAppriciationData,
            employeeBirthDayData,
            employeeAnniversaryData,
            willSubscriptionModuleVisible,
        };
    },
};
</script>

<style scoped>
.clock-in .ant-result {
    margin-bottom: -24px;
    text-align: center;
    margin-top: -22px;
}
.antd-theme {
    color: var(--ant-text-color);
}
</style>
