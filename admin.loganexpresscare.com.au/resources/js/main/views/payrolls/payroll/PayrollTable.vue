<template>
    <template v-if="$slots.actions">
        <slot
            name="actions"
            :generate="generate"
            :table="table"
            :setUrlData="setUrlData"
            :showSelectedDeleteConfirm="showSelectedDeleteConfirm"
            :regenerate="regenerate"
            :spinning="spinning"
        >
        </slot>
    </template>

    <admin-page-table-content :isPageTableContent="isPageTableContent">
        <template v-if="$slots.alert">
            <slot name="alert"></slot>
        </template>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: record.status !== 'generated',
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        :bordered="bordered"
                        :size="tableSize"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex == 'payment_date'">
                                {{
                                    record.payment_date != null
                                        ? formatDate(record.payment_date)
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex == 'month'">
                                {{ getMonthNameByNumber(record.month) }}
                                {{ record.year }}
                            </template>
                            <template v-if="column.dataIndex == 'net_salary'">
                                <SalaryVisibility
                                    :salary="record.net_salary"
                                    :visibleAll="salaryVisibleAll"
                                />
                            </template>
                            <template v-if="column.dataIndex == 'status'">
                                <div v-if="record.status == 'generated'">
                                    <a-tag color="yellow">
                                        {{ $t(`payroll.generated`) }}
                                    </a-tag>
                                </div>
                                <div v-if="record.status == 'paid'">
                                    <a-tag color="green">
                                        {{ $t(`common.paid`) }}
                                    </a-tag>
                                </div>
                            </template>

                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        v-if="
                                            permsArray.includes('payrolls_view') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="salaryView(record)"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('payrolls_edit') ||
                                            (permsArray.includes('admin') &&
                                                record.status !== 'paid')
                                        "
                                        type="primary"
                                        @click="() => editItem(record)"
                                    >
                                        <template #icon><EditOutlined /></template>
                                    </a-button>

                                    <a-button
                                        v-if="
                                            permsArray.includes('payrolls_delete') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                    >
                                        <template #icon><DeleteOutlined /></template>
                                    </a-button>
                                    <PdfDownload
                                        :title="$t('payroll.payroll_slip')"
                                        :fileName="`${
                                            record.user?.name
                                        }-${getMonthNameByNumber(record.month)}-${
                                            record.year
                                        }`"
                                        :url="`payroll-pdf/${record.xid}`"
                                        :payload="{
                                            title: `${$t('payroll.payroll_slip')} ${
                                                record.year
                                            }`,
                                            year: record.year,
                                            lang: selectedLang,
                                        }"
                                    />
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />

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
        @addListSuccess="reSetFormData"
    />

    <ViewSalary
        :addEditType="addEditType"
        :visible="viewVisible"
        :url="addEditUrl"
        @addEditSuccess="addEditSuccess"
        @closed="closeView"
        :formData="formData"
        :data="modalData"
        :pageTitle="$t('payroll.view_salary')"
        :successMessage="successMessage"
    />
</template>

<script>
import { onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    SendOutlined,
    ReloadOutlined,
    DollarCircleOutlined,
    DownloadOutlined,
    CloseOutlined,
    CheckOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import { notification } from "ant-design-vue";
import ViewSalary from "./ViewSalary.vue";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import { useI18n } from "vue-i18n";
import { find } from "lodash-es";
import SalaryVisibility from "../../../components/SalaryVisibility.vue";

export default {
    props: {
        selectable: {
            default: false,
        },
        isPageTableContent: {
            default: true,
        },
        tableSize: {
            default: "large",
        },
        bordered: {
            default: false,
        },
        filters: {
            default: {},
        },
        perPageItems: Number,
        salaryVisibleAll: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["onRowSelection"],
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        SendOutlined,
        ReloadOutlined,
        DollarCircleOutlined,
        DownloadOutlined,
        CloseOutlined,
        CheckOutlined,
        SalaryVisibility,

        AddEdit,
        UserInfo,
        ViewSalary,
        PdfDownload,
    },
    setup(props, { emit }) {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            url,
        } = fields();
        const {
            permsArray,
            appSetting,
            formatDate,
            formatAmountCurrency,
            selectedLang,
        } = common();
        const { getMonthNameByNumber } = hrmManagement();

        const crudVariables = crud();
        const { t } = useI18n();
        const spinning = ref(false);
        const viewVisible = ref(false);
        const modalData = ref();
        const downLoadPayrollSlip = window.config.download_payroll_slip;

        const userOpen = ref(false);
        const userId = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.x_user_id;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        onMounted(() => {
            if (
                permsArray.value.includes("payrolls_edit") ||
                permsArray.value.includes("payrolls_delete") ||
                permsArray.value.includes("admin")
            ) {
                columns.value = [
                    ...columns.value,
                    {
                        title: t("common.action"),
                        dataIndex: "action",
                    },
                ];
            }

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "payroll";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];

            setUrlData();
        });

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};
            var userFilter = {};

            if (tableFilter.user_id) {
                userFilter.user_id = tableFilter.user_id;
            }

            if (tableFilter.month) {
                extraFilterObject.month = tableFilter.month;
            }
            if (tableFilter.year) {
                extraFilterObject.year = tableFilter.year.format("YYYY");
            }
            crudVariables.tableUrl.value = {
                url,
                filters: userFilter,
                extraFilters: extraFilterObject,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const salaryView = (item) => {
            modalData.value = item;
            viewVisible.value = true;
        };
        const closeView = () => {
            viewVisible.value = false;
        };

        const generate = () => {
            spinning.value = true;
            axiosAdmin
                .post("payrolls/generate", {
                    month: props.filters.month,
                    year: props.filters.year.format("YYYY"),
                })
                .then((successResponse) => {
                    if (successResponse) {
                        spinning.value = false;
                    }
                    setUrlData();
                    notification.success({
                        message: t("common.success"),
                        description: t(`common.generated`),
                        placement: "bottomRight",
                    });
                });
        };

        const regenerate = () => {
            // For extracting user id from selected ids
            const slectedRowUserIds = [];
            crudVariables.table.selectedRowKeys.map((selectedRowKey) => {
                const filteredRow = find(crudVariables.table.data, {
                    xid: selectedRowKey,
                });
                if (filteredRow !== undefined) {
                    slectedRowUserIds.push(filteredRow.x_user_id);
                }
            });
            spinning.value = true;

            axiosAdmin
                .post("payrolls/generate", {
                    month: props.filters.month,
                    year: props.filters.year.format("YYYY"),
                    users: slectedRowUserIds,
                })
                .then((successResponse) => {
                    if (successResponse) {
                        spinning.value = false;
                    }
                    setUrlData();
                    notification.success({
                        message: t("common.success"),
                        description: t(`common.regenerated`),
                        placement: "bottomRight",
                    });
                });
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            viewVisible,

            downLoadPayrollSlip,
            modalData,
            regenerate,
            generate,
            spinning,
            salaryView,
            closeView,

            setUrlData,
            getMonthNameByNumber,
            formatDate,
            formatAmountCurrency,
            selectedLang,
            closed,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
