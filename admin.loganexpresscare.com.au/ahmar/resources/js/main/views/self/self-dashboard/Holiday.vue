<template>
    <div class="table-responsive">
        <a-table
            :columns="columns"
            :row-key="(record) => record.xid"
            :data-source="data"
            :pagination="false"
            :loading="false"
            bordered
            size="middle"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'user_id'">
                    {{ record.name }}
                </template>

                <template v-if="column.dataIndex === 'date'">
                    {{ formatDate(record.date) }}
                </template>
            </template>
        </a-table>
    </div>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

export default {
    props: ["data", "visible", "pageTitle"],
    components: {},
    setup() {
        const { t } = useI18n();
        const { formatDate } = common();

        const columns = [
            {
                title: t("holiday.name"),
                dataIndex: "name",
            },

            {
                title: t("holiday.date"),
                dataIndex: "date",
            },
        ];

        onMounted(() => {});

        return {
            columns,
            formatDate,
        };
    },
};
</script>
