<template>
    <a-drawer
        :title="data.name"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
    >
        <div class="user-details">
            <a-row :gutter="[16, 16]" justify="end">
                <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                    <PdfDownload
                        :title="$t('account.account_statement')"
                        :fileName="data.name"
                        :url="`account-pdf`"
                        :payload="{
                            title: $t('account.account_statement'),
                            xid: data.xid,
                            lang: selectedLang,
                            date: extraFilters.date,
                            type: extraFilters.type,
                        }"
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                    <DateRangePicker
                        ref="dateRangePickerRef"
                        @dateTimeChanged="
                            (changedDateTime) => {
                                extraFilters.date = changedDateTime;
                                setUrlData();
                            }
                        "
                    />
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                    <a-select
                        v-model:value="extraFilters.type"
                        @change="setUrlData"
                        show-search
                        style="width: 100%"
                        :placeholder="
                            $t('common.select_default_text', [$t('account.transition')])
                        "
                        :allowClear="true"
                        ><a-select-option value="">
                            {{ $t("account.all") }}
                        </a-select-option>
                        <a-select-option value="asset">
                            {{ $t("account.asset") }}
                        </a-select-option>
                        <a-select-option value="deposit">
                            {{ $t("account.deposit") }}
                        </a-select-option>
                        <a-select-option value="pre_payment">
                            {{ $t("account.pre_payment") }}
                        </a-select-option>
                        <a-select-option value="expense">
                            {{ $t("account.expense") }}
                        </a-select-option>
                        <a-select-option value="payroll">
                            {{ $t("account.payroll") }}
                        </a-select-option>
                        <a-select-option value="appreciation">
                            {{ $t("account.appreciation") }}
                        </a-select-option>
                        <a-select-option value="initial_balance">
                            {{ $t("account.initial_balance") }}
                        </a-select-option>
                    </a-select>
                </a-col>
            </a-row>

            <a-row :gutter="[16, 16]" class="mt-10">
                <a-col :span="24">
                    <div class="table-responsive">
                        <a-table
                            :data-source="table.data"
                            :pagination="table.pagination"
                            :loading="table.loading"
                            @change="handleTableChange"
                            bordered
                            size="middle"
                            :columns="transactionColumns"
                            :row-key="(record) => record.xid"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'account_number'">
                                    {{
                                        record.account.account_number
                                            ? record.account.account_number
                                            : "-"
                                    }}
                                </template>
                                <template v-if="column.dataIndex === 'name'">
                                    {{ record.account.name }}
                                </template>
                                <template v-if="column.dataIndex === 'date'">
                                    {{ formatDate(record.date) }}
                                </template>
                                <template v-if="column.dataIndex === 'amount'">
                                    {{ formatAmountCurrency(record.amount) }}
                                </template>
                                <template v-if="column.dataIndex === 'type'">
                                    <span v-if="record.type == 'asset'">
                                        {{ $t("account.asset") }}
                                    </span>
                                    <span v-if="record.type == 'deposit'">
                                        {{ $t("account.deposit") }}
                                    </span>
                                    <span v-if="record.type == 'expense'">
                                        {{ $t("account.expense") }}
                                    </span>
                                    <span v-if="record.type == 'pre_payment'">
                                        {{ $t("account.pre_payment") }}
                                    </span>
                                    <span v-if="record.type == 'payroll'">
                                        {{ $t("account.payroll") }}
                                    </span>
                                    <span v-if="record.type == 'appreciation'">
                                        {{ $t("account.appreciation") }}
                                    </span>
                                    <span v-if="record.type == 'initial_balance'">
                                        {{ $t("account.initial_balance") }}
                                    </span>
                                </template>
                            </template>
                        </a-table>
                    </div>
                </a-col>
            </a-row>
        </div>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, computed, nextTick } from "vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import common from "../../../../common/composable/common";
import datatable from "../../../../common/composable/datatable";
import fields from "./fields";
import DateRangePicker from "@/common/components/common/calendar/DateRangePicker.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";

export default defineComponent({
    props: ["data", "visible"],
    emits: ["closed"],
    components: { DateRangePicker, PdfDownload },
    setup(props, { emit }) {
        const { formatAmountCurrency, formatDate, selectedLang } = common();
        const { t } = useI18n();
        const datatableVariables = datatable();
        const { transactionColumns } = fields();
        const dateRangePickerRef = ref(null);
        const extraFilters = ref({
            date: [],
            account_id: undefined,
            type: "",
        });

        const onClose = () => {
            emit("closed");
        };

        const setUrlData = () => {
            datatableVariables.tableUrl.value = {
                url: `get-all-transitions?fields=id,xid,date,amount,type,account_id,x_account_id,account{id,xid,account_number,name}`,
                extraFilters,
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        watch(
            () => props.visible,

            async (newVal, oldVal) => {
                await nextTick();
                if (props.visible) {
                    extraFilters.value.date = [];

                    extraFilters.value.account_id = props.data.xid;
                    if (dateRangePickerRef.value) {
                        dateRangePickerRef.value.resetPicker();
                    }
                    setUrlData();
                }
            }
        );

        return {
            ...datatableVariables,
            formatAmountCurrency,
            formatDate,
            onClose,
            setUrlData,
            extraFilters,
            transactionColumns,
            selectedLang,
            dateRangePickerRef,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>

<style lang="less">
.user-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
</style>
