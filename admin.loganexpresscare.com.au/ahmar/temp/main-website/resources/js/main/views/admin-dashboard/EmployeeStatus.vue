<template>
    <div class="employee-status-card">
        <a-card :bodyStyle="{ fontSize: '10px', padding: '15px', height: '448px' }">
            <template #title>
                <StrikethroughOutlined />
                {{ $t("hrm_dashboard.employee_status") }}
            </template>
            <template #extra>
                <a-space>
                    <span>
                        <a-select
                            v-model:value="filters.user_id"
                            @change="() => fetchUserData()"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('leave.user')])
                            "
                            :allowClear="true"
                            optionFilterProp="title"
                        >
                            <a-select-option
                                v-for="allUser in allUsers"
                                :key="allUser.xid"
                                :value="allUser.xid"
                                :title="allUser.name"
                                >{{ allUser.name }}
                            </a-select-option>
                        </a-select>
                    </span>
                    <span>
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
                    </span>
                </a-space>
            </template>

            <div class="card-container">
                <a-row :gutter="[8, 8]" class="mt-30 mb-10" style="display: none">
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <DateRangePicker
                            ref="serachDateRangePicker"
                            @dateTimeChanged="
                                (changedDateTime) =>
                                    (filters.status_date = changedDateTime)
                            "
                            :showPreset="false"
                        />
                    </a-col>
                </a-row>
                <a-card
                    v-if="willSubscriptionModuleVisible('appreciations')"
                    :title="$t('hrm_dashboard.appreciations')"
                    :bordered="true"
                    style="width: 100%"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ appreciations }}</h3>
                </a-card>

                <a-card
                    v-if="willSubscriptionModuleVisible('expenses')"
                    :title="$t('hrm_dashboard.expenses')"
                    :bordered="true"
                    style="width: 100%"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ expenses }}</h3>
                </a-card>

                <a-card
                    v-if="willSubscriptionModuleVisible('offboardings')"
                    :title="$t('hrm_dashboard.warnings')"
                    :bordered="true"
                    style="width: 100%"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ warnings }}</h3>
                </a-card>

                <a-card
                    v-if="willSubscriptionModuleVisible('offboardings')"
                    :title="$t('hrm_dashboard.complaints')"
                    :bordered="true"
                    style="width: 100%"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ complaints }}</h3>
                </a-card>
                <a-card
                    v-if="willSubscriptionModuleVisible('forms')"
                    :title="$t('hrm_dashboard.assign_survey')"
                    :bordered="true"
                    style="width: 100%"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ feedbackCounts }}</h3>
                </a-card>
                <a-card
                    v-if="willSubscriptionModuleVisible('assets')"
                    :title="$t('hrm_dashboard.assets_lent')"
                    :bordered="true"
                    style="width: 100%"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ assetsLent }}</h3>
                </a-card>
            </div>

            <div style="font-weight: bold; font-size: 16px; padding: 6px">
                {{ $t("hrm_dashboard.top_performer") }}
            </div>

            <div
                class="top-performer"
                v-if="
                    staffUser &&
                    Object.keys(staffUser).length > 0 &&
                    willSubscriptionModuleVisible('offboardings')
                "
            >
                <a-card
                    :bordered="true"
                    class="performer-card"
                    :bodyStyle="{ padding: '12px' }"
                    size="small"
                >
                    <div class="performer-details">
                        <div class="performer-info">
                            <img
                                :src="staffUser.profile_image_url"
                                class="performer-avatar"
                            />
                            <div class="performer-text">
                                <span class="performer-name">{{ staffUser.name }}</span>
                                <span class="performer-role">{{
                                    staffUser.role != null ? staffUser.role.name : "-"
                                }}</span>
                            </div>
                        </div>
                        <div class="performer-appreciation">
                            <span class="appreciation-label">{{
                                $t("hrm_dashboard.appreciations")
                            }}</span>
                            <span class="appreciation-count">{{
                                topUserAppreciations
                            }}</span>
                        </div>
                    </div>
                </a-card>
            </div>
        </a-card>
    </div>
</template>

<script>
import { ref, defineComponent, watch, onMounted } from "vue";
import { CalendarOutlined, StrikethroughOutlined } from "@ant-design/icons-vue";
import { find, map } from "lodash-es";
import common from "@/common/composable/common";
import DateRangePicker from "../../../common/components/common/calendar/DateRangePicker.vue";

export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        StrikethroughOutlined,
        DateRangePicker,
    },
    setup(props, { emit }) {
        const appreciations = ref("");
        const expenses = ref("");
        const warnings = ref("");
        const complaints = ref("");
        const assetsLent = ref("");
        const feedbackCounts = ref("");
        const activeDateSelector = ref("");
        const serachDateRangePicker = ref(null);
        const selectedFilter = ref("year");
        const topUserAppreciations = ref("");

        const { user, dayjs, willSubscriptionModuleVisible } = common();
        const allUsers = ref([]);
        const filters = ref({
            user_id: undefined,
            status_date: [],
        });
        const staffMembersUrl =
            "users?fields=id,xid,user_type,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary,probation_start_date,probation_end_date,notice_start_date,notice_end_date,duration,shift_id,x_shift_id,shift{id,xid,name},department_id,x_department_id,department{id,xid,name},end_date,annual_ctc,salary_group_id,x_salary_group_id,salaryGroup{id,xid}";
        const staffUser = ref({});

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });
        });

        const fetchUserData = () => {
            const startOfYear = dayjs().startOf("year").format("YYYY-MM-DD");
            const endOfYear = dayjs().endOf("year").format("YYYY-MM-DD");
            const defaultDateRange = [startOfYear, endOfYear];

            emit("employeeStatusData", {
                xid: filters.value.user_id,
                status_date:
                    filters.value.status_date?.length > 0
                        ? filters.value.status_date
                        : defaultDateRange,
                type: "employee_status",
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
            fetchUserData();
        };

        watch(props, (newVal, oldVal) => {
            appreciations.value = newVal.data.getEmployeeStatus?.appreciations;
            topUserAppreciations.value =
                newVal.data.getEmployeeStatus?.top_user_appreciation_count;
            expenses.value = newVal.data.getEmployeeStatus?.expenses;
            warnings.value = newVal.data.getEmployeeStatus?.warnings;
            complaints.value = newVal.data.getEmployeeStatus?.complaints;
            assetsLent.value = newVal.data.getEmployeeStatus?.assetsCount;
            feedbackCounts.value = newVal.data.getEmployeeStatus?.assetsCount;
            staffUser.value = newVal.data.getEmployeeStatus?.user;
        });

        return {
            appreciations,
            expenses,
            complaints,
            warnings,
            allUsers,
            filters,
            fetchUserData,
            staffUser,
            activeDateSelector,
            serachDateRangePicker,
            selectedFilter,
            dateSelectorClicked,
            assetsLent,
            feedbackCounts,
            topUserAppreciations,
            willSubscriptionModuleVisible,
        };
    },
});
</script>

<style>
.employee-status-card {
    max-width: 600px;
    margin: auto;
}
.number-employee {
    color: var(--ant-text-color);
    margin-right: 12px;
    font-size: 18px;
    font-weight: bold;
}
.card-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    width: 100%;
    margin-top: 16px;
}
.card-container .ant-card {
    width: 100%;
}
.top-performer {
    margin-top: 12px;
}
.top-performer h4 {
    font-size: 15px;
}
.performer-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.performer-details {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 255px;
    width: 100%;
}
.performer-info {
    display: flex;
    align-items: center;
    width: 100%;
}
.performer-avatar {
    border-radius: 50%;
    margin-inline-end: 10px;
    width: 50px;
    height: 50px;
}
.performer-text {
    display: flex;
    flex-direction: column;
}
.performer-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: inline-block;
}
.performer-role {
    font-size: 12px;
    color: gray;
}
.performance {
    font-weight: bold;
    font-size: 14px;
    text-align: right;
}
.performance p {
    margin: 2px;
    font-size: 14px;
    color: rgb(34, 33, 33);
}
.performer-appreciation {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: auto;
    width: 100%;
}
.appreciation-label {
    font-size: 14px;
}

.appreciation-count {
    font-size: 16px;
}
</style>
