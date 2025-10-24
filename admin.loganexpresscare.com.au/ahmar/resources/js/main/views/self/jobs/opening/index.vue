<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.openings`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.openings`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="extraFilters.job_category_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('opening.job_category_id'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="title"
                                >
                                    <a-select-option
                                        v-for="jobCategory in jobCategories"
                                        :key="jobCategory.xid"
                                        :value="jobCategory.xid"
                                        :title="jobCategory.name"
                                    >
                                        {{ jobCategory.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="extraFilters.location_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('opening.location_id'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="title"
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
                            <a-col :xs="24" :sm="24" :md="16" :lg="6" :xl="6">
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
                                                $t('opening.job_title'),
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
                />

                <a-row>
                    <a-col :span="24">
                        <a-tabs
                            v-model:activeKey="tagFilters.list_type"
                            @change="setUrlData"
                        >
                            <a-tab-pane key="opening" :tab="`${$t('menu.openings')}`" />
                            <a-tab-pane
                                key="applied"
                                :tab="`${$t('application.applied')}`"
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
                                <template #bodyCell="{ column, record, text }">
                                    <template v-if="column.dataIndex === 'active'">
                                        <a-tag :color="openingColors[text]">
                                            {{
                                                record.active == 1
                                                    ? $t(`common.yes`)
                                                    : $t(`common.no`)
                                            }}
                                        </a-tag>
                                    </template>
                                    <template v-if="column.dataIndex === 'opening_id'"
                                        >{{ record.opening?.job_title }}
                                    </template>
                                    <template v-if="column.dataIndex === 'location_id'"
                                        >{{ record.location?.name }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'job_category_id'"
                                        >{{ record.job_catgory?.name }}
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-button
                                            v-if="tagFilters.list_type == 'opening'"
                                            type="primary"
                                            @click="applicationAddOpen(record)"
                                        >
                                            {{ $t("application.apply") }}
                                        </a-button>
                                        <a-button
                                            v-if="tagFilters.list_type == 'applied'"
                                            type="primary"
                                            @click="viewItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><EyeOutlined /></template>
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
            </admin-page-table-content>
        </a-col>
    </a-row>
    <ApplicationAdd
        addEditType="add"
        :visible="applicationVisible"
        :url="applicationUrl"
        @addEditSuccess="applicationAddClose"
        @closed="applicationAddClose"
        :formData="fieldData"
        :recordData="recordData"
        :pageTitle="$t('application.add')"
        :successMessage="$t('application.submitted')"
    />

    <ApplicationView
        :application="viewData"
        :visible="detailsVisible"
        @closed="onCloseDetail"
    />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ArrowDownOutlined,
    EyeOutlined,
    ArrowLeftOutlined,
    SendOutlined,
    MoreOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "../application/AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import ApplicationAdd from "../application/AddEdit.vue";
import ApplicationView from "./View.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ArrowDownOutlined,
        EyeOutlined,
        AddEdit,
        AdminPageHeader,
        ApplicationAdd,
        ApplicationView,
        ArrowLeftOutlined,
        SendOutlined,
        MoreOutlined,
    },
    setup() {
        const { permsArray } = common();
        const {
            url,
            addEditUrl,
            initData,
            openingColumns,
            detailsColumns,
            filterableColumns,
            hashableColumns,
            filterableColumns2,
            initDataApplication,
            hashableApplication,
        } = fields();
        const crudVariables = crud();
        const recordData = ref({});
        const applicationUrl = ref({});
        const fieldData = ref({
            address: "",
            applicant_name: "",
            contact_email: "",
            cover_letter: "",
            data_question: "",
            date_of_birth: undefined,
            gender: "female",
            image: undefined,
            image_url: undefined,
            opening_id: undefined,
            phone_number: "",
            resume: undefined,
            resume_url: undefined,
            source: "",
            stage: "initial",
        });
        const visibleResponse = ref(false);
        const applicationId = ref("");
        const jobIds = ref([]);
        const columns = ref([]);
        const applicationVisible = ref(false);
        const locations = ref([]);
        const jobCategories = ref([]);
        const locationUrl = "self/locations";
        const jobCategoriesUrl = "self/job-categories";
        const tagFilters = ref({
            list_type: "opening",
        });
        const extraFilters = ref({
            location_id: undefined,
            job_category_id: undefined,
            status: "1",
        });

        onMounted(() => {
            const jobCategoriesPromise = axiosAdmin.get(jobCategoriesUrl);
            const locationPromise = axiosAdmin.get(locationUrl);
            Promise.all([jobCategoriesPromise, locationPromise]).then(
                ([jobCategoriesResponse, locationResponse]) => {
                    jobCategories.value = jobCategoriesResponse.data;
                    locations.value = locationResponse.data;
                }
            );
            setUrlData();
        });

        const onCloseDetail = () => {
            crudVariables.detailsVisible.value = false;
        };

        const openingColors = {
            1: "success",
            0: "error",
        };
        const applicationAddOpen = (item) => {
            applicationVisible.value = true;
            applicationUrl.value = "applications";
            recordData.value = item;
        };
        const applicationAddClose = () => {
            applicationVisible.value = false;
            setUrlData();
            fieldData.value = {};
        };

        const viewResponse = (item) => {
            jobIds.value = [];

            visibleResponse.value = true;
            recordData.value = item;
        };

        const closed = () => {
            visibleResponse.value = false;
        };

        const setUrlData = () => {
            const applicationUrl =
                "self/applications?fields=id,xid,applicant_name,image,data_question,image_url,address,date_of_birth,gender,cover_letter,contact_email,phone_number,source,resume,resume_url,cover_letter,stage,created_at,opening_id,x_opening_id,opening{id,xid,job_title,publish_date,end_date}";

            columns.value = [];
            if (tagFilters.value.list_type == "applied") {
                columns.value = detailsColumns;
            } else {
                columns.value = openingColumns;
            }

            crudVariables.tableUrl.value = {
                url: tagFilters.value.list_type == "applied" ? applicationUrl : url,
                extraFilters:
                    tagFilters.value.list_type == "applied" ? [] : extraFilters.value,
            };
            crudVariables.table.filterableColumns =
                tagFilters.value.list_type == "applied"
                    ? filterableColumns2
                    : filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value =
                tagFilters.value.list_type == "applied"
                    ? "self/applications"
                    : addEditUrl;
            crudVariables.langKey.value = "opening";
            crudVariables.initData.value =
                tagFilters.value.list_type == "applied"
                    ? { ...initDataApplication }
                    : { ...initData };
            crudVariables.formData.value =
                tagFilters.value.list_type == "applied"
                    ? { ...initDataApplication }
                    : { ...initData };
            crudVariables.hashableColumns.value =
                tagFilters.value.list_type == "applied"
                    ? { ...hashableApplication }
                    : { ...hashableColumns };
        };

        return {
            permsArray,
            columns,
            ...crudVariables,
            filterableColumns,
            openingColors,
            applicationAddOpen,
            applicationVisible,
            applicationAddClose,
            setUrlData,
            recordData,
            applicationUrl,
            fieldData,
            closed,
            viewResponse,
            onCloseDetail,
            applicationId,
            jobIds,
            visibleResponse,
            extraFilters,
            locations,
            jobCategories,
            tagFilters,
        };
    },
};
</script>
