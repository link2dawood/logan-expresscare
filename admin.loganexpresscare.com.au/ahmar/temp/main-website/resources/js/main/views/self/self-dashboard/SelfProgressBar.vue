<template>
    <a-card class="working-hours-card">
        <template #title>
            {{ $t("menu.work_hour_details") }}
        </template>
        <template #extra>
            <a-space>
                <a-dropdown>
                    <template #overlay>
                        <a-menu>
                            <a-menu-item
                                key="lastYear"
                                @click="
                                    selectedFilter = 'lastYear';
                                    dateSelectorClicked('lastYear');
                                "
                            >
                                {{ $t("hrm_dashboard.lastYear") }}
                            </a-menu-item>
                            <a-menu-item
                                key="lastMonth"
                                @click="
                                    selectedFilter = 'lastMonth';
                                    dateSelectorClicked('lastMonth');
                                "
                            >
                                {{ $t("hrm_dashboard.lastMonth") }}
                            </a-menu-item>
                            <a-menu-item
                                key="lastWeek"
                                @click="
                                    selectedFilter = 'lastWeek';
                                    dateSelectorClicked('lastWeek');
                                "
                            >
                                {{ $t("hrm_dashboard.lastWeek") }}
                            </a-menu-item>
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
                        <template #icon>
                            <CalendarOutlined />
                        </template>
                        {{ $t(`hrm_dashboard.${selectedFilter}`) }}
                    </a-button>
                </a-dropdown>
            </a-space>
        </template>
        <div class="summary">
            <a-row :gutter="[8, 8]" class="mt-30 mb-10" style="display: none">
                <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                    <DateRangePicker
                        ref="serachDateRangePicker"
                        @dateTimeChanged="
                            (changedDateTime) => (filters.status_date = changedDateTime)
                        "
                        :showPreset="false"
                    />
                </a-col>
            </a-row>
            <div class="summary-item" v-for="(item, index) in summaryData" :key="index">
                <div class="summary-label">
                    <span class="dot" :class="item.class"></span>
                    <span>{{ item.label }}</span>
                </div>
                <strong class="summary-value">{{ item.value }}</strong>
            </div>
        </div>
        <div class="timeline">
            <div class="progress-bar">
                <div
                    v-for="(segment, index) in progressSegments"
                    :key="index"
                    :class="['segment', segment.type]"
                    :style="{ width: segment.width }"
                ></div>
            </div>
        </div>
    </a-card>
</template>

<script>
import { ref, defineComponent, watch, onMounted } from "vue";
import { CalendarOutlined, StrikethroughOutlined } from "@ant-design/icons-vue";
import { find, map } from "lodash-es";
import common from "@/common/composable/common";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";

export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        StrikethroughOutlined,
        DateRangePicker,
        hrmManagement,
    },
    setup(props, { emit }) {
        const { getMonthNameByNumber, formatMinutes } = hrmManagement();
        const summaryData = ref([
            { label: "Total office time", value: "0h 0m", class: "total" },
            { label: "Total worked time", value: "0h 0m", class: "productive" },
            { label: "Late days", value: "0m 0s", class: "break" },
        ]);

        const progressSegments = ref([
            { type: "total", width: "10%" },
            { type: "productive", width: "40%" },
            { type: "break", width: "50%" },
        ]);
        const { dayjs } = common();
        const filters = ref({
            status_date: [],
        });
        const activeDateSelector = ref("");
        const serachDateRangePicker = ref(null);
        const selectedFilter = ref("today");

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
                lastWeek: [
                    dayjs().subtract(7, "day").startOf("day"),
                    dayjs().subtract(1, "day").endOf("day"),
                ],
                lastMonth: [
                    dayjs().subtract(1, "month").startOf("month"),
                    dayjs().subtract(1, "month").endOf("month"),
                ],
                lastYear: [
                    dayjs().subtract(1, "year").startOf("year"),
                    dayjs().subtract(1, "year").endOf("year"),
                ],
            };

            if (dateRanges[selectedKey]) {
                serachDateRangePicker.value.setDatePicker(dateRanges[selectedKey]);
                filters.status_date = map(dateRanges[selectedKey], (date) =>
                    date.format("YYYY-MM-DD")
                );
            }
            fetchAttendaceData();
        };
        const fetchAttendaceData = () => {
            const today = dayjs().format("YYYY-MM-DD");
            const defaultDateRange = [today, today];
            emit("attendaceData", {
                status_date:
                    filters.value.status_date?.length > 0
                        ? filters.value.status_date
                        : defaultDateRange,
                type: "attendance_bar",
            });
        };

        watch(
            () => props,
            (newVal) => {
                if (newVal) {
                    const totalOfficeTimes = formatMinutes(
                        newVal.data.attendanceSummaryByDate?.total_office_time
                    );
                    const totalWorkedTimes = formatMinutes(
                        newVal.data.attendanceSummaryByDate?.clock_in_duration
                    );
                    const totalLateTimes = formatMinutes(
                        newVal.data.attendanceSummaryByDate?.total_late_time
                    );
                    const totalLateTime =
                        newVal.data.attendanceSummaryByDate?.total_late_time ?? 0;

                    const clockInDuration =
                        newVal.data.attendanceSummaryByDate?.clock_in_duration ?? 0;

                    const totalOfficeTime =
                        newVal.data.attendanceSummaryByDate?.total_office_time ?? 0;
                    summaryData.value = [
                        {
                            label: "Total office time",
                            value: totalOfficeTimes,
                            class: "total",
                        },
                        {
                            label: "Total worked time",
                            value: totalWorkedTimes,
                            class: "productive",
                        },
                        {
                            label: "Total Late time",
                            value: totalLateTimes,
                            class: "break",
                        },
                    ];

                    // Set the dynamic values
                    progressSegments.value = [
                        {
                            type: "total",
                            width: `${totalOfficeTime}%`,
                        },
                        {
                            type: "productive",
                            width: `${clockInDuration}%`,
                        },
                        { type: "break", width: `${totalLateTime}%` },
                    ];
                }
            },
            { deep: true, immediate: true }
        );

        return {
            progressSegments,
            summaryData,
            dateSelectorClicked,
            activeDateSelector,
            serachDateRangePicker,
            selectedFilter,
            filters,
        };
    },
});
</script>

<style scoped>
.working-hours-card {
    width: 98.9%;
    margin: auto;
    padding: 14px;
}

.summary {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.summary-item {
    display: flex;
    flex-direction: column;
    align-items: left;
    text-align: left;
}

.summary-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.summary-value {
    font-weight: bold;
    font-size: 18px;
    margin-top: 5px;
}

.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    color: var(--ant-text-color);
}

.timeline {
    margin-top: 10px;
}

.time-labels {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin-bottom: 5px;
}

.progress-bar {
    display: flex;
    height: 10px;
    background-color: #f0f0f0;
    border-radius: 5px;
    overflow: hidden;
}

.segment {
    height: 100%;
    transition: width 0.5s ease-in-out;
}
.total {
    background-color: black;
}
.productive {
    background-color: green;
}
.break {
    background-color: rgb(167, 47, 47);
}
.present {
    background-color: rgb(247, 0, 255);
}
.total_days {
    background-color: rgb(0, 255, 170);
}
</style>
