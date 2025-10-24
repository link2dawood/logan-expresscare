<template>
    <a-card class="holiday-card">
        <div class="holiday-content">
            <div class="holiday-info">
                <div class="holiday-title">
                    {{ $t("holiday.next_holiday") }}
                </div>
                <div class="holiday-date" v-if="nextHoliday">
                    {{ nextHoliday }}, {{ formattedDate }}
                </div>
                <div v-else class="holiday-date">{{ "N/A" }}</div>
            </div>
            <router-link
                :to="{
                    name: 'admin.self.holidays.index',
                }"
            >
                <a-button type="default" class="view-button">
                    {{ $t("holiday.view_all") }}
                </a-button>
            </router-link>
        </div>
    </a-card>
</template>

<script>
import { onMounted, ref, watch } from "vue";
import { ExclamationCircleFilled } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";

export default {
    props: ["data"],
    components: {
        ExclamationCircleFilled,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const nextHoliday = ref("");
        const formattedDate = ref("");

        watch(
            props,
            (newVal, oldVal) => {
                nextHoliday.value =
                    newVal.data.categorizeHolidays.next_holiday?.name;
                formattedDate.value =
                    newVal.data.categorizeHolidays.next_holiday?.formatted_date;
            },
            { deep: true }
        );

        return {
            nextHoliday,
            formattedDate,
        };
    },
};
</script>

<style scoped>
.holiday-card {
    background: #f7b500;
    color: black;
    padding: 24px;
    max-width: 100%;
}

.holiday-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.holiday-info {
    display: flex;
    flex-direction: column;
}

.holiday-title {
    font-weight: bold;
    font-size: 14px;
}

.holiday-date {
    font-size: 12px;
    opacity: 0.9;
}

.view-button {
    background: white;
    color: black;
    font-weight: bold;
    border-radius: 4px;
    padding: 4px 10px;
}
</style>
