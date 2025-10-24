<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.terminations`)" class="p-0" />
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
                    {{ $t(`menu.terminations`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('terminations_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("termination.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('terminations_delete') ||
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
                                    v-model:value="extraFilters.user_id"
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
                                            extraFilters.start_date = changedDateTime;
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
                                            $t('common.placeholder_search_text', [
                                                $t('termination.title'),
                                            ])
                                        "
                                    />
                                </a-input-group>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>

            <admin-page-table-content>
                <AddEdit
                    :addEditType="addEditType"
                    :visible="addEditVisible"
                    :url="addEditUrl"
                    @addEditSuccess="addEditSuccess"
                    @closed="onCloseAddEdit"
                    :formData="formData"
                    :data="viewData"
                    :pageTitle="pageTitle"
                    :successMessage="successMessage"
                    @addListSuccess="reSetFormData"
                />

                <a-row>
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table
                                :row-selection="{
                                    selectedRowKeys: table.selectedRowKeys,
                                    onChange: onRowSelectChange,
                                    getCheckboxProps: (record) => ({
                                        disabled: false,
                                        name: record.xid,
                                    }),
                                }"
                                :columns="columns"
                                :row-key="(record) => record.xid"
                                :data-source="table.data"
                                :pagination="table.pagination"
                                :loading="table.loading"
                                @change="handleTableChange"
                                bordered
                                size="middle"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'title'">
                                        <a-typography-link
                                            type="link"
                                            @click="viewTermination(record)"
                                        >
                                            {{ record.title }}
                                        </a-typography-link>
                                    </template>
                                    <template v-if="column.dataIndex === 'user_id'">
                                        <a-button
                                            type="link"
                                            @click="openUserView(record)"
                                        >
                                            <UserInfo :user="record.user" />
                                        </a-button>
                                    </template>
                                    <template v-if="column.dataIndex === 'start_date'">
                                        {{
                                            formatDateTime(
                                                record.start_date
                                                    ? record.start_date
                                                    : "-"
                                            )
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'end_date'">
                                        {{
                                            formatDateTime(
                                                record.end_date ? record.end_date : "-"
                                            )
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <template v-if="column.dataIndex === 'action'">
                                            <a-button
                                                type="primary"
                                                @click="viewTermination(record)"
                                                style="margin-left: 4px"
                                            >
                                                <template #icon><EyeOutlined /></template>
                                            </a-button>
                                        </template>
                                        <a-button
                                            v-if="
                                                permsArray.includes(
                                                    'terminations_edit'
                                                ) || permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="editItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><EditOutlined /></template>
                                        </a-button>
                                        <a-button
                                            v-if="
                                                permsArray.includes(
                                                    'terminations_delete'
                                                ) || permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="showDeleteConfirm(record.xid)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><DeleteOutlined /></template>
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
                <View
                    :data="terminationData"
                    :visible="visibleTermination"
                    @close="closed"
                    :pageTitle="$t('termination.termination_details')"
                />
            </admin-page-table-content>
            <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
        </a-col>
    </a-row>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import View from "./View.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        DateRangePicker,
        View,
        UserInfo,
        UserListDisplay,
    },
    setup() {
        const { permsArray, formatDateTime } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            multiDimensalObjectColumns,
        } = fields();
        const crudVariables = crud();
        const { t } = useI18n();
        const allUsers = ref([]);
        const staffMembersUrl = "users";
        const extraFilters = ref({
            user_id: undefined,
            start_date: [],
        });
        const terminationData = ref({});
        const visibleTermination = ref(false);
        const userOpen = ref(false);
        const userId = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.x_user_id;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        const viewTermination = (item) => {
            visibleTermination.value = true;
            terminationData.value = item;
        };

        const closed = () => {
            visibleTermination.value = false;
        };

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "termination";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
            crudVariables.multiDimensalObjectColumns.value = {
                ...multiDimensalObjectColumns,
            };

            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            hashableColumns,
            setUrlData,
            allUsers,
            staffMembersUrl,
            extraFilters,
            formatDateTime,
            terminationData,
            visibleTermination,
            viewTermination,
            closed,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
