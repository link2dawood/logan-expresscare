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
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'title'">
                                <a-typography-link @click="viewWarning(record)">
                                    {{ record.title }}
                                </a-typography-link>
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'description'">
                                {{ record.description ? record.description : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'warning_date'">
                                {{ formatDateTime(record.warning_date) }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    type="primary"
                                    @click="viewWarning(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('warnings_edit') ||
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
                                        permsArray.includes('warnings_delete') ||
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

    <View
        :data="warningData"
        :visible="visibleWarning"
        @close="closed"
        :pageTitle="$t('warning.warning_details')"
    />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    EyeOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
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
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        DownloadOutlined,

        AddEdit,
        UserInfo,
        View,
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
        const { permsArray, formatDateTime } = common();

        const crudVariables = crud();

        const visibleWarning = ref(false);
        const warningData = ref({});

        const viewWarning = (item) => {
            visibleWarning.value = true;
            warningData.value = item;
        };

        const closed = () => {
            visibleWarning.value = false;
        };

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
            crudVariables.langKey.value = "warning";
            crudVariables.initData.value = newData;
            crudVariables.formData.value = newInitData;
            crudVariables.hashableColumns.value = [...hashableColumns];
            crudVariables.multiDimensalObjectColumns.value = {
                ...multiDimensalObjectColumns,
            };

            setUrlData();
        });

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};

            if (tableFilter.warning_date) {
                extraFilterObject.warning_date = tableFilter.warning_date;
            }
            if (tableFilter.user_id) {
                extraFilterObject.user_id = tableFilter.user_id;
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
            visibleWarning,
            warningData,
            viewWarning,
            closed,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
