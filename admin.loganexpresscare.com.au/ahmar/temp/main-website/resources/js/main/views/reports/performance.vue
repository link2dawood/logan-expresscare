<template>
    <a-card class="performance-card" title="Performance">
        <template #title>
            {{ $t("menu.appreciations") }}
        </template>
        <template #extra>
            <a-space>
                <span>
                    <a-date-picker
                        v-model:value="filters.year"
                        :placeholder="
                            $t('common.select_default_text', [
                                $t('holiday.year'),
                            ])
                        "
                        picker="year"
                        style="width: 100%"
                        :allowClear="false"
                        @change="filterYearData(filters.year)"
                    />
                </span>
                <span>
                    <a-select
                        v-model:value="filters.user_id"
                        @change="() => filterYearData()"
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
            </a-space>
        </template>

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
import { useI18n } from "vue-i18n";
export default defineComponent({
    props: ["data"],
    components: {
        LineChart,
    },
    setup(props, { emit }) {
        const { user, dayjs } = common();
        const { t } = useI18n();
        const allUsers = ref([]);
        const filterMonthYear = ref();
        const filters = ref({
            user_id: undefined,
            year: undefined,
        });
        const staffMembersUrl =
            "users?fields=id,xid,user_type,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary,probation_start_date,probation_end_date,notice_start_date,notice_end_date,duration,shift_id,x_shift_id,shift{id,xid,name},department_id,x_department_id,department{id,xid,name},end_date,annual_ctc,salary_group_id,x_salary_group_id,salaryGroup{id,xid}";

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
                    label: t("appreciation.appreciations_count"),
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
                    suggestedMin: 0,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: function (value) {
                            return Number.isInteger(value) ? value : "";
                        },
                    },
                },
            },
        });

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

        const filterYearData = (yearValue) => {
            if (yearValue) {
                filterMonthYear.value = dayjs(yearValue).format("YYYY");
            }
            emit("employeeAppriciationData", {
                user_id: filters.value.user_id,
                filterMonthYear: filterMonthYear.value,
                type: "employee_appriciation",
            });
        };

        return {
            chartOptions,
            chartData,
            filterYearData,
            filters,
            allUsers,
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
