<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.pre_payments`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.payrolls`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.pre_payments`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <PrepaymentTable
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
    >
        <template #actions="{ addItem, showSelectedDeleteConfirm, setUrlData, table }">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('pre_payments_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("pre_payment.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('pre_payments_delete') ||
                                        permsArray.includes('admin'))
                                "
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
                            <a-col
                                v-if="
                                    permsArray.includes('pre_payments_view') ||
                                    permsArray.includes('admin')
                                "
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="8"
                                :xl="8"
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
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                                <a-range-picker
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    v-model:value="filters.dates"
                                    :style="{ width: '100%' }"
                                    @change="setUrlData"
                                />
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>
        </template>
    </PrepaymentTable>
</template>
<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import PrepaymentTable from "./PrepaymentTable.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
        EyeOutlined,
        UserListDisplay,
        PrepaymentTable,
    },
    setup() {
        const { permsArray, appSetting } = common();
        const filters = ref({ dates: [], user_id: undefined });
        const allUsers = ref([]);
        const staffMembersUrl = "users";

        onMounted(() => {
            const usersPromise = axiosAdmin.get(staffMembersUrl);

            Promise.all([usersPromise]).then(([userResponse]) => {
                allUsers.value = userResponse.data;
            });
        });

        return {
            permsArray,
            appSetting,
            filters,
            allUsers,
        };
    },
};
</script>
