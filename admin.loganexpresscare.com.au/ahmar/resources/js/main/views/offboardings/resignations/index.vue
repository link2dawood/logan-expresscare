<template>
    <div>
        <AdminPageHeader>
            <template #header>
                <a-page-header :title="$t(`menu.resignations`)" class="p-0" />
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
                        {{ $t(`menu.resignations`) }}
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
                                        permsArray.includes('resignations_create') ||
                                        permsArray.includes('admin')
                                    "
                                >
                                    <a-button type="primary" @click="addItem">
                                        <PlusOutlined />
                                        {{ $t("resignation.add") }}
                                    </a-button>
                                </template>
                                <a-button
                                    v-if="
                                        table.selectedRowKeys.length > 0 &&
                                        (permsArray.includes('resignations_delete') ||
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
                                        permsArray.includes('resignations_view') ||
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
                                                $t('resignation.user'),
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
                                                    $t('resignation.title'),
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
                            <a-tabs
                                v-model:activeKey="extraFilters.status"
                                @change="setUrlData"
                            >
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
                                    <template #bodyCell="{ column, record, text }">
                                        <template v-if="column.dataIndex === 'title'">
                                            <a-typography-link
                                                type="link"
                                                @click="viewResignation(record)"
                                            >
                                                {{ record.title }}
                                            </a-typography-link>
                                        </template>
                                        <template v-if="column.dataIndex == 'user_id'">
                                            <a-button
                                                type="link"
                                                @click="openUserView(record)"
                                            >
                                                <UserInfo :user="record.user" />
                                            </a-button>
                                        </template>
                                        <template v-if="column.dataIndex === 'status'">
                                            <a-tag :color="resignationColors[text]">
                                                {{ $t(`common.${text}`) }}
                                            </a-tag>
                                        </template>
                                        <template
                                            v-if="column.dataIndex === 'start_date'"
                                        >
                                            <span v-if="record && record.start_date">
                                                {{ formatDateTime(record.start_date) }}
                                            </span>
                                            <span v-else>-</span>
                                        </template>
                                        <template v-if="column.dataIndex === 'end_date'">
                                            <span v-if="record && record.end_date">
                                                {{ formatDateTime(record.end_date) }}
                                            </span>
                                            <span v-else>-</span>
                                        </template>
                                        <template v-if="column.dataIndex === 'action'">
                                            <a-tooltip placement="topLeft">
                                                <template #title>
                                                    <span>{{
                                                        $t("common.approve/reject")
                                                    }}</span>
                                                </template>
                                                <a-button
                                                    v-if="
                                                        permsArray.includes('admin') &&
                                                        record.status == 'pending'
                                                    "
                                                    type="primary"
                                                    @click="updateStatus(record)"
                                                    style="margin-left: 4px"
                                                >
                                                    <template #icon
                                                        ><AppstoreOutlined
                                                    /></template>
                                                </a-button>
                                            </a-tooltip>

                                            <a-button
                                                type="primary"
                                                @click="viewResignation(record)"
                                                style="margin-left: 4px"
                                            >
                                                <template #icon><EyeOutlined /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    permsArray.includes(
                                                        'resignations_edit'
                                                    ) || permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="editItem(record)"
                                                style="margin-left: 4px"
                                            >
                                                <template #icon
                                                    ><EditOutlined
                                                /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    permsArray.includes(
                                                        'resignations_delete'
                                                    ) || permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="showDeleteConfirm(record.xid)"
                                                style="margin-left: 4px"
                                            >
                                                <template #icon
                                                    ><DeleteOutlined
                                                /></template>
                                            </a-button>
                                        </template>
                                    </template>
                                </a-table>
                            </div>
                        </a-col>
                    </a-row>
                    <UpdateStatus
                        :data="newFormData"
                        :visible="visibleStatus"
                        @close="closed"
                        :pageTitle="$t('resignation.update_resignation_status')"
                    />
                    <View
                        :data="resignationData"
                        :visible="visibleResignation"
                        @close="closed"
                        :pageTitle="$t('resignation.resignation_details')"
                    />
                </admin-page-table-content>
                <user-view-page
                    :visible="userOpen"
                    :userId="userId"
                    @closed="closeUser"
                />
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    AppstoreOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import View from "./View.vue";
import UpdateStatus from "./UpdateStatus.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,
        AppstoreOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        DateRangePicker,
        DateTimePicker,
        View,
        UpdateStatus,
        UserInfo,
        UserListDisplay,
    },
    setup() {
        const { permsArray, resignationColors, formatDateTime } = common();
        const { loading, rules } = apiAdmin();
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
            status: "pending",
            user_id: undefined,
            start_date: [],
        });

        const newFormData = ref({
            status: "approved",
        });
        const resignationData = ref({});
        const visibleResignation = ref(false);
        const visibleStatus = ref(false);
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

        const viewResignation = (item) => {
            visibleResignation.value = true;
            resignationData.value = item;
        };

        const updateStatus = (item) => {
            visibleStatus.value = true;
            newFormData.value.xid = item.xid;
        };

        const closed = () => {
            setUrlData();
            visibleResignation.value = false;
            visibleStatus.value = false;
        };

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "resignation";
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
            resignationColors,
            setUrlData,
            extraFilters,
            updateStatus,
            visibleStatus,
            newFormData,
            loading,
            rules,
            allUsers,
            staffMembersUrl,
            formatDateTime,
            resignationData,
            visibleResignation,
            viewResignation,
            closed,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
