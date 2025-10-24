<template>
    <div v-if="user">
        <div class="profile-widget">
            <a-badge
                :count="user.is_manager ? 'M' : ''"
                color="green"
                :number-style="{ marginTop: '3px', marginRight: '3px', fontSize: '10px' }"
            >
                <div class="profile-image">
                    <img :src="user.profile_image_url" :alt="user.name" />
                </div>
            </a-badge>

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
    <div v-else>-</div>
</template>

<script>
import { SettingTwoTone } from "@ant-design/icons-vue";
export default {
    props: ["user", "size"],
    components: {
        SettingTwoTone,
    },
};
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
