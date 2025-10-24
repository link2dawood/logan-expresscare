<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.unpaid_leaves`)" class="p-0" />
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
                    {{ $t(`menu.unpaid_leaves`) }}
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
                    {{ getMonthNameByNumber(selectedYearStartEndMonths.start_month) }}
                    {{ selectedYearStartEndMonths.start_year }} -
                    {{ getMonthNameByNumber(selectedYearStartEndMonths.end_month) }}
                    {{ selectedYearStartEndMonths.end_year }}
                </a-typography-text>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
                        <a-date-picker
                            v-model:value="filters.year"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
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
                        :data-source="unpaidLeaves"
                        :pagination="false"
                        :loading="tableLoading"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'name'">
                                {{
                                    record.month ? getMonthNameByNumber(record.month) : ""
                                }}
                                {{ record.year ? record.year : "" }}
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../../../common/composable/common";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const { getMonthNameByNumber } = hrmManagement();
        const columns = [
            {
                title: t("leave.leave_type"),
                dataIndex: "name",
            },
            {
                title: t("menu.unpaid_leaves"),
                dataIndex: "unpaid_leaves",
            },
        ];
        const unpaidLeaves = ref([]);
        const tableLoading = ref(true);
        const { dayjs } = common();
        const filters = ref({
            year: dayjs(),
        });
        const selectedYearStartEndMonths = ref([]);

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            tableLoading.value = true;
            var filterYear = filters.value.year.format("YYYY");

            axiosAdmin.get(`self/unpaid-leaves?year=${filterYear}`).then((response) => {
                unpaidLeaves.value = response.data.data;
                selectedYearStartEndMonths.value = response.data.month_year;

                tableLoading.value = false;
            });
        };

        return {
            columns,
            unpaidLeaves,
            filters,
            getMonthNameByNumber,
            selectedYearStartEndMonths,
            tableLoading,
            setUrlData,
        };
    },
};
</script>
