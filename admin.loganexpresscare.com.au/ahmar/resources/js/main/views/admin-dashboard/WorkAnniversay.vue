<template>
    <a-card class="notifications-container" :bodyStyle="{ padding: '13px' }">
        <template #title>
            <UserOutlined />
            {{ $t("dashboard.work_anniversary") }}
        </template>
        <template #extra>
            <a-space>
                <span>
                    <a-date-picker
                        v-model:value="filters.date"
                        :placeholder="
                            $t('common.select_default_text', [
                                $t('holiday.date'),
                            ])
                        "
                        @change="fetchUserData"
                        style="width: 100%"
                        :allowClear="true"
                /></span>
                <span>
                    <a-select
                        v-model:value="filters.user_id"
                        @change="() => fetchUserData()"
                        show-search
                        style="width: 100%"
                        :placeholder="
                            $t('common.select_default_text', [$t('leave.user')])
                        "
                        :allowClear="true"
                        optionFilterProp="title"
                    >
                        <a-select-option
                            v-for="allUser in allUsers"
                            :key="allUser.xid"
                            :value="allUser.xid"
                            :title="allUser.name"
                            >{{ allUser.name }}
                        </a-select-option>
                    </a-select>
                </span></a-space
            ></template
        >
        <a-result v-if="!hasUpcomingAnniversaries">
            <template #icon>
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
                    >{{ $t("hrm_dashboard.there_is_no_anniversary") }}</span
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
                    :data-source="upcomingAnniversaries"
                    item-layout="horizontal"
                    class="notifications-list"
                    size="small"
                    v-if="hasUpcomingAnniversaries"
                >
                    <template #renderItem="{ item }">
                        <a-list-item>
                            <a-list-item-meta>
                                <template #avatar>
                                    <a-avatar :src="item.profile_image_url" />
                                </template>
                                <template #title>
                                    <div class="notification-header">
                                        <div class="notification-left">
                                            <strong class="notification-name">
                                                {{ item.name }}
                                            </strong>
                                            <div class="notification-date">
                                                {{ "on" }}
                                                {{ item.formatted_date }}
                                            </div>
                                        </div>
                                        <div class="notification-count">
                                            {{ item.anniversary_count }}
                                        </div>
                                    </div>
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
import { ref, defineComponent, watch, computed, onMounted } from "vue";
import { UserOutlined, ExclamationCircleFilled } from "@ant-design/icons-vue";

export default defineComponent({
    props: ["data"],
    components: {
        UserOutlined,
        ExclamationCircleFilled,
    },
    setup(props, { emit }) {
        const upcomingAnniversaries = ref([]);
        const allUsers = ref([]);
        const filters = ref({
            user_id: undefined,
            date: undefined,
        });
        const staffMembersUrl =
            "users?fields=id,xid,user_type,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary,probation_start_date,probation_end_date,notice_start_date,notice_end_date,duration,shift_id,x_shift_id,shift{id,xid,name},department_id,x_department_id,department{id,xid,name},end_date,annual_ctc,salary_group_id,x_salary_group_id,salaryGroup{id,xid}";

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });
        });
        const fetchUserData = () => {
            emit("employeeAnniversaryData", {
                xid: filters.value.user_id,
                date: filters.value.date,

                type: "anniversary_data",
            });
        };

        const hasUpcomingAnniversaries = computed(() => {
            return upcomingAnniversaries.value.length > 0;
        });

        watch(props, (newVal) => {
            upcomingAnniversaries.value = newVal.data.getUpcomingAnniversaries;
        });

        return {
            upcomingAnniversaries,
            hasUpcomingAnniversaries,
            allUsers,
            fetchUserData,
            filters,
        };
    },
});
</script>

<style scoped>
.notifications-container {
    width: 100%;
    height: 496px;
}

.notification-text {
    font-size: 14px;
    padding: 6px;
}
.notification-time {
    font-size: 14px;
    color: gray;
    white-space: nowrap;
    font-weight: bold;
    padding: 6px;
}
.last-item {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.pending-leave-hrm .ps {
    height: 378px;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center; /* Ensures proper vertical alignment */
    width: 100%;
}

.notification-left {
    display: flex;
    flex-direction: column; /* Name on top, date below */
}

.notification-name {
    font-size: 14px;
    font-weight: bold;
    padding-bottom: 2px;
}

.notification-date {
    font-size: 13px;
    color: gray;
    font-weight: normal;
}

/* Green Square Anniversary Count */
.notification-count {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 35px; /* Square Size */
    height: 35px; /* Square Size */
    background-color: #28a745; /* Green Background */
    color: white; /* White Text */
    font-size: 12px;
    font-weight: bold;
    border-radius: 6px; /* Slightly rounded edges */
    text-align: center;
    margin-left: 10px;
}
</style>
