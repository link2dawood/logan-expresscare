<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header
                :title="$t(`menu.increments_promotions`)"
                class="p-0"
            />
        </template>
        <template #breadcrumb>
            <a-row align="middle" justify="space-between">
                <a-col>
                    <a-breadcrumb separator="-" style="font-size: 12px">
                        <a-breadcrumb-item>
                            <router-link
                                :to="{ name: 'admin.dashboard.index' }"
                            >
                                {{ $t(`menu.dashboard`) }}
                            </router-link>
                        </a-breadcrumb-item>
                        <a-breadcrumb-item>
                            {{ $t(`menu.payrolls`) }}
                        </a-breadcrumb-item>
                        <a-breadcrumb-item>
                            {{ $t(`menu.increments_promotions`) }}
                        </a-breadcrumb-item>
                    </a-breadcrumb>
                </a-col>
                <a-col>
                    <a-switch
                        v-model:checked="salaryVisibleAll"
                        :checked-children="$t('common.visible')"
                        :un-checked-children="$t('common.hidden')"
                        :style="{ top: '-20px', marginRight: '2px' }"
                    />
                </a-col>
            </a-row>
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
                                $t('common.select_default_text', [
                                    $t('holiday.year'),
                                ])
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
                                $t('common.select_default_text', [
                                    $t('holiday.month'),
                                ])
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
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs
                    v-model:activeKey="extraFilters.type"
                    @change="setUrlData"
                >
                    <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                    <a-tab-pane
                        key="increment"
                        :tab="`${$t('increment_promotion.increment')}`"
                    />
                    <a-tab-pane
                        key="promotion"
                        :tab="`${$t('increment_promotion.promotion')}`"
                    />
                    <a-tab-pane
                        key="increment_promotion"
                        :tab="`${$t(
                            'increment_promotion.increment_promotion'
                        )}`"
                    />
                    <a-tab-pane
                        key="decrement"
                        :tab="`${$t('increment_promotion.decrement')}`"
                    />
                    <a-tab-pane
                        key="decrement_demotion"
                        :tab="`${$t('increment_promotion.decrement_demotion')}`"
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
                            <template v-if="column.dataIndex == 'net_salary'">
                                <SalaryVisibility
                                    :salary="record.net_salary"
                                    :visibleAll="salaryVisibleAll"
                                />
                            </template>
                            <template v-if="column.dataIndex == 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                            <template
                                v-if="
                                    column.dataIndex ===
                                    'current_designation_id'
                                "
                            >
                                {{ record.current_designation?.name ?? "-" }}
                            </template>
                            <template
                                v-if="
                                    column.dataIndex ===
                                    'promoted_designation_id'
                                "
                            >
                                {{ record.promoted_designation?.name ?? "-" }}
                            </template>
                            <template v-if="column.dataIndex == 'description'">
                                {{ record.description }}
                            </template>
                            <template v-if="column.dataIndex == 'details'">
                                <span
                                    v-if="
                                        record.current_designation &&
                                        record.current_designation.name
                                    "
                                >
                                    {{
                                        $t(
                                            "increment_promotion.current_designation_id"
                                        )
                                    }}: {{ record.current_designation.name }}
                                </span>
                                <br />
                                <span
                                    v-if="
                                        record.promoted_designation &&
                                        record.promoted_designation.name
                                    "
                                >
                                    {{
                                        $t(
                                            "increment_promotion.promoted_designation_id"
                                        )
                                    }}:
                                    {{ record.promoted_designation.name }}
                                </span>
                            </template>

                            <template v-if="column.dataIndex === 'type'">
                                <div v-if="record.type == 'promotion'">
                                    <a-tag color="yellow">
                                        {{
                                            $t(
                                                `increment_promotion.${"promotion"}`
                                            )
                                        }}
                                    </a-tag>
                                </div>
                                <div v-if="record.type == 'increment'">
                                    <a-tag color="green">
                                        {{
                                            $t(
                                                `increment_promotion.${"increment"}`
                                            )
                                        }}
                                    </a-tag>
                                </div>
                                <div
                                    v-if="record.type == 'increment_promotion'"
                                >
                                    <a-tag color="green">
                                        {{
                                            $t(
                                                `increment_promotion.${"increment_promotion"}`
                                            )
                                        }}
                                    </a-tag>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="viewIncrementPromotion(record)"
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
        <View
            :data="promotionData"
            :visible="visible"
            :pageTitle="$t('pre_payment.pre_payment_details')"
            @close="closed"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import { EyeOutlined } from "@ant-design/icons-vue";
import fields from "./fields";
import datatable from "../../../../../common/composable/datatable";
import common from "../../../../../common/composable/common";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import View from "./View.vue";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import SalaryVisibility from "../../../../components/SalaryVisibility.vue";

export default {
    components: {
        EyeOutlined,
        AdminPageHeader,
        View,
        SalaryVisibility,
    },
    setup() {
        const { columns, filterableColumns, hashableColumns } = fields();
        const datatableVariables = datatable();
        const { monthArrays } = hrmManagement();
        const salaryVisibleAll = ref(false);

        const {
            permsArray,
            appSetting,
            formatDate,
            formatAmountCurrency,
            dayjs,
        } = common();
        const extraFilters = ref({
            type: "all",
            year: dayjs(),
            month: undefined,
        });
        const promotionData = ref({});
        const visible = ref(false);

        const viewIncrementPromotion = (item) => {
            visible.value = true;
            promotionData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `self/increments-promotions?fields=id,xid,user_id,x_user_id,user{id,xid,name},promoted_designation_id,x_promoted_designation_id,promotedDesignation{id,xid,name},current_designation_id,x_current_designation_id,currentDesignation{id,xid,name},net_salary,type,description,date`,
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
            formatDate,
            setUrlData,
            extraFilters,
            formatAmountCurrency,
            viewIncrementPromotion,
            visible,
            promotionData,
            closed,
            monthArrays,
            salaryVisibleAll,
        };
    },
};
</script>
