<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.deposits`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.finance`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.deposits`) }}
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
                            permsArray.includes('deposits_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("deposit.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('deposits_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="filters.account_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('deposit.account')])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="account in accounts"
                                :key="account.xid"
                                :value="account.xid"
                                :title="account.name"
                            >
                                {{ account.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="filters.payer_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('deposit.payer')])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="payer in payers"
                                :key="payer.xid"
                                :value="payer.xid"
                                :title="payer.name"
                            >
                                {{ payer.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="filters.deposit_category_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('deposit.deposit_category'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="deposit_category in depositCategories"
                                :key="deposit_category.xid"
                                :value="deposit_category.xid"
                                :title="deposit_category.name"
                            >
                                {{ deposit_category.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="16" :lg="8" :xl="8">
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
                                        $t('deposit.amount'),
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
            @addListSuccess="reSetFormData"
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
                            <template v-if="column.dataIndex == 'amount'">
                                {{ formatAmountCurrency(record.amount) }}
                            </template>
                            <template v-if="column.dataIndex == 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template v-if="column.dataIndex === 'file'">
                                <a-typography-link
                                    :href="record.file_url"
                                    target="_blank"
                                >
                                    <a-tag color="success">
                                        <template #icon>
                                            <DownloadOutlined />
                                        </template>
                                        {{ $t("common.download") }}
                                    </a-tag>
                                </a-typography-link>
                            </template>
                            <template v-if="column.dataIndex === 'account_id'">
                                {{ record.account?.name }}
                            </template>
                            <template v-if="column.dataIndex === 'payer_id'">
                                {{ record.payer?.name }}
                            </template>
                            <template v-if="column.dataIndex === 'deposit_category_id'">
                                {{ record.deposit_category?.name }}
                            </template>
                            <template v-if="column.dataIndex === 'reference_number'">
                                {{
                                    record.reference_number
                                        ? record.reference_number
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    type="primary"
                                    @click="viewDeposit(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('deposits_edit') ||
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
                                        permsArray.includes('deposits_delete') ||
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
        <View
            :data="depositData"
            :visible="visible"
            :pageTitle="$t('deposit.deposit_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import { useI18n } from "vue-i18n";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        DownloadOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        View,
    },
    setup() {
        const { permsArray, formatDateTime, formatAmountCurrency } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const depositData = ref({});
        const visible = ref(false);
        const { t } = useI18n();

        const filters = ref({
            deposit_category_id: undefined,
            payer_id: undefined,
            account_id: undefined,
        });

        const accounts = ref({});
        const accountUrl = "accounts";
        const payers = ref([]);
        const payerUrl = "payers";
        const depositCategories = ref({});
        const depositCategoryUrl = "deposit-categories";

        const viewDeposit = (item) => {
            (visible.value = true), (depositData.value = item);
        };

        const closed = () => {
            visible.value = false;
        };

        onMounted(() => {
            setUrlData();
            const accountsPromise = axiosAdmin.get(accountUrl);
            const depositCategoriesPromise = axiosAdmin.get(depositCategoryUrl);
            const payersPromise = axiosAdmin.get(payerUrl);
            Promise.all([accountsPromise, payersPromise, depositCategoriesPromise]).then(
                ([accountResponse, payerResponse, depositCategoryResponse]) => {
                    accounts.value = accountResponse.data;
                    payers.value = payerResponse.data;
                    depositCategories.value = depositCategoryResponse.data;
                }
            );
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                filters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "deposit";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        };

        return {
            permsArray,
            filters,
            columns,
            ...crudVariables,
            filterableColumns,
            hashableColumns,
            setUrlData,
            formatDateTime,
            formatAmountCurrency,
            depositData,
            visible,
            viewDeposit,
            closed,
            accounts,
            payers,
            depositCategories,
        };
    },
};
</script>
