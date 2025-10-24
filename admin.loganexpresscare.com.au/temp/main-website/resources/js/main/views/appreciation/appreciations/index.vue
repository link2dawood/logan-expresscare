<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.appreciations`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.appreciations`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.appreciations`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <AppreciationTable
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
    >
        <template
            #actions="{ addItem, selectedRowKeys, showSelectedDeleteConfirm, setUrlData }"
        >
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('appreciations_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("appreciation.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    selectedRowKeys.length > 0 &&
                                    (permsArray.includes('appreciations_delete') ||
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
                                    permsArray.includes('appreciations_view') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-select
                                    v-model:value="filters.user_id"
                                    @change="setUrlData()"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('appreciation.user'),
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
                                    >
                                        <UserListDisplay
                                            :user="allUser"
                                            whereToShow="select"
                                        />
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="filters.award_id"
                                    @change="setUrlData()"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('appreciation.award'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option
                                        v-for="award in awards"
                                        :key="award.xid"
                                        :value="award.xid"
                                        :title="award.name"
                                    >
                                        {{ award.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                                <DateRangePicker
                                    @dateTimeChanged="
                                        (changedDateTime) => {
                                            filters.date = changedDateTime;
                                            setUrlData();
                                        }
                                    "
                                />
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>
        </template>
    </AppreciationTable>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import AppreciationTable from "./AppreciationTable.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        DownloadOutlined,

        AdminPageHeader,
        DateRangePicker,
        UserListDisplay,
        AppreciationTable,
    },
    setup() {
        const { permsArray } = common();
        const allUsers = ref([]);
        const awards = ref([]);
        const staffMembersUrl = "users";
        const awardsUrl = "awards";
        const filters = ref({
            award_id: undefined,
            date: [],
            user_id: undefined,
        });

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const awardPromise = axiosAdmin.get(awardsUrl);
            Promise.all([staffMemberPromise, awardPromise]).then(
                ([staffMemberResponse, awardResponse]) => {
                    allUsers.value = staffMemberResponse.data;
                    awards.value = awardResponse.data;
                }
            );
        });

        return {
            allUsers,
            awards,
            filters,
            permsArray,
        };
    },
};
</script>
