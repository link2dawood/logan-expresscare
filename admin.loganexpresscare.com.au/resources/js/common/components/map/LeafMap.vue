<template>
    <div id="map" style="height: 400px"></div>
</template>

<script>
import L from "leaflet";

export default {
    props: {
        latitude: {
            type: Number,
            required: true,
        },
        longitude: {
            type: Number,
            required: true,
        },
    },
    mounted() {
        const map = L.map("map").setView([this.latitude, this.longitude], 15);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap1 contributors",
        }).addTo(map);

        L.marker([this.latitude, this.longitude])
            .addTo(map)
            .bindPopup("Clock-in Location")
            .openPopup();
    },
};
</script>
