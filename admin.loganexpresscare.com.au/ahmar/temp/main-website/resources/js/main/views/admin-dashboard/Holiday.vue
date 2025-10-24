<template>
    <div class="birthday-section">
        <a-card
            bordered
            :bodyStyle="{
                padding: '15px',
                height: '439px',
                position: 'relative',
            }"
        >
            <template #title>
                <GitlabOutlined />
                {{ $t("hrm_dashboard.birthdays") }}
            </template>
            <a-result
                v-if="
                    todayBirthdays.length <= 0 &&
                    tomorrowBirthdays.length <= 0 &&
                    futureBirthdays.length <= 0
                "
                ><template #icon>
                    <ExclamationCircleFilled
                        :style="{
                            fontSize: '52px',
                            padding: '0px',
                            marginTop: '42px !important',
                        }"
                    />
                </template>
                <template #extra>
                    <span
                        type="primary"
                        :style="{
                            fontSize: '28px',
                        }"
                        >{{ $t("hrm_dashboard.there_is_no_birthday") }}</span
                    >
                </template>
            </a-result>
            <div class="birthday-content">
                <!-- Today Section -->
                <div v-if="todayBirthdays.length" class="birthday-category">
                    <h4>{{ $t("hrm_dashboard.today") }}</h4>
                    <div
                        v-for="employee in todayBirthdays"
                        :key="employee.id"
                        class="birthday-item"
                    >
                        <a-card
                            :bordered="true"
                            class="birthday-card"
                            :bodyStyle="{ padding: '19px' }"
                        >
                            <div class="card-content">
                                <div class="left-side">
                                    <a-avatar
                                        :src="employee.profile_image_url"
                                        size="medium"
                                    />
                                    <div class="employee-details">
                                        <div class="employee-name">
                                            {{ employee.name }}
                                        </div>
                                        <div class="employee-role">
                                            {{ employee?.designation?.name }}
                                        </div>
                                    </div>
                                </div>
                                <a-button
                                    type="primary"
                                    ghost
                                    class="send-button"
                                >
                                    <template #icon
                                        ><CalendarOutlined
                                    /></template>
                                    {{ $t("hrm_dashboard.send") }}
                                </a-button>
                            </div>
                        </a-card>
                    </div>
                </div>

                <!-- Tomorrow Section -->
                <div class="birthday-category" v-if="tomorrowBirthdays.length">
                    <h4>{{ $t("hrm_dashboard.tomorrow") }}</h4>
                    <div
                        v-for="employee in tomorrowBirthdays"
                        :key="employee.id"
                        class="birthday-item"
                    >
                        <a-card
                            :bordered="true"
                            class="birthday-card"
                            :bodyStyle="{ padding: '19px' }"
                        >
                            <div class="card-content">
                                <div class="left-side">
                                    <a-avatar
                                        :src="employee.profile_image_url"
                                        size="medium"
                                    />
                                    <div class="employee-details">
                                        <div class="employee-name">
                                            {{ employee.name }}
                                        </div>
                                        <div class="employee-role">
                                            {{ employee?.designation?.name }}
                                        </div>
                                    </div>
                                </div>
                                <a-button
                                    type="primary"
                                    ghost
                                    class="send-button"
                                >
                                    <template #icon
                                        ><CalendarOutlined
                                    /></template>
                                    {{ $t("hrm_dashboard.send") }}
                                </a-button>
                            </div>
                        </a-card>
                    </div>
                </div>

                <!-- Future Section -->
                <div
                    v-for="employee in futureBirthdays"
                    :key="employee.id"
                    class="birthday-category"
                    v-show="futureBirthdays.length"
                >
                    <h4 class="dob">{{ employee.dob }}</h4>

                    <div class="birthday-item">
                        <a-card
                            :bordered="true"
                            class="birthday-card"
                            :bodyStyle="{ padding: '20px' }"
                        >
                            <div class="card-content">
                                <div class="left-side">
                                    <a-avatar
                                        :src="employee.profile_image_url"
                                        size="medium"
                                    />
                                    <div class="employee-details">
                                        <div class="employee-name">
                                            {{ employee.name }}
                                        </div>
                                        <div class="employee-role">
                                            {{ employee?.designation?.name }}
                                        </div>
                                    </div>
                                </div>
                                <a-button
                                    type="primary"
                                    ghost
                                    class="send-button"
                                >
                                    <template #icon
                                        ><CalendarOutlined
                                    /></template>
                                    {{ $t("hrm_dashboard.send") }}
                                </a-button>
                            </div>
                        </a-card>
                    </div>
                </div>
            </div>
        </a-card>
    </div>
</template>
<script>
import { ref, defineComponent, watch } from "vue";
import {
    CalendarOutlined,
    GitlabOutlined,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";

export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        GitlabOutlined,
        ExclamationCircleFilled,
    },
    setup(props) {
        const todayBirthdays = ref([]);

        const tomorrowBirthdays = ref([]);

        const futureBirthdays = ref([]);

        watch(props, (newVal, oldVal) => {
            todayBirthdays.value = newVal.data.upcomingBirthday.todayBirthdays;
            tomorrowBirthdays.value =
                newVal.data.upcomingBirthday.tomorrowBirthdays;
            futureBirthdays.value =
                newVal.data.upcomingBirthday.futureBirthdays;
        });

        return {
            todayBirthdays,
            tomorrowBirthdays,
            futureBirthdays,
        };
    },
});
</script>

<style>
.birthday-card {
    padding: 0px;
    height: 72px;
    margin-bottom: 9px;
}

.birthday-card .card-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.birthday-card .left-side {
    display: flex;
    align-items: center;
}

.birthday-card .employee-details {
    margin-inline-start: 14px;
    font-size: 10px;
}

.birthday-card .employee-name {
    font-weight: bold;
    font-size: 13px;
}

.birthday-card .employee-role {
    font-size: 11px;
}

.birthday-card .send-button {
    margin-inline-start: auto;
    padding: 4px 6px;
    font-size: 12px;
}

.birthday-category {
    margin-bottom: 10px;
}

.birthday-category:last-child {
    margin-bottom: 0px;
}

.birthday-category h4 {
    margin-bottom: 6px;
    font-size: 14px;
    color: var(--ant-text-color);
    font-weight: bold;
}

.a-avatar {
    width: 44px;
    height: 44px;
}
</style>
