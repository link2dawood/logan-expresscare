<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.attendances`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.attendances`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.attendances`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('attendances_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("attendance.add") }}
                        </a-button>
                    </template>
                    <ImportAttendance
                        :pageTitle="$t('common.import')"
                        importUrl="attendances/import"
                        @onUploadSuccess="setUrlData"
                        :checkVisible="true"
                        :buttonType="true"
                        :url="`attendance-static-csv`"
                        :fileName="$t('common.sample_attendance_file')"
                    />
                    <AttendanceEntry />

                    <BulkAttendance @fetchAttendance="setUrlData" />
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('attendances_delete') ||
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
            <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col
                        v-if="
                            permsArray.includes('attendances_view') ||
                            permsArray.includes('admin')
                        "
                        :xs="24"
                        :sm="24"
                        :md="12"
                        :lg="12"
                        :xl="8"
                    >
                        <a-select
                            v-model:value="extraFilters.user_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('attendance.user'),
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
                            >
                                <user-list-display
                                    :user="user"
                                    whereToShow="select"
                                />
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
                        <a-range-picker
                            v-model:value="extraFilters.date"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                            @change="setUrlData"
                        />
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
                    <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                    <a-tab-pane
                        key="on_leave"
                        :tab="`${$t('attendance.on_leave')}`"
                    />
                    <a-tab-pane
                        key="present"
                        :tab="`${$t('attendance.present')}`"
                    />
                    <a-tab-pane
                        key="half_day"
                        :tab="`${$t('attendance.half_day')}`"
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
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                            <template
                                v-if="column.dataIndex === 'clock_in_date_time'"
                            >
                                {{ formatTime(record.clock_in_date_time) }}
                            </template>
                            <template
                                v-if="
                                    column.dataIndex === 'clock_out_date_time'
                                "
                            >
                                {{ formatTime(record.clock_out_date_time) }}
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                <a-button
                                    type="link"
                                    @click="openUserView(record)"
                                >
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template
                                v-if="column.dataIndex === 'total_duration'"
                            >
                                {{ formatMinutes(record.duration) }}
                            </template>
                            <template v-if="column.dataIndex === 'is_late'">
                                <a-tag
                                    v-if="record.is_late == 0"
                                    color="success"
                                >
                                    {{ $t("common.no") }}
                                </a-tag>
                                <a-tag v-else color="error">
                                    {{ $t("common.yes") }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'is_half_day'">
                                {{
                                    record.is_half_day == 0
                                        ? $t("common.no")
                                        : $t("common.yes")
                                }}
                            </template>

                            <template
                                v-if="
                                    column.dataIndex === 'clock_in_ip_address'
                                "
                            >
                                {{
                                    record.clock_in_ip_address
                                        ? record.clock_in_ip_address
                                        : "-"
                                }}
                            </template>
                            <template
                                v-if="
                                    column.dataIndex === 'clock_out_ip_address'
                                "
                            >
                                {{
                                    record.clock_out_ip_address
                                        ? record.clock_out_ip_address
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <AttendanceStatus
                                    :status="getAttendanceStatus(record)"
                                />
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        (permsArray.includes(
                                            'attendances_edit'
                                        ) ||
                                            permsArray.includes('admin')) &&
                                        getAttendanceStatus(record) !=
                                            'on_leave'
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
                                            'attendances_delete'
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
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
</template>
<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";
import AttendanceStatus from "./AttendanceStatus.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import ImportAttendance from "../../../../common/core/ui/Import.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import BulkAttendance from "./BulkAttendance.vue";
import AttendanceEntry from "./AttendanceEntry.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        AttendanceStatus,
        UserInfo,
        UserListDisplay,
        ImportAttendance,
        DownloadOutlined,
        PdfDownload,
        BulkAttendance,
        AttendanceEntry,
    },
    setup() {
        const { t } = useI18n();
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            url,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const users = ref([]);
        const userUrl = "users?limit=10000";
        const extraFilters = ref({
            user_id: undefined,
            date: undefined,
            status: "all",
        });
        const {
            permsArray,
            appSetting,
            formatDate,
            formatTime,
            formatDateTime,
        } = common();
        const { formatMinutes } = hrmManagement();
        const userOpen = ref(false);
        const userId = ref(undefined);
        const sampleFileUrl = window.config.attendance_sample_file;

        const openUserView = (item) => {
            userId.value = item.x_user_id;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        onMounted(() => {
            if (
                permsArray.value.includes("attendances_edit") ||
                permsArray.value.includes("attendances_delete") ||
                permsArray.value.includes("admin")
            ) {
                columns.value = [
                    ...columns.value,
                    {
                        title: t("common.action"),
                        dataIndex: "action",
                    },
                ];
            }

            getUsers();
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;
            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "attendance";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
            crudVariables.table.sorter = { field: "date", order: "desc" };

            crudVariables.fetch({
                page: 1,
            });
        };

        const getUsers = () => {
            const usersPromise = axiosAdmin.get(userUrl);
            Promise.all([usersPromise]).then(([usersResponse]) => {
                users.value = usersResponse.data;
            });
        };

        const getAttendanceStatus = (record) => {
            if (record.is_half_day) {
                return "half_day";
            } else if (record.x_leave_type_id) {
                return "on_leave";
            } else if (
                record.clock_in_date_time &&
                record.clock_out_date_time
            ) {
                return "present";
            }
        };

        return {
            extraFilters,
            users,
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            formatTime,
            formatDateTime,
            formatMinutes,
            setUrlData,
            getAttendanceStatus,
            userOpen,
            userId,
            openUserView,
            closeUser,
            sampleFileUrl,
            // downloadAttendanceCsvUrl,
        };
    },
};
</script>
