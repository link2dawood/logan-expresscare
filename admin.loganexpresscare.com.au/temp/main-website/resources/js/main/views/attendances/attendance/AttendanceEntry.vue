<template>
    <div
        v-if="
            permsArray.includes('attendances_view') ||
            permsArray.includes('admin')
        "
    >
        <a-button @click="opened" type="primary">
            <template #icon><FilePdfOutlined /> </template>
            {{ $t("common.export") }}
        </a-button>
    </div>
    <a-modal
        :title="$t('attendance.attendance_statement')"
        :open="visible"
        :closable="false"
        :centered="true"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('attendance.user')"
                        name="user_ids"
                        :help="rule.user_ids ? rule.user_ids.message : null"
                        :validateStatus="rule.user_ids ? 'error' : null"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.user_ids"
                                mode="multiple"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('attendance.user'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                                @change="resetName(formData.user_ids)"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                    :title="user.name"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('attendance.date')"
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
                            <a-radio-button value="attendance-pdf">
                                {{ $t("common.pdf") }}</a-radio-button
                            >
                            <a-radio-button value="attendance-csv">{{
                                $t("common.csv")
                            }}</a-radio-button>
                            <a-radio-button value="attendance-xlsx">{{
                                $t("common.excel")
                            }}</a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item v-if="formData.doc_type === 'attendance-pdf'">
                        <a-checkbox v-model:checked="formData.showHeaderFooter">
                            {{ $t("common.show_company_header_footer") }}
                        </a-checkbox>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <PdfDownload
                :title="$t('common.download')"
                :url="formData.doc_type"
                :validate="true"
                :fileName="`${attendanceName || 'users'}_${formatDate(
                    formData.date[0]
                )}_To_${formatDate(formData.date[1])}_attendance_statement`"
                :payload="{
                    title: $t('attendance.attendance_statement'),
                    user_ids: formData.user_ids,
                    lang: 'en',
                    date: formData.date,
                    type: formData.type,
                    modalType: 'attendance',
                    showHeaderFooter: formData.showHeaderFooter,
                }"
                @downloaded="onClose"
                @addSuccess="errorHandle"
                ><template #icon><DownloadOutlined /></template>
            </PdfDownload>
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
            user_ids: [],
            date: [],
            doc_type: "attendance-pdf",
            showHeaderFooter: true,
            type: "pdf",
        });
        const dateRangePicker = ref(null);
        const users = ref({});
        const attendanceName = ref("");
        const userUrl = "users";
        const rule = ref({});

        const opened = () => {
            visible.value = true;
            const usersPromise = axiosAdmin.get(userUrl);
            Promise.all([usersPromise]).then(([userResponse]) => {
                users.value = userResponse.data;
                formData.user_ids = [];
                formData.date = [];
            });
        };

        const onClose = () => {
            formData.user_ids = [];
            formData.date = [];
            rule.value = {};
            visible.value = false;
        };

        const resetName = (ids) => {
            if (Array.isArray(ids) && ids.length > 0) {
                const selectedUsers = users.value.filter((u) =>
                    ids.includes(u.xid)
                );
                attendanceName.value = selectedUsers
                    .map((u) => u.name)
                    .join(", ");
            } else {
                attendanceName.value = "";
            }
        };

        const errorHandle = (obj) => {
            rule.value = obj;
        };

        watch(
            () => visible.value,
            async (newVal, oldVal) => {
                attendanceName.value = "";
                await nextTick();
                if (newVal) {
                    formData.doc_type = "attendance-pdf";
                    dateRangePicker.value.resetPicker();
                    formData.user_ids = [];
                }
            }
        );

        watch(
            () => formData.doc_type,
            (newVal) => {
                if (newVal === "attendance-pdf") {
                    formData.type = "pdf";
                } else if (newVal === "attendance-csv") {
                    formData.type = "csv";
                } else if (newVal === "attendance-xlsx") {
                    formData.type = "xlsx";
                } else {
                    formData.type = "pdf";
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
            users,
            attendanceName,
            resetName,
            dateRangePicker,
            errorHandle,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
