<template>
    <a-card class="leave-card" :bodyStyle="{ height: '100%' }">
        <template #title> {{ $t("hrm_dashboard.attendance_details") }}</template>

        <a-row :gutter="16" align="middle">
            <!-- Leave Statistics (Left Side) -->
            <a-col :span="12">
                <div class="leave-stats">
                    <div
                        v-for="(item, index) in leaveDetails"
                        :key="index"
                        class="stat-item"
                    >
                        <span
                            class="stat-dot"
                            :style="{ backgroundColor: item.color }"
                        ></span>
                        <span class="stat-value">{{ item.value }}</span>
                        <span class="stat-label">{{ item.label }}</span>
                    </div>
                </div>
            </a-col>

            <!-- Pie Chart (Right Side) -->
            <a-col :span="12">
                <div class="chart-container">
                    <DoughnutChart
                        :chartData="chartData"
                        :options="chartOptions"
                        :height="270"
                    />
                </div>
            </a-col>
        </a-row>
    </a-card>
</template>

<script>
import { defineComponent, ref, computed, watch } from "vue";
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);
import { DoughnutChart } from "vue-chart-3";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: ["data"],
    name: "AttendanceChart",
    components: {
        DoughnutChart,
    },
    setup(props) {
        const { t } = useI18n();

        const leaveDetails = ref([
            { label: t("leave.total_attendance"), value: 0, color: "#9C27B0" },
            { label: t("leave.present"), value: 0, color: "#4CAF50" },
            { label: t("leave.leaves"), value: 0, color: "#F44336" },
            { label: t("leave.half_day"), value: 0, color: "#2196F3" },
            { label: t("leave.late_attendance"), value: 0, color: "#FF9800" },
        ]);

        // Chart data for Doughnut Chart
        const chartData = computed(() => ({
            labels: leaveDetails.value.map((item) => item.label),
            datasets: [
                {
                    data: leaveDetails.value.map((item) => item.value),
                    backgroundColor: leaveDetails.value.map((item) => item.color),
                    borderWidth: 0,
                    cutout: "70%",
                },
            ],
        }));

        // Chart options
        const chartOptions = computed(() => ({
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
            },
        }));

        watch(props, (newVal) => {
            if (newVal && newVal.data && newVal.data.attendanceSummary) {
                const summary = newVal.data.attendanceSummary;
                leaveDetails.value = [
                    {
                        label: t("leave.total_attendance"),
                        value: parseInt(Number(summary.total_attendance)),
                        color: "#9C27B0",
                    },
                    {
                        label: t("leave.present"),
                        value: parseInt(Number(summary.total_present)),
                        color: "#4CAF50",
                    },
                    {
                        label: t("leave.leaves"),
                        value: parseInt(Number(summary.total_on_leave)),
                        color: "#F44336",
                    },
                    {
                        label: t("leave.half_day"),
                        value: parseInt(Number(summary.total_half_days)),
                        color: "#2196F3",
                    },
                    {
                        label: t("leave.late_attendance"),
                        value: parseInt(Number(summary.total_late)),
                        color: "#FF9800",
                    },
                ];
            }
        });

        return {
            leaveDetails,
            chartData,
            chartOptions,
        };
    },
});
</script>

<style scoped>
.leave-card {
    max-width: 100%;
    height: 100%;
}

.chart-container {
    display: flex;
    justify-content: right;
    align-items: right;
}

.leave-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 3px;
}

.stat-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.stat-value {
    font-weight: bold;
    font-size: 14px;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

.performance-note {
    margin-top: 15px;
    text-align: left;
    font-size: 14px;
    color: #555;
}
.percentage {
    color: green;
    font-weight: bold;
}
</style>
