<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.leaves`)" class="p-0" />
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
                    {{ $t(`menu.leaves`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <LeavesTable
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
    >
        <template
            #actions="{ addItem, showSelectedDeleteConfirm, setUrlData, table }"
        >
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <a-button type="primary" @click="addItem">
                                <PlusOutlined />
                                {{ $t("leave.add") }}
                            </a-button>
                            <a-button
                                v-if="table.selectedRowKeys.length > 0"
                                type="primary"
                                @click="showSelectedDeleteConfirm"
                                danger
                            >
                                <template #icon><DeleteOutlined /></template>
                                {{ $t("common.delete") }}
                            </a-button>
                        </a-space>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                <a-date-picker
                                    v-model:value="filters.attendance_month"
                                    @change="setUrlData"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('leave.leave_month'),
                                        ])
                                    "
                                    valueFormat="YYYY-MM"
                                    picker="month"
                                    style="width: 100%"
                                    :allowClear="true"
                                />
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                                <a-select
                                    v-model:value="filters.leave_type_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('leave.leave_type'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option
                                        v-for="leaveType in leaveTypes"
                                        :key="leaveType.xid"
                                        :value="leaveType.xid"
                                        :title="leaveType.name"
                                    >
                                        {{ leaveType.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="8"
                                :xl="8"
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
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>
        </template>
        <template #tabs="{ setUrlData }">
            <a-row>
                <a-col :span="24">
                    <a-tabs
                        v-model:activeKey="filters.status"
                        @change="setUrlData"
                    >
                        <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                        <a-tab-pane
                            key="pending"
                            :tab="`${$t('common.pending')}`"
                        />
                        <a-tab-pane
                            key="approved"
                            :tab="`${$t('common.approved')}`"
                        />
                        <a-tab-pane
                            key="rejected"
                            :tab="`${$t('common.rejected')}`"
                        />
                    </a-tabs>
                </a-col>
            </a-row>
        </template>
    </LeavesTable>
</template>
<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    CloseOutlined,
    CheckOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import LeavesTable from "./leavesTable.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
        CloseOutlined,
        CheckOutlined,
        EyeOutlined,

        LeavesTable,
        UserListDisplay,
    },
    setup() {
        const { permsArray, formatDate, user } = common();
        const filters = ref({
            status: "all",
            user_id: undefined,
            leave_type_id: undefined,
            attendance_month: undefined,
        });
        const allUsers = ref([]);
        const staffMembersUrl = "users";
        const leaveTypes = ref([]);
        const leaveTypesUrl = "leave-types";

        onMounted(() => {
            const leaveTypesPromise = axiosAdmin.get(leaveTypesUrl);
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise, leaveTypesPromise]).then(
                ([staffMemberResponse, leaveTypesResponse]) => {
                    allUsers.value = staffMemberResponse.data;
                    leaveTypes.value = leaveTypesResponse.data;
                }
            );
        });

        return {
            permsArray,
            allUsers,
            filters,
            leaveTypes,
        };
    },
};
</script>
