<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.warnings`)" class="p-0" />
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
                    {{ $t(`menu.warnings`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <WarningTable
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
                onTableSearch,
                table,
            }"
        >
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('warnings_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("warning.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('warnings_delete') ||
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
                                    permsArray.includes('warnings_view') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-select
                                    v-model:value="filters.user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('warning.user'),
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
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <DateRangePicker
                                    @dateTimeChanged="
                                        (changedDateTime) => {
                                            filters.warning_date =
                                                changedDateTime;
                                            setUrlData();
                                        }
                                    "
                                />
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="16" :lg="6" :xl="6">
                                <a-input-group compact>
                                    <a-input-search
                                        style="width: 100%"
                                        v-model:value="table.searchString"
                                        show-search
                                        @change="onTableSearch"
                                        @search="onTableSearch"
                                        :loading="table.filterLoading"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_search_text',
                                                [$t('warning.title')]
                                            )
                                        "
                                    />
                                </a-input-group>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>
        </template>
    </WarningTable>
</template>

<script>
import { onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import WarningTable from "./warningTable.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,

        AdminPageHeader,
        DateRangePicker,
        UserListDisplay,
        WarningTable,
    },
    setup() {
        const { permsArray } = common();
        const allUsers = ref([]);
        const staffMembersUrl = "users";
        const filters = ref({
            warning_date: [],
            user_id: undefined,
        });

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
            closed,
        };
    },
};
</script>
