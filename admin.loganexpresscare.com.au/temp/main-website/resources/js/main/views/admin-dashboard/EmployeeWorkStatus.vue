<template>
    <div class="employee-status-card">
        <a-card
            :bodyStyle="{ fontSize: '10px', padding: '13px', height: '444px' }"
        >
            <template #title>
                <StrikethroughOutlined />
                {{ $t("hrm_dashboard.employee_work_status") }}
            </template>

            <a-row class="total-employee">
                <a-col
                    span="12"
                    style="
                        text-align: start;
                        margin-inline-start: auto;
                        margin-inline-end: auto;
                    "
                >
                    <p style="margin-left: 13px">
                        {{ $t("dashboard.total_active_employees") }}
                    </p>
                </a-col>
                <a-col
                    span="12"
                    style="
                        text-align: end;
                        margin-inline-start: auto;
                        margin-inline-end: auto;
                    "
                >
                    <h2 class="number-employee">{{ totalUsers }}</h2>
                </a-col>
            </a-row>

            <a-progress
                :percent="100"
                :show-info="false"
                :stroke-width="20"
                :stroke-color="progressGradient"
            />

            <div class="card-container">
                <a-card
                    :title="$t('employee_work_status.probation')"
                    :bordered="true"
                    style="width: 100%; margin-bottom: 10px"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ probation }}</h3>
                </a-card>

                <a-card
                    :title="$t('employee_work_status.contract')"
                    :bordered="true"
                    style="width: 100%; margin-bottom: 10px"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ contract }}</h3>
                </a-card>

                <a-card
                    :title="$t('employee_work_status.fulltime')"
                    :bordered="true"
                    style="width: 100%; margin-bottom: 10px"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ fulltime }}</h3>
                </a-card>

                <a-card
                    :title="$t('employee_work_status.work_from_home')"
                    :bordered="true"
                    style="width: 100%; margin-bottom: 10px"
                    :bodyStyle="{ padding: '10px' }"
                    :headStyle="{ padding: '10px' }"
                    size="small"
                >
                    <h3>{{ workFromHome }}</h3>
                </a-card>
            </div>

            <div style="font-weight: bold; font-size: 16px; padding: 6px">
                {{ $t("hrm_dashboard.top_performer") }}
            </div>

            <div
                class="top-performer"
                v-if="staffUser && Object.keys(staffUser).length > 0"
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
                                <span class="performer-name">{{
                                    staffUser.name
                                }}</span>
                                <span class="performer-role">{{
                                    staffUser.role != null
                                        ? staffUser.role.name
                                        : "-"
                                }}</span>
                            </div>
                        </div>
                        <div class="performer-appreciation">
                            <span class="appreciation-label">{{
                                $t("hrm_dashboard.appreciations")
                            }}</span>
                            <span class="appreciation-count">{{
                                appreciations
                            }}</span>
                        </div>
                    </div>
                </a-card>
            </div>
        </a-card>
    </div>
</template>

<script>
import { ref, defineComponent, watch, computed } from "vue";
import { CalendarOutlined, StrikethroughOutlined } from "@ant-design/icons-vue";
export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        StrikethroughOutlined,
    },
    setup(props) {
        const workFromHome = ref("");
        const probation = ref("");
        const contract = ref("");
        const fulltime = ref("");
        const totalUsers = ref("");
        const staffUser = ref({});
        const appreciations = ref(0);

        const performer = ref({});
        watch(props, (newVal, oldVal) => {
            fulltime.value = newVal.data.employeeWorkStatusCount.fulltime;
            probation.value = newVal.data.employeeWorkStatusCount.probation;
            workFromHome.value =
                newVal.data.employeeWorkStatusCount.work_from_home;
            contract.value = newVal.data.employeeWorkStatusCount.contract;
            totalUsers.value = newVal.data.stateData.totalActiveEmployee;
            staffUser.value = newVal.data.getEmployeeStatus?.user;
            appreciations.value =
                newVal.data.getEmployeeStatus?.top_user_appreciation_count;
        });

        const workStatusPercentages = computed(() => {
            const totalUsers = props.data?.stateData?.totalActiveUsers || 1;
            const statusCounts = props.data?.employeeWorkStatusCount || {};

            return {
                fulltime: ((statusCounts.fulltime || 0) / totalUsers) * 100,
                contract: ((statusCounts.contract || 0) / totalUsers) * 100,
                probation: ((statusCounts.probation || 0) / totalUsers) * 100,
                work_from_home:
                    ((statusCounts.work_from_home || 0) / totalUsers) * 100,
            };
        });

        const progressGradient = computed(() => {
            const fulltime = workStatusPercentages.value.fulltime;
            const contract = fulltime + workStatusPercentages.value.contract;
            const probation = contract + workStatusPercentages.value.probation;
            const workFromHome = 100;

            return {
                [`0%`]: "#FFC107",
                [`${fulltime}%`]: "#607D8B",
                [`${contract}%`]: "#E91E63",
                [`${probation}%`]: "#4CAF50",
                [`${workFromHome}%`]: "#4CAF50",
            };
        });

        return {
            performer,
            fulltime,
            probation,
            contract,
            workFromHome,
            totalUsers,
            staffUser,
            progressGradient,
            appreciations,
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
    margin-bottom: 10px;
}
.top-performer h4 {
    font-size: 15px;
}
::v-deep(.performer-card) {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.performer-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}
.performer-info {
    display: flex;
    align-items: center;
}
.performer-avatar {
    border-radius: 50%;
    margin-right: 10px;
    width: 50px;
    height: 50px;
}
.performer-text {
    display: flex;
    flex-direction: column;
}
.performer-name {
    font-weight: bold;
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
</style>
