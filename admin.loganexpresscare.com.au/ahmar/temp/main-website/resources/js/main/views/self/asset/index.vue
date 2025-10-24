<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.assets`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.assets`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="8" :lg="7" :xl="7">
                        <a-select
                            v-model:value="extraFilters.asset_type_id"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('asset.asset_type_id'),
                                ])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
                        >
                            <a-select-option
                                v-for="assetTy in assetType"
                                :key="assetTy.xid"
                                :title="assetTy.name"
                                :value="assetTy.xid"
                            >
                                {{ assetTy.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs
                    v-model:activeKey="extraFilters.status"
                    @change="setUrlData"
                >
                    <a-tab-pane key="all" :tab="`${$t('common.all_asset')}`" />
                    <a-tab-pane
                        key="broken"
                        :tab="`${$t('common.broken_by_you')}`"
                    />
                </a-tabs>
            </a-col>
        </a-row>

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
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.asset?.name }}
                            </template>

                            <template v-if="column.dataIndex === 'image'">
                                <a-image
                                    :width="32"
                                    :src="record.user?.profile_image_url"
                                />
                            </template>
                            <template v-if="column.dataIndex === 'lend_by'">
                                {{ record.user ? record.user?.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'return_by'">
                                {{
                                    record.return_by
                                        ? record.return_by.name
                                        : "-"
                                }}
                            </template>
                            <template
                                v-if="column.dataIndex === 'actual_return_date'"
                            >
                                {{
                                    record.actual_return_date
                                        ? record.actual_return_date
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="viewRecordItem(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon
                                            ><EyeOutlined
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
    <AssetViewVue
        :data="viewRecord"
        :visible="viewRecordVisible"
        @closed="viewClosed"
    />
</template>
<script>
import { onMounted, ref } from "vue";
import { EyeOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import AssetViewVue from "./AssetView.vue";

export default {
    components: {
        EyeOutlined,
        AdminPageHeader,
        AssetViewVue,
    },
    setup() {
        const { columns, filterableColumns, hashableColumns } = fields();
        const datatableVariables = datatable();
        const { formatDate, dayjs } = common();
        const viewRecordVisible = ref(false);
        const viewRecord = ref({});
        const assetType = ref([]);
        const extraFilters = ref({
            asset_type_id: undefined,
            status: "all",
        });

        onMounted(() => {
            const assetTypePromise = axiosAdmin.get("asset-types");

            Promise.all([assetTypePromise]).then(([assetTypeResponse]) => {
                assetType.value = assetTypeResponse.data;
            });
            setUrlData();
        });

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `self/assets?fields=id,xid,lent_to,x_lent_to,lend_date,return_date,notes,lent_by,x_lent_by,return_by,x_return_by,actual_return_date,user{id,xid,name,profile_image,profile_image_url},returnBy{id,xid,name,profile_image,profile_image_url},lentBy{id,xid,name,profile_image,profile_image_url},asset{id,xid,name,image,image_url}`,
                extraFilters,
            };

            datatableVariables.table.filterableColumns = filterableColumns;
            datatableVariables.hashable.value = { ...hashableColumns };

            datatableVariables.fetch({
                page: 1,
            });
        };

        const viewRecordItem = (item) => {
            viewRecord.value = item;
            viewRecordVisible.value = true;
        };

        const viewClosed = () => {
            viewRecordVisible.value = false;
        };

        return {
            ...datatableVariables,
            columns,
            filterableColumns,
            formatDate,
            setUrlData,
            extraFilters,
            assetType,
            viewRecordItem,
            viewClosed,
            viewRecordVisible,
            viewRecord,
        };
    },
};
</script>
