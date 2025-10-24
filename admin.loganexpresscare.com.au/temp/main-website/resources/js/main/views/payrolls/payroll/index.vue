<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.payrolls`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-row align="middle" justify="space-between">
                <a-col>
                    <a-breadcrumb separator="-" style="font-size: 12px">
                        <a-breadcrumb-item>
                            <router-link
                                :to="{ name: 'admin.dashboard.index' }"
                            >
                                {{ $t(`menu.dashboard`) }}
                            </router-link>
                        </a-breadcrumb-item>
                        <a-breadcrumb-item>
                            {{ $t(`menu.payrolls`) }}
                        </a-breadcrumb-item>
                        <a-breadcrumb-item>
                            {{ $t(`menu.payrolls`) }}
                        </a-breadcrumb-item>
                    </a-breadcrumb>
                </a-col>
                <a-col>
                    <a-switch
                        v-model:checked="salaryVisibleAll"
                        :checked-children="$t('common.visible')"
                        :un-checked-children="$t('common.hidden')"
                        :style="{ top: '-20px', marginRight: '2px' }"
                    />
                </a-col>
            </a-row>
        </template>
    </AdminPageHeader>

    <PayrollTable
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
        :salaryVisibleAll="salaryVisibleAll"
    >
        <template
            #actions="{
                regenerate,
                showSelectedDeleteConfirm,
                setUrlData,
                table,
                generate,
                spinning,
            }"
        >
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('payrolls_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="regenerate"
                                        v-if="table.selectedRowKeys.length > 0"
                                        :disabled="filters.month == undefined"
                                    >
                                        <ReloadOutlined />
                                        {{ $t("payroll.regenerate") }}
                                    </a-button>
                                    <a-button
                                        type="primary"
                                        @click="generate"
                                        v-else
                                        :disabled="filters.month == undefined"
                                    >
                                        <SendOutlined />
                                        {{ $t("payroll.generate") }}
                                    </a-button>
                                    <a-spin :spinning="spinning"></a-spin>
                                </a-space>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('payrolls_delete') ||
                                        permsArray.includes('admin'))
                                "
                                type="primary"
                                @click="showSelectedDeleteConfirm"
                                danger
                            >
                                <template #icon><DeleteOutlined /></template>
                                {{ $t("common.delete") }}
                            </a-button>
                            <template
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('payrolls_edit') ||
                                        permsArray.includes('admin'))
                                "
                            >
                                <PayrollStatus
                                    @onSuccess="setUrlData"
                                    :payrolls="table.selectedRowKeys"
                                />
                            </template>
                        </a-space>
                    </a-col>

                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col
                                v-if="
                                    permsArray.includes('payrolls_view') ||
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
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('payroll.user_id'),
                                        ])
                                    "
                                    :allowClear="true"
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
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
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
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                                <a-select
                                    v-model:value="filters.month"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('holiday.month'),
                                        ])
                                    "
                                    :allowClear="true"
                                    style="width: 100%"
                                    optionFilterProp="title"
                                    show-search
                                    @change="setUrlData"
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
        <template #alert>
            <a-alert
                v-if="
                    permsArray.includes('basic_salaries_add') ||
                    permsArray.includes('basic_salaries_edit') ||
                    permsArray.includes('admin')
                "
                class="mb-20"
                :message="$t('payroll.setup_basic_salary_to_generate_payroll')"
                type="info"
                show-icon
            >
                <template #action>
                    <a-button
                        size="small"
                        type="primary"
                        @click="
                            $router.push({ name: 'admin.basic_salaries.index' })
                        "
                    >
                        <DollarCircleOutlined />
                        {{ $t("payroll.basic_salary_setup") }}
                    </a-button>
                </template>
            </a-alert>
        </template>
    </PayrollTable>
</template>
<script>
import { onMounted, ref } from "vue";
import {
    DeleteOutlined,
    CloseOutlined,
    CheckOutlined,
    SendOutlined,
    ReloadOutlined,
    EyeOutlined,
    DollarCircleOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import dayjs from "dayjs";
import hrmManagement from "../../../../common/composable/hrmManagement";
import PayrollStatus from "./PayrollStatus.vue";
import PayrollTable from "./PayrollTable.vue";
import UserListDisplay from "@/common/components/user/UserListDisplay.vue";

export default {
    components: {
        DeleteOutlined,
        SendOutlined,
        ReloadOutlined,
        DollarCircleOutlined,
        DownloadOutlined,
        AdminPageHeader,
        CloseOutlined,
        CheckOutlined,
        PayrollStatus,
        EyeOutlined,
        UserListDisplay,
        PayrollTable,
    },
    setup() {
        const { permsArray } = common();
        const filters = ref({
            month: dayjs().format("MM"),
            year: dayjs(),
            user_id: undefined,
        });
        const allUsers = ref([]);
        const staffMembersUrl = "users";
        const { getMonthNameByNumber, monthArrays } = hrmManagement();
        const salaryVisibleAll = ref(false);

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });
        });

        return {
            permsArray,
            allUsers,
            filters,
            monthArrays,
            salaryVisibleAll,
        };
    },
};
</script>
