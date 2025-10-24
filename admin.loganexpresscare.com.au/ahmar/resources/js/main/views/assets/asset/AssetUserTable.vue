<template>
    <admin-page-table-content :isPageTableContent="isPageTableContent">
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
                        :columns="recordViewColumns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        :bordered="bordered"
                        :size="tableSize"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'name'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'lend_date'">
                                {{ formatDate(record.lend_date) }}
                            </template>
                            <template v-if="column.dataIndex === 'return_date'">
                                {{ formatDate(record.return_date) }}
                            </template>
                            <template v-if="column.dataIndex === 'actual_return_date'">
                                {{
                                    record.actual_return_date
                                        ? formatDate(record.actual_return_date)
                                        : "-"
                                }}
                            </template>

                            <template v-if="column.dataIndex === 'image'">
                                <a-image
                                    :width="32"
                                    :src="record.user.profile_image_url"
                                />
                            </template>
                            <template v-if="column.dataIndex === 'lend_by'">
                                <UserInfo :user="record.lent_by" />
                            </template>
                            <template v-if="column.dataIndex === 'return_by'">
                                <UserInfo :user="record.return_by" />
                            </template>
                            <template v-if="column.dataIndex === 'actual_return_date'">
                                {{
                                    record.actual_return_date
                                        ? record.actual_return_date
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        v-if="record.actual_return_date == null"
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><DeleteOutlined /></template>
                                    </a-button>
                                </a-space>
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
import { onMounted, ref, watch, createVNode } from "vue";
import { Modal, notification } from "ant-design-vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    AppstoreOutlined,
    EyeOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import fields from "./fields";

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
        ExclamationCircleOutlined,
        UserInfo,
    },
    setup(props, { emit }) {
        const {
            initData,
            recordViewColumns,
            filterableColumns,
            hashableColumns,
            multiDimensalObjectColumns,
        } = fields();
        const { permsArray, complaintsColors, formatDateTime, formatDate } = common();
        const userOpen = ref(false);
        const userId = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.x_lent_to;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        const crudVariables = crud();

        onMounted(() => {
            crudVariables.crudUrl.value = "remove-asset-user";
            crudVariables.langKey.value = "asset";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = { ...hashableColumns };
            crudVariables.multiDimensalObjectColumns.value = {
                ...multiDimensalObjectColumns,
            };

            setUrlData();
        });

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};

            if (tableFilter.user_id) {
                extraFilterObject.user_id = tableFilter.user_id;
            }
            if (tableFilter.asset_id) {
                extraFilterObject.asset_id = tableFilter.asset_id;
            }
            crudVariables.tableUrl.value = {
                url:
                    "asset-users?fields=id,xid,lend_date,return_date,actual_return_date,notes,asset_id,x_asset_id,asset{id,xid,name},lent_to,x_lent_to,user{id,xid,name,profile_image,profile_image_url},lent_by,x_lent_by,lentBy{id,xid,name,profile_image,profile_image_url},return_by,x_return_by,returnBy{id,xid,name,profile_image,profile_image_url}",
                extraFilters: extraFilterObject,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            permsArray,
            recordViewColumns,
            ...crudVariables,
            filterableColumns,
            formatDateTime,
            formatDate,
            setUrlData,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
