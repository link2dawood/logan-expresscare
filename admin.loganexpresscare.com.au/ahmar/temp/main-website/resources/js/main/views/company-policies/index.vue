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
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes(`company_policies_create`) ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("company_policy.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('company_policies_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="7" :xl="7">
                        <a-select
                            v-model:value="extraFilters.location_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('asset.location')])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="location in locations"
                                :key="location.xid"
                                :value="location.xid"
                                :title="location.name"
                            >
                                {{ location.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="7" :xl="7">
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
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'description'">
                                {{ record.description }}
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
                                                key="edit"
                                                v-if="
                                                    permsArray.includes(
                                                        `company_policies_edit`
                                                    ) || permsArray.includes('admin')
                                                "
                                                @click="editItem(record)"
                                            >
                                                <EditOutlined />
                                                {{ $t("common.edit") }}
                                            </a-menu-item>
                                            <a-menu-item
                                                key="delete"
                                                v-if="
                                                    permsArray.includes(
                                                        `company_policies_delete`
                                                    ) || permsArray.includes('admin')
                                                "
                                                @click="showDeleteConfirm(record.xid)"
                                            >
                                                <DeleteOutlined />
                                                {{ $t("common.delete") }}
                                            </a-menu-item>
                                            <a-menu-divider />
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
                                            <PdfDownload
                                                v-else
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
                                            <PdfDownload
                                                v-if="record.method_type == 'create'"
                                                :url="`company-policy-pdf/${record.xid}`"
                                                :fileName="`company-policy-${record.title}`"
                                            >
                                                <template #custom="{ download }">
                                                    <a-menu-item
                                                        key="download_full"
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
                                                        {{
                                                            $t(
                                                                "generate.download_header_footer"
                                                            )
                                                        }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>

                                            <a-menu-divider />
                                            <PdfDownload
                                                v-if="record.method_type == 'create'"
                                                :title="$t('common.print')"
                                                :url="`company-policy-pdf/${record.xid}`"
                                                :payload="{
                                                    show_header_footer: 'no',
                                                }"
                                                :isPrint="true"
                                            >
                                                <template #icon>
                                                    <PrinterOutlined />
                                                </template>
                                                <template #custom="{ download }">
                                                    <a-menu-item
                                                        key="print"
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
                                                        <PrinterOutlined />
                                                        {{ $t("common.print") }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>
                                            <PdfDownload
                                                v-if="record.method_type == 'create'"
                                                :title="$t('common.print')"
                                                :url="`company-policy-pdf/${record.xid}`"
                                                :isPrint="true"
                                            >
                                                <template #icon>
                                                    <PrinterOutlined />
                                                </template>
                                                <template #custom="{ download }">
                                                    <a-menu-item
                                                        key="print_full"
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
                                                        <PrinterOutlined />
                                                        {{
                                                            $t(
                                                                "generate.print_header_footer"
                                                            )
                                                        }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>
                                        </a-menu>
                                    </template>
                                </a-dropdown>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined,
    MoreOutlined,
    PrinterOutlined,
} from "@ant-design/icons-vue";
import AddEdit from "./AddEdit.vue";
import fields from "./fields";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        DownloadOutlined,
        MoreOutlined,
        PrinterOutlined,
        AddEdit,
        AdminPageHeader,
        PdfDownload,
    },
    setup() {
        const {
            url,
            addEditUrl,
            initData,
            columns,
            hashableColumns,
            filterableColumns,
        } = fields();
        const crudVariables = crud();
        const { permsArray } = common();
        const locations = ref([]);
        const locationUrl = "locations?limit=10000";
        const extraFilters = ref({
            location_id: undefined,
        });

        onMounted(() => {
            const locationPromise = axiosAdmin.get(locationUrl);
            Promise.all([locationPromise]).then(([locationResponse]) => {
                locations.value = locationResponse.data;
            });
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "company_policy";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            columns,
            ...crudVariables,
            permsArray,
            locations,
            extraFilters,
            setUrlData,
        };
    },
};
</script>
