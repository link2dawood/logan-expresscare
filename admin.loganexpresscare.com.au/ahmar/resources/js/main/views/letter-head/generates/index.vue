<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.generates`)" class="p-0"> </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.letter_heads`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.generates`) }}
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
                            permsArray.includes('generates_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("generate.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('templates_delete') ||
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
                    <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                        <a-select
                            v-model:value="extraFilters.user_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('generate.user_id')])
                            "
                            :allowClear="true"
                            optionFilterProp="title"
                        >
                            <a-select-option
                                v-for="user in allUsers"
                                :key="user.xid"
                                :value="user.xid"
                                :title="user.name"
                                ><user-list-display :user="user" whereToShow="select" />
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                        <a-select
                            v-model:value="extraFilters.letterhead_template_id"
                            @change="setUrlData"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('generate.letter_head_template'),
                                ])
                            "
                            :allowClear="true"
                        >
                            <a-select-option
                                v-for="leaveType in letterTemplates"
                                :key="leaveType.xid"
                                :value="leaveType.xid"
                                :title="leaveType.title"
                            >
                                {{ leaveType.title }}
                            </a-select-option>
                        </a-select>
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
                            <template v-if="column.dataIndex === 'user_id'">
                                <a-button type="link" @click="openUserView(record)">
                                    <UserInfo :user="record.user" />
                                </a-button>
                            </template>
                            <template v-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-if="column.dataIndex === 'letterhead_template_id'"
                                >{{ record.letter_head_template.title }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-dropdown placement="bottomRight">
                                    <MoreOutlined />
                                    <template #overlay>
                                        <a-menu>
                                            <a-menu-item
                                                key="edit"
                                                v-if="
                                                    permsArray.includes(
                                                        'generates_edit'
                                                    ) || permsArray.includes('admin')
                                                "
                                                @click="editItem(record)"
                                            >
                                                <EditOutlined />
                                                {{ $t("common.edit") }}
                                            </a-menu-item>
                                            <a-menu-item
                                                key="delete"
                                                v-if="
                                                    permsArray.includes(
                                                        'generates_delete'
                                                    ) || permsArray.includes('admin')
                                                "
                                                @click="showDeleteConfirm(record.xid)"
                                            >
                                                <DeleteOutlined />
                                                {{ $t("common.delete") }}
                                            </a-menu-item>
                                            <a-menu-divider />
                                            <PdfDownload
                                                :fileName="`${record.user.name}-${record.letter_head_template.title}`"
                                                :url="`generate-pdf/${record.xid}`"
                                                :payload="{
                                                    show_header_footer: 'no',
                                                }"
                                            >
                                                <template #custom="{ load, download }">
                                                    <a-menu-item
                                                        key="download"
                                                        @click="
                                                            () => {
                                                                table.loading = true;
                                                                download(record).then(
                                                                    () => {
                                                                        table.loading = false;
                                                                    }
                                                                );
                                                            }
                                                        "
                                                    >
                                                        <DownloadOutlined />
                                                        {{ $t("common.download") }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>
                                            <PdfDownload
                                                :fileName="`${record.user.name}-${record.letter_head_template.title}`"
                                                :url="`generate-pdf/${record.xid}`"
                                            >
                                                <template #custom="{ load, download }">
                                                    <a-menu-item
                                                        key="download_full"
                                                        @click="
                                                            () => {
                                                                table.loading = true;
                                                                download(record).then(
                                                                    () => {
                                                                        table.loading = false;
                                                                    }
                                                                );
                                                            }
                                                        "
                                                    >
                                                        <DownloadOutlined />
                                                        {{
                                                            $t(
                                                                "generate.download_header_footer"
                                                            )
                                                        }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>

                                            <a-menu-divider />
                                            <PdfDownload
                                                :fileName="`${record.user.name}-${record.letter_head_template.title}`"
                                                :title="$t('common.print')"
                                                :url="`generate-pdf/${record.xid}`"
                                                :payload="{
                                                    show_header_footer: 'no',
                                                }"
                                                :isPrint="true"
                                            >
                                                <template #icon>
                                                    <PrinterOutlined />
                                                </template>
                                                <template #custom="{ load, download }">
                                                    <a-menu-item
                                                        key="print"
                                                        @click="
                                                            () => {
                                                                table.loading = true;
                                                                download(record).then(
                                                                    () => {
                                                                        table.loading = false;
                                                                    }
                                                                );
                                                            }
                                                        "
                                                    >
                                                        <PrinterOutlined />
                                                        {{ $t("common.print") }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>
                                            <PdfDownload
                                                :fileName="`${record.user.name}-${record.letter_head_template.title}`"
                                                :title="$t('common.print')"
                                                :url="`generate-pdf/${record.xid}`"
                                                :isPrint="true"
                                            >
                                                <template #icon>
                                                    <PrinterOutlined />
                                                </template>
                                                <template #custom="{ load, download }">
                                                    <a-menu-item
                                                        key="print_full"
                                                        @click="
                                                            () => {
                                                                table.loading = true;
                                                                download(record).then(
                                                                    () => {
                                                                        table.loading = false;
                                                                    }
                                                                );
                                                            }
                                                        "
                                                    >
                                                        <PrinterOutlined />
                                                        {{
                                                            $t(
                                                                "generate.print_header_footer"
                                                            )
                                                        }}
                                                    </a-menu-item>
                                                </template>
                                            </PdfDownload>
                                        </a-menu>
                                    </template>
                                </a-dropdown>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    MoreOutlined,
    DownloadOutlined,
    PrinterOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import { useRoute } from "vue-router";
import common from "../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import fields from "./fields";
import axios from "axios";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "@/common/components/user/UserListDisplay.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        DownloadOutlined,
        MoreOutlined,
        PrinterOutlined,

        AdminPageHeader,
        UserInfo,
        UserListDisplay,
        AddEdit,
        PdfDownload,
    },
    setup() {
        const { permsArray, appSetting, formatDate } = common();
        const route = useRoute();
        const {
            url,
            addEditUrl,
            columns,
            initData,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const allUsers = ref([]);
        const staffMembersUrl = "users";
        const letterTemplates = ref([]);
        const templateUrl = "letter-head-templates";
        const extraFilters = ref({
            user_id: undefined,
            letterhead_template_id: undefined,
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
            const letterTemplatePromise = axiosAdmin.get(templateUrl);
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise, letterTemplatePromise]).then(
                ([staffMemberResponse, letterTemplateResponse]) => {
                    allUsers.value = staffMemberResponse.data;
                    letterTemplates.value = letterTemplateResponse.data;
                }
            );
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "generate";

            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };

            crudVariables.hashableColumns.value = { ...hashableColumns };
        };

        return {
            permsArray,
            appSetting,
            columns,
            ...crudVariables,
            filterableColumns,
            setUrlData,
            letterTemplates,
            allUsers,
            extraFilters,
            formatDate,
            userOpen,
            userId,
            openUserView,
            closeUser,
        };
    },
};
</script>
