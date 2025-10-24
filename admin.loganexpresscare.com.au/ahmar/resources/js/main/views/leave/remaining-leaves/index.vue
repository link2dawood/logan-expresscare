<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.remaining_leaves`)" class="p-0" />
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
                    {{ $t(`menu.remaining_leaves`) }}
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
                            optionFilterProp="title"
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
                        :row-key="(record) => record.xid"
                        :data-source="remainingLeaves"
                        :pagination="false"
                        :loading="tableLoading"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }"> </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../common/composable/hrmManagement";
import common from "../../../../common/composable/common";
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
        const { getMonthNameByNumber } = hrmManagement();
        const columns = [
            {
                title: t("leave.leave_type"),
                dataIndex: "name",
            },
            {
                title: t("menu.remaining_leaves"),
                dataIndex: "remaining_leaves",
            },
        ];
        const remainingLeaves = ref([]);
        const tableLoading = ref(true);
        const { dayjs, user, permsArray } = common();
        const filters = ref({
            year: dayjs(),
            user_id: undefined,
        });
        const selectedYearStartEndMonths = ref([]);
        const allUsers = ref([]);
        const staffMembersUrl = "users";

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
                    `leaves/remaining-leaves?year=${filterYear}&user_id=${filters.value.user_id}`
                )
                .then((response) => {
                    remainingLeaves.value = response.data.data;
                    selectedYearStartEndMonths.value = response.data.month_year;

                    tableLoading.value = false;
                });
        };

        return {
            columns,
            remainingLeaves,
            selectedYearStartEndMonths,
            getMonthNameByNumber,
            filters,

            tableLoading,
            setUrlData,
            allUsers,
            permsArray,
        };
    },
};
</script>
