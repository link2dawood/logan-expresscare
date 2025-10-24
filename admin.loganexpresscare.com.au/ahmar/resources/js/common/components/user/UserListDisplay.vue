<template>
    <div v-if="user">
        <div v-if="whereToShow != 'table'">
            <div
                :style="{
                    display: 'flex',
                    alignItems: 'center',
                    borderRadius: '10px',
                    maxWidth: '300px',
                    width: '100%',
                }"
            >
                <div class="profile-image">
                    <img
                        :style="{
                            bordeRadius: '50%',
                            width: '20px',
                            height: '20px',
                            objectFit: 'cover',
                        }"
                        :src="user.profile_image_url"
                        :alt="user.name"
                    />
                </div>
                <div class="profile-info">
                    <span
                        :style="{
                            marginTop: '0px',
                            marginBottom: '0px',
                            paddingTop: '0px',
                            paddingBottom: '0px',
                            fontSize: '14px',
                        }"
                    >
                        {{ user.name }}
                    </span>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="profile-widget">
                <div class="profile-image">
                    <img :src="user.profile_image_url" :alt="user.name" />
                </div>
                <div class="profile-info">
                    <span class="name">{{ user.name }}</span>
                    <p class="designation">
                        {{
                            user.designation && user.designation.name
                                ? user.designation.name
                                : ""
                        }}
                        {{
                            user.location && user.location.name
                                ? user.designation && user.designation.name
                                    ? ` - ${user.location.name}`
                                    : user.location.name
                                : ""
                        }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div v-else>-</div>
</template>

<script>
import { defineComponent, computed } from "vue";

export default defineComponent({
    props: {
        user: {
            type: Object,
            default: {},
        },
        whereToShow: {
            type: String,
            default: "table",
        },
        vislble: {
            default: false,
        },
    },
});
</script>

<style lang="less">
.profile-widget {
    display: flex;
    align-items: center;
    border-radius: 10px;
    max-width: 400px;
    width: 100%;
}

.profile-image img {
    border-radius: 50%;
    width: 38px;
    height: 38px;
    object-fit: cover;
}

.profile-info {
    margin-left: 15px;
    text-align: left;
}

.name {
    margin: 0;
    font-size: 14px;
    font-weight: bold;
}

.designation {
    margin: 0px;
    font-size: 13px;
    color: #666;
}
</style>
