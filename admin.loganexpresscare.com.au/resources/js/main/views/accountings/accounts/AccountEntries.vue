<template>
    <div
        v-if="
            permsArray.includes('accounts_view') || permsArray.includes('admin')
        "
    >
        <a-button @click="opened" type="primary">
            <template #icon><FilePdfOutlined /> </template>
            {{ $t("account.account_statement") }}
        </a-button>
    </div>
    <a-modal
        :title="$t('account.account_statement')"
        :open="visible"
        :closable="false"
        :centered="true"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('deposit.account')"
                        name="account_id"
                        :help="rule.account_id ? rule.account_id.message : null"
                        :validateStatus="rule.account_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.account_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('deposit.account'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                                @change="resetName(formData.account_id)"
                            >
                                <a-select-option
                                    v-for="account in accounts"
                                    :key="account.xid"
                                    :value="account.xid"
                                    :title="account.name"
                                >
                                    {{ account.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('account.date')"
                        name="date"
                        :help="rule.date ? rule.date.message : null"
                        :validateStatus="rule.date ? 'error' : null"
                        class="required"
                    >
                        <DateRangePicker
                            ref="dateRangePicker"
                            @dateTimeChanged="
                                (changedDateTime) => {
                                    formData.date = changedDateTime;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item :label="$t('common.doc_type')" name="doc_type">
                        <a-radio-group
                            v-model:value="formData.doc_type"
                            button-style="solid"
                        >
                            <a-radio-button value="account-pdf">
                                {{ $t("common.pdf") }}</a-radio-button
                            >
                            <a-radio-button value="account-xlsx">{{
                                $t("common.excel")
                            }}</a-radio-button>
                            <a-radio-button value="account-csv">{{
                                $t("common.csv")
                            }}</a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <PdfDownload
                :title="$t('common.download')"
                :url="formData.doc_type"
                :validate="true"
                :fileName="`${accountName}_${formatDate(
                    formData.date[0]
                )}_To_${formatDate(formData.date[1])}_Statement`"
                :payload="{
                    title: $t('account.account_statement'),
                    xid: formData.account_id,
                    lang: 'en',
                    date: formData.date,
                    type: formData.type,
                }"
                @downloaded="onClose"
                @addSuccess="errorHandle"
                ><template #icon><DownloadOutlined /></template
            ></PdfDownload>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, reactive, watch, nextTick } from "vue";
import { DownloadOutlined, FilePdfOutlined } from "@ant-design/icons-vue";
import DateRangePicker from "@/common/components/common/calendar/DateRangePicker.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import common from "@/common/composable/common";
import { find } from "lodash-es";

export default defineComponent({
    props: ["pageTitle", "successMessage"],
    components: {
        DownloadOutlined,
        FilePdfOutlined,
        DateRangePicker,
        PdfDownload,
    },
    setup(props, { emit }) {
        const { permsArray, formatDate } = common();
        const visible = ref(false);
        const formData = reactive({
            account_id: undefined,
            date: [],
            doc_type: "account-pdf",
            type: "pdf",
        });
        const dateRangePicker = ref(null);
        const accounts = ref({});
        const accountName = ref("");
        const accountUrl = "accounts";
        const rule = ref({});

        const opened = () => {
            visible.value = true;
            const accountsPromise = axiosAdmin.get(accountUrl);
            Promise.all([accountsPromise]).then(([accountResponse]) => {
                accounts.value = accountResponse.data;
                formData.account_id = undefined;
                formData.date = [];
            });
        };

        const onClose = () => {
            formData.account_id = undefined;
            formData.date = [];
            rule.value = {};
            visible.value = false;
        };

        const resetName = (id) => {
            var accountDetail = find(accounts.value, { xid: id });
            if (accountDetail) {
                accountName.value = accountDetail.name;
            } else {
                accountName.value = "";
            }
        };

        const errorHandle = (obj) => {
            rule.value = obj;
        };

        watch(
            () => visible.value,
            async (newVal, oldVal) => {
                await nextTick();
                if (newVal) {
                    formData.doc_type = "account-pdf";
                    dateRangePicker.value.resetPicker();
                }
            }
        );
        watch(
            () => formData.doc_type,
            (newVal) => {
                if (newVal === "account-pdf") {
                    formData.type = "pdf";
                } else if (newVal === "account-csv") {
                    formData.type = "csv";
                } else if (newVal === "account-xlsx") {
                    formData.type = "xlsx";
                }
            }
        );

        return {
            permsArray,
            rule,
            opened,
            onClose,
            formatDate,
            formData,
            visible,
            accounts,
            accountName,
            resetName,
            dateRangePicker,
            errorHandle,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
