<template>
    <a-card :bodyStyle="{ padding: '15px', height: '446px' }">
        <template #title>
            <BellOutlined />
            {{ $t("hrm_dashboard.weekend_holiday") }}
        </template>
        <template #extra>
            <a-date-picker
                v-model:value="extraFilters.year"
                :placeholder="$t('common.select_default_text', [$t('holiday.year')])"
                picker="year"
                style="width: 100%"
                :allowClear="false"
                @change="filterYearData(extraFilters.year)"
        /></template>

        <a-segmented
            v-model:value="activeKey"
            :options="pendingTabs"
            class="segment-control"
        />
        <a-result v-if="weekendHolidays.length <= 0"
            ><template #icon>
                <ExclamationCircleFilled
                    :style="{
                        fontSize: '52px',
                        padding: '0px',
                        marginTop: '35px !important',
                    }"
                />
            </template>
            <template #extra>
                <span
                    type="primary"
                    :style="{
                        fontSize: '28px',
                    }"
                    >{{ $t(`hrm_dashboard.there_is_no_${activeKey}`) }}</span
                >
            </template>
        </a-result>
        <div class="pending-leave-hrm">
            <perfect-scrollbar
                :options="{
                    wheelSpeed: 1,
                    swipeEasing: true,
                    suppressScrollX: true,
                }"
            >
                <a-list
                    item-layout="horizontal"
                    :data-source="weekendHolidays"
                    v-if="weekendHolidays.length > 0"
                >
                    <template #renderItem="{ item }">
                        <a-list-item>
                            <template #extra> </template>
                            <a-skeleton
                                avatar
                                :title="false"
                                :loading="!!item.loading"
                                active
                            >
                                <a-list-item-meta :description="item.reason">
                                    <template #title>
                                        <div class="notification-header">
                                            <div class="notification-text">
                                                <strong>{{
                                                    item.name == "weekend"
                                                        ? $t("hrm_dashboard.weekend")
                                                        : item.name
                                                }}</strong>
                                            </div>
                                            <div class="notification-time">
                                                {{ item.date }}
                                            </div>
                                        </div>
                                    </template>
                                </a-list-item-meta>
                            </a-skeleton>
                        </a-list-item>
                        <hr />
                    </template>
                </a-list>
            </perfect-scrollbar>
        </div>
    </a-card>
</template>

<script>
import { onMounted, ref, watch } from "vue";
import {
    CheckOutlined,
    CloseOutlined,
    BellOutlined,
    DoubleRightOutlined,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";

export default {
    props: ["data"],
    components: {
        CheckOutlined,
        CloseOutlined,
        BellOutlined,
        DoubleRightOutlined,
        ExclamationCircleFilled,
    },
    setup(props, { emit }) {
        const { permsArray, formatDate, dayjs } = common();
        const { t } = useI18n();
        const weekendHolidays = ref([]);
        const activeKey = ref("holidays");
        const filterMonthYear = ref(dayjs());
        const extraFilters = ref({
            year: dayjs(),
        });

        const pendingTabs = [
            { label: t("hrm_dashboard.holiday"), value: "holidays" },
            { label: t("hrm_dashboard.weekend"), value: "weekends" },
        ];
        onMounted(() => {
            updateWeekendHolidays();
        });
        const updateWeekendHolidays = () => {
            if (activeKey.value === "weekends") {
                weekendHolidays.value = props.data.categorizeHolidays?.weekends || [];
            } else if (activeKey.value === "holidays") {
                weekendHolidays.value = props.data.categorizeHolidays?.holidays || [];
            }
        };
        const filterYearData = (yearValue) => {
            if (yearValue) {
                filterMonthYear.value = dayjs(yearValue).format("YYYY");
            }
            emit("fetchYearData", {
                filterMonthYear: filterMonthYear.value,
                type: "weekendHoliday",
            });
        };

        watch(
            [() => props.data, activeKey],
            ([newData, newActiveKey], [oldData, oldActiveKey]) => {
                updateWeekendHolidays();
            },
            { deep: true }
        );

        return {
            pendingTabs,
            permsArray,
            activeKey,
            weekendHolidays,
            formatDate,
            extraFilters,
            filterYearData,
        };
    },
};
</script>

<style lang="less">
.segment-control {
    width: calc(100% - 30px);
    border: none;
    margin-left: 14px;
    margin-right: 14px;
    margin-bottom: 10px;
}

.segment-control .ant-segmented-item {
    flex: 1;
    display: flex;
    justify-content: center;
    box-sizing: border-box;
}

.pending-leave-hrm .ps {
    height: 378px;
}
hr {
    border: none;
    border-top: 1px solid var(--ant-border-color);
    margin: 4px 0;
}
.notification-header {
    display: flex;
    justify-content: space-between;
}
</style>
