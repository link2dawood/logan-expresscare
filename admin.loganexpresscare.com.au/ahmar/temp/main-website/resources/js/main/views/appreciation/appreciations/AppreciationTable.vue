<template>
    <template v-if="$slots.actions">
        <slot
            name="actions"
            :addItem="addItem"
            :selectedRowKeys="table.selectedRowKeys"
            :showSelectedDeleteConfirm="showSelectedDeleteConfirm"
            :setUrlData="setUrlData"
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
                            <template v-if="column.dataIndex === 'price_given'">
                                <div v-if="record && record.price_given">
                                    <ul
                                        v-for="price in record.price_given"
                                        :key="price.price_given"
                                    >
                                        <li>{{ price.price_given }}</li>
                                    </ul>
                                </div>
                                <span v-else>-</span>
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'description'">
                                {{
                                    record.description != null ? record.description : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'award_id'">
                                {{ record.award ? record.award.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'account_id'">
                                {{ record.account ? record.account.account_number : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'date'">
                                {{ formatDateTime(record.date) }}
                            </template>
                            <template v-if="column.dataIndex === 'price_amount'">
                                {{ formatAmountCurrency(record.price_amount) }}
                            </template>
                            <template v-if="column.dataIndex == 'active'">
                                {{ record.active ? $t("common.yes") : $t("common.no") }}
                            </template>
                            <template v-if="column.dataIndex === 'payout_type'">
                                {{
                                    record.payout_type == "monthly"
                                        ? $t("appreciation.monthly")
                                        : $t("appreciation.daily")
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="viewApprication(record)"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('appreciations_edit') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                    >
                                        <template #icon><EditOutlined /></template>
                                    </a-button>

                                    <PdfDownload
                                        v-if="record.generate && record.generate.xid"
                                        :fileName="record.generate.title"
                                        :url="`generate-pdf/${record.generate.xid}`"
                                    />

                                    <a-button
                                        v-if="
                                            permsArray.includes('appreciations_delete') ||
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
        :visible="visible"
        :data="appreciationData"
        :pageTitle="$t('appreciation.appreciation_details')"
        @close="closed"
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
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import fields from "../../../views/appreciation/appreciations/fields";
import AddEdit from "../../../views/appreciation/appreciations/AddEdit.vue";
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
        PdfDownload,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            multiDimensalObjectColumns,
            url,
        } = fields();
        const { formatAmountCurrency, permsArray, formatDateTime } = common();

        const crudVariables = crud();
        const visible = ref(false);
        const appreciationData = ref({});
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

        const viewApprication = (item) => {
            visible.value = true;
            appreciationData.value = item;
        };

        const closed = () => {
            visible.value = false;
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
            crudVariables.langKey.value = "appreciation";
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

            if (tableFilter.date) {
                extraFilterObject.date = tableFilter.date;
            }
            if (tableFilter.user_id) {
                extraFilterObject.user_id = tableFilter.user_id;
            }
            crudVariables.tableUrl.value = {
                url,
                filters: {
                    award_id: tableFilter.award_id ? tableFilter.award_id : undefined,
                },
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
            formatAmountCurrency,
            visible,
            appreciationData,
            viewApprication,
            closed,
            openUserView,
            userId,
            userOpen,
            closeUser,
        };
    },
};
</script>
