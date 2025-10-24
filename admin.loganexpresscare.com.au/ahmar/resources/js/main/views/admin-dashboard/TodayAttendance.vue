<template>
    <a-card :bodyStyle="{ padding: '0px', height: '446px' }">
        <template #title>
            <UserOutlined />
            {{ $t("hrm_dashboard.today_attendance") }}
        </template>

        <template #extra>
            <a-tabs
                v-if="!isHoliday && !initLoading"
                v-model:activeKey="attendanceStatus"
                @change="attendanceStatusChanged"
                size="small"
            >
                <a-tab-pane key="not_marked">
                    <template #tab>
                        {{ $t("hrm_dashboard.not_marked") }}
                        <a-badge
                            v-if="totalNotMarked > 0"
                            :number-style="{ backgroundColor: '#13c2c2' }"
                            :count="totalNotMarked"
                        />
                    </template>
                </a-tab-pane>
                <a-tab-pane key="present">
                    <template #tab>
                        {{ $t("hrm_dashboard.present") }}
                        <a-badge
                            v-if="totalPresent > 0"
                            :number-style="{ backgroundColor: '#52c41a' }"
                            :count="totalPresent"
                        />
                    </template>
                </a-tab-pane>
                <a-tab-pane key="absent">
                    <template #tab>
                        {{ $t("hrm_dashboard.absent") }}
                        <a-badge v-if="totalAbsent > 0" :count="totalAbsent" />
                    </template>
                </a-tab-pane>
            </a-tabs>
        </template>

        <div class="today-attendance-status-hrm">
            <perfect-scrollbar
                :options="{
                    wheelSpeed: 1,
                    swipeEasing: true,
                    suppressScrollX: true,
                }"
            >
                <a-result
                    v-if="isHoliday && !initLoading"
                    :title="$t('hrm_dashboard.today_is_holiday')"
                >
                    <template #icon>
                        <CoffeeOutlined />
                    </template>
                </a-result>
                <a-list
                    v-else
                    :loading="initLoading"
                    item-layout="horizontal"
                    :data-source="filteredUsers"
                >
                    <template #renderItem="{ item }">
                        <a-list-item>
                            <a-list-item-meta :description="item.reason">
                                <template #title>
                                    <a-typography-text strong>
                                        {{ item.name }}
                                    </a-typography-text>
                                </template>
                                <template #avatar>
                                    <a-avatar :src="item.profile_image_url" />
                                </template>
                            </a-list-item-meta>
                        </a-list-item>
                    </template>
                </a-list>
            </perfect-scrollbar>
        </div>
    </a-card>
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import { UserOutlined, CoffeeOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { filter } from "lodash-es";
import common from "../../../common/composable/common";

export default {
    components: {
        UserOutlined,
        CoffeeOutlined,
    },
    setup() {
        const { permsArray, dayjs, formatTime, formatDate } = common();
        const { t } = useI18n();
        const initLoading = ref(false);
        const allUsers = ref([]);
        const attendanceStatus = ref("not_marked");
        const isHoliday = ref(false);
        const holiday = ref({});
        const filteredUsers = ref([]);

        const totalNotMarked = ref(0);
        const totalPresent = ref(0);
        const totalAbsent = ref(0);

        onMounted(() => {
            refetchDataList();
        });

        const refetchDataList = () => {
            initLoading.value = true;

            axiosAdmin
                .get("hrm/dashboard/today-attendance-users")
                .then((response) => {
                    allUsers.value = response.data.users;
                    holiday.value = response.data.holiday;
                    isHoliday.value = response.data.is_holiday;

                    totalNotMarked.value = filter(allUsers.value, {
                        status: "not_marked",
                    }).length;
                    totalPresent.value = filter(allUsers.value, {
                        status: "present",
                    }).length;
                    totalAbsent.value = filter(allUsers.value, {
                        status: "absent",
                    }).length;

                    attendanceStatusChanged();
                });
        };

        const attendanceStatusChanged = () => {
            initLoading.value = true;

            filteredUsers.value = filter(allUsers.value, {
                status: attendanceStatus.value,
            });

            initLoading.value = false;
        };

        return {
            attendanceStatus,
            initLoading,
            permsArray,
            formatDate,

            isHoliday,
            allUsers,
            holiday,
            filteredUsers,
            attendanceStatusChanged,

            totalNotMarked,
            totalPresent,
            totalAbsent,
        };
    },
};
</script>

<style lang="less">
.today-attendance-status-hrm .ps {
    height: 400px;
}
</style>
