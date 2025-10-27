<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`referral`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`referral`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('referrals_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.status"
                            @change="reFetchDatatable"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('referral.status'),
                                ])
                            "
                            style="width: 100%"
                            allowClear
                        >
                            <a-select-option value="pending">
                                {{ $t("common.pending") }}
                            </a-select-option>
                            <a-select-option value="in_review">
                                {{ $t("common.in_review") }}
                            </a-select-option>
                            <a-select-option value="approved">
                                {{ $t("common.approved") }}
                            </a-select-option>
                            <a-select-option value="rejected">
                                {{ $t("common.rejected") }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row class="mt-20">
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
                        <template #bodyCell="{ column, text, record }">
                            <template v-if="column.dataIndex === 'first_name'">
                                {{ record.first_name }} {{ record.last_name }}
                            </template>
                            <template v-else-if="column.dataIndex === 'email_address'">
                                {{ record.email_address }}
                            </template>
                            <template v-else-if="column.dataIndex === 'phone_number'">
                                {{ record.phone_number }}
                            </template>
                            <template v-else-if="column.dataIndex === 'primary_service'">
                                {{ record.primary_service }}
                            </template>
                            <template v-else-if="column.dataIndex === 'status'">
                                <a-tag :color="statusColors[text]">
                                    {{ $t(`common.${text}`) }}
                                </a-tag>
                            </template>
                            <template v-else-if="column.dataIndex === 'submission_date'">
                                {{ formatDateTime(record.submission_date) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        v-if="
                                            permsArray.includes(
                                                `referrals_delete`
                                            ) || permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                        danger
                                    >
                                        <template #icon
                                            ><DeleteOutlined
                                        /></template>
                                    </a-button>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref, computed, reactive } from "vue";
import {
    DeleteOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import { Modal } from "ant-design-vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { createVNode } from "vue";

export default {
    name: "ReferralsIndex",
    components: {
        DeleteOutlined,
        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const {
            crudUrl,
            formData,
            addEditType,
            currentPage,
            table,
            fetch,
            tableUrl,
        } = crud();
        const { permsArray, formatDateTime } = common();

        const filters = reactive({
            status: undefined,
        });

        const columns = [
            {
                title: t("referral.first_name"),
                dataIndex: "first_name",
                key: "first_name",
                width: 150,
            },
            {
                title: t("referral.email"),
                dataIndex: "email_address",
                key: "email_address",
                width: 150,
            },
            {
                title: t("referral.phone"),
                dataIndex: "phone_number",
                key: "phone_number",
                width: 120,
            },
            {
                title: t("referral.primary_service"),
                dataIndex: "primary_service",
                key: "primary_service",
                width: 150,
            },
            {
                title: t("referral.status"),
                dataIndex: "status",
                key: "status",
                width: 100,
            },
            {
                title: t("referral.submission_date"),
                dataIndex: "submission_date",
                key: "submission_date",
                width: 130,
            },
            {
                title: t("common.action"),
                dataIndex: "action",
                key: "action",
                width: 80,
                fixed: "right",
            },
        ];

        const statusColors = {
            pending: "processing",
            in_review: "blue",
            approved: "success",
            rejected: "error",
        };

        const reFetchDatatable = () => {
            currentPage.value = 1;
            tableUrl.value.filters.status = filters.status;
            fetch({
                page: 1,
            });
        };

        const onRowSelectChange = (selectedRowKeys) => {
            table.selectedRowKeys = selectedRowKeys;
        };

        const handleTableChange = (pagination, filters, sorter) => {
            const pager = { ...table.pagination };
            pager.current = pagination.current;
            pager.pageSize = pagination.pageSize;
            table.pagination = pager;
            table.sorter = sorter;
            currentPage.value = pagination.current;

            fetch({
                page: pagination.current,
            });
        };

        const showDeleteConfirm = (id) => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("referral.delete_message"),
                onOk() {
                    return axiosAdmin
                        .delete(`${crudUrl.value}/${id}`)
                        .then((successResponse) => {
                            fetch({ page: currentPage.value });
                            notification.success({
                                message: t("common.success"),
                                description: t("referral.deleted"),
                            });
                        });
                },
            });
        };

        const showSelectedDeleteConfirm = () => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("referral.selected_delete_message"),
                onOk() {
                    table.selectedRowKeys.forEach((id) => {
                        axiosAdmin.delete(`${crudUrl.value}/${id}`);
                    });
                    table.selectedRowKeys = [];
                    fetch({ page: currentPage.value });
                    notification.success({
                        message: t("common.success"),
                        description: t("referral.deleted"),
                    });
                },
            });
        };

        crudUrl.value = "referrals";
        tableUrl.value = {
            url: "referrals",
            filters: {
                status: undefined,
            },
        };

        onMounted(() => {
            fetch({
                page: 1,
            });
        });

        return {
            columns,
            table,
            crudUrl,
            currentPage,
            filters,
            statusColors,
            permsArray,
            formatDateTime,
            reFetchDatatable,
            onRowSelectChange,
            handleTableChange,
            showDeleteConfirm,
            showSelectedDeleteConfirm,
        };
    },
};
</script>

<style scoped>
.table-responsive {
    overflow-x: auto;
}
</style>
