import { reactive, ref, createVNode, watch, computed } from "vue";
import { Modal, notification } from "ant-design-vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { useAuthStore } from "../../main/store/authStore";
import datatable from "./datatable";
import { includes, has, get, forEach } from "lodash-es";

const attendance = () => {
    const locationModalVisible = ref(false);
    const selectedLocationRecord = ref({});
    const locationViewType = ref("clock_in");

    const closeLocationDetailsModal = () => {
        locationModalVisible.value = false;
        selectedLocationRecord.value = {};
    };

    const showLocationDetailsModal = (record, viewAttendanceType) => {
        locationViewType.value = viewAttendanceType;
        selectedLocationRecord.value = record;
        locationModalVisible.value = true;
    };

    return {
        locationModalVisible,
        selectedLocationRecord,
        locationViewType,

        closeLocationDetailsModal,
        showLocationDetailsModal
    };
};

export default attendance;
