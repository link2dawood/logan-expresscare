<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.news`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.news`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
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
                                        $t('news.title'),
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
                                {{ record.title }}
                            </template>
                            <template v-if="column.dataIndex === 'visible'">
                                <div v-if="record.visible_to_all == 0">
                                    <span
                                        v-for="employee in record.news_user"
                                        :key="employee.xid"
                                    >
                                        {{ employee.user.name }} <br />
                                    </span>
                                </div>
                                <div v-else>
                                    {{ $t("common.all") }}
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    type="primary"
                                    @click="viewNewsData(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <View
            :data="newsData"
            :visible="visible"
            :pageTitle="$t('news.news_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import fields from "./fields";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import { useI18n } from "vue-i18n";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        AdminPageHeader,
        View,
    },
    setup() {
        const { t } = useI18n();
        const { columns, filterableColumns } = fields();
        const datatableVariables = datatable();
        const { permsArray, appSetting, formatDate, dayjs } = common();
        const newsData = ref({});
        const visible = ref(false);

        onMounted(() => {
            setUrlData();
        });

        const viewNewsData = (item) => {
            visible.value = true;
            newsData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `self/news?fields=id,xid,title,visible_to_all,description,status,newsUser{id,xid,user_id},newsUser:user{id,xid,name}`,
            };

            datatableVariables.table.filterableColumns = filterableColumns;

            datatableVariables.fetch({
                page: 1,
            });
        };

        return {
            ...datatableVariables,
            columns,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            setUrlData,
            newsData,
            visible,
            viewNewsData,
            closed,
        };
    },
};
</script>
