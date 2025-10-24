<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.company`)" class="p-0">
                <template
                    v-if="
                        permsArray.includes('font_settings') ||
                        permsArray.includes('admin')
                    "
                    #extra
                >
                    <PdfFontSettings @close="closed" btnType="primary">
                        <template #icon>
                            <SettingOutlined />
                        </template>
                        {{ $t("pdf_font.pdf_settings") }}
                    </PdfFontSettings>
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.settings`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.pdf_fonts`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="4"
            :xl="4"
            :style="{
                background: themeMode == 'dark' ? '#141414' : '#fff',
                borderRight:
                    themeMode == 'dark' ? '1px solid #303030' : '1px solid #f0f0f0',
            }"
        >
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('pdf_fonts_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("pdf_font.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('pdf_fonts_delete') ||
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
                            <a-col :xs="24" :sm="24" :md="16" :lg="12" :xl="10">
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
                                    <template v-if="column.dataIndex === 'name'">
                                        <a-space direction="vertical">
                                            {{ record.name }}

                                            <span
                                                v-if="
                                                    getPdfFontSettingData &&
                                                    getPdfFontSettingData.x_pdf_font_id ==
                                                        record.xid
                                                "
                                            >
                                                <a-tag color="success">{{
                                                    $t("pdf_font.current")
                                                }}</a-tag>
                                            </span>
                                        </a-space>
                                    </template>
                                    <template v-if="column.dataIndex === 'user_kashida'">
                                        <span v-if="record.user_kashida == 1">
                                            {{ $t("common.yes") }}
                                        </span>
                                        <span v-else>
                                            {{ $t("common.no") }}
                                        </span>
                                    </template>
                                    <template v-if="column.dataIndex === 'use_otl'">
                                        <span v-if="record.use_otl == 255">
                                            {{ $t("common.yes") }}
                                        </span>
                                        <span v-else>
                                            {{ $t("common.no") }}
                                        </span>
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-typography-link
                                            v-if="record && record.file_url"
                                            :href="record.file_url"
                                            target="_blank"
                                        >
                                            <a-button
                                                type="primary"
                                                style="margin-left: 4px"
                                            >
                                                <template #icon
                                                    ><DownloadOutlined
                                                /></template>
                                            </a-button>
                                        </a-typography-link>

                                        <a-button
                                            v-if="
                                                permsArray.includes('pdf_fonts_edit') ||
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
                                                permsArray.includes('pdf_fonts_delete') ||
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
        </a-col>
    </a-row>
</template>

<script>
import { onMounted, ref, computed } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined,
    SettingOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import PdfFontSettings from "./PdfFontSettings.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        DownloadOutlined,
        SettingOutlined,
        PdfFontSettings,
        AddEdit,
        SettingSidebar,
        AdminPageHeader,
    },
    setup() {
        const { permsArray, themeMode, appSetting } = common();
        const { url, addEditUrl, initData, columns, filterableColumns } = fields();
        const crudVariables = crud();
        const getPdfFontSettingData = computed(() => appSetting.value);

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "pdf_font";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
        };

        const closed = () => {
            setUrlData();
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            themeMode,
            closed,
            getPdfFontSettingData,
            setUrlData,
        };
    },
};
</script>
