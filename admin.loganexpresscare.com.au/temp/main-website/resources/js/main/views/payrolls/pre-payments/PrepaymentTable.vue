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
                            <template v-if="column.dataIndex == 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template v-if="column.dataIndex == 'user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex == 'account_id'">
                                {{ record.account.name }}
                            </template>
                            <template v-if="column.dataIndex == 'amount'">
                                {{ formatAmountCurrency(record.amount) }}
                            </template>
                            <template v-if="column.dataIndex == 'deduct_month'">
                                {{ getMonthNameByNumber(record.payroll_month) }}
                                {{ record.payroll_year }}
                            </template>

                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button type="primary" @click="viewPayment(record)">
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-show="
                                            permsArray.includes('pre_payments_edit') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                    >
                                        <template #icon><EditOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('pre_payments_delete') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
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
        :data="paymentData"
        :visible="visible"
        :pageTitle="$t('pre_payment.pre_payment_details')"
        @close="closed"
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
import { useI18n } from "vue-i18n";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";
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
    },
    setup(props, { emit }) {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const { permsArray, formatAmountCurrency, formatDateTime } = common();

        const { getMonthNameByNumber } = hrmManagement();
        const { t } = useI18n();

        const crudVariables = crud();
        const visible = ref(false);
        const paymentData = ref({});
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

        const viewPayment = (item) => {
            visible.value = true;
            paymentData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        onMounted(() => {
            if (
                permsArray.value.includes("pre_payments_edit") ||
                permsArray.value.includes("pre_payments_delete") ||
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
            crudVariables.langKey.value = "pre_payment";
            crudVariables.initData.value = newData;
            crudVariables.formData.value = newInitData;
            crudVariables.hashableColumns.value = [...hashableColumns];

            setUrlData();
        });

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};

            if (tableFilter.dates) {
                extraFilterObject.dates = tableFilter.dates;
            }
            if (tableFilter.user_id) {
                extraFilterObject.user_id = tableFilter.user_id;
            }
            crudVariables.tableUrl.value = {
                url:
                    "pre-payments?fields=id,xid,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},user:designation{id,xid,name},user:location{id,xid,name},amount,notes,payroll_year,payroll_month,date_time,deduct_from_payroll,account_id,x_account_id,account{id,xid,name}",
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
            visible,
            paymentData,
            viewPayment,
            getMonthNameByNumber,
            formatAmountCurrency,
            closed,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
