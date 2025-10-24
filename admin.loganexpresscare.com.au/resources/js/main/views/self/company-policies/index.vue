<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.company_policies`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.company_policies`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
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
                                        $t('company_policy.title'),
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
                            <template v-if="column.dataIndex === 'title'">
                                <a-typography-link
                                    type="link"
                                    @click="viewCompanyPolicy(record)"
                                >
                                    {{ record.title }}
                                </a-typography-link>
                            </template>
                            <template v-if="column.dataIndex === 'policy_document'">
                                <a-typography-link
                                    :href="record.policy_document_url"
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
                            <template v-if="column.dataIndex === 'location_id'">
                                {{ record.location.name }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-dropdown placement="bottomRight">
                                    <MoreOutlined />
                                    <template #overlay>
                                        <a-menu>
                                            <a-menu-item
                                                key="view"
                                                @click="viewCompanyPolicy(record)"
                                            >
                                                <EyeOutlined />
                                                {{ $t("common.view") }}
                                            </a-menu-item>

                                            <a-menu-divider />
                                            <PdfDownload
                                                v-if="record.method_type == 'create'"
                                                :fileName="`company-policy-${record.title}`"
                                                :url="`company-policy-pdf/${record.xid}`"
                                                :payload="{
                                                    show_header_footer: 'no',
                                                }"
                                            >
                                                <template #custom="{ download }">
                                                    <a-menu-item
                                                        key="download"
                                                        @click="
                                                            () => {
                                                                table.loading = true;
                                                                download(record).then(
                                                                    () => {
                                                                        table.loading = false;
                                                                    }
                                                                );
                                                            }
                                                        "
                                                    >
                                                        <DownloadOutlined />
                                                        {{ $t("common.download") }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>
                                            <a-menu-item
                                                key="download"
                                                v-if="record.method_type == 'upload'"
                                            >
                                                <a-typography-link
                                                    :href="record.policy_document_url"
                                                    target="_blank"
                                                >
                                                    <DownloadOutlined />

                                                    {{ $t("common.download") }}
                                                </a-typography-link>
                                            </a-menu-item>
                                        </a-menu>
                                    </template>
                                </a-dropdown>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <View
            :data="companyPolicyData"
            :visible="visible"
            @close="closed"
            :pageTitle="$t('company_policy.company_policy_details')"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref } from "vue";
import { DownloadOutlined, EyeOutlined, MoreOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import datatable from "../../../../common/composable/datatable";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";

export default {
    components: {
        DownloadOutlined,
        EyeOutlined,
        MoreOutlined,
        AdminPageHeader,
        View,
        PdfDownload,
    },
    setup() {
        const { url, columns, filterableColumns, hashableColumns } = fields();
        const datatableVariables = datatable();
        const companyPolicyData = ref({});
        const visible = ref(false);

        const viewCompanyPolicy = (item) => {
            visible.value = true;
            companyPolicyData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        const extraFilters = ref({
            location_id: undefined,
        });

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url,
                extraFilters,
            };

            datatableVariables.table.filterableColumns = filterableColumns;
            datatableVariables.hashable.value = { ...hashableColumns };

            datatableVariables.fetch({
                page: 1,
            });
        };

        return {
            ...datatableVariables,
            columns,
            filterableColumns,
            setUrlData,
            companyPolicyData,
            visible,
            viewCompanyPolicy,
            closed,
        };
    },
};
</script>
