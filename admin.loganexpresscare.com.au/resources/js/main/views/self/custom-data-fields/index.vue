<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.custom_data_fields`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.custom_data_fields") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <a-row>
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="4"
            :xl="4"
            :style="{
                background: themeMode == 'dark' ? '#141414' : '#fff',
                borderRight:
                    themeMode == 'dark'
                        ? '1px solid #303030'
                        : '1px solid #f0f0f0',
            }"
        >
            <SettingSidebar
                :fieldTypesData="fieldTypesData"
                @sectionClicked="selectSection"
            />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <a-row class="mt-20">
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table
                                :columns="columns"
                                :row-key="(record) => record.xid"
                                :data-source="filteredDocuments"
                                :pagination="false"
                                bordered
                                size="middle"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template
                                        v-if="column.dataIndex === 'name'"
                                    >
                                        {{ record.field_types?.name }}
                                    </template>

                                    <template
                                        v-if="column.dataIndex === 'value'"
                                    >
                                        <template
                                            v-if="
                                                record.field_types?.type ===
                                                'file'
                                            "
                                        >
                                            <template v-if="record.values_url">
                                                <a-button
                                                    :href="record.values_url"
                                                    target="_blank"
                                                    type="link"
                                                    style="margin-left: -2%"
                                                >
                                                    <a-tag
                                                        color="success"
                                                        style="cursor: pointer"
                                                    >
                                                        <template #icon>
                                                            <DownloadOutlined />
                                                        </template>
                                                        {{
                                                            $t(
                                                                "common.download"
                                                            )
                                                        }}
                                                    </a-tag>
                                                </a-button>
                                            </template>
                                            <template v-else> - </template>
                                        </template>

                                        <template v-else>
                                            {{ record.values || "-" }}
                                        </template>
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>
<script>
import { onMounted, ref, computed } from "vue";
import { SaveOutlined, DownloadOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import Common from "../../../../common/composable/common";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import SettingSidebar from "./SettingSidebar.vue";

export default {
    components: {
        SaveOutlined,
        DownloadOutlined,
        SettingSidebar,
        AdminPageHeader,
        FormItemHeading,
    },
    setup() {
        const { loading, rules } = apiAdmin();
        const { t } = useI18n();
        const { themeMode } = Common();
        const userDocumentData = ref([]);
        const fieldTypesData = ref([]);
        const fieldTypeDataUrl = "self/field-types";
        const userDocumentUrl = "self/get-user-documents";
        const selectedSectionId = ref(null);

        onMounted(() => {
            setUrlData();
        });

        const selectSection = (xid) => {
            selectedSectionId.value = null;
            selectedSectionId.value = xid;
        };

        const columns = [
            {
                title: t("user.name"),
                dataIndex: "name",
                key: "name",
            },
            {
                title: t("user.value"),
                dataIndex: "value",
                key: "value",
            },
        ];

        const setUrlData = () => {
            const fieldTypesDataPromise = axiosAdmin.get(fieldTypeDataUrl);
            const userDocumentPromise = axiosAdmin.get(userDocumentUrl);

            Promise.all([fieldTypesDataPromise, userDocumentPromise]).then(
                ([fieldTypesDataResponse, userDocumentResponse]) => {
                    fieldTypesData.value =
                        fieldTypesDataResponse.data?.data || [];
                    userDocumentData.value =
                        userDocumentResponse.data?.data || [];
                }
            );
        };

        const filteredDocuments = computed(() =>
            userDocumentData.value.filter(
                (doc) =>
                    doc.field_types.x_field_type_id === selectedSectionId.value
            )
        );

        return {
            loading,
            rules,
            themeMode,
            columns,
            fieldTypesData,
            setUrlData,
            selectSection,
            selectedSectionId,
            userDocumentData,
            filteredDocuments,
        };
    },
};
</script>
