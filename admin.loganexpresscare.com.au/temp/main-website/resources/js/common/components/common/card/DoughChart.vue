<template>
    <div>
        <div class="doughnut-chart-container">
            <DoughnutChart
                ref="chartRef"
                :chartData="testData"
                :options="options"
                :height="290"
            />
        </div>
        <!-- Total Attendance Section -->
        <div class="total-attendance">
            <a-typography strong
                >{{ $t("hrm_dashboard.total_attendance") }}:
                {{ totalUsers }}</a-typography
            >
        </div>
        <!-- Status List -->
        <ul class="status-list">
            <li>
                <Badge color="green" />
                <a-typography class="status-text">{{
                    $t("hrm_dashboard.present")
                }}</a-typography>
                <a-typography strong class="status-percentage"
                    >{{ percentages.present }}% ({{
                        employeeNumber.present
                    }})</a-typography
                >
            </li>
            <li>
                <Badge color="red" />
                <a-typography class="status-text">{{
                    $t("hrm_dashboard.absent")
                }}</a-typography>
                <a-typography strong class="status-percentage"
                    >{{ percentages.absent }}% ({{
                        employeeNumber.absent
                    }})</a-typography
                >
            </li>
            <li>
                <Badge color="orange" />
                <a-typography class="status-text">{{
                    $t("hrm_dashboard.not_marked")
                }}</a-typography>
                <a-typography strong class="status-percentage"
                    >{{ percentages.not_marked }}% ({{
                        employeeNumber.not_marked
                    }})</a-typography
                >
            </li>
        </ul>
    </div>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);
import { DoughnutChart } from "vue-chart-3";
import { Badge, Avatar, Divider } from "ant-design-vue";

export default defineComponent({
    props: ["data"],
    components: {
        DoughnutChart,
        Badge,
        Avatar,
        Divider,
    },
    setup(props) {
        const chartRef = ref();

        const options = ref({
            responsive: true,
            plugins: {
                legend: {
                    position: "bottom",
                },
                title: {
                    display: false,
                    text: "Today's Attendence Position",
                },
            },
            rotation: -90,
            circumference: 180,
        });

        const testData = ref({});
        const totalUsers = ref();
        const percentages = ref({});
        const employeeNumber = ref({});

        watch(props, (newVal, oldVal) => {
            testData.value = {
                labels: newVal.data.todaysAttendence
                    ? newVal.data.todaysAttendence.labels
                    : [],
                datasets: [
                    {
                        data: newVal.data.todaysAttendence
                            ? newVal.data.todaysAttendence.values
                            : [],
                        backgroundColor: newVal.data.todaysAttendence
                            ? newVal.data.todaysAttendence.colors
                            : [],
                        borderWidth: 0,
                        cutout: "80%",
                    },
                ],
            };
            totalUsers.value = newVal.data.todaysAttendence.total_users;
            percentages.value = newVal.data.todaysAttendence.percentages;
            employeeNumber.value = newVal.data.todaysAttendence.employeeNumber;
        });

        return {
            chartRef,
            testData,
            options,
            totalUsers,
            percentages,
            employeeNumber,
        };
    },
});
</script>
<style>
div {
    padding: 0;
    margin: 0;
}

.doughnut-chart-container {
    margin: 0;
    padding: 0;
}

.status-list {
    list-style: none;
    padding: 6px;
    margin: 10px 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
    color: var(--ant-text-color);
}

.status-list li {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.status-text {
    flex-grow: 1;
    margin-left: 5px;
    font-size: 14px;
    color: var(--ant-text-color);
}

.status-percentage {
    font-size: 14px;
    color: var(--ant-text-color);
}
.total-attendance {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    font-weight: bold;
    font-size: 15px;
    color: #302f2f;
}
</style>
