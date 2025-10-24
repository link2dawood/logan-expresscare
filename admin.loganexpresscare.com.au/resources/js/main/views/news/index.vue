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

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('news_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("news.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('news_delete') ||
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
                                <template #bodyCell="{ column, record, text }">
                                    <template v-if="column.dataIndex === 'title'">
                                        {{ record.title }}
                                    </template>
                                    <template v-if="column.dataIndex === 'visible'">
                                        <div v-if="record.visible_to_all == 0">
                                            <span v-for="employee in record.news_user">
                                                <UserInfo :user="employee.user" />
                                            </span>
                                        </div>
                                        <div v-else>
                                            {{ $t("common.all") }}
                                        </div>
                                    </template>
                                    <template v-if="column.dataIndex === 'status'">
                                        <a-tag :color="newsColors[text]">
                                            {{ $t(`common.${text}`) }}
                                        </a-tag>
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-space>
                                            <a-tooltip
                                                v-if="
                                                    (permsArray.includes('news_edit') ||
                                                        permsArray.includes('admin')) &&
                                                    record.status == 'draft'
                                                "
                                                :title="$t('news.publish')"
                                            >
                                                <a-button
                                                    type="primary"
                                                    @click="publishNews(record.xid)"
                                                    style="
                                                        background-color: green;
                                                        color: white;
                                                    "
                                                >
                                                    <template #icon>
                                                        <NotificationOutlined />
                                                    </template>
                                                </a-button>
                                            </a-tooltip>
                                            <a-button
                                                v-if="
                                                    permsArray.includes('news_edit') ||
                                                    permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="editItem(record)"
                                            >
                                                <template #icon
                                                    ><EditOutlined
                                                /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    permsArray.includes('news_delete') ||
                                                    permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="showDeleteConfirm(record.xid)"
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
        </a-col>
    </a-row>
</template>

<script>
import { onMounted, createVNode, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    NotificationOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import { notification, Modal } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import UserInfo from "../../../common/components/user/UserInfo.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,
        NotificationOutlined,

        AddEdit,
        AdminPageHeader,
        UserInfo,
    },
    setup() {
        const { permsArray, newsColors } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const { t } = useI18n();

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
            crudVariables.langKey.value = "news";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = { ...hashableColumns };
        };

        const publishNews = (newsId) => {
            Modal.confirm({
                title: t("news.publish") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("news.confirm_message"),
                centered: true,
                okText: t("news.publish"),
                okType: "danger",
                cancelText: t("news.cancel"),
                onOk() {
                    return sendRequest(newsId);
                },
                onCancel() {},
            });
        };

        const sendRequest = (newsId) => {
            return axiosAdmin
                .post(`news-publish/${newsId}`)
                .then((successResponse) => {
                    setUrlData();
                    notification.success({
                        message: t("news.news_published"),
                        description: t("news.news_published_success"),
                        placement: "bottomRight",
                    });
                })
                .catch(() => {
                    return Promise.reject();
                });
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            newsColors,
            setUrlData,
            publishNews,
        };
    },
};
</script>
