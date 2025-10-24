<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.users`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.users`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('users_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <SubscriptionModuleVisibility moduleName="user">
                            <a-space>
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("user.add") }}
                                </a-button>
                                <a-button type="primary" @click="addItems">
                                    <PlusOutlined />
                                    {{ $t("user.quick_add") }}
                                </a-button>
                            </a-space>
                        </SubscriptionModuleVisibility>
                    </template>
                    <ImportUsers
                        :pageTitle="$t('common.import')"
                        importUrl="users/import"
                        @onUploadSuccess="setUrlData"
                        :checkVisible="false"
                        :buttonType="true"
                        :url="`users-static-csv`"
                        :fileName="$t('user.sample_users_file')"
                    />
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes(`${userType}_delete`) ||
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
            <a-col :xs="24" :sm="24" :md="12" :lg="16" :xl="16">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="extraFilters.shift"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('user.shift_id'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="shift in shifts"
                                :key="shift.xid"
                                :value="shift.xid"
                                :title="shift.name"
                            >
                                {{ shift.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="extraFilters.location"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('user.location_id'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="location in locations"
                                :key="location.xid"
                                :value="location.xid"
                                :title="location.name"
                            >
                                {{ location.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="extraFilters.department"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('user.department_id'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="department in departments"
                                :key="department.xid"
                                :value="department.xid"
                                :title="department.name"
                            >
                                {{ department.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                        <a-select
                            v-model:value="extraFilters.designation"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('user.designation_id'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="designation in designations"
                                :key="designation.xid"
                                :value="designation.xid"
                                :title="designation.name"
                            >
                                {{ designation.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-input-search
                            style="width: 100%"
                            v-model:value="table.searchString"
                            show-search
                            :allowClear="true"
                            @change="onTableSearch"
                            @search="onTableSearch"
                            :loading="table.filterLoading"
                            :placeholder="
                                $t('common.placeholder_search_text', [
                                    $t('common.name_email'),
                                ])
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <SubscriptionModuleVisibilityMessage moduleName="user" />

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
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: user.xid == record.xid ? true : false,
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
                        <template #bodyCell="{ column, text, record }">
                            <template v-if="column.dataIndex === 'name'">
                                <a-button
                                    type="link"
                                    @click="openUserView(record)"
                                >
                                    <user-info :user="record" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'report_to'">
                                {{
                                    record.reporter ? record.reporter.name : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'location_id'">
                                {{
                                    record.location ? record.location.name : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'department'">
                                {{
                                    record.department
                                        ? record.department.name
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'duration'">
                                {{ record.duration ? record.duration : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <a-tag :color="userStatusColors[text]">
                                    {{ $t(`common.${text}`) }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'created_at'">
                                {{ formatDateTime(record.created_at) }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes('users_view') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="viewItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('users_edit') ||
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
                                        permsArray.includes('users_delete') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="showDeleteConfirm(record.xid)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon
                                        ><DeleteOutlined
                                    /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <user-view-page
        :visible="userOpen"
        :userId="userId"
        :getUserDocumentData="getUserDocumentData"
        @closed="closeUser"
    />
    <ViewVue
        :user="viewData"
        :visible="detailsVisible"
        @closed="onCloseDetails"
    />
    <AddQuick
        :visible="detailsVisibles"
        @closed="detailsVisibles = false"
        addEditType="add"
        url="users"
        @addEditSuccess="setUrlData"
        :successMessage="successMessage"
        :pageTitle="pageTitle"
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
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ImportUsers from "../../../../common/core/ui/Import.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import ViewVue from "./View.vue";
import AddQuick from "./AddQuick .vue";
import UserListDisplayVue from "@/common/components/user/UserListDisplay.vue";
import SubscriptionModuleVisibility from "../../../../common/components/common/visibility/SubscriptionModuleVisibility.vue";
import SubscriptionModuleVisibilityMessage from "../../../../common/components/common/visibility/SubscriptionModuleVisibilityMessage.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        UserListDisplayVue,
        AddEdit,
        AdminPageHeader,
        ImportUsers,
        UserInfo,
        ViewVue,
        AddQuick,
        SubscriptionModuleVisibility,
        SubscriptionModuleVisibilityMessage,
    },
    setup() {
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const { permsArray, userStatusColors, formatDateTime, user } = common();
        const sampleFileUrl = window.config.staff_member_sample_file;
        const extraFilters = ref({
            status: "active",
            location: undefined,
            department: undefined,
            designation: undefined,
            shift: undefined,
        });
        const departments = ref([]);
        const designations = ref([]);
        const locations = ref([]);
        const shifts = ref([]);
        const shiftUrl = "shifts?limit=10000";
        const departmentUrl = "departments?limit=10000";
        const designationUrl = "designations?limit=10000";
        const locationUrl = "locations?limit=10000";
        const detailsVisibles = ref(false);
        const userOpen = ref(false);
        const userId = ref(undefined);
        const getUserDocumentData = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.xid;
            getUserDocumentData.value = item;
            userOpen.value = true;
        };

        const closeUser = () => {
            crudVariables.viewData.value = {};
            userOpen.value = false;
        };

        onMounted(() => {
            setUrlData();
            const locationPromise = axiosAdmin.get(locationUrl);
            const departmentsPromise = axiosAdmin.get(departmentUrl);
            const designationsPromise = axiosAdmin.get(designationUrl);
            const shiftsPromise = axiosAdmin.get(shiftUrl);

            Promise.all([
                departmentsPromise,
                designationsPromise,
                locationPromise,
                shiftsPromise,
            ]).then(
                ([
                    departmentsResponse,
                    designationsResponse,
                    locationResponse,
                    shiftsResponse,
                ]) => {
                    departments.value = departmentsResponse.data;
                    designations.value = designationsResponse.data;
                    locations.value = locationResponse.data;
                    shifts.value = shiftsResponse.data;
                }
            );
        });

        const addItems = () => {
            detailsVisibles.value = true;
        };

        const setUrlData = () => {
            detailsVisibles.value = false;
            crudVariables.tableUrl.value = {
                url: url,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "user";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = { ...hashableColumns };
        };

        return {
            columns,
            filterableColumns,
            permsArray,
            userStatusColors,
            formatDateTime,
            ...crudVariables,
            sampleFileUrl,
            setUrlData,
            user,
            departments,
            designations,
            locations,
            shifts,
            extraFilters,
            addItems,
            detailsVisibles,
            openUserView,
            closeUser,
            userOpen,
            userId,
            getUserDocumentData,
        };
    },
};
</script>
