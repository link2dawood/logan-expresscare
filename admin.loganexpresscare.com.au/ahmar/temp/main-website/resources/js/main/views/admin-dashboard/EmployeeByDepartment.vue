<template>
    <div class="employee-chart-container">
        <a-card class="chart-card" :bodyStyle="{ padding: '19px' }">
            <template #title>
                <TeamOutlined />
                {{ $t("hrm_dashboard.employees_by_department") }}
            </template>
            <BarChart ref="chartRef" :chartData="chartData" :options="chartOptions" />
        </a-card>
    </div>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { Chart, registerables } from "chart.js";
import { BarChart } from "vue-chart-3";
import { TeamOutlined } from "@ant-design/icons-vue";

Chart.register(...registerables);

export default defineComponent({
    props: ["data"],
    components: {
        TeamOutlined,
        BarChart,
    },
    setup(props) {
        const { t } = useI18n();
        const chartOptions = ref({
            responsive: true,
            scales: {
                x: {
                    grid: {
                        borderDash: [5, 5],
                    },
                    ticks: {
                        color: "#888",
                        stepSize: 1,
                        beginAtZero: true,
                        precision: 0,
                    },
                },
                y: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: "#555",
                        stepSize: 1,
                        beginAtZero: true,
                        precision: 0,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        });
        const chartData = ref({});

        watch(
            () => props.data,
            (newVal, oldVal) => {
                chartData.value = {
                    labels: newVal.departmentByNameCount
                        ? newVal.departmentByNameCount.departments
                        : [],
                    datasets: [
                        {
                            data: newVal.departmentByNameCount
                                ? newVal.departmentByNameCount.employee_counts
                                : [],
                            backgroundColor: "rgba(255, 99, 71, 0.8)",
                            borderRadius: 5,
                            barThickness: 15,
                            label: t("hrm_dashboard.number_of_employees"),
                        },
                    ],
                };
            }
        );

        return {
            chartData,
            chartOptions,
        };
    },
});
</script>

<style scoped>
.chart-card {
    border-radius: 4px;
}
</style>
