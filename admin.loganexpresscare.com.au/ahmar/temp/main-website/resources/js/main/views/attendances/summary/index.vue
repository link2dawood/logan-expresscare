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
                    <a-col
                        v-if="
                            permsArray.includes('attendances_view') ||
                            permsArray.includes('admin')
                        "
                        :xs="24"
                        :sm="24"
                        :md="12"
                        :lg="6"
                        :xl="6"
                    >
                        <a-select
                            v-model:value="filters.user_id"
                            @change="filterData"
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
                            >
                                <user-list-display :user="allUser" whereToShow="select" />
                            </a-select-option>
                        </a-select>
                    </a-col>
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
                            <template v-if="column.dataIndex == 'name'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
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
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
</template>
<script>
import { onMounted, ref, reactive } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    CheckOutlined,
    CloseOutlined,
    CalendarFilled,
    ArrowRightOutlined,
    CalculatorOutlined,
    CarOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import hrmManagement from "../../../../common/composable/hrmManagement";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import HalfStar from "./HalfStar.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        CheckOutlined,
        CloseOutlined,
        CalendarFilled,
        ArrowRightOutlined,
        CarOutlined,
        CalculatorOutlined,
        HalfStar,
        UserInfo,
        AdminPageHeader,
        UserListDisplay,
    },
    setup() {
        const { t } = useI18n();
        const { getMonthNameByNumber, monthArrays } = hrmManagement();
        const { dayjs, formatTime, permsArray } = common();
        const filters = ref({
            month: dayjs().format("MM"),
            year: dayjs(),
            user_id: undefined,
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
        const allUsers = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const userOpen = ref(false);
        const userId = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.user.xid;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });

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

            var url = `attendances/summary?month=${filterMonth}&year=${filterYear}&offset=${offset}&limit=${limit}`;

            if (filters.value.user_id) {
                url += `&user_id=${filters.value.user_id}`;
            }
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
            allUsers,

            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
