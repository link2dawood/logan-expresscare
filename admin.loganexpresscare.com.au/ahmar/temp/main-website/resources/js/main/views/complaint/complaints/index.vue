<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.complaints`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.offboardings`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.complaints`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <ComplaintTable
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
    >
        <template
            #actions="{
                addItem,
                showSelectedDeleteConfirm,
                setUrlData,
                table,
                onTableSearch,
            }"
        >
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('complaints_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("complaint.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('complaints_delete') ||
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
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="6"
                                :xl="6"
                                v-if="
                                    permsArray.includes('complaints_view') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-select
                                    v-model:value="filters.from_user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('complaint.from_staff'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="title"
                                >
                                    <a-select-option
                                        v-for="user in users"
                                        :key="user.xid"
                                        :value="user.xid"
                                        :title="user.name"
                                        ><user-list-display
                                            :user="user"
                                            whereToShow="select"
                                        />
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="6"
                                :xl="6"
                                v-if="
                                    permsArray.includes('complaints_view') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-select
                                    v-model:value="filters.to_user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('complaint.to_staff'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="title"
                                >
                                    <a-select-option
                                        v-for="user in users"
                                        :key="user.xid"
                                        :value="user.xid"
                                        :title="user.name"
                                        ><user-list-display
                                            :user="user"
                                            whereToShow="select"
                                        />
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <DateRangePicker
                                    @dateTimeChanged="
                                        (changedDateTime) => {
                                            filters.date_time = changedDateTime;
                                            setUrlData();
                                        }
                                    "
                                />
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <a-input-group compact>
                                    <a-input-search
                                        style="width: 100%"
                                        v-model:value="table.searchString"
                                        show-search
                                        @change="onTableSearch"
                                        @search="onTableSearch"
                                        :loading="table.filterLoading"
                                        :placeholder="
                                            $t('common.placeholder_search_text', [
                                                $t('complaint.title'),
                                            ])
                                        "
                                    />
                                </a-input-group>
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
                        v-model:activeKey="filters.complaintStatus"
                        @change="setUrlData"
                    >
                        <a-tab-pane key="pending" :tab="`${$t('common.pending')}`" />
                        <a-tab-pane key="approved" :tab="`${$t('common.approved')}`" />
                        <a-tab-pane key="rejected" :tab="`${$t('common.rejected')}`" />
                    </a-tabs>
                </a-col>
            </a-row>
        </template>
    </ComplaintTable>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    AppstoreOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import ComplaintTable from "./ComplaintTable.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
        DateRangePicker,
        AppstoreOutlined,
        EyeOutlined,
        UserListDisplay,
        ComplaintTable,
    },
    setup() {
        const { permsArray } = common();
        const users = ref([]);
        const userUrl = "users";
        const filters = ref({
            complaintStatus: "pending",
            date_time: [],
            to_user_id: undefined,
            from_user_id: undefined,
        });

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            Promise.all([userPromise]).then(([userResponse]) => {
                users.value = userResponse.data;
            });
        });

        return {
            permsArray,
            filters,
            users,
        };
    },
};
</script>
