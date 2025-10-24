<template>
    <div style="height: 300px; width: 100%">
        <div v-if="!isValidCoords" class="text-center text-gray-500 p-4">
            📍 {{ $t("attendance.location_not_available") }}
        </div>
        <div v-else ref="mapContainer" style="height: 300px; width: 100%"></div>
    </div>
</template>

<script>
import { ref, watch, onMounted, onBeforeUnmount, nextTick, computed } from "vue";
import { useI18n } from "vue-i18n";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
// Point Leaflet to its bundled images
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

export default {
    props: ["latitude", "longitude", "markerText"],
    emits: ["loaded"],
    setup(props, { emit }) {
        const { locale, t } = useI18n();
        const mapContainer = ref(null);
        let map = null;
        let marker = null;

        const isValidCoords = computed(() => {
            const lat = parseFloat(props.latitude);
            const lng = parseFloat(props.longitude);
            return !isNaN(lat) && !isNaN(lng);
        });

        const initMap = () => {
            if (!isValidCoords.value || !mapContainer.value) return;

            const coords = [parseFloat(props.latitude), parseFloat(props.longitude)];

            if (!mapContainer.value) return;

            if (!map) {
                map = L.map(mapContainer.value).setView(coords, 17);

                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "&copy; OpenStreetMap contributors",
                }).addTo(map);

                marker = L.marker(coords)
                    .addTo(map)
                    .bindPopup(props.markerText)
                    .openPopup();

                emit("loaded");
            } else {
                map.setView(coords, 15);
                marker.setLatLng(coords);
                map.invalidateSize();
                emit("loaded");
            }
        };

        onMounted(() => {
            nextTick(() => {
                setTimeout(() => {
                    if (!isValidCoords.value) {
                        emit("loaded"); // Stop the spinner
                        return;
                    }
                    initMap();
                }, 300); // let drawer + tab finish rendering
            });
        });

        watch([() => props.latitude, () => props.longitude], () => {
            if (!isValidCoords.value) {
                emit("loaded"); // Stop spinner if coords become invalid
                return;
            }

            if (map && isValidCoords.value) {
                const coords = [parseFloat(props.latitude), parseFloat(props.longitude)];
                map.setView(coords, 15);
                marker.setLatLng(coords);
                map.invalidateSize();
            }
        });

        onBeforeUnmount(() => {
            if (map) {
                map.remove();
                map = null;
            }
        });

        return {
            mapContainer,
            isValidCoords,
        };
    },
};
</script>
