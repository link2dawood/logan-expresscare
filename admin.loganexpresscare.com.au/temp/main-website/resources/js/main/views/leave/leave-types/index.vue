<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.leave_types`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.leaves`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.leave_types`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('leave_types_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-space>
                            <a-button type="primary" @click="addItem">
                                <PlusOutlined />
                                {{ $t("leave_type.add") }}
                            </a-button>
                        </a-space>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('leave_types_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
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
                                        $t('common.name'),
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
                            <template v-if="column.dataIndex == 'is_paid'">
                                <div v-if="record.is_paid == 1">
                                    <a-tag :color="statusColors['enabled']">
                                        {{ $t("common.yes") }}
                                    </a-tag>
                                </div>
                                <div v-else>
                                    <a-tag :color="statusColors['disabled']">
                                        {{ $t("common.no") }}
                                    </a-tag>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'month'">
                                <div
                                    v-for="month in monthArrays"
                                    :key="month.key"
                                >
                                    <div v-if="month.value == record.month">
                                        {{ month.key }}
                                    </div>
                                </div>
                            </template>
                            <template
                                v-if="column.dataIndex === 'total_leaves'"
                            >
                                {{
                                    record.count_type === "employee_specific" &&
                                    record.employee_specific_leave_count.length
                                        ? record
                                              .employee_specific_leave_count[0]
                                              .total_leaves
                                        : record.total_leaves || "-"
                                }}
                            </template>

                            <template
                                v-if="
                                    column.dataIndex === 'max_leaves_per_month'
                                "
                            >
                                {{
                                    record.max_leaves_per_month
                                        ? record.max_leaves_per_month
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'edit_count'">
                                <a-typography-link
                                    v-if="
                                        record.count_type ===
                                        'employee_specific'
                                    "
                                    @click="modalOpened(record)"
                                    >{{
                                        $t("leave_type.edit_count")
                                    }}</a-typography-link
                                >
                                <span v-else> {{ "-" }}</span>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes(
                                            'leave_types_edit'
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
                                            'leave_types_delete'
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
    <EmployeeSpecificLeaveCount
        :visible="modalOpen"
        :title="$t('leave_type.employee_specific_leave_count')"
        @close="modalClosed"
        :data="modalData"
        addEditType="edit"
    />
</template>
<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import EmployeeSpecificLeaveCount from "./EmployeeSpecificLeaveCount.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        EmployeeSpecificLeaveCount,
    },
    setup() {
        const { addEditUrl, initData, columns, filterableColumns, url } =
            fields();
        const crudVariables = crud();
        const { permsArray, appSetting, formatDate, statusColors } = common();
        const modalOpen = ref(false);
        const modalData = ref({});

        onMounted(() => {
            setUrlData();
        });

        const modalOpened = (items) => {
            modalData.value = items;
            modalOpen.value = true;
        };

        const modalClosed = () => {
            setUrlData();
            modalOpen.value = false;
        };

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "leave_type";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
        };

        return {
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            statusColors,
            modalOpen,
            modalOpened,
            modalClosed,
            modalData,
        };
    },
};
</script>
