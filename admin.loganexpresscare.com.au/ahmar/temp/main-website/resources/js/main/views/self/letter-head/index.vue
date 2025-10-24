<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.letter_heads`)" class="p-0"> </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.letter_heads`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-date-picker
                            v-model:value="extraFilters.year"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            picker="year"
                            @change="setUrlData"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="extraFilters.month"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.month')])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
                        >
                            <a-select-option
                                v-for="month in monthArrays"
                                :key="month.name"
                                :value="month.value"
                                :title="month.name"
                            >
                                {{ month.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                        <a-select
                            v-model:value="extraFilters.letterhead_template_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('generate.letter_head_template'),
                                ])
                            "
                            :allowClear="true"
                            optionFilterProp="title"
                        >
                            <a-select-option
                                v-for="leaveType in letterTemplates"
                                :key="leaveType.xid"
                                :value="leaveType.xid"
                                :title="leaveType.title"
                            >
                                {{ leaveType.title }}
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
                <div class="table-responsive">
                    <a-table
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
                            <template v-if="column.dataIndex === 'created_at'"
                                >{{ formatDate(record.created_at) }}
                            </template>
                            <template v-if="column.dataIndex === 'letterhead_template_id'"
                                >{{ record.letter_head_template.title }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <PdfDownload
                                    v-if="
                                        record &&
                                        record.user &&
                                        record.letter_head_template
                                    "
                                    :fileName="`${record.user.name} - ${record.letter_head_template.title}`"
                                    :url="`generate-pdf/${record.xid}`"
                                />
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
    MoreOutlined,
    DownloadOutlined,
    PrinterOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import dayjs from "dayjs";
import hrmManagement from "../../../../common/composable/hrmManagement";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        DownloadOutlined,
        MoreOutlined,
        PrinterOutlined,

        AdminPageHeader,
        PdfDownload,
    },
    setup() {
        const { formatDate } = common();
        const { url, addEditUrl, columns, filterableColumns, hashableColumns } = fields();
        const crudVariables = crud();
        const letterTemplates = ref([]);
        const templateUrl = "self/letter-head-templates";
        const { monthArrays } = hrmManagement();
        const extraFilters = ref({
            letterhead_template_id: undefined,
            year: dayjs(),
            month: undefined,
        });

        onMounted(() => {
            const letterTemplatePromise = axiosAdmin.get(templateUrl);
            Promise.all([letterTemplatePromise]).then(([letterTemplateResponse]) => {
                letterTemplates.value = letterTemplateResponse.data;
            });
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "generate";
            crudVariables.hashableColumns.value = { ...hashableColumns };
        };

        return {
            columns,
            ...crudVariables,
            filterableColumns,
            setUrlData,
            letterTemplates,
            extraFilters,
            formatDate,
            monthArrays,
        };
    },
};
</script>
