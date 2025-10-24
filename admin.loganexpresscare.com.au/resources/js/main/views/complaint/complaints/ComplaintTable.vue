<template>
    <template v-if="$slots.actions">
        <slot
            name="actions"
            :addItem="addItem"
            :table="table"
            :setUrlData="setUrlData"
            :showSelectedDeleteConfirm="showSelectedDeleteConfirm"
            :onTableSearch="onTableSearch"
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
                        :bordered="bordered"
                        :size="tableSize"
                    >
                        <template #bodyCell="{ column, record, text }">
                            <template v-if="column.dataIndex === 'title'">
                                <a-button type="link" @click="viewComplaint(record)">
                                    {{ record.title }}
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'proff_of_document'">
                                <span v-if="record.proff_of_document">
                                    <a-image
                                        :width="32"
                                        :src="record.proff_of_document_url"
                                /></span>
                                <span v-else>-</span>
                            </template>
                            <template v-if="column.dataIndex === 'from_user_id'">
                                <a-button type="link" @click="openFromUserView(record)">
                                    <UserInfo :user="record.from_staff" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'to_user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.to_staff" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <a-tag :color="complaintsColors[text]">
                                    {{ $t(`common.${text}`) }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex == 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    type="primary"
                                    @click="viewComplaint(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                                <a-tooltip placement="topLeft">
                                    <template #title>
                                        <span>{{ $t("common.approve/reject") }}</span>
                                    </template>
                                    <a-button
                                        v-if="
                                            (permsArray.includes('admin') ||
                                                permsArray.includes('complaints_edit')) &&
                                            record.status == 'pending'
                                        "
                                        type="primary"
                                        @click="updateStatus(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><AppstoreOutlined /></template>
                                    </a-button>
                                </a-tooltip>

                                <a-button
                                    v-if="
                                        permsArray.includes('complaints_edit') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="editItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EditOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('complaints_delete') ||
                                        permsArray.includes('admin')
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
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
    <user-view-page
        :visible="fromUserOpen"
        :userId="fromUserId"
        @closed="closeFromUser"
    />

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

    <UpdateStatus
        :data="newFormData"
        :visible="visibleStatus"
        @close="closed"
        :pageTitle="$t('complaint.update_complaint_status')"
    />
    <View
        :data="recordComplaint"
        :visible="visibleComplaint"
        @close="closed"
        :pageTitle="$t('complaint.complaint_details')"
    />
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
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import UpdateStatus from "./UpdateStatus.vue";
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
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AppstoreOutlined,
        EyeOutlined,

        AddEdit,
        UserInfo,
        View,
        UpdateStatus,
    },
    setup(props, { emit }) {
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            multiDimensalObjectColumns,
        } = fields();
        const { permsArray, complaintsColors, formatDateTime } = common();

        const crudVariables = crud();

        const visibleComplaint = ref(false);
        const visibleStatus = ref(false);

        const recordComplaint = ref({});
        const newFormData = ref({ status: "approved" });

        const userOpen = ref(false);
        const userId = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.x_to_user_id;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        const fromUserOpen = ref(false);
        const fromUserId = ref(undefined);

        const openFromUserView = (item) => {
            fromUserId.value = item.x_from_user_id;
            fromUserOpen.value = true;
        };

        const closeFromUser = () => {
            fromUserId.value = undefined;
            fromUserOpen.value = false;
        };

        const viewComplaint = (item) => {
            visibleComplaint.value = true;
            recordComplaint.value = item;
        };

        const updateStatus = (item) => {
            visibleStatus.value = true;
            newFormData.value.xid = item.xid;
        };

        const closed = () => {
            setUrlData();
            visibleComplaint.value = false;
            visibleStatus.value = false;
        };

        onMounted(() => {
            const newInitData = {
                ...initData,
                to_user_id:
                    props.filters.to_user_id != undefined
                        ? props.filters.to_user_id
                        : undefined,
            };

            const newData = {
                ...initData,
                to_user_id:
                    props.filters.to_user_id != undefined
                        ? props.filters.to_user_id
                        : undefined,
            };
            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "complaint";
            crudVariables.initData.value = newData;
            crudVariables.formData.value = newInitData;
            crudVariables.hashableColumns.value = { ...hashableColumns };
            crudVariables.multiDimensalObjectColumns.value = {
                ...multiDimensalObjectColumns,
            };

            setUrlData();
        });

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};

            if (tableFilter.complaintStatus) {
                extraFilterObject.complaintStatus = tableFilter.complaintStatus;
            }

            if (tableFilter.date_time) {
                extraFilterObject.date_time = tableFilter.date_time;
            }
            if (tableFilter.to_user_id) {
                extraFilterObject.to_user_id = tableFilter.to_user_id;
            }
            if (tableFilter.from_user_id) {
                extraFilterObject.from_user_id = tableFilter.from_user_id;
            }
            crudVariables.tableUrl.value = {
                url,
                extraFilters: extraFilterObject,
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
            formatDateTime,
            setUrlData,
            complaintsColors,
            visibleComplaint,
            visibleStatus,
            newFormData,
            recordComplaint,
            closed,
            viewComplaint,
            updateStatus,
            userOpen,
            userId,
            openUserView,
            closeUser,
            fromUserOpen,
            fromUserId,
            openFromUserView,
            closeFromUser,
        };
    },
};
</script>
