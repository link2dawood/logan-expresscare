<template>
    <div style="width: 100%; min-height: 300px">
        <div v-if="!isValidCoords" class="text-center text-gray-500 p-4">
            📍 {{ $t("attendance.location_not_available") }}
        </div>
        <iframe
            v-else
            :src="googleMapUrl"
            width="100%"
            height="300"
            frameborder="0"
            style="border: 0"
            allowfullscreen
            loading="lazy"
            @load="onLoad"
        ></iframe>
    </div>
</template>

<script setup>
import { computed, watch, onMounted } from "vue";

const props = defineProps(["latitude", "longitude"]);
const emit = defineEmits(["loaded"]);

const isValidCoords = computed(() => {
    const lat = parseFloat(props.latitude);
    const lng = parseFloat(props.longitude);
    return !isNaN(lat) && !isNaN(lng);
});

const googleMapUrl = computed(
    () =>
        `https://maps.google.com/maps?q=${props.latitude},${props.longitude}&z=14&output=embed`
);

const onLoad = () => {
    emit("loaded");
};

// ✅ Immediately emit 'loaded' if lat/lng are invalid
watch([() => props.latitude, () => props.longitude], () => {
    if (!isValidCoords.value) {
        emit("loaded");
    }
});

onMounted(() => {
    if (!isValidCoords.value) {
        emit("loaded");
    }
});
</script>
