<template>
    <a-tag v-if="status == 'present'" color="success">
        <template #icon>
            <CheckCircleOutlined />
        </template>
        {{ $t("attendance.present") }}
    </a-tag>
    <a-tag v-else-if="status == 'on_leave'" color="error">
        <template #icon>
            <CarOutlined />
        </template>
        {{ $t("attendance.on_leave") }}
    </a-tag>
    <a-tag v-else-if="status == 'absent'" color="error">
        <template #icon>
            <CloseCircleOutlined />
        </template>
        {{ $t("attendance.absent") }}
    </a-tag>
    <a-tag v-else-if="status == 'holiday'" color="processing">
        <template #icon>
            <CalendarOutlined />
        </template>
        {{ $t("attendance.holiday") }}
    </a-tag>
    <a-tag v-else-if="status == 'half_holiday'" color="red">
        <template #icon>
            <CalendarOutlined />
        </template>
        {{ $t("attendance.half_holiday") }}
    </a-tag>
    <a-tag v-else-if="status == 'not_marked'" color="default">
        <template #icon>
            <ExclamationCircleOutlined />
        </template>
        {{ $t("attendance.not_marked") }}
    </a-tag>
    <a-tag v-else-if="status == 'half_day'" color="orange">
        <template #icon>
            <ClockCircleOutlined />
        </template>
        {{ $t("attendance.half_day") }}
    </a-tag>
</template>

<script>
import { defineComponent, onMounted, ref } from "vue";
import {
    ClockCircleOutlined,
    CheckCircleOutlined,
    ExclamationCircleOutlined,
    CloseCircleOutlined,
    CalendarOutlined,
    CarOutlined,
} from "@ant-design/icons-vue";

export default defineComponent({
    props: ["status"],
    components: {
        ClockCircleOutlined,
        CheckCircleOutlined,
        ExclamationCircleOutlined,
        CloseCircleOutlined,
        CalendarOutlined,
        CarOutlined,
    },
    setup(props, { emit }) {
        return {};
    },
});
</script>
