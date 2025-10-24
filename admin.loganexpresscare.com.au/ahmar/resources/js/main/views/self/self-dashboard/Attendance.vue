<template>
    <a-card class="attendance-card">
        <a-skeleton v-if="pageLoading" active />
        <template v-else>
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
                    <SmileOutlined />
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
                        >{{ $t("hrm_dashboard.office_hours_expired") }}</span
                    >
                </template>
            </a-result>
            <div v-else>
                <div class="attendance-time">
                    <h3>{{ $t("menu.attendances") }}</h3>
                    <p>{{ attendanceTime }}</p>
                </div>

                <div class="progress-section" v-if="hrmStore.isSelfClocking">
                    <a-progress
                        type="circle"
                        :percent="percent"
                        :format="() => totalHours"
                        :strokeColor="progressColor"
                    />
                </div>

                <div class="details" v-if="hrmStore.isSelfClocking">
                    <a-tag :color="tagColor">
                        {{ $t(`attendance.production`) }}:
                        {{ productionHours }}</a-tag
                    >
                    <p>
                        <ClockCircleOutlined />
                        {{ $t(`attendance.clock_in_date_time`) }}
                        {{ punchInTime }}
                    </p>
                </div>
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
                <div
                    v-if="!hrmStore.isSelfClocking && pageLoading"
                    :style="{
                        textAlign: 'center',
                        marginTop: '76px',
                        color: 'red',
                        fontSize: '25px',
                    }"
                >
                    <div :span="24">
                        {{ $t("hrm_dashboard.self_clocking_is_disabled") }}
                    </div>
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
    </a-card>
</template>

<script>
import {
    ref,
    watchEffect,
    defineComponent,
    watch,
    onUnmounted,
    computed,
    onMounted,
} from "vue";
import {
    ClockCircleOutlined,
    LogoutOutlined,
    ExclamationCircleFilled,
    SmileOutlined,
    PauseCircleOutlined,
    PlayCircleOutlined,
} from "@ant-design/icons-vue";
import { theme } from "ant-design-vue";
import common from "@/common/composable/common";
import { Modal } from "ant-design-vue";
import { useHrmStore } from "../../../../main/store/hrmStore";
import AttendanceModal from "../../admin-dashboard/AttendanceModal.vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../common/composable/hrmManagement";
import { find } from "lodash-es";
import { UAParser } from "ua-parser-js";

export default defineComponent({
    props: ["data"],
    components: {
        ClockCircleOutlined,
        LogoutOutlined,
        ExclamationCircleFilled,
        SmileOutlined,
        AttendanceModal,
        PauseCircleOutlined,
        PlayCircleOutlined,
    },
    setup(props) {
        const { token } = theme.useToken();
        const { dayjs, formatTime } = common();
        const { formatMinutes } = hrmManagement();
        const hrmStore = useHrmStore();
        const attendanceTime = ref("");
        const totalHours = ref("");
        const productionHours = ref("");
        const punchInTime = ref("");

        const progressColor = ref("#52c41a");
        const tagColor = ref("black");
        const clockInLoading = ref(false);
        const clockOutLoading = ref(false);
        const ipAddress = ref("");
        const isSelfClocking = computed(() => hrmStore.isSelfClocking);
        const attendanceData = ref([]);
        const showClockInButton = computed(() => hrmStore.showClockInButton);
        const showClockOutButton = computed(() => hrmStore.showClockOutButton);
        const clockInDateTime = computed(() => hrmStore.clockInDateTime);
        const clockOutDateTime = computed(() => hrmStore.clockOutDateTime);
        const percent = ref(0);
        const interval = ref(null);
        const attendanceStatus = ref("initial");
        const modalVisible = ref(false);
        const data = ref({});
        const addEditType = ref("add");
        const pageTitle = ref("Add Attendance");
        const successMessage = ref("Attendance added successfully");
        const reason = ref("");
        const { t } = useI18n();
        const pageLoading = ref(false);
        const fetchedLatestAttendance = ref({});
        const captureLocation = ref(false);

        onMounted(() => {
            setInterval(updateAttendanceTime, 1000);
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

        const submitReason = (val) => {
            reason.value = val;
            modalVisible.value = false;

            clockInLoading.value = true;
            axiosAdmin
                .post("/hrm/mark-attendance", {
                    type: "pause",
                    reason: val,
                })
                .then((response) => {
                    fetchedLatestAttendance.value = {};
                    clockInLoading.value = false;
                    ipAddress.value = response.data.ip_address;
                    attendanceData.value = response.data;
                    attendanceStatus.value = response.data.attendance_status;
                    fetchedLatestAttendance.value =
                        response.data.latest_attendance;
                    updateHrmStoreData(response);
                });
        };

        const markAttendance = (action) => {
            if (action === "pause") {
                modalVisible.value = true;
                return;
            }

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
                await sendAttendanceRequest(basicData, action);
            }
        };

        const sendAttendanceRequest = async (data, action) => {
            try {
                const response = await axiosAdmin.post(
                    "/hrm/mark-attendance",
                    data
                );

                ipAddress.value = response.data.ip_address;
                attendanceData.value = response.data;
                fetchedLatestAttendance.value = response.data.latest_attendance;

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

        const setLoadingFalse = () => {
            clockInLoading.value = false;
            clockOutLoading.value = false;
        };

        const modalClosed = () => {
            modalVisible.value = false;
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

        watchEffect(() => {
            document.documentElement.style.setProperty(
                "--text-color",
                token.colorText
            );
            document.documentElement.style.setProperty(
                "--secondary-text-color",
                token.colorTextSecondary
            );
            document.documentElement.style.setProperty(
                "--card-background",
                token.colorBgContainer
            );
            document.documentElement.style.setProperty(
                "--border-color",
                token.colorBorder
            );

            progressColor.value = token.colorSuccess;
            tagColor.value = token.colorPrimary;
        });

        const updateAttendanceTime = () => {
            const now = dayjs();
            attendanceTime.value = now.format("hh:mm A, DD MMM YYYY");
        };

        onUnmounted(() => {
            if (interval.value) {
                clearInterval(interval.value);
            }
        });

        const updateRunningProductionTime = (attendance) => {
            if (attendance && attendance.clock_in_time) {
                const clockInTime = dayjs(
                    `${attendance.date} ${attendance.clock_in_time}`,
                    "YYYY-MM-DD HH:mm:ss"
                );
                var start = true;
                var selectedStaff = find(attendance.work_duration, {
                    end_time: null,
                    status: "paused",
                });
                if (selectedStaff) {
                    start = false;
                }

                const initialDuration = attendance.duration || 0;
                const stopTime = dayjs();

                const updateProductionTime = () => {
                    const now = dayjs();
                    var secondsElapsed = 0;
                    if (start) {
                        secondsElapsed = now.diff(stopTime, "second");
                    }
                    const totalDurationInMinutes =
                        parseFloat(initialDuration) +
                        parseFloat(Math.floor(secondsElapsed / 60));
                    punchInTime.value = clockInTime.format("hh:mm A");
                    productionHours.value = formatMinutes(
                        totalDurationInMinutes
                    );
                    totalHours.value = formatMinutes(totalDurationInMinutes);

                    const workDayMinutes = attendance.shift_duration || 1;
                    percent.value = Math.min(
                        Math.round(
                            (totalDurationInMinutes / workDayMinutes) * 100
                        ),
                        100
                    );
                };

                updateProductionTime();

                if (interval.value) {
                    clearInterval(interval.value);
                }

                interval.value = setInterval(updateProductionTime, 1000);
            } else {
                attendanceTime.value = "N/A";
                punchInTime.value = "N/A";
                productionHours.value = "N/A";
                totalHours.value = "N/A";
            }
        };

        watch(
            props,
            (newVal) => {
                const attendance = newVal?.data?.attendances?.[0];
                updateRunningProductionTime(attendance);
            },
            { immediate: true }
        );

        watch(
            () => fetchedLatestAttendance.value,
            (newVal) => {
                const latestAttend = newVal;
                updateRunningProductionTime(latestAttend);
            },
            { immediate: true }
        );

        return {
            attendanceTime,
            attendanceData,
            totalHours,
            productionHours,
            punchInTime,
            progressColor,
            tagColor,
            markAttendance,
            clockInLoading,
            clockOutLoading,
            formatTime,
            isSelfClocking,
            percent,
            showClockInButton,
            showClockOutButton,
            clockInDateTime,
            clockOutDateTime,
            attendanceStatus,
            hrmStore,
            modalVisible,
            data,
            addEditType,
            pageTitle,
            successMessage,
            modalClosed,
            submitReason,
            formatMinutes,
            pageLoading,
        };
    },
});
</script>

<style scoped>
.attendance-card {
    width: 100%;
    text-align: center;
    padding: 9px;
    color: var(--text-color);
    height: 350px;
}

.attendance-time h3 {
    margin-bottom: 4px;
    font-weight: 600;
    color: var(--text-color);
}

.attendance-time p {
    color: var(--secondary-text-color);
}

.progress-section {
    margin: 16px 0;
}

.details {
    margin-bottom: 16px;
}

.details p {
    color: var(--secondary-text-color);
    font-size: 14px;
    margin: 8px 0 0;
    margin-bottom: -20px;
}
.ant-result,
.ant-result-icon {
    margin-bottom: -33px;
}
</style>
