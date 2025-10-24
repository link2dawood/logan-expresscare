<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.feedbacks`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.survey_forms`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.feedbacks`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('feedbacks_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("feedback.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('feedbacks_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-date-picker
                            v-model:value="extraFilters.year"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            :allowClear="false"
                            picker="year"
                            @change="setUrlData"
                            style="width: 100%"
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
                                        $t('feedback.title'),
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
            @addListSuccess="reSetFormData"
        />

        <a-row>
            <a-col :span="24">
                <a-tabs v-model:activeKey="extraFilters.status" @change="setUrlData">
                    <a-tab-pane key="active" :tab="`${$t('feedback.active')}`" />
                    <a-tab-pane key="expired" :tab="`${$t('feedback.expired')}`" />
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
                            <template v-if="column.dataIndex === 'visible_to'">
                                <span v-if="record.visible_to == 1">
                                    {{ $t("feedback.all_employee") }}
                                </span>
                                <span v-else>
                                    <a-popover placement="bottom">
                                        <template #content>
                                            <p
                                                v-for="employee in record.feedback_user"
                                                :key="employee.xid"
                                            >
                                                <user-list-display
                                                    :user="employee.user"
                                                    whereToShow="select"
                                                />
                                            </p>
                                        </template>
                                        <template #title>
                                            <span>{{
                                                $t("feedback.selected_employees")
                                            }}</span>
                                        </template>
                                        <span>
                                            {{ record.feedback_user.length }}
                                            {{ $t("feedback.employee") }}
                                        </span>
                                    </a-popover>
                                </span>
                            </template>

                            <template v-if="column.dataIndex === 'replied'">
                                <a-typography-link
                                    type="link"
                                    @click="viewResponse(record, (key = false))"
                                >
                                    {{ record.replied }}
                                </a-typography-link>
                            </template>
                            <template v-if="column.dataIndex === 'not_replied'">
                                <a-typography-link
                                    type="link"
                                    @click="viewResponse(record, (key = true))"
                                >
                                    {{ record.not_replied }}
                                </a-typography-link>
                            </template>

                            <template v-if="column.dataIndex === 'last_date'">
                                {{
                                    record.last_date ? formatDate(record.last_date) : "-"
                                }}
                            </template>

                            <template v-if="column.dataIndex === 'title'">
                                <a-typography-link
                                    type="link"
                                    @click="viewResponse(record, (key = false))"
                                >
                                    {{ record.title }}
                                </a-typography-link>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes('feedbacks_view') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="viewResponse(record, (key = false))"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('feedbacks_edit') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="editItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EditOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('feedbacks_delete') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="showDeleteConfirm(record.xid)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><DeleteOutlined /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <ResponseList
        :visible="visibleResponse"
        :data="recordData"
        :employeeIds="employeeIds"
        :feedbackId="feedbackId"
        :pageTitle="recordData.title"
        :tabKey="tagKeys"
        @close="closed"
    />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import { forEach } from "lodash-es";
import dayjs from "dayjs";
import isSameOrBefore from "dayjs/plugin/isSameOrBefore";
import ResponseList from "./ResponseLIst.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import hrmManagement from "../../../../common/composable/hrmManagement";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        ExclamationCircleOutlined,
        AddEdit,
        AdminPageHeader,
        ResponseList,
        UserListDisplay,
    },
    setup() {
        const { permsArray, user, formatDate, dayjs } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const loggedUser = ref("");
        const visibleResponse = ref(false);
        const recordData = ref({});
        const feedbackId = ref("");
        const employeeIds = ref([]);
        const activeKey = ref("active");
        const tagKeys = ref("");
        const { monthArrays } = hrmManagement();
        const extraFilters = ref({
            status: "active",
            year: dayjs(),
            month: undefined,
        });

        dayjs.extend(isSameOrBefore);

        onMounted(() => {
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
            crudVariables.langKey.value = "feedback";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = { ...hashableColumns };
        };

        const viewResponse = (item, key) => {
            tagKeys.value = key == true ? "not_replied" : "replied";
            employeeIds.value = [];
            forEach(item.feedback_user, (feedbackUser) => {
                employeeIds.value.push(feedbackUser.user.xid);
            });

            visibleResponse.value = true;
            recordData.value = item;
            feedbackId.value = item.xid;
        };

        const closed = () => {
            visibleResponse.value = false;
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            setUrlData,
            dayjs,
            isSameOrBefore,
            loggedUser,
            viewResponse,
            visibleResponse,
            recordData,
            closed,
            employeeIds,
            feedbackId,
            extraFilters,
            activeKey,
            formatDate,
            monthArrays,
            tagKeys,
        };
    },
};
</script>
