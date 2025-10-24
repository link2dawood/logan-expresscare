<template>
    <a-skeleton v-if="pageLoading" active />
    <div class="attendance-card" v-else>
        <a-result v-if="attendanceData.is_on_full_day_leave"
            ><template #icon>
                <ExclamationCircleFilled
                    :style="{
                        fontSize: '44px',
                        padding: '0px',
                        marginTop: '-35px !important',
                        marginBottom: '-23px !important',
                    }"
                />
            </template>
            <template #extra>
                <span
                    type="primary"
                    :style="{
                        fontSize: '20px',
                    }"
                    >{{ $t("hrm_dashboard.you_are_on_leave") }}</span
                >
            </template>
        </a-result>
        <a-result v-else-if="attendanceData.today_is_holiday"
            ><template #icon>
                <SmileOutlined
                    :style="{
                        fontSize: '44px',
                        padding: '0px',
                        marginTop: '-35px !important',
                        marginBottom: '-23px !important',
                    }"
                />
            </template>
            <template #extra>
                <span
                    type="primary"
                    :style="{
                        fontSize: '20px',
                    }"
                    >{{ $t("hrm_dashboard.today_is_holiday") }}</span
                >
            </template>
        </a-result>
        <a-result v-else-if="attendanceData.office_hours_expired"
            ><template #icon>
                <ExclamationCircleFilled
                    :style="{
                        fontSize: '44px',
                        padding: '5px',
                        marginTop: '-35px !important',
                        marginBottom: '-23px !important',
                    }"
                />
            </template>
            <template #extra>
                <span
                    type="primary"
                    :style="{
                        fontSize: '20px',
                    }"
                    >{{ $t("hrm_dashboard.office_hours_expired") }}</span
                >
            </template>
        </a-result>
        <div v-else>
            <a-row>
                <a-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                    <a-typography-title :level="5">
                        {{ $t(`hrm_dashboard.current_ip_address`) }}
                    </a-typography-title>
                    <p>{{ ipAddress }}</p>
                </a-col>
                <a-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                    <a-typography-title :level="5">
                        {{ $t(`hrm_dashboard.current_time`) }}
                    </a-typography-title>
                    <p>{{ currentTime }}</p>
                </a-col>
            </a-row>

            <a-row
                v-if="hrmStore.isSelfClocking"
                class="mt-30"
                :style="{ textAlign: 'center' }"
            >
                <a-col :span="24">
                    <a-space>
                        <a-button
                            v-if="
                                hrmStore.showClockInButton &&
                                attendanceStatus === 'initial'
                            "
                            type="primary"
                            @click="markAttendance('clock-in')"
                            :loading="clockInLoading"
                            :disabled="!hrmStore.showClockOutButton"
                        >
                            <ClockCircleOutlined />
                            {{ $t("hrm_dashboard.clock_in") }}
                        </a-button>

                        <a-button
                            v-else-if="attendanceStatus === 'started'"
                            type="primary"
                            @click="markAttendance('pause')"
                            :loading="clockInLoading"
                            :disabled="!hrmStore.showClockOutButton"
                        >
                            <PauseCircleOutlined />
                            {{ $t("hrm_dashboard.pause") }}
                        </a-button>

                        <a-button
                            v-else-if="attendanceStatus === 'paused'"
                            type="primary"
                            @click="markAttendance('start')"
                            :loading="clockInLoading"
                            :disabled="!hrmStore.showClockOutButton"
                        >
                            <PlayCircleOutlined />
                            {{ $t("hrm_dashboard.start") }}
                        </a-button>

                        <a-button
                            v-if="hrmStore.showClockOutButton"
                            type="primary"
                            @click="markAttendance('clock-out')"
                            :loading="clockOutLoading"
                        >
                            <LogoutOutlined />
                            {{ $t("hrm_dashboard.clock_out") }}
                        </a-button>
                        <a-button v-else type="primary" :disabled="true">
                            {{ $t("hrm_dashboard.clocked_out") }} :
                            {{ formatTime(hrmStore.clockOutDateTime) }}
                        </a-button>
                    </a-space>
                </a-col>
            </a-row>
            <a-row v-else class="mt-30" :style="{ textAlign: 'center' }">
                <a-col :span="24">
                    <a-typography-text type="danger" strong>
                        {{ $t("hrm_dashboard.self_clocking_is_disabled") }}
                    </a-typography-text>
                </a-col>
            </a-row>
        </div>
        <AttendanceModal
            :visible="modalVisible"
            :addEditType="addEditType"
            :pageTitle="pageTitle"
            @closed="modalClosed"
            @submitReason="submitReason"
        />
    </div>
</template>

<script>
import { onMounted, ref, computed } from "vue";
import {
    ClockCircleOutlined,
    LogoutOutlined,
    ExclamationCircleFilled,
    SmileOutlined,
    PauseCircleOutlined,
    PlayCircleOutlined,
} from "@ant-design/icons-vue";
import { useHrmStore } from "../../../main/store/hrmStore";
import common from "../../../common/composable/common";
import AttendanceModal from "./AttendanceModal.vue";
import { useI18n } from "vue-i18n";
import { Modal } from "ant-design-vue";
import { UAParser } from "ua-parser-js";

export default {
    components: {
        ClockCircleOutlined,
        LogoutOutlined,
        ExclamationCircleFilled,
        SmileOutlined,
        AttendanceModal,
        PauseCircleOutlined,
        PlayCircleOutlined,
    },
    setup(props, { emit }) {
        const { permsArray, dayjs, formatTime } = common();
        const hrmStore = useHrmStore();
        const ipAddress = ref("");
        const currentTime = ref("");
        const pageLoading = ref(false);
        const attendanceData = ref({});
        const clockInLoading = ref(false);
        const clockOutLoading = ref(false);
        const attendanceStatus = ref("initial");
        const modalVisible = ref(false);
        const data = ref({});
        const addEditType = ref("add");
        const pageTitle = ref("Add Attendance");
        const successMessage = ref("Attendance added successfully");
        const reason = ref("");
        const { t } = useI18n();
        const captureLocation = ref(false);

        onMounted(() => {
            setInterval(displayTime, 1000);
            pageLoading.value = true;

            axiosAdmin.get("/hrm/today-attendance-details").then((response) => {
                attendanceData.value = response.data;
                ipAddress.value = response.data.ip_address;
                captureLocation.value = response.data.capture_location;

                updateHrmStoreData(response);

                if (response.data.attendance_status) {
                    attendanceStatus.value = response.data.attendance_status;
                } else if (
                    response.data.clock_in_date_time &&
                    !response.data.clock_out_date_time
                ) {
                    attendanceStatus.value = "started";
                } else {
                    attendanceStatus.value = "initial";
                }

                pageLoading.value = false;
            });
        });

        const modalClosed = () => {
            modalVisible.value = false;
        };

        const submitReason = (val) => {
            reason.value = val;
            modalVisible.value = false;

            // Now send pause request
            clockInLoading.value = true;
            axiosAdmin
                .post("/hrm/mark-attendance", {
                    type: "pause",
                    reason: val,
                })
                .then((response) => {
                    clockInLoading.value = false;
                    ipAddress.value = response.data.ip_address;
                    attendanceData.value = response.data;
                    attendanceStatus.value = response.data.attendance_status;
                    updateHrmStoreData(response);
                });
        };

        // const markAttendance = (action) => {
        //     if (action === "pause") {
        //         modalVisible.value = true;
        //         return;
        //     }

        //     if (["clock-in", "start"].includes(action)) {
        //         clockInLoading.value = true;
        //     } else if (action === "clock-out") {
        //         clockOutLoading.value = true;
        //     }

        //     axiosAdmin
        //         .post("/hrm/mark-attendance", {
        //             type: action,
        //             reason: reason.value ? reason.value : null,
        //         })
        //         .then((response) => {
        //             ipAddress.value = response.data.ip_address;
        //             attendanceData.value = response.data;

        //             if (["clock-in", "start"].includes(action)) {
        //                 clockInLoading.value = false;
        //                 attendanceStatus.value =
        //                     response.data.attendance_status;
        //             } else if (action === "clock-out") {
        //                 clockOutLoading.value = false;
        //             }

        //             updateHrmStoreData(response);
        //         });
        // };

        const markAttendance = (action) => {
            if (action === "pause") {
                modalVisible.value = true;
                return;
            }

            // Show confirmation before clock-out
            if (action === "clock-out") {
                Modal.confirm({
                    title: t("hrm_dashboard.modal_title"),
                    content: t("hrm_dashboard.modal_content"),
                    okText: t("hrm_dashboard.modal_text"),
                    cancelText: t("common.cancel"),
                    onOk() {
                        submitAttendance(action);
                    },
                });
                return;
            }

            submitAttendance(action);
        };

        // const submitAttendance = (action) => {
        //     if (["clock-in", "start"].includes(action)) {
        //         clockInLoading.value = true;
        //     } else if (action === "clock-out") {
        //         clockOutLoading.value = true;
        //     }

        //     axiosAdmin
        //         .post("/hrm/mark-attendance", {
        //             type: action,
        //             reason: reason.value ? reason.value : null,
        //         })
        //         .then((response) => {
        //             ipAddress.value = response.data.ip_address;
        //             attendanceData.value = response.data;

        //             if (["clock-in", "start"].includes(action)) {
        //                 clockInLoading.value = false;
        //                 attendanceStatus.value =
        //                     response.data.attendance_status;
        //             } else if (action === "clock-out") {
        //                 clockOutLoading.value = false;
        //             }

        //             updateHrmStoreData(response);
        //         });
        // };

        const submitAttendance = async (action) => {
            if (["clock-in", "start"].includes(action)) {
                clockInLoading.value = true;
            } else if (action === "clock-out") {
                clockOutLoading.value = true;
            }

            const basicData = {
                type: action,
                reason: reason.value || null,
            };

            if (["clock-in", "clock-out"].includes(action)) {
                // Step 1: Get browser info
                const parser = new UAParser();
                const result = parser.getResult();

                const browserInfo = {
                    browser: result.browser.name + " " + result.browser.version,
                    platform: result.os.name,
                    device_type: result.device.type || "desktop",
                    user_agent: navigator.userAgent,
                };

                if (captureLocation.value) {
                    const options = {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    };

                    // Step 2: Get geolocation
                    const watchID = navigator.geolocation.watchPosition(
                        async (position) => {
                            navigator.geolocation.clearWatch(watchID);
                            const coords = position.coords;

                            const fullData = {
                                ...basicData,
                                latitude: coords.latitude,
                                longitude: coords.longitude,
                                location_accuracy: coords.accuracy,
                                ...browserInfo,
                            };

                            await sendAttendanceRequest(fullData, action);
                        },
                        (error) => {
                            navigator.geolocation.clearWatch(watchID);
                            console.log(error);
                            setLoadingFalse();
                            Modal.warning({
                                title: t("attendance.location_access_required"),
                                content:
                                    error.message ||
                                    t(
                                        "attendance.allow_location_access_to_mark_attendance"
                                    ),
                                centered: true,
                                okText: t("common.ok"),
                                onOk: () => {},
                            });
                        },
                        options
                    );
                } else {
                    const fullData = {
                        ...basicData,
                        ...browserInfo,
                    };

                    await sendAttendanceRequest(fullData, action);
                }
            } else {
                // Other actions (break, pause, etc.)
                await sendAttendanceRequest(basicData, action);
            }
        };

        // ✅ Reusable API call
        const sendAttendanceRequest = async (data, action) => {
            try {
                const response = await axiosAdmin.post(
                    "/hrm/mark-attendance",
                    data
                );

                ipAddress.value = response.data.ip_address;
                attendanceData.value = response.data;

                if (["clock-in", "start"].includes(action)) {
                    clockInLoading.value = false;
                    attendanceStatus.value = response.data.attendance_status;
                } else if (action === "clock-out") {
                    clockOutLoading.value = false;
                }

                updateHrmStoreData(response);
            } catch (error) {
                setLoadingFalse();
            }
        };

        // ✅ Clean loading state function
        const setLoadingFalse = () => {
            clockInLoading.value = false;
            clockOutLoading.value = false;
        };

        const updateHrmStoreData = (response) => {
            hrmStore.updateSelfClocking(response.data.self_clocking);
            hrmStore.updateShowClockInButton(
                response.data.show_clock_in_button
            );
            hrmStore.updateShowClockOutButton(
                response.data.show_clock_out_button
            );
            hrmStore.updateClockInDateTime(response.data.clock_in_date_time);
            hrmStore.updateClockOutDateTime(response.data.clock_out_date_time);
        };

        const displayTime = () => {
            currentTime.value = dayjs().format("hh:mm:ss a");
        };

        return {
            pageLoading,
            attendanceData,
            ipAddress,
            currentTime,
            markAttendance,
            clockInLoading,
            clockOutLoading,
            formatTime,
            attendanceStatus,
            hrmStore,
            modalVisible,
            data,
            addEditType,
            pageTitle,
            successMessage,
            modalClosed,
            submitReason,
        };
    },
};
</script>
<style scoped>
.ant-result,
.ant-result-icon {
    margin-bottom: -63px;
}

.attendance-card {
    width: 100%;
    text-align: center;
    height: 130px;
}
</style>
