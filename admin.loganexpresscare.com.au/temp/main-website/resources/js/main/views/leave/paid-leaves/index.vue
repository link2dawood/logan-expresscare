<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.paid_leaves`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.leaves`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.paid_leaves`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]" align="middle">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-typography-text
                    v-if="
                        selectedYearStartEndMonths &&
                        selectedYearStartEndMonths.start_month
                    "
                    strong
                >
                    {{
                        getMonthNameByNumber(
                            selectedYearStartEndMonths.start_month
                        )
                    }}
                    {{ selectedYearStartEndMonths.start_year }} -
                    {{
                        getMonthNameByNumber(
                            selectedYearStartEndMonths.end_month
                        )
                    }}
                    {{ selectedYearStartEndMonths.end_year }}
                </a-typography-text>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col
                        :xs="24"
                        :sm="24"
                        :md="12"
                        :lg="6"
                        :xl="6"
                        v-if="
                            permsArray.includes('admin') ||
                            permsArray.includes('leaves_view')
                        "
                    >
                        <a-select
                            v-model:value="filters.user_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('leave.user'),
                                ])
                            "
                        >
                            <a-select-option
                                v-for="allUser in allUsers"
                                :key="allUser.xid"
                                :value="allUser.xid"
                                :title="allUser.name"
                            >
                                {{ allUser.name }}
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
                            @change="setUrlData"
                            style="width: 100%"
                            :allowClear="false"
                        />
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
                        :row-selection="{
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.month"
                        :data-source="paidLeaves"
                        :pagination="false"
                        :loading="tableLoading"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'month'">
                                {{
                                    record.month
                                        ? getMonthNameByNumber(record.month)
                                        : ""
                                }}
                                {{ record.year ? record.year : "" }}
                            </template>

                            <template v-if="column.dataIndex === 'leave_types'">
                                <div
                                    v-if="
                                        record.leave_counts &&
                                        Object.keys(record.leave_counts).length
                                    "
                                >
                                    <div
                                        v-for="(
                                            value, key, index
                                        ) in record.leave_counts"
                                        :key="key"
                                        class="lt-row"
                                        :class="{
                                            'no-border':
                                                index ===
                                                Object.keys(record.leave_counts)
                                                    .length -
                                                    1,
                                        }"
                                    >
                                        <div class="lt-key">{{ key }}</div>
                                        <div class="lt-val">{{ value }}</div>
                                    </div>
                                </div>
                                <div v-else>-</div>
                            </template>
                        </template>
                        <template #summary>
                            <a-table-summary fixed>
                                <a-table-summary-row>
                                    <a-table-summary-cell
                                        :index="0"
                                    ></a-table-summary-cell>
                                    <a-table-summary-cell
                                        :index="1"
                                    ></a-table-summary-cell>
                                    <a-table-summary-cell :index="2">
                                        <div
                                            v-for="(total, type) in leaveTotals"
                                            :key="type"
                                            class="lt-row"
                                            style="
                                                font-weight: bold;
                                                font-size: 14px;
                                            "
                                        >
                                            <div class="lt-key">
                                                {{ $t(`common.total`) }}
                                                {{ type }}&nbsp;-
                                            </div>
                                            <div class="lt-val">
                                                {{ total }}
                                            </div>
                                        </div>
                                    </a-table-summary-cell>
                                </a-table-summary-row>
                            </a-table-summary>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, computed } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import hrmManagement from "../../../../common/composable/hrmManagement";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const { getMonthNameByNumber, monthArrays } = hrmManagement();
        const columns = [
            {
                title: t("common.month"),
                dataIndex: "month",
            },
            {
                title: t("leave.leave_type"),
                dataIndex: "leave_types",
            },
        ];
        const paidLeaves = ref([]);
        const tableLoading = ref(true);
        const { dayjs, user, permsArray } = common();
        const filters = ref({
            year: dayjs(),
            user_id: undefined,
        });
        const selectedYearStartEndMonths = ref([]);
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

                setUrlData();
            });
        });

        const setUrlData = () => {
            tableLoading.value = true;
            var filterYear = filters.value.year.format("YYYY");
            axiosAdmin
                .get(
                    `leaves/paid-leaves?year=${filterYear}&user_id=${filters.value.user_id}`
                )
                .then((response) => {
                    paidLeaves.value = response.data.data;
                    selectedYearStartEndMonths.value = response.data.month_year;

                    tableLoading.value = false;
                });
        };
        const leaveTotals = computed(() => {
            const totals = {};
            paidLeaves.value.forEach((row) => {
                Object.entries(row.leave_counts || {}).forEach(
                    ([type, value]) => {
                        totals[type] = (totals[type] || 0) + value;
                    }
                );
            });
            return totals;
        });

        return {
            columns,
            paidLeaves,
            filters,
            getMonthNameByNumber,
            selectedYearStartEndMonths,
            leaveTotals,
            tableLoading,
            setUrlData,
            allUsers,
            permsArray,
            monthArrays,
        };
    },
};
</script>
<style scoped>
.lt-row {
    display: grid;
    grid-template-columns: 80px auto;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding: 4px 0;
}
.lt-row.no-border {
    border-bottom: none;
}
.lt-key {
    white-space: nowrap;
}
.lt-val {
    text-align: left;
}
</style>
