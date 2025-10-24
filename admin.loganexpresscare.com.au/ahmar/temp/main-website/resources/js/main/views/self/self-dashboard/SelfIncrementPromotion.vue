<template>
    <a-card
        class="meeting-schedule-container"
        :bodyStyle="{ padding: '14px', height: '443px' }"
    >
        <template #title>
            {{ $t("increment_promotion.increment_promotion") }}
        </template>
        <template #extra>
            <a-date-picker
                v-model:value="extraFilters.year"
                :placeholder="
                    $t('common.select_default_text', [$t('holiday.year')])
                "
                picker="year"
                style="width: 100%"
                :allowClear="false"
                @change="filterYearData(extraFilters.year)"
        /></template>
        <a-result v-if="meetings.length <= 0"
            ><template #icon>
                <ExclamationCircleFilled
                    :style="{
                        fontSize: '52px',
                        padding: '0px',
                        marginTop: '42px !important',
                    }"
                />
            </template>
            <template #extra>
                <span
                    type="primary"
                    :style="{
                        fontSize: '28px',
                    }"
                    >{{
                        $t("hrm_dashboard.there_are_no_increment_promotions")
                    }}</span
                >
            </template>
        </a-result>
        <div class="pending-leave-hrm">
            <perfect-scrollbar
                :options="{
                    wheelSpeed: 1,
                    swipeEasing: true,
                    suppressScrollX: true,
                }"
            >
                <a-timeline class="meeting-timeline">
                    <a-timeline-item
                        v-for="(meeting, index) in meetings"
                        :key="index"
                        :color="meeting.color"
                        :class="{ 'last-item': index === meetings.length - 1 }"
                    >
                        <span class="meeting-time">{{
                            formatDate(meeting.date)
                        }}</span
                        >&nbsp;
                        <span class="meeting-time">
                            {{
                                meeting.type === "promotion"
                                    ? $t("increment_promotion.promotion")
                                    : meeting.type === "increment_promotion"
                                    ? $t(
                                          "increment_promotion.increment_promotion"
                                      )
                                    : meeting.type === "decrement"
                                    ? $t("increment_promotion.decrement")
                                    : meeting.type === "decrement_demotion"
                                    ? $t(
                                          "increment_promotion.decrement_demotion"
                                      )
                                    : meeting.type
                            }}
                        </span>

                        <a-card
                            class="meeting-card"
                            :bodyStyle="{ padding: '5px' }"
                        >
                            <div class="meeting-title">
                                {{ meeting?.user.name }}
                            </div>
                            <div
                                v-if="meeting.type === 'decrement'"
                                style="margin-top: 12px"
                            >
                                <div>
                                    <SalaryVisibility
                                        :salary="meeting.net_salary"
                                    />
                                </div>
                            </div>

                            <div class="meeting-category" v-else>
                                <span>
                                    {{
                                        meeting?.current_designation?.name
                                    }}</span
                                >
                                <span :class="getArrowClass(meeting)"> → </span>
                                <span>
                                    {{ meeting?.promoted_designation?.name }}
                                </span>
                                <div v-if="meeting.type !== 'promotion'">
                                    <SalaryVisibility
                                        :salary="meeting.net_salary"
                                    />
                                </div>
                            </div>
                        </a-card>
                    </a-timeline-item>
                </a-timeline>
            </perfect-scrollbar>
        </div>
    </a-card>
</template>

<script>
import { ref, watchEffect, watch, defineComponent, onMounted } from "vue";
import {
    CalendarOutlined,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import { theme } from "ant-design-vue";
import common from "@/common/composable/common";
import SalaryVisibility from "../../../components/SalaryVisibility.vue";

export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        SalaryVisibility,
        ExclamationCircleFilled,
    },
    setup(props, { emit }) {
        const { token } = theme.useToken();
        const { formatDate, formatAmountCurrency, dayjs } = common();
        const meetings = ref([]);
        const allUsers = ref([]);
        const extraFilters = ref({
            year: undefined,
        });
        const filterMonthYear = ref(dayjs());
        const staffMembersUrl =
            "self/users?fields=id,xid,user_type,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary,probation_start_date,probation_end_date,notice_start_date,notice_end_date,duration,shift_id,x_shift_id,shift{id,xid,name},department_id,x_department_id,department{id,xid,name},end_date,annual_ctc,salary_group_id,x_salary_group_id,salaryGroup{id,xid}";

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });
        });
        const getArrowClass = (meeting) => {
            return ["promotion", "increment_promotion", "increment"].includes(
                meeting.type
            )
                ? "arrow-green"
                : "arrow-red";
        };

        const getMeetingColor = (meeting) => {
            if (meeting.type === "increment") return "green";
            if (meeting.type === "increment_promotion") return "green";
            if (meeting.type === "promotion") return "green";
            if (meeting.type === "decrement") return "red";
            if (meeting.type === "decrement_demotion") return "red";
        };

        const filterYearData = (yearValue) => {
            if (yearValue) {
                filterMonthYear.value = dayjs(yearValue).format("YYYY");
            }
            emit("employeeIncrementData", {
                filterMonthYear: filterMonthYear.value,
                type: "employee_increment",
            });
        };

        watch(
            () => props.data,
            (newVal) => {
                meetings.value = [];
                if (newVal?.getIncrementPromotions) {
                    meetings.value = newVal.getIncrementPromotions.map(
                        (meeting) => {
                            return {
                                ...meeting,
                                color: getMeetingColor(meeting),
                            };
                        }
                    );
                }
            }
        );

        watchEffect(() => {
            document.documentElement.style.setProperty(
                "--text-color",
                token.colorText
            );
            document.documentElement.style.setProperty(
                "--secondary-text-color",
                token.colorTextSecondary
            );
            document.documentElement.style.setProperty(
                "--card-background",
                token.colorBgContainer
            );
            document.documentElement.style.setProperty(
                "--timeline-background",
                token.colorFillTertiary
            );
        });

        return {
            meetings,
            formatDate,
            getArrowClass,
            formatAmountCurrency,
            allUsers,
            filterYearData,
            extraFilters,
        };
    },
});
</script>

<style scoped>
.meeting-schedule-container {
    width: 100%;
    margin: auto;
    color: var(--text-color);
}

.meeting-timeline {
    padding-bottom: 0;
    background: var(--timeline-background);
}

.meeting-time {
    font-weight: bold;
    margin-bottom: 2px;
    font-size: 12px;
    color: var(--text-color);
}

.meeting-card {
    background: var(--card-background);
    border-radius: 6px;
    padding: 4px;
    font-size: 10px;
}

.meeting-title {
    font-weight: bold;
    font-size: 14px;
    color: var(--text-color);
    margin-bottom: -10px;
}

.meeting-category {
    color: var(--secondary-text-color);
    font-size: 11px;
    margin-top: 0px;
}

.last-item {
    margin-bottom: -12px !important;
    padding-bottom: 0 !important;
}
.arrow-green {
    color: green;
    font-weight: bold;
    margin: 0 2px;
    font-size: 24px;
}

.arrow-red {
    color: red;
    font-weight: bold;
    font-size: 24px;
    margin: 0 2px;
}
.pending-leave-hrm .ps {
    height: 378px;
}
</style>
