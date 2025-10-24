<template>
    <a-card class="performance-card">
        <template #title>
            {{ $t("menu.appreciations") }}
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

        <div class="chart-container">
            <LineChart :chart-data="chartData" :chart-options="chartOptions" />
        </div>
    </a-card>
</template>

<script>
import { ref, onMounted, defineComponent, watch } from "vue";
import {
    Card as ACard,
    Typography as ATypography,
    Tag as ATag,
} from "ant-design-vue";
import { LineChart } from "vue-chart-3";
import common from "@/common/composable/common";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale
);

export default defineComponent({
    props: ["data"],
    components: {
        LineChart,
    },
    setup(props, { emit }) {
        const { dayjs } = common();
        const allUsers = ref([]);
        const filterMonthYear = ref();
        const extraFilters = ref({
            filterMonthYear: undefined,
        });
        const staffMembersUrl =
            "self/users?fields=id,xid,user_type,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary,probation_start_date,probation_end_date,notice_start_date,notice_end_date,duration,shift_id,x_shift_id,shift{id,xid,name},department_id,x_department_id,department{id,xid,name},end_date,annual_ctc,salary_group_id,x_salary_group_id,salaryGroup{id,xid}";

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });
        });
        const chartData = ref({
            labels: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            datasets: [
                {
                    label: "Performance",
                    data: Array(12).fill(0),
                    borderColor: "green",
                    backgroundColor: "rgba(0,200,0,0.2)",
                    fill: true,
                },
            ],
        });

        const chartOptions = ref({
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: function (value) {
                            return Number.isInteger(value) ? value : null;
                        },
                    },
                },
            },
        });

        const filterYearData = (yearValue) => {
            if (yearValue) {
                filterMonthYear.value = dayjs(yearValue).format("YYYY");
            }
            emit("employeeAppriciationData", {
                filterMonthYear: filterMonthYear.value,
                type: "employee_appriciation",
            });
        };

        watch(
            props,
            (newVal) => {
                if (!newVal) return;

                let dataArray = Array(12).fill(0);

                newVal.data?.appriciationByDate.forEach((item) => {
                    if (item.month_number >= 1 && item.month_number <= 12) {
                        dataArray[item.month_number - 1] = item.count;
                    }
                });

                chartData.value = {
                    ...chartData.value,
                    datasets: [
                        {
                            ...chartData.value.datasets[0],
                            data: dataArray,
                        },
                    ],
                };
            },
            { deep: true }
        );

        return {
            chartOptions,
            chartData,
            allUsers,
            filterYearData,
            extraFilters,
        };
    },
});
</script>

<style scoped>
.performance-card {
    width: 100%;
    height: 99%;
}
.stats {
    display: flex;
    align-items: center;
    gap: 6px;
}
.chart-container {
    width: 100%;
}
</style>
