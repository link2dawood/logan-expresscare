<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.attendance_summary`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.attendances`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.attendance_summary`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]" align="middle">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <HalfStar :style="{ color: 'green' }" />
                    {{ $t("attendance.half_day") }}
                    <a-divider type="vertical" />
                    <span>
                        <CloseOutlined :style="{ color: 'red' }" />
                        <span :style="{ marginLeft: '10px' }">
                            {{ $t("attendance.absent") }}
                        </span>
                    </span>

                    <a-divider type="vertical" />
                    <span>
                        <CarOutlined :style="{ color: 'red' }" />
                        <span :style="{ marginLeft: '10px' }">
                            {{ $t("attendance.on_leave") }}
                        </span>
                    </span>

                    <a-divider type="vertical" />
                    <span>
                        <CheckOutlined :style="{ color: 'green' }" />
                        <span :style="{ marginLeft: '10px' }">
                            {{ $t("attendance.present") }}
                        </span>
                    </span>
                    <a-divider type="vertical" />
                    <span>
                        <CalendarFilled :style="{ color: 'green' }" />
                        <span :style="{ marginLeft: '10px' }">
                            {{ $t("attendance.holiday") }}
                        </span>
                    </span>
                </a-space>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="6">
                        <a-date-picker
                            v-model:value="filters.year"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            picker="year"
                            @change="filterData"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                        <a-select
                            v-model:value="filters.month"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.month')])
                            "
                            :allowClear="false"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="filterData"
                        >
                            <a-select-option
                                v-for="month in monthArrays"
                                :key="month.name"
                                :value="month.value"
                                :title="month.name"
                            >
                                {{ month.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-for="columnName in 31" :key="columnName">
                                <template v-if="column.dataIndex == columnName">
                                    <a-tooltip
                                        v-if="record[columnName]['status'] == 'present'"
                                    >
                                        <template #title>
                                            {{ $t("leave.clock_in") }} :
                                            {{
                                                record[columnName]["clock_in"]
                                                    ? formatTime(
                                                          record[columnName]["clock_in"]
                                                      )
                                                    : ""
                                            }}<br />
                                            {{ $t("leave.clock_out") }} :
                                            {{
                                                record[columnName]["clock_out"]
                                                    ? formatTime(
                                                          record[columnName]["clock_out"]
                                                      )
                                                    : ""
                                            }}<br />
                                        </template>
                                        <CheckOutlined :style="{ color: 'green' }" />
                                    </a-tooltip>
                                    <a-tooltip
                                        v-else-if="
                                            record[columnName]['status'] == 'half_day' &&
                                            record[columnName]['is_leave']
                                        "
                                    >
                                        <template #title>
                                            {{ record[columnName]["leave_name"] }}
                                            <br />
                                            {{ $t("leave.clock_in") }} :
                                            {{
                                                record[columnName]["clock_in"]
                                                    ? formatTime(
                                                          record[columnName]["clock_in"]
                                                      )
                                                    : ""
                                            }}<br />
                                            {{ $t("leave.clock_out") }} :
                                            {{
                                                record[columnName]["clock_out"]
                                                    ? formatTime(
                                                          record[columnName]["clock_out"]
                                                      )
                                                    : ""
                                            }}<br />
                                        </template>
                                        <HalfStar />
                                    </a-tooltip>
                                    <a-tooltip
                                        v-else-if="
                                            record[columnName]['status'] == 'holiday' &&
                                            record[columnName]['is_holiday']
                                        "
                                    >
                                        <template #title>
                                            {{ record[columnName]["holiday_name"] }}
                                        </template>
                                        <CalendarFilled :style="{ color: 'green' }" />
                                    </a-tooltip>

                                    <a-tooltip
                                        v-else-if="
                                            record[columnName]['status'] == 'absent' &&
                                            record[columnName]['is_leave']
                                        "
                                    >
                                        <template #title>
                                            {{ record[columnName]["leave_name"] }}
                                            :
                                            {{ record[columnName]["leave_reason"] }}
                                        </template>
                                        <CarOutlined :style="{ color: 'red' }" />
                                    </a-tooltip>
                                    <a-tooltip
                                        v-else-if="
                                            record[columnName]['status'] == 'absent'
                                        "
                                    >
                                        <template #title>
                                            {{ $t("attendance.absent") }}
                                        </template>
                                        <CloseOutlined :style="{ color: 'red' }" />
                                    </a-tooltip>
                                    <span v-else>-</span>
                                </template>
                            </template>
                            <template v-if="column.dataIndex == 'total'">
                                <a-tooltip>
                                    <template #title>
                                        {{ $t("attendance.present_working_days") }}
                                    </template>
                                    {{ record.present_days }} /
                                    {{ record.working_days }}
                                </a-tooltip>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, reactive } from "vue";
import {
    CheckOutlined,
    CloseOutlined,
    CalendarFilled,
    CarOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import common from "../../../../../common/composable/common";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import HalfStar from "./HalfStar.vue";

export default {
    components: {
        CheckOutlined,
        CloseOutlined,
        CalendarFilled,
        CarOutlined,

        HalfStar,

        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const { getMonthNameByNumber, monthArrays } = hrmManagement();
        const { dayjs, formatTime, permsArray } = common();
        const filters = ref({
            month: dayjs().format("MM"),
            year: dayjs(),
        });
        const columns = ref([]);
        const currentPage = ref(1);
        const table = reactive({
            data: [],
            pagination: {
                pageSize: 10,
                showSizeChanger: true,
            },
            loading: false,
        });

        onMounted(() => {
            setUrlData();
        });

        const filterData = () => {
            currentPage.value = 1;
            setUrlData();
        };

        const setUrlData = () => {
            table.loading = true;
            var filterMonth = filters.value.month;
            var filterYear = filters.value.year.format("YYYY");
            const limit = table.pagination.pageSize;
            const offset = (currentPage.value - 1) * limit;

            var url = `self/summary?month=${filterMonth}&year=${filterYear}&offset=${offset}&limit=${limit}`;

            axiosAdmin.get(url).then((response) => {
                const columnFields = [
                    {
                        title: "",
                        dataIndex: "name",
                    },
                ];

                forEach(response.data.columns, (columnValue) => {
                    columnFields.push({
                        title: columnValue,
                        dataIndex: columnValue,
                    });
                });

                columnFields.push({
                    title: t("common.total"),
                    dataIndex: "total",
                });

                const pagination = { ...table.pagination };
                pagination.total = response.data.meta.paging.total;
                table.data = [...response.data.data];
                table.pagination = pagination;
                columns.value = columnFields;
                table.loading = false;
            });
        };

        const handleTableChange = (pagination, filters, sorter) => {
            const pager = { ...table.pagination };
            pager.current = pagination.current;
            pager.pageSize = pagination.pageSize;
            table.pagination = pager;
            currentPage.value = pagination.current;

            setUrlData();
        };

        return {
            table,
            currentPage,
            handleTableChange,
            columns,
            getMonthNameByNumber,
            filters,
            filterData,
            monthArrays,
            permsArray,

            setUrlData,
            formatTime,
        };
    },
};
</script>
