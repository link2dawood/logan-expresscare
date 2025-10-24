<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.attendance_details`)" class="p-0" />
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
                    {{ $t(`menu.attendance_details`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <AttendanceDetail
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
    >
        <template #actions="{ filterData, monthArrays }">
            <admin-page-filters>
                <a-row :gutter="[16, 16]" align="middle">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
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
                                        $t('common.select_default_text', [
                                            $t('leave.user'),
                                        ])
                                    "
                                    optionFilterProp="title"
                                >
                                    <a-select-option
                                        v-for="allUser in allUsers"
                                        :key="allUser.xid"
                                        :value="allUser.xid"
                                        :title="allUser.name"
                                        ><user-list-display
                                            :user="allUser"
                                            whereToShow="select"
                                        />
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
                                <a-date-picker
                                    v-model:value="filters.year"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('holiday.year'),
                                        ])
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
                                        $t('common.select_default_text', [
                                            $t('holiday.month'),
                                        ])
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
        </template>
        <template
            #card="{
                totalPresentDays,
                workingDays,
                totalOfficeTime,
                formatMinutes,
                clockInDuration,
                clockInDurationPercentage,
                totalLateDaysPercentage,
                totalLateDays,
                halfDayCount,
            }"
        >
            <div class="mb-30">
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="5">
                        <a-card>
                            <a-statistic
                                :title="$t('attendance.present_working_days')"
                                :value="`${totalPresentDays} / ${workingDays} ${$t(
                                    'attendance.days'
                                )}`"
                                :value-style="{
                                    color:
                                        totalPresentDays >= workingDays
                                            ? '#3f8600'
                                            : '#cf1322',
                                }"
                                style="margin-right: 50px"
                            />
                        </a-card>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="5">
                        <a-card>
                            <a-statistic
                                :title="$t('attendance.total_office_time')"
                                :value="
                                    totalOfficeTime > 0
                                        ? formatMinutes(totalOfficeTime)
                                        : '--'
                                "
                                :value-style="{ color: '#3f8600' }"
                            />
                        </a-card>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6">
                        <a-card>
                            <a-statistic
                                :title="$t('attendance.total_worked_time')"
                                :value="
                                    clockInDurationPercentage > 0
                                        ? `${formatMinutes(
                                              clockInDuration
                                          )} (${clockInDurationPercentage}%)`
                                        : '--'
                                "
                                :value-style="{
                                    color:
                                        clockInDuration >= totalOfficeTime
                                            ? '#3f8600'
                                            : '#cf1322',
                                }"
                            />
                        </a-card>
                    </a-col>

                    <a-col :xs="24" :sm="24" :md="8" :lg="5">
                        <a-card>
                            <a-statistic
                                :title="$t('attendance.late')"
                                :value="
                                    totalLateDaysPercentage > 0
                                        ? `${totalLateDays} ${$t(
                                              'attendance.days'
                                          )} (${totalLateDaysPercentage}%)`
                                        : '--'
                                "
                                class="demo-class"
                                :value-style="{ color: '#cf1322' }"
                            />
                        </a-card>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="3">
                        <a-card>
                            <a-statistic
                                :title="$t('attendance.half_days')"
                                :value="`${halfDayCount}`"
                                class="demo-class"
                                :value-style="{ color: '#cf1322' }"
                            />
                        </a-card>
                    </a-col>
                </a-row>
            </div>
        </template>
    </AttendanceDetail>
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
    ClockCircleOutlined,
    CheckCircleOutlined,
    ExclamationCircleOutlined,
    CloseCircleOutlined,
    CalendarOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import hrmManagement from "../../../../common/composable/hrmManagement";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import AttendanceStatus from "../attendance/AttendanceStatus.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import AttendanceDetail from "./AttendanceDetail.vue";

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
        ClockCircleOutlined,
        CheckCircleOutlined,
        ExclamationCircleOutlined,
        CloseCircleOutlined,
        CalendarOutlined,

        AdminPageHeader,
        AttendanceStatus,
        UserListDisplay,
        AttendanceDetail,
    },
    setup() {
        const { dayjs, formatTime, formatDate, user, permsArray } = common();
        const filters = ref({
            month: dayjs().format("MM"),
            year: dayjs(),
            user_id: undefined,
        });
        const allUsers = ref([]);
        const staffMembersUrl = "users?limit=10000";

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;

                filters.value = {
                    ...filters.value,
                    user_id: user.value.xid,
                };
            });
        });

        return {
            filters,
            permsArray,
            allUsers,
        };
    },
};
</script>
