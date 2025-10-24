<template>
    <div class="table-responsive">
        <a-table
            :columns="columns"
            :row-key="(record) => record.xid"
            :data-source="data"
            :pagination="false"
            :loading="false"
            size="middle"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'user_id'">
                    <UserInfoVue :user="record.user" :size="30" />
                </template>
                <template v-if="column.dataIndex === 'award_id'">
                    {{ record.award?.name }}
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
import UserInfoVue from "@/common/components/user/UserInfo.vue";
import common from "../../../../common/composable/common";

export default {
    props: ["data", "visible", "pageTitle"],
    components: { UserInfoVue },
    setup() {
        const { t } = useI18n();
        const { formatDate } = common();
        const columns = [
            {
                title: t("appreciation.user"),
                dataIndex: "user_id",
            },
            {
                title: t("appreciation.date"),
                dataIndex: "date",
            },
            {
                title: t("appreciation.award"),
                dataIndex: "award_id",
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
