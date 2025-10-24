<template>
    <a-drawer
        title="View Asset Details"
        :width="drawerWidth"
        :visible="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
    >
        <div>
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="4" :lg="4">
                    <a-image :src="data.asset.image_url" />
                </a-col>
                <a-col :xs="24" :sm="24" :md="20" :lg="20">
                    <a-descriptions
                        title="Asset Info"
                        layout="horizontal"
                        :contentStyle="{
                            fontWeight: 500,
                            marginBottom: '20px',
                        }"
                    >
                        <a-descriptions-item :label="$t('asset.name')">
                            {{ data.asset ? data.asset?.name : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.lend_by')">
                            {{ data.lent_by ? data.lent_by.name : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.return_by')">
                            {{ data.return_by ? data.return_by.name : "-" }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.lend_date')">
                            {{
                                data.lend_date
                                    ? formatDate(data.lend_date)
                                    : "-"
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.return_date')">
                            {{
                                data.return_date
                                    ? formatDate(data.return_date)
                                    : "-"
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            :label="$t('asset.actual_return_date')"
                        >
                            {{
                                data.actual_return_date
                                    ? formatDate(data.actual_return_date)
                                    : "-"
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item :label="$t('asset.notes')">
                            {{ data.notes ? data.notes : "-" }}
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
            </a-row>
        </div>
    </a-drawer>
</template>
<script>
import { defineComponent } from "vue";
import common from "../../../../common/composable/common";

export default defineComponent({
    props: ["data", "visible"],
    emits: ["closed"],
    components: {},

    setup(props, { emit }) {
        const { formatDate } = common();

        const onClose = () => {
            emit("closed");
        };

        return {
            onClose,
            formatDate,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "70%",
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
