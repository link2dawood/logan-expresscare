<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.basic_salaries`)" class="p-0" />
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
                            {{ $t(`menu.basic_salaries`) }}
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
            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                <a-space>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes(`users_delete`) ||
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
            <a-col :xs="24" :sm="24" :md="12" :lg="18" :xl="18">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-input-search
                            style="width: 100%"
                            v-model:value="table.searchString"
                            show-search
                            @change="onTableSearch"
                            @search="onTableSearch"
                            :loading="table.filterLoading"
                            :placeholder="
                                $t('common.placeholder_search_text', [
                                    $t('common.name'),
                                ])
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs
                    v-model:activeKey="extraFilters.status"
                    @change="setUrlData"
                >
                    <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                    <a-tab-pane key="active" :tab="`${$t('common.active')}`" />
                    <a-tab-pane
                        key="inactive"
                        :tab="`${$t('common.inactive')}`"
                    />
                </a-tabs>
            </a-col>
        </a-row>
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
                        <template #bodyCell="{ column, record }"
                            ><template v-if="column.dataIndex === 'name'">
                                <a-button
                                    type="link"
                                    @click="openUserView(record)"
                                >
                                    <UserInfo :user="record" />
                                </a-button>
                            </template>
                            <template
                                v-if="
                                    column.dataIndex === 'employee_salary_group'
                                "
                            >
                                <span
                                    v-if="
                                        record.salary_group_users &&
                                        record.salary_group_users.length
                                    "
                                >
                                    <span
                                        v-for="group in record.salary_group_users"
                                        :key="group.xid"
                                    >
                                        {{ group.salary_group?.name || "-" }}
                                    </span>
                                </span>
                                <span v-else>-</span>
                            </template>

                            <template
                                v-if="column.dataIndex === 'salary_component'"
                            >
                                <template
                                    v-if="
                                        record.salary_group_users?.some(
                                            (user) =>
                                                user.salary_group
                                                    ?.salary_group_components
                                                    ?.length
                                        )
                                    "
                                >
                                    <span
                                        v-for="(
                                            component, index
                                        ) in record.salary_group_users?.flatMap(
                                            (user) =>
                                                user.salary_group
                                                    ?.salary_group_components ||
                                                []
                                        ) || []"
                                        :key="index"
                                    >
                                        {{
                                            component.salary_component?.name ||
                                            "N/A"
                                        }}
                                        <span
                                            v-if="
                                                index <
                                                (
                                                    record.salary_group_users?.flatMap(
                                                        (user) =>
                                                            user.salary_group
                                                                ?.salary_group_components ||
                                                            []
                                                    ) || []
                                                ).length -
                                                    1
                                            "
                                        >
                                            ,
                                        </span>
                                    </span>
                                </template>
                                <span v-else>-</span>
                            </template>

                            <template
                                v-if="column.dataIndex === 'basic_salary'"
                            >
                                <SalaryVisibility
                                    :salary="record.basic_salary"
                                    :visibleAll="salaryVisibleAll"
                                />
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes(
                                            'salary_settings'
                                        ) || permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="modelOpen(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EditOutlined /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <AddEdit
            :user="recordData"
            :visible="addEditVisible"
            @close="closed"
            :pageTitle="$t('basic_salary.basic_salary')"
            addEditType="edit"
            @modelClose="modelClose"
        />
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
</template>
<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import datatable from "../../../../common/composable/datatable";
import AddEdit from "./AddEdit.vue";
import { useI18n } from "vue-i18n";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import crud from "../../../../common/composable/crud";
import SalaryVisibility from "../../../components/SalaryVisibility.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
        AddEdit,
        UserInfo,
        SalaryVisibility,
    },
    setup() {
        const {
            permsArray,
            statusColors,
            formatDateTime,
            user,
            formatAmountCurrency,
        } = common();
        const sampleFileUrl = window.config.staff_member_sample_file;
        const { t } = useI18n();
        const addEditVisible = ref(false);
        const recordData = ref("");
        const crudVariables = crud();
        const userOpen = ref(false);
        const userId = ref(undefined);
        const salaryVisibleAll = ref(false);

        const openUserView = (item) => {
            userId.value = item.xid;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        const extraFilters = ref({
            status: "active",
        });

        const closed = () => {
            addEditVisible.value = false;
        };
        const modelClose = () => {
            addEditVisible.value = false;
            setUrlData();
        };
        const modelOpen = (item) => {
            recordData.value = item;
            addEditVisible.value = true;
        };

        const columns = [
            {
                title: t("basic_salary.name"),
                dataIndex: "name",
            },
            {
                title: t("basic_salary.employee_salary_group"),
                dataIndex: "employee_salary_group",
            },
            {
                title: t("basic_salary.salary_component"),
                dataIndex: "salary_component",
            },
            {
                title: t("basic_salary.basic_salary"),
                dataIndex: "basic_salary",
            },
            {
                title: t("common.action"),
                dataIndex: "action",
            },
        ];

        onMounted(() => {
            setUrlData();
        });

        const filterableColumns = [
            {
                key: "name",
                value: t("user.name"),
            },
        ];

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: "users?fields=id,xid,name,basic_salary,annual_ctc,annual_amount,monthly_amount,ctc_value,special_allowances,calculation_type,profile_image,profile_image_url,designation{id,xid,name},location{id,xid,name},salaryGroupUsers{id,xid,salary_group_id,x_salary_group_id},salaryGroupUsers:salaryGroup{id,xid,name},salaryGroupUsers:salaryGroup:salaryGroupComponents{id,xid},salaryGroupUsers:salaryGroup:salaryGroupComponents{id,xid,x_salary_component_id,x_salary_component_id},salaryGroupUsers:salaryGroup:salaryGroupComponents:salaryComponent{id,xid,name,value_type,type,monthly,semi_monthly,weekly,bi_weekly},basicSalaryDetails{id,xid,user_id,x_user_id,salary_component_id,x_salary_component_id,type,value_type,monthly},salaryGroup{id,xid,name},salaryGroup:salaryGroupComponents{id,xid,x_salary_component_id,x_salary_component_id},salaryGroup:salaryGroupComponents:salaryComponent{id,xid,name,value_type,type,monthly,semi_monthly,weekly,bi_weekly}",
                extraFilters,
            };

            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            columns,
            filterableColumns,
            permsArray,
            statusColors,
            extraFilters,
            formatDateTime,
            ...crudVariables,
            sampleFileUrl,
            setUrlData,
            user,
            closed,
            modelOpen,
            addEditVisible,
            recordData,
            formatAmountCurrency,
            modelClose,
            userOpen,
            userId,
            openUserView,
            closeUser,
            salaryVisibleAll,
        };
    },
};
</script>
