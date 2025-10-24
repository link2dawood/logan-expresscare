<template>
    <a-card class="notifications-container" :bodyStyle="{ padding: '10px' }">
        <template #title>
            <CalendarOutlined />
            {{ $t("hrm_dashboard.birthdays") }}
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
        <a-result v-if="!upcomingBirthdays || upcomingBirthdays?.length <= 0">
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
                    >{{ $t("hrm_dashboard.there_is_no_birthday") }}</span
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
                    :data-source="upcomingBirthdays"
                    item-layout="horizontal"
                    class="notifications-list"
                    size="small"
                    v-if="!upcomingBirthdays || upcomingBirthdays?.length > 0"
                >
                    <template #renderItem="{ item }">
                        <a-list-item>
                            <a-list-item-meta>
                                <template #avatar>
                                    <a-avatar :src="item.image_url" />
                                </template>
                                <template #title>
                                    <div class="notification-header">
                                        <div class="notification-text">
                                            <strong>{{ item.name }}</strong>
                                        </div>
                                        <div class="notification-time">
                                            {{ item.formatted_date }}
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
import { ref, defineComponent, watch, onMounted } from "vue";
import {
    CalendarOutlined,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";

export default defineComponent({
    props: ["data"],
    components: {
        CalendarOutlined,
        ExclamationCircleFilled,
    },
    setup(props, { emit }) {
        const upcomingBirthdays = ref([]);

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
            emit("employeeBirthDayData", {
                xid: filters.value.user_id,
                date: filters.value.date,

                type: "birthday_data",
            });
        };
        watch(
            () => props.data,
            (newVal, oldVal) => {
                upcomingBirthdays.value = newVal.upcomingBirthday;
            },
            { immediate: true }
        );

        return { upcomingBirthdays, filters, allUsers, fetchUserData };
    },
});
</script>

<style scoped>
.notifications-container {
    width: 100%;
    height: 495px;
}
.notification-header {
    display: flex;
    justify-content: space-between;
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
    height: 388px;
}
</style>
