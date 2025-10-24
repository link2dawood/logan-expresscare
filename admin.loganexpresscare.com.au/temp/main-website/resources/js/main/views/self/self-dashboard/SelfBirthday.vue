<template>
    <a-card class="birthday-card">
        <div class="birthday-header">
            {{ $t("menu.team_birthdays") }}
        </div>
        <a-carousel arrows>
            <template #prevArrow>
                <div class="custom-slick-arrow" style="left: 10px; z-index: 1">
                    <LeftCircleOutlined />
                </div>
            </template>
            <template #nextArrow>
                <div class="custom-slick-arrow" style="right: 10px">
                    <RightCircleOutlined />
                </div>
            </template>

            <div
                v-for="(member, index) in upcomingBirthdays"
                :key="index"
                class="carousel-slide"
            >
                <div class="birthday-content">
                    <a-avatar :size="50" :src="member.image_url" />
                    <div class="member-name">{{ member.name }}</div>
                    <div class="member-role">{{ member.formatted_date }}</div>
                </div>
            </div>
        </a-carousel>
    </a-card>
</template>

<script>
import { ref, defineComponent, watch } from "vue";
import { LeftCircleOutlined, RightCircleOutlined } from "@ant-design/icons-vue";

export default defineComponent({
    props: ["data"],
    components: {
        LeftCircleOutlined,
        RightCircleOutlined,
    },
    setup(props) {
        const upcomingBirthdays = ref([]);

        watch(
            () => props.data,
            (newVal, oldVal) => {
                upcomingBirthdays.value = newVal.upcomingBirthday;
            },
            { immediate: true }
        );

        return { upcomingBirthdays };
    },
});
</script>

<style scoped>
.birthday-card {
    background: #1e242b;
    color: white;
    text-align: center;
    max-width: 100%;
    padding: 16px;
    height: 85%;
}

.birthday-header {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
}

.birthday-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
}

.member-name {
    font-size: 16px;
    font-weight: bold;
    margin-top: 10px;
    color: white;
}

.member-role {
    font-size: 14px;
    color: #b0b0b0;
    margin-bottom: 12px;
}

.wish-button {
    background: #ff7733;
    border-color: #ff7733;
    color: white;
    font-weight: bold;
    border-radius: 6px;
    transition: background 0.3s;
}

.wish-button:hover {
    background: #ff9555;
    border-color: #ff9555;
}

.carousel-slide {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 220px;
}

.custom-slick-arrow {
    font-size: 24px;
    color: white;
    cursor: pointer;
    transition: color 0.3s;
}

.custom-slick-arrow:hover {
    color: #ff7733;
}
</style>
