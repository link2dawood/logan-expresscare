<template>
    <template v-if="$slots.actions">
        <slot
            name="actions"
            :addItem="addItem"
            :table="table"
            :setUrlData="setUrlData"
            :showSelectedDeleteConfirm="showSelectedDeleteConfirm"
        >
        </slot>
    </template>

    <admin-page-table-content :isPageTableContent="isPageTableContent">
        <template v-if="$slots.tabs">
            <slot name="tabs" :setUrlData="setUrlData"></slot>
        </template>
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
                        :bordered="bordered"
                        :size="tableSize"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'user_id'">
                                <a-button
                                    type="link"
                                    @click="openUserView(record)"
                                >
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex == 'net_salary'">
                                <SalaryVisibility
                                    :salary="record.net_salary"
                                    :visibleAll="salaryVisibleAll"
                                />
                            </template>
                            <template v-if="column.dataIndex == 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                            <template v-if="column.dataIndex == 'description'">
                                {{ record.description }}
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
                                <div v-if="record.type == 'decrement_demotion'">
                                    <a-tag color="red">
                                        {{
                                            $t(
                                                `increment_promotion.${"decrement_demotion"}`
                                            )
                                        }}
                                    </a-tag>
                                </div>
                                <div v-if="record.type == 'decrement'">
                                    <a-tag color="red">
                                        {{
                                            $t(
                                                `increment_promotion.${"decrement"}`
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
                                    <a-button
                                        v-if="
                                            permsArray.includes(
                                                'increments_promotions_edit'
                                            ) || permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                    >
                                        <template #icon
                                            ><EditOutlined
                                        /></template>
                                    </a-button>
                                    <PdfDownload
                                        v-if="
                                            record.generate &&
                                            record.generate.xid
                                        "
                                        :fileName="record.generate.title"
                                        :url="`generate-pdf/${record.generate.xid}`"
                                    />
                                    <a-button
                                        v-if="
                                            permsArray.includes(
                                                'increments_promotions_delete'
                                            ) || permsArray.includes('admin')
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

    <View
        :data="promotionData"
        :visible="visible"
        :pageTitle="$t('pre_payment.pre_payment_details')"
        @close="closed"
    />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import View from "./View.vue";
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
        SalaryVisibility,
        AddEdit,
        UserInfo,
        View,
        PdfDownload,
    },
    setup(props, { emit }) {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            multiDimensalObjectColumns,
        } = fields();
        const { permsArray, appSetting, formatDate, formatAmountCurrency } =
            common();

        const crudVariables = crud();
        const promotionData = ref({});
        const visible = ref(false);
        const { t } = useI18n();

        const viewIncrementPromotion = (item) => {
            visible.value = true;
            promotionData.value = item;
        };

        const closed = () => {
            visible.value = false;
        };

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
                permsArray.value.includes("increments_promotions_edit") ||
                permsArray.value.includes("increments_promotions_delete") ||
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

            const newInitData = {
                ...initData,
                user_id:
                    props.filters.user_id != undefined
                        ? props.filters.user_id
                        : undefined,
            };

            const newData = {
                ...initData,
                user_id:
                    props.filters.user_id != undefined
                        ? props.filters.user_id
                        : undefined,
            };

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "increment_promotion";
            crudVariables.initData.value = newData;
            crudVariables.formData.value = newInitData;
            crudVariables.hashableColumns.value = [...hashableColumns];
            crudVariables.multiDimensalObjectColumns.value = {
                ...multiDimensalObjectColumns,
            };

            setUrlData();
        });

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};
            var userFilterObject = {};

            if (tableFilter.type) {
                extraFilterObject.type = tableFilter.type;
            }

            if (tableFilter.dates) {
                extraFilterObject.dates = tableFilter.dates;
            }
            if (tableFilter.user_id) {
                userFilterObject.user_id = tableFilter.user_id;
            }

            crudVariables.tableUrl.value = {
                url: "increments-promotions?fields=id,xid,letterhead_template_id,x_letterhead_template_id,letterHeadTemplate{id,xid},user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},user:designation{id,xid,name},user:location{id,xid,name},promoted_designation_id,x_promoted_designation_id,promotedDesignation{id,xid,name},current_designation_id,x_current_designation_id,currentDesignation{id,xid,name},net_salary,type,description,date,generate_id,x_generate_id,generate{id,xid,description,title}",
                filters: userFilterObject,
                extraFilters: extraFilterObject,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            formatDate,
            setUrlData,
            formatAmountCurrency,
            viewIncrementPromotion,
            promotionData,
            visible,
            closed,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
