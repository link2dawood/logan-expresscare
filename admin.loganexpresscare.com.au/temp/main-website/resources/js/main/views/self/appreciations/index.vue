<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.appreciations`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.appreciations`) }}
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.award_id"
                            @change="setUrlData"
                            optionFilterProp="title"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('appreciation.award'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="award in awards"
                                :key="award.xid"
                                :value="award.xid"
                                :title="award.name"
                            >
                                {{ award.name }}
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
                            <template v-if="column.dataIndex === 'price_given'">
                                <span v-if="column.dataIndex === 'price_given'">
                                    <ul
                                        v-for="price in record.price_given"
                                        :key="price.price_given"
                                    >
                                        <li>{{ price.price_given }}</li>
                                    </ul>
                                </span>
                            </template>
                            <template v-if="column.dataIndex === 'description'">
                                {{
                                    record.description != null ? record.description : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'award_id'">
                                {{ record.award ? record.award.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'date'">
                                {{ formatDateTime(record.date) }}
                            </template>
                            <template v-if="column.dataIndex === 'price_amount'">
                                {{ formatAmountCurrency(record.price_amount) }}
                            </template>
                            <template v-if="column.dataIndex == 'active'">
                                {{ record.active ? $t("common.yes") : $t("common.no") }}
                            </template>
                            <template v-if="column.dataIndex === 'payout_type'">
                                {{
                                    record.payout_type == "monthly"
                                        ? $t("appreciation.monthly")
                                        : $t("appreciation.daily")
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="viewApprication(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-tooltip
                                        :title="$t('appreciation.appreciation_letter')"
                                    >
                                        <PdfDownload
                                            v-if="record.generate && record.generate.xid"
                                            :fileName="record.generate.title"
                                            :url="`generate-pdf/${record.generate.xid}`"
                                        />
                                    </a-tooltip>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <View
            :visible="visible"
            :data="appreciationData"
            :pageTitle="$t('appreciation.appreciation_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import { EyeOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import hrmManagement from "../../../../common/composable/hrmManagement";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";

export default {
    components: {
        EyeOutlined,
        AdminPageHeader,
        View,
        PdfDownload,
    },
    setup() {
        const { columns, filterableColumns, hashableColumns } = fields();
        const datatableVariables = datatable();
        const {
            permsArray,
            appSetting,
            formatDateTime,
            formatAmountCurrency,
            dayjs,
        } = common();
        const { monthArrays } = hrmManagement();
        const awards = ref([]);
        const awardsUrl = "self/awards";
        const filters = ref({ award_id: undefined });
        const extraFilters = ref({
            year: dayjs(),
            month: undefined,
        });

        const visible = ref(false);
        const appreciationData = ref({});

        const viewApprication = (item) => {
            visible.value = true;
            appreciationData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        onMounted(() => {
            const awardPromise = axiosAdmin.get(awardsUrl);
            Promise.all([awardPromise]).then(([awardResponse]) => {
                awards.value = awardResponse.data;
            });
            setUrlData();
        });

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `self/appreciations?fields=id,xid,user_id,x_user_id,user{id,xid,name},award_id,x_award_id,award{id,xid,name},date,description,price_amount,price_given,profile_image,profile_image_url,generates_id,x_generates_id,generate{id,xid,description,title,left_space,right_space,top_space,bottom_space}`,
                filters,
                extraFilters: {
                    ...extraFilters.value,
                    year: extraFilters.value.year.format("YYYY"),
                },
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
            permsArray,
            appSetting,
            formatDateTime,
            formatAmountCurrency,
            setUrlData,
            monthArrays,
            extraFilters,
            filters,
            awards,
            visible,
            appreciationData,
            viewApprication,
            closed,
        };
    },
};
</script>
