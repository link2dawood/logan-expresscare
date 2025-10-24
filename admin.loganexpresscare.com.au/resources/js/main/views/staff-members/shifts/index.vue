<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.shifts`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.hrm`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.shifts`) }}
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
                            permsArray.includes('shifts_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("shift.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('shifts_delete') ||
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
                            <a-select
                                style="width: 25%"
                                v-model:value="table.searchColumn"
                                :placeholder="
                                    $t('common.select_default_text', [''])
                                "
                            >
                                <a-select-option
                                    v-for="filterableColumn in filterableColumns"
                                    :key="filterableColumn.key"
                                >
                                    {{ filterableColumn.value }}
                                </a-select-option>
                            </a-select>
                            <a-input-search
                                style="width: 75%"
                                v-model:value="table.searchString"
                                show-search
                                @change="onTableSearch"
                                @search="onTableSearch"
                                :loading="table.filterLoading"
                                :placeholder="
                                    $t('common.placeholder_search_text', [
                                        $t('shift.name'),
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
                            <template
                                v-if="column.dataIndex === 'employee_count'"
                            >
                                {{ record.employee_count.employee_count }}
                            </template>
                            <template
                                v-if="column.dataIndex === 'late_mark_after'"
                            >
                                <div v-if="record.late_mark_after != null">
                                    {{ record.late_mark_after }}
                                    {{ $t("common.minutes") }}
                                </div>
                                <div v-else>
                                    {{ "-" }}
                                </div>
                            </template>
                            <template
                                v-if="
                                    column.dataIndex === 'early_clock_in_time'
                                "
                            >
                                <div v-if="record.early_clock_in_time != null">
                                    {{ record.early_clock_in_time }}
                                    {{ $t("common.minutes") }}
                                </div>
                                <div v-else>
                                    {{ "-" }}
                                </div>
                            </template>
                            <template
                                v-if="
                                    column.dataIndex === 'allow_clock_out_till'
                                "
                            >
                                <div v-if="record.allow_clock_out_till != null">
                                    {{ record.allow_clock_out_till }}
                                    {{ $t("common.minutes") }}
                                </div>
                                <div v-else>
                                    {{ "-" }}
                                </div>
                            </template>
                            <template
                                v-if="column.dataIndex === 'allowed_ip_address'"
                            >
                                <ul
                                    v-for="ipAddress in record.allowed_ip_address"
                                    :key="ipAddress.allowed_ip_address"
                                >
                                    <li>
                                        {{ ipAddress.allowed_ip_address }}
                                    </li>
                                </ul>
                            </template>
                            <template
                                v-if="column.dataIndex == 'self_clocking'"
                            >
                                <div v-if="record.self_clocking == 1">
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

                            <template
                                v-if="column.dataIndex === 'clock_in_time'"
                            >
                                {{
                                    formatShiftTime12Hours(record.clock_in_time)
                                }}
                            </template>
                            <template
                                v-if="column.dataIndex === 'clock_out_time'"
                            >
                                {{
                                    formatShiftTime12Hours(
                                        record.clock_out_time
                                    )
                                }}{{
                                    record.is_next_day == 1 ? " (Next Day)" : ""
                                }}
                            </template>

                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes('shifts_edit') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="editItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon>
                                        <EditOutlined />
                                    </template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('shifts_delete') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="showDeleteConfirm(record.xid)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon>
                                        <DeleteOutlined />
                                    </template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import dayjs from "dayjs";
import hrmManagement from "../../../../common/composable/hrmManagement";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const { addEditUrl, initData, columns, filterableColumns } = fields();
        const crudVariables = crud();
        const { permsArray, appSetting, statusColors } = common();
        const { formatShiftTime12Hours } = hrmManagement();

        onMounted(() => {
            if (
                permsArray.value.includes("shifts_edit") ||
                permsArray.value.includes("shifts_delete") ||
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

            crudVariables.tableUrl.value = {
                url: "shifts?fields=id,xid,name,capture_location,clock_in_time,clock_out_time,late_mark_after,allow_clock_out_till,self_clocking,allowed_ip_address,early_clock_in_time,employee_count,is_next_day",
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "shift";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
        });

        return {
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
            appSetting,
            formatShiftTime12Hours,
            statusColors,
        };
    },
};
</script>
