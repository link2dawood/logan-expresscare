<template>
    <a-drawer
        :title="
            locationViewType == 'clock_in'
                ? $t('attendance.clock_in_location')
                : $t('attendance.clock_out_location')
        "
        :width="drawerWidth"
        :visible="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="true"
        @close="onClosed"
    >
        <a-tabs v-model:activeKey="activeMapTab">
            <a-tab-pane key="leaflet" force-render>
                <template #tab>
                    <span
                        ><GlobalOutlined />
                        {{ $t("attendance.custom_map") }}</span
                    >
                </template>
            </a-tab-pane>
            <a-tab-pane key="google" force-render>
                <template #tab>
                    <span
                        ><EnvironmentOutlined />
                        {{ $t("attendance.google_map") }}</span
                    >
                </template>
            </a-tab-pane>
        </a-tabs>

        <div v-if="activeMapTab === 'leaflet'">
            <a-spin
                :spinning="loadingLeaflet"
                :tip="$t('attendance.loading_map')"
            >
                <LeafletMap
                    v-if="visible"
                    :latitude="
                        locationViewType == 'clock_in'
                            ? data?.clock_in_latitude
                            : data?.clock_out_latitude
                    "
                    :longitude="
                        locationViewType == 'clock_in'
                            ? data?.clock_in_longitude
                            : data.clock_out_longitude
                    "
                    @loaded="loadingLeaflet = false"
                    :markerText="
                        locationViewType == 'clock_in'
                            ? $t('attendance.clock_in_location')
                            : $t('attendance.clock_out_location')
                    "
                />
            </a-spin>
        </div>

        <div v-if="activeMapTab === 'google'">
            <a-spin
                :spinning="loadingGoogle"
                :tip="$t('attendance.loading_map')"
            >
                <GoogleMap
                    :latitude="
                        locationViewType == 'clock_in'
                            ? data?.clock_in_latitude
                            : data.clock_out_latitude
                    "
                    :longitude="
                        locationViewType == 'clock_in'
                            ? data?.clock_in_longitude
                            : data.clock_out_longitude
                    "
                    @loaded="loadingGoogle = false"
                />
            </a-spin>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-20">
            <div class="rounded-lg bg-blue-50 p-4">
                <h4 class="mb-3 text-lg font-semibold text-blue-700">
                    {{ $t("attendance.clock_in_details") }}
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.browser") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_in_browser ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.platform") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_in_platform ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.device_type") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_in_device_type ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.latitude") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_in_latitude ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.longitude") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_in_longitude ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.location_address") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_in_location_name ?? "-" }}</span
                        >
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-orange-50 p-4">
                <h4 class="mb-3 text-lg font-semibold text-orange-700">
                    {{ $t("attendance.clock_out_details") }}
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.browser") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_out_browser ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.platform") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_out_platform ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.device_type") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_out_device_type ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.latitude") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_out_latitude ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.longitude") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_out_longitude ?? "-" }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">
                            {{ $t("attendance.location_address") }} :
                        </span>
                        <span class="font-medium text-gray-800">
                            {{ data.clock_out_location_name ?? "-" }}</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </a-drawer>
</template>
<script>
import { onMounted, ref, reactive, watch } from "vue";
import { EnvironmentOutlined, GlobalOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import hrmManagement from "../../../../common/composable/hrmManagement";
import common from "../../../../common/composable/common";
import LeafMap from "../../../../common/components/map/LeafMap.vue";
import LeafletMap from "../../../../common/components/map/LeafletMap.vue";
import GoogleMap from "../../../../common/components/map/GoogleMap.vue";

export default {
    props: ["data", "visible", "locationViewType"],
    emits: ["closed"],
    components: {
        EnvironmentOutlined,
        GlobalOutlined,

        LeafMap,
        LeafletMap,
        GoogleMap,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { getMonthNameByNumber, formatMinutes, monthArrays } =
            hrmManagement();
        const { dayjs, formatOnlyTime, formatDate, user, permsArray } =
            common();
        const activeMapTab = ref("leaflet");

        const loadingLeaflet = ref(true);
        const loadingGoogle = ref(true);

        const onClosed = () => {
            emit("closed");
        };

        // Reset loader whenever the tab is changed
        watch(activeMapTab, (tab) => {
            if (tab === "leaflet") {
                loadingLeaflet.value = true;
            } else if (tab === "google") {
                loadingGoogle.value = true;
            }
        });

        watch(
            () => props.visible,
            (val) => {
                if (val) {
                    activeMapTab.value = "leaflet";
                    loadingLeaflet.value = true;
                    loadingGoogle.value = true;
                }
            }
        );

        return {
            onClosed,
            activeMapTab,

            loadingLeaflet,
            loadingGoogle,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
};
</script>
