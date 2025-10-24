<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.assets`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.assets`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.assets`) }}
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
                            permsArray.includes('assets_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("asset.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('assets_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="7" :xl="7">
                        <a-select
                            v-model:value="extraFilters.location_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('asset.location')])
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="7" :xl="7">
                        <a-select
                            v-model:value="extraFilters.user_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('attendance.user')])
                            "
                            :allowClear="true"
                            optionFilterProp="title"
                        >
                            <a-select-option
                                v-for="user in users"
                                :key="user.xid"
                                :value="user.xid"
                                :title="user.name"
                            >
                                <user-list-display :user="user" whereToShow="select" />
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="16" :lg="7" :xl="7">
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
                                        $t('asset.name'),
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
                    <a-tab-pane key="working" :tab="`${$t('common.working')}`" />
                    <a-tab-pane key="not_working" :tab="`${$t('common.not_working')}`" />
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
                        <template #bodyCell="{ column, record, text }">
                            <template v-if="column.dataIndex === 'name'">
                                <a-typography-link @click="viewRecordItem(record)">
                                    {{ record.name }}
                                </a-typography-link>
                            </template>
                            <template v-if="column.dataIndex === 'asset_type_id'">
                                {{
                                    record.x_asset_type_id ? record.asset_type.name : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'location'">
                                {{ record.location ? record.location.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'image'">
                                <a-image :width="32" :src="record.image_url" />
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <a-tag :color="assetsColors[text]">
                                    {{ $t(`common.${text}`) }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'serial_number'">
                                {{ record.serial_number ? record.serial_number : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        v-if="
                                            permsArray.includes('assets_edit') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="viewRecordItem(record)"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('assets_edit') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                    >
                                        <template #icon><EditOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('assets_delete') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                    >
                                        <template #icon><DeleteOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            record.x_asset_user_id == null &&
                                            record.status == 'working'
                                        "
                                        @click="AddLent(record)"
                                        style="background-color: green; color: white"
                                    >
                                        {{ $t("asset.lent_to") }}</a-button
                                    >
                                    <a-button
                                        v-if="
                                            record.x_asset_user_id != null &&
                                            record.status == 'working'
                                        "
                                        type="primary"
                                        @click="RemoveLent(record)"
                                        danger
                                    >
                                        {{ $t("asset.return") }}</a-button
                                    >
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />

    <AddLend
        :data="recordData"
        :visible="seen"
        @closed="lendClosed"
        @addSuccess="setUrlData()"
    />
    <AddReturn
        :data="returnRecord"
        :visible="returnVisible"
        :asset="recordId"
        addEditType="add"
        @closed="returnClosed"
        @addSuccess="setUrlData"
    />
    <AssetViewVue
        :data="viewRecord"
        :visible="viewRecordVisible"
        @closed="viewClosed"
        @regetRecord="regetRecord"
    />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    UserAddOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import AddLend from "./AddLend.vue";
import AddReturn from "./AddReturn.vue";
import { find } from "lodash-es";
import AssetViewVue from "./AssetView.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        UserAddOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        AddLend,
        AddReturn,
        AssetViewVue,
        UserInfo,
        UserListDisplay,
    },
    setup() {
        const { permsArray, assetsColors } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const seen = ref(false);
        const returnVisible = ref(false);
        const returnRecord = ref();
        const recordData = ref();
        const recordId = ref();
        const viewRecord = ref();
        const viewRecordVisible = ref(false);
        const users = ref([]);
        const userUrl = "users";
        const locations = ref([]);
        const locationUrl = "locations";
        const extraFilters = ref({
            location_id: undefined,
            user_id: undefined,
            status: "working",
        });

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
            const usersPromise = axiosAdmin.get(userUrl);
            const locationPromise = axiosAdmin.get(locationUrl);
            Promise.all([usersPromise, locationPromise]).then(
                ([usersResponse, locationResponse]) => {
                    users.value = usersResponse.data;
                    locations.value = locationResponse.data;
                }
            );
            setUrlData();
        });

        const setUrlData = () => {
            returnClosed();
            seen.value = false;
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };
            crudVariables.langKey.value = "asset";
            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = { ...hashableColumns };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const regetRecord = (xid) => {
            setUrlData();
            const assetsPromise = axiosAdmin.get(
                `assets/${xid}?fields=id,xid,name,serial_number,image,image_url,description,status,location_id,x_location_id,location{id,xid,name}user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},asset_type_id,x_asset_type_id,assetType{id,xid,name},return{id,xid,lent_to,x_lent_to,lend_date,return_date,notes,lent_by,x_lent_by,return_by,x_return_by,actual_return_date},assetUser{id,xid,lent_to,x_lent_to,lend_date,return_date,notes,lent_by,x_lent_by,return_by,x_return_by,actual_return_date},assetUser:user{id,xid,name,profile_image,profile_image_url},assetUser:returnBy{id,xid,name,profile_image,profile_image_url},assetUser:lentBy{id,xid,name,profile_image,profile_image_url}`
            );

            Promise.all([assetsPromise]).then(([assetsResponse]) => {
                viewRecord.value = assetsResponse.data;
            });
        };

        const RemoveLent = (item) => {
            recordId.value = item.xid;
            returnRecord.value = item.return;
            returnVisible.value = true;
        };

        const AddLent = (item) => {
            seen.value = true;
            recordData.value = item;
        };

        const viewRecordItem = (item) => {
            viewRecord.value = item;
            viewRecordVisible.value = true;
        };

        const returnClosed = () => {
            returnVisible.value = false;
        };

        const lendClosed = () => {
            seen.value = false;
        };

        const viewClosed = () => {
            viewRecordVisible.value = false;
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            assetsColors,
            AddLent,
            recordData,
            seen,
            returnRecord,
            returnVisible,
            recordId,
            viewRecordVisible,
            viewRecord,
            extraFilters,
            users,
            locations,

            lendClosed,
            setUrlData,
            RemoveLent,
            returnClosed,
            viewRecordItem,
            viewClosed,
            regetRecord,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
