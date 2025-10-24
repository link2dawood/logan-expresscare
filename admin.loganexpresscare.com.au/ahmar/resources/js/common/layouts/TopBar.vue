<template>
    <a-layout-header
        :style="{
            padding: '0 16px',
            background: themeMode == 'dark' ? '#141414' : '#fff',
        }"
    >
        <a-row>
            <a-col :span="4">
                <a-space>
                    <MenuOutlined class="trigger" @click="showHideMenu" />
                </a-space>
            </a-col>
            <a-col :span="20">
                <HeaderRightIcons>
                    <a-space>
                        <ThemeModeChanger />
                        <a-divider type="vertical" />
                        <a-dropdown
                            :placement="appSetting.rtl ? 'bottomLeft' : 'bottomRight'"
                        >
                            <a class="ant-dropdown-link" @click.prevent>
                                {{ selectedLang }}
                                <DownOutlined />
                            </a>
                            <template #overlay>
                                <a-menu>
                                    <a-menu-item
                                        v-for="lang in langs"
                                        :key="lang.key"
                                        @click="langSelected(lang.key)"
                                    >
                                        <a-space>
                                            <a-avatar
                                                shape="square"
                                                size="small"
                                                :src="lang.image_url"
                                            />
                                            {{ lang.name }}
                                        </a-space>
                                    </a-menu-item>
                                </a-menu>
                            </template>
                        </a-dropdown>
                        <a-divider type="vertical" />
                        <a-button
                            type="link"
                            @click="
                                () => {
                                    $router.push({
                                        name: 'admin.settings.profile.index',
                                    });
                                }
                            "
                            class="p-0"
                        >
                            <a-avatar size="small" :src="user.profile_image_url" />
                        </a-button>
                    </a-space>
                </HeaderRightIcons>
            </a-col>
        </a-row>
    </a-layout-header>
</template>

<script>
import { ref, reactive, computed, onMounted } from "vue";
import { useAuthStore } from "../../main/store/authStore";
import { MenuOutlined, DownOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { loadLocaleMessages } from "../i18n";
import { HeaderRightIcons } from "./style";
import common from "../../common/composable/common";
import MenuMode from "./MenuMode.vue";
import AffixButton from "./AffixButton.vue";
import ThemeModeChanger from "./ThemeModeChanger.vue";

export default {
    components: {
        MenuOutlined,
        DownOutlined,
        HeaderRightIcons,
        MenuMode,
        AffixButton,
        ThemeModeChanger,
    },
    setup(props, { emit }) {
        const { user, appSetting, permsArray, menuCollapsed, themeMode } = common();
        const authStore = useAuthStore();
        const selectedLang = ref(authStore.lang);
        const { locale, t } = useI18n();
        const themeModeLoading = ref(false);

        const langSelected = async (lang) => {
            authStore.updateLang(lang);
            await loadLocaleMessages(i18n, lang);

            selectedLang.value = lang;
            locale.value = lang;
        };

        const showHideMenu = () => {
            authStore.updateMenuCollapsed(!menuCollapsed.value);
        };

        const logout = () => {
            authStore.logoutAction();
        };

        const themeModeChanged = (checked) => {
            const mode = checked ? "dark" : "light";
            themeModeLoading.value = true;

            axiosAdmin
                .post("change-theme-mode", {
                    theme_mode: mode,
                })
                .then((response) => {
                    if (response.data.status == "success") {
                        window.location.reload();
                    }
                    themeModeLoading.value = false;
                });
        };

        return {
            permsArray,
            appSetting,
            logout,
            showHideMenu,
            langSelected,
            selectedLang,
            langs: computed(() => authStore.allLangs),

            user,

            themeMode,
            themeModeChanged,
            themeModeLoading,
            themeMode,
        };
    },
};
</script>

<style lang="less">
.trigger {
    font-size: 18px;
    line-height: 64px;
    padding-top: 4px;
    cursor: pointer;
    transition: color 0.3s;
}

.trigger:hover {
    color: #1890ff;
}
</style>
