<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header
                :title="$t(`menu.email_templates`)"
                @back="
                    () =>
                        $router.push({
                            name: 'admin.settings.email.index',
                        })
                "
                class="p-0"
            />
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
                    <router-link :to="{ name: 'admin.settings.email.index' }">
                        {{ $t(`menu.email_settings`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.email_templates`) }}
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
            class="bg-setting-sidebar"
        >
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col :xs="24" :sm="24" :md="16" :lg="12" :xl="10">
                                <a-input-group compact>
                                    <a-select
                                        style="width: 25%"
                                        v-model:value="table.searchColumn"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                '',
                                            ])
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
                <a-tabs
                    v-model:activeKey="activeTabKey"
                    style="margin-top: -12px"
                >
                    <a-tab-pane
                        key="company"
                        :tab="$t('mail_settings.company')"
                    />
                    <a-tab-pane
                        key="employee"
                        :tab="$t('mail_settings.employee')"
                    />
                </a-tabs>
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
                                        v-if="column.dataIndex === 'name'"
                                    >
                                        {{ record?.credentials?.title ?? "" }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'type'"
                                    >
                                        {{
                                            $t(
                                                `mail_settings.${record.name_key
                                                    .replace(/^employee_/, "")
                                                    .replace(/^company_/, "")
                                                    .replace(/^/, "on_")}`
                                            )
                                        }}
                                    </template>

                                    <template
                                        v-if="column.dataIndex === 'action'"
                                    >
                                        <a-button
                                            v-if="
                                                permsArray.includes(
                                                    'locations_edit'
                                                ) ||
                                                permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="editItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon
                                                ><EditOutlined
                                            /></template>
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
import { ref, onMounted, watch } from "vue";
import { PlusOutlined, EditOutlined } from "@ant-design/icons-vue";
import common from "../../../../../common/composable/common";
import SettingSidebar from "../../SettingSidebar.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import crud from "../../../../../common/composable/crud";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,

        SettingSidebar,
        AdminPageHeader,
        AddEdit,
    },
    setup() {
        const { permsArray, themeMode } = common();
        const { url, addEditUrl, initData, columns, filterableColumns } =
            fields();
        const crudVariables = crud();
        const activeTabKey = ref("company");
        const extraFilters = ref({});

        onMounted(() => {
            extraFilters.value = { type: activeTabKey.value };
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "email_template";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
        });

        // Add this watcher to filter data based on tab
        watch(activeTabKey, (newKey) => {
            extraFilters.value = { type: newKey };
            crudVariables.fetch({ page: 1 });
        });

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            themeMode,
            activeTabKey,
        };
    },
};
</script>
