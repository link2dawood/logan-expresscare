<template>
    <template v-if="$slots.actions">
        <slot
            name="actions"
            :addItem="addItem"
            :showSelectedDeleteConfirm="showSelectedDeleteConfirm"
            :onTableSearch="onTableSearch"
            :setUrlData="setUrlData"
            :table="table"
        >
        </slot>
    </template>

    <admin-page-table-content :isPageTableContent="isPageTableContent">
        <template v-if="$slots.tabs">
            <slot name="tabs" :setUrlData="setUrlData"></slot>
        </template>

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled:
                                    permsArray.includes('admin') ||
                                    permsArray.includes('leaves_delete') ||
                                    record.status == 'pending'
                                        ? false
                                        : true,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        :bordered="bordered"
                        :size="tableSize"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'user_id'">
                                <a-button
                                    type="link"
                                    @click="openUserView(record)"
                                >
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex == 'start_date'">
                                {{ formatDate(record.start_date) }}
                            </template>
                            <template v-if="column.dataIndex == 'end_date'">
                                {{ formatDate(record.end_date) }}
                            </template>
                            <template v-if="column.dataIndex == 'total_leaves'">
                                <span
                                    >{{ record.total_days + " "
                                    }}{{ $t("leave.days") }}</span
                                >
                                <!-- {{
                                        calculateLeaveCount(
                                            record.start_date,
                                            record.end_date
                                        )
                                    }}
                                    leave(s) -->
                            </template>
                            <template v-if="column.dataIndex == 'is_half_day'">
                                {{
                                    record.is_half_day
                                        ? $t("common.yes")
                                        : $t("common.no")
                                }}
                            </template>

                            <template
                                v-if="column.dataIndex == 'leave_type_id'"
                            >
                                {{ record.leave_type.name }}
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <div v-if="record.status == 'pending'">
                                    <a-tag color="yellow">
                                        {{ $t(`common.${"pending"}`) }}
                                    </a-tag>
                                </div>
                                <div v-if="record.status == 'approved'">
                                    <a-tag color="green">
                                        {{ $t(`common.${"approved"}`) }}
                                    </a-tag>
                                </div>
                                <div v-if="record.status == 'rejected'">
                                    <a-tag color="red">
                                        {{ $t(`common.${"rejected"}`) }}
                                    </a-tag>
                                </div>
                            </template>

                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>{{ $t("common.view") }}</span>
                                        </template>
                                        <a-button
                                            type="primary"
                                            @click="viewLeaveData(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon
                                                ><EyeOutlined
                                            /></template>
                                        </a-button>
                                    </a-tooltip>

                                    <template
                                        v-if="
                                            record.status == 'pending' &&
                                            (permsArray.includes('admin') ||
                                                permsArray.includes(
                                                    'leaves_approve_reject'
                                                ))
                                        "
                                    >
                                        <a-tooltip placement="topLeft">
                                            <template #title>
                                                <span>{{
                                                    $t("common.approve")
                                                }}</span>
                                            </template>
                                            <a-button
                                                type="primary"
                                                @click="
                                                    leaveApproved(record.xid)
                                                "
                                            >
                                                <template #icon
                                                    ><CheckOutlined
                                                /></template>
                                            </a-button>
                                        </a-tooltip>
                                    </template>

                                    <template
                                        v-if="
                                            record.status == 'pending' &&
                                            (permsArray.includes('admin') ||
                                                permsArray.includes(
                                                    'leaves_approve_reject'
                                                ))
                                        "
                                    >
                                        <a-tooltip placement="topLeft">
                                            <template #title>
                                                <span>{{
                                                    $t("common.reject")
                                                }}</span>
                                            </template>
                                            <a-button
                                                type="primary"
                                                @click="
                                                    leaveRejected(record.xid)
                                                "
                                                danger
                                            >
                                                <template #icon
                                                    ><CloseOutlined
                                                /></template>
                                            </a-button>
                                        </a-tooltip>
                                    </template>

                                    <template
                                        v-if="
                                            record.status == 'pending' &&
                                            (record.x_user_id == user.xid ||
                                                permsArray.includes('admin') ||
                                                permsArray.includes(
                                                    'leaves_edit'
                                                ))
                                        "
                                    >
                                        <a-tooltip placement="topLeft">
                                            <template #title>
                                                <span>{{
                                                    $t("common.edit")
                                                }}</span>
                                            </template>
                                            <a-button
                                                type="primary"
                                                @click="editItem(record)"
                                            >
                                                <template #icon
                                                    ><EditOutlined
                                                /></template>
                                            </a-button>
                                        </a-tooltip>
                                    </template>

                                    <template
                                        v-if="
                                            (record.status == 'pending' &&
                                                record.x_user_id == user.xid) ||
                                            permsArray.includes('admin') ||
                                            permsArray.includes('leaves_delete')
                                        "
                                    >
                                        <a-tooltip placement="topLeft">
                                            <template #title>
                                                <span>{{
                                                    $t("common.delete")
                                                }}</span>
                                            </template>
                                            <a-button
                                                type="primary"
                                                @click="
                                                    showDeleteConfirm(
                                                        record.xid
                                                    )
                                                "
                                            >
                                                <template #icon
                                                    ><DeleteOutlined
                                                /></template>
                                            </a-button>
                                        </a-tooltip>
                                    </template>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <View :visible="visible" :data="leaveData" @close="closed" />
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />

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
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import {
    CloseOutlined,
    CheckOutlined,
    EyeOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import View from "./View.vue";

export default {
    props: {
        selectable: {
            default: false,
        },
        isPageTableContent: {
            default: true,
        },
        tableSize: {
            default: "large",
        },
        bordered: {
            default: false,
        },
        filters: {
            default: {},
        },
        perPageItems: Number,
    },
    emits: ["onRowSelection"],
    components: {
        CloseOutlined,
        CheckOutlined,
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        DownloadOutlined,
        ExclamationCircleOutlined,

        AddEdit,
        UserInfo,
        View,
    },
    setup(props, { emit }) {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const { t } = useI18n();
        const { permsArray, appSetting, formatDate, user } = common();
        const leaveData = ref({});
        const visible = ref(false);

        const crudVariables = crud();
        const statusFilter = ref({
            status: "all",
        });

        const userOpen = ref(false);
        const userId = ref(undefined);

        const calculateLeaveCount = (startDate, endDate) => {
            const start = new Date(startDate);
            const end = new Date(endDate);

            const diffTime = end - start;

            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            return diffDays;
        };

        const openUserView = (item) => {
            userId.value = item.x_user_id;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        onMounted(() => {
            const newInitData = {
                ...initData,
                user_id:
                    props.filters.user_id != undefined
                        ? props.filters.user_id
                        : undefined,
            };

            const newData = {
                ...initData,
                user_id:
                    props.filters.user_id != undefined
                        ? props.filters.user_id
                        : undefined,
            };

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "leave";
            crudVariables.initData.value = newData;
            crudVariables.formData.value = newInitData;
            crudVariables.hashableColumns.value = [...hashableColumns];

            setUrlData();
        });

        const viewLeaveData = (item) => {
            visible.value = true;
            leaveData.value = item;
        };

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};

            if (tableFilter.status) {
                extraFilterObject.status = tableFilter.status;
            }

            if (tableFilter.attendance_month) {
                extraFilterObject.attendance_month =
                    tableFilter.attendance_month;
            }

            if (tableFilter.leave_type_id) {
                extraFilterObject.leave_type_id = tableFilter.leave_type_id;
            }
            if (tableFilter.user_id) {
                extraFilterObject.user_id = tableFilter.user_id;
            }
            crudVariables.tableUrl.value = {
                url: "leaves?fields=id,xid,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},user:designation{id,xid,name},user:location{id,xid,name},leave_type_id,x_leave_type_id,leaveType{id,xid,name},start_date,end_date,is_half_day,reason,status,half_leave,total_days",
                extraFilters: extraFilterObject,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const leaveApproved = (xid) => {
            Modal.confirm({
                title: t("common.approved") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("leave.approved_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`leaves/update-status/${xid}`, {
                            status: "approved",
                        })
                        .then((successResponse) => {
                            setUrlData();
                            // Update Visible Subscription Modules
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
            });
        };

        const leaveRejected = (xid) => {
            Modal.confirm({
                title: t("common.rejected") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.rejected_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`leaves/update-status/${xid}`, {
                            status: "rejected",
                        })
                        .then((successResponse) => {
                            setUrlData();
                            // Update Visible Subscription Modules
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
            });
        };

        const closed = () => {
            visible.value = false;
        };

        return {
            permsArray,
            statusFilter,
            columns,
            ...crudVariables,
            filterableColumns,
            formatDate,
            setUrlData,
            leaveApproved,
            leaveRejected,
            closed,
            user,
            userOpen,
            userId,
            openUserView,
            closeUser,

            viewLeaveData,
            visible,
            leaveData,
            calculateLeaveCount,
        };
    },
};
</script>
