<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.dashboard`)" style="padding: 0px" />
        </template>
    </AdminPageHeader>

    <div class="dashboard-page-content-container">
        <div class="mt-30 mb-20">
            <a-row :gutter="[8, 8]" class="mt-30 mb-10" style="display: none">
                <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                    <DateRangePicker
                        ref="serachDateRangePicker"
                        @dateTimeChanged="
                            (changedDateTime) => (filters.dates = changedDateTime)
                        "
                        :showPreset="false"
                    />
                </a-col>
            </a-row>
            <div class="mb-20">
                <a-row :gutter="[15, 15]">
                    <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                        <DashboardProfileUser :data="responseData" />
                    </a-col>
                    <a-col
                        :xs="24"
                        :sm="24"
                        :md="24"
                        :lg="willSubscriptionModuleVisible('leaves') ? 8 : 16"
                        :xl="willSubscriptionModuleVisible('leaves') ? 8 : 16"
                    >
                        <LeaveDetailsChartFixed :data="responseData" />
                    </a-col>
                    <a-col
                        v-if="willSubscriptionModuleVisible('leaves')"
                        :xs="24"
                        :sm="24"
                        :md="24"
                        :lg="8"
                        :xl="8"
                    >
                        <LeaveDetailsFixed :data="responseData" />
                    </a-col>
                </a-row>
            </div>

            <a-row class="mb-20" :gutter="[15, 15]">
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <Attendance :data="responseData" />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="16" :xl="16">
                    <a-row class="mb-20" :gutter="[15, 15]">
                        <a-col
                            v-if="willSubscriptionModuleVisible('appreciations')"
                            :xs="24"
                            :sm="24"
                            :md="24"
                            :lg="6"
                            :xl="6"
                        >
                            <StateWidget>
                                <template #image>
                                    <TrophyOutlined
                                        style="color: #fff; font-size: 24px"
                                    />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData">
                                        {{ responseData.appreciations }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{ $t("menu.appreciations") }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col>
                        <a-col
                            v-if="willSubscriptionModuleVisible('offboardings')"
                            :xs="24"
                            :sm="24"
                            :md="24"
                            :lg="6"
                            :xl="6"
                        >
                            <StateWidget>
                                <template #image>
                                    <WomanOutlined style="color: #fff; font-size: 24px" />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData">
                                        {{ responseData.warnings }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{ $t("menu.warnings") }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col>
                        <a-col
                            v-if="willSubscriptionModuleVisible('expenses')"
                            :xs="24"
                            :sm="24"
                            :md="24"
                            :lg="6"
                            :xl="6"
                        >
                            <StateWidget>
                                <template #image>
                                    <DollarCircleOutlined
                                        style="color: #fff; font-size: 24px"
                                    />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData">
                                        {{
                                            responseData.expenses
                                                ? responseData.expenses
                                                : 0
                                        }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{ $t("menu.expenses") }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col>
                        <a-col
                            v-if="willSubscriptionModuleVisible('offboardings')"
                            :xs="24"
                            :sm="24"
                            :md="24"
                            :lg="6"
                            :xl="6"
                        >
                            <StateWidget>
                                <template #image>
                                    <BankOutlined style="color: #fff; font-size: 24px" />
                                </template>
                                <template #description>
                                    <h2 v-if="responseData">
                                        {{ responseData.complaints }}
                                    </h2>
                                    <p class="antd-theme">
                                        {{ $t("menu.complaints") }}
                                    </p>
                                </template>
                            </StateWidget>
                        </a-col>
                    </a-row>
                    <a-row :gutter="[15, 15]" style="margin-top: 25px"
                        ><SelfProgressBar
                            :data="responseData"
                            @attendaceData="getAttendaceData"
                        />
                    </a-row>
                </a-col>
            </a-row>

            <a-row class="mb-20" :gutter="[15, 15]">
                <a-col
                    v-if="willSubscriptionModuleVisible('forms')"
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8"
                    :xl="8"
                >
                    <AssignedSurvey :data="responseData" />
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
                    <SelfIncrementPromotion
                        :data="responseData"
                        @employeeIncrementData="employeeIncrementData"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <Birthday
                        :data="responseData"
                        @employeeBirthDayData="employeeBirthDayData"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <SelfWorkAnniversay
                        :data="responseData"
                        @employeeAnniversaryData="employeeAnniversaryData"
                    />
                </a-col>
                <a-col
                    v-if="willSubscriptionModuleVisible('holidays')"
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8"
                    :xl="8"
                >
                    <SelfWeekendHoliday
                        :data="responseData"
                        @fetchYearData="fetchWeekend"
                    />
                </a-col>
            </a-row>
            <a-row class="mb-20" :gutter="[15, 15]">
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <a-row class="mb-20" :gutter="[15, 15]">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <SelfBirthday :data="responseData" />
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <a-row
                        v-if="willSubscriptionModuleVisible('company_policies')"
                        class="mb-20"
                        :gutter="[15, 15]"
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <SelfCompanyPolicy :data="responseData" />
                        </a-col>
                    </a-row>
                    <a-row
                        v-if="willSubscriptionModuleVisible('holidays')"
                        class="mb-20"
                        :gutter="[15, 15]"
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <SelfNextHoliday :data="responseData" />
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
        </div>
    </div>
</template>

<script>
import { onMounted, ref, watch, reactive } from "vue";

import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import {
    ClockCircleOutlined,
    BankOutlined,
    TrophyOutlined,
    WomanOutlined,
    UserOutlined,
    DollarCircleOutlined,
} from "@ant-design/icons-vue";
import common from "../../../common/composable/common";

import DashboardProfileUser from "./self-dashboard/DashboardProfileUser.vue";
import LeaveDetailsChartFixed from "./self-dashboard/LeaveDetailsChartFixed.vue";
import LeaveDetailsFixed from "./self-dashboard/LeaveDetailsFixed.vue";
import Attendance from "./self-dashboard/Attendance.vue";
import SelfProgressBar from "./self-dashboard/SelfProgressBar.vue";
import Performance from "./self-dashboard/Performance.vue";
import AssignedSurvey from "./self-dashboard/AssignedSurvey.vue";
import SelfIncrementPromotion from "./self-dashboard/SelfIncrementPromotion.vue";
import SelfBirthday from "./self-dashboard/SelfBirthday.vue";
import SelfCompanyPolicy from "./self-dashboard/SelfCompanyPolicy.vue";
import SelfNextHoliday from "./self-dashboard/SelfNextHoliday.vue";
import StateWidget from "../../../common/components/common/card/StateWidget.vue";
import Birthday from "./self-dashboard/Birthday.vue";
import SelfWorkAnniversay from "./self-dashboard/SelfWorkAnniversay.vue";
import SelfWeekendHoliday from "./self-dashboard/SelfWeekendHoliday.vue";
import DateRangePicker from "../../../common/components/common/calendar/DateRangePicker.vue";

import { map } from "lodash-es";

export default {
    components: {
        AdminPageHeader,
        ClockCircleOutlined,
        StateWidget,
        DashboardProfileUser,
        LeaveDetailsChartFixed,
        LeaveDetailsFixed,
        Attendance,
        SelfProgressBar,
        Performance,
        AssignedSurvey,
        SelfIncrementPromotion,
        SelfBirthday,
        SelfCompanyPolicy,
        SelfNextHoliday,
        BankOutlined,
        TrophyOutlined,
        Birthday,
        SelfWorkAnniversay,
        SelfWeekendHoliday,
        DateRangePicker,
        WomanOutlined,
        UserOutlined,
        DollarCircleOutlined,
    },
    setup() {
        const responseData = ref([]);
        const { permsArray, dayjs, user, willSubscriptionModuleVisible } = common();
        const filters = reactive({
            year: dayjs().year(),
            userId: user.value.xid,
            filterMonthYear: undefined,
            filterUserId: undefined,
            date: undefined,
            status_date: [dayjs().format("YYYY-MM-DD"), dayjs().format("YYYY-MM-DD")],
            type: "all",
        });
        const activeDateSelector = ref("");
        const serachDateRangePicker = ref(null);
        const selectedFilter = ref("today");

        onMounted(() => {
            const dashboardPromise = axiosAdmin.post("self/dashboard", filters);

            Promise.all([dashboardPromise]).then(([dashboardResponse]) => {
                responseData.value = {
                    ...responseData.value,
                    ...dashboardResponse.data,
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
                serachDateRangePicker.value.setDatePicker(dateRanges[selectedKey]);
                filters.dates = map(dateRanges[selectedKey], (date) =>
                    date.format("YYYY-MM-DD")
                );
            }
        };

        const fetchWeekend = (weekendObj) => {
            filters.year = weekendObj.filterMonthYear;
            filters.type = weekendObj.type;
        };
        const getAttendaceData = (attendaceData) => {
            filters.status_date = attendaceData.status_date;
            filters.type = attendaceData.type;
        };
        const employeeIncrementData = (increamentData) => {
            filters.filterMonthYear = increamentData.filterMonthYear;
            filters.type = increamentData.type;
        };
        const employeeBirthDayData = (birthdayData) => {
            filters.filterUserId = birthdayData.xid;
            filters.date = birthdayData.date;
            filters.type = birthdayData.type;
        };

        const employeeAnniversaryData = (anniversaryData) => {
            filters.filterUserId = anniversaryData.xid;
            filters.date = anniversaryData.date;
            filters.type = anniversaryData.type;
        };

        const employeeAppriciationData = (appreciationData) => {
            filters.filterMonthYear = appreciationData.filterMonthYear;
            filters.type = appreciationData.type;
        };
        watch(
            filters,
            (newVal, oldVal) => {
                axiosAdmin.post("self/dashboard", newVal).then((response) => {
                    responseData.value = {
                        ...responseData.value,
                        ...response.data,
                    };
                });
            },
            { deep: true }
        );

        return {
            responseData,
            permsArray,
            filters,
            activeDateSelector,
            serachDateRangePicker,
            selectedFilter,
            dateSelectorClicked,
            fetchWeekend,
            getAttendaceData,
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
.antd-theme {
    color: var(--ant-text-color);
}
</style>
