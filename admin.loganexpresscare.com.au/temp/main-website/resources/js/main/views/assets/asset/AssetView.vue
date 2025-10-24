<template>
    <a-drawer
        title="View Asset Details"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
    >
        <div>
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="4" :lg="4">
                    <a-image :src="data.image_url" />
                </a-col>
                <a-col :xs="24" :sm="24" :md="20" :lg="20">
                    <a-descriptions title="Asset Info" layout="horizontal">
                        <a-descriptions-item :label="$t('asset.name')">
                            {{ data.name ? data.name : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.asset_type_id')">
                            {{
                                data.asset_type && data.asset_type.name
                                    ? data.asset_type.name
                                    : "-"
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.serial_number')">
                            {{ data.serial_number ? data.serial_number : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.location_id')">
                            {{ data.location ? data.location.name : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.status')">
                            <a-tag :color="assetsColors[data.status]">
                                {{ $t(`common.${data.status}`) }}
                            </a-tag>
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.description')">
                            {{ data.description ? data.description : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.broken_by')">
                            {{ data.broken_by ? data.broken_by.name : "-" }}
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
            </a-row>

            <a-tabs v-model:activeKey="tabKeys">
                <a-tab-pane key="assets" :tab="$t('asset.asset_history')" force-render>
                    <AssetUserTableVue
                        ref="assetUserTableRef"
                        :filters="filters"
                        tableSize="middle"
                        :bordered="true"
                        :selectable="true"
                        :isPageTableContent="false"
                    >
                    </AssetUserTableVue>
                </a-tab-pane>
            </a-tabs>
        </div>
        <AddReturn
            :visible="editRecordVisible"
            :data="recordData"
            :asset="assetId"
            addEditType="edit"
            @closed="returnClosed"
            @addSuccess="returnSuccess"
        />
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, computed, nextTick } from "vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import fields from "./fields";
import { EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import AddReturn from "./AddReturn.vue";
import AssetUserTableVue from "./AssetUserTable.vue";

export default defineComponent({
    props: ["data", "visible"],
    emits: ["closed", "regetRecord"],
    components: {
        EditOutlined,
        DeleteOutlined,
        AddReturn,
        AssetUserTableVue,
    },

    setup(props, { emit }) {
        const { formatAmountCurrency, assetsColors } = common();
        const { recordViewColumns } = fields();
        const { t } = useI18n();
        const recordData = ref();
        const assetId = ref();
        const editRecordVisible = ref(false);
        const assetUserTableRef = ref(null);
        const tabKeys = ref("assets");
        const filters = ref({
            asset_id: undefined,
        });

        const onClose = () => {
            emit("closed");
        };

        const editItem = (item) => {
            editRecordVisible.value = true;
            recordData.value = item;
        };

        const returnClosed = () => {
            editRecordVisible.value = false;
        };

        const returnSuccess = () => {
            editRecordVisible.value = false;
            emit("regetRecord", assetId.value);
        };

        watch(
            () => props.data,
            async (newVal, oldVal) => {
                assetId.value = newVal.xid;
                filters.value.asset_id = newVal.xid;
                await nextTick();
                if (newVal) {
                    if (assetUserTableRef.value) {
                        assetUserTableRef.value.setUrlData();
                    }
                }
            }
        );

        return {
            formatAmountCurrency,
            onClose,
            recordViewColumns,
            editItem,
            returnClosed,
            recordData,
            editRecordVisible,
            assetId,
            returnSuccess,
            assetsColors,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "70%",
            filters,
            tabKeys,
            assetUserTableRef,
        };
    },
});
</script>

<style lang="less">
.user-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
</style>
