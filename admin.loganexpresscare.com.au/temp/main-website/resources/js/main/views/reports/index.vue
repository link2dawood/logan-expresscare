<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.reports`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.reports`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <admin-page-filters>
                <!-- <a-row :gutter="[16, 16]">
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
                </a-row> -->
            </admin-page-filters>

            <admin-page-table-content>
                <a-row>
                    <a-col :span="24">
                        <div class="table-responsive"></div>
                    </a-col>
                </a-row>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { onMounted, createVNode, reactive } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import UserInfo from "../../../common/components/user/UserInfo.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,

        AdminPageHeader,
        UserInfo,
    },
    setup() {
        const { permsArray, newsColors, dayjs } = common();

        const filters = reactive({
            year: dayjs().year(),
            filterMonthYear: undefined,
            date: undefined,
            status_date: [dayjs().format("YYYY-MM-DD"), dayjs().format("YYYY-MM-DD")],
            type: "all",
        });

        onMounted(() => {
            const reportPromise = axiosAdmin.post("reports", filters);

            Promise.all([reportPromise]).then(([reportResponse]) => {});
        });

        return {
            permsArray,
            newsColors,
        };
    },
};
</script>
