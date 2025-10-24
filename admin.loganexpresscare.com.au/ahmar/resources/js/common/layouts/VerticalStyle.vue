<template>
    <Div>
        <section id="components-layout-demo-responsive">
            <a-layout>
                <LeftSidebarBar />

                <a-layout :style="themeMode == 'dark' ? { background: '#141414' } : {}">
                    <MainArea
                        :innerWidth="innerWidth"
                        :collapsed="menuCollapsed"
                        :isRtl="appSetting.rtl"
                    >
                        <TopBar />
                        <MainContentArea>
                            <a-layout-content>
                                <router-view></router-view>
                            </a-layout-content>
                        </MainContentArea>
                    </MainArea>
                </a-layout>
            </a-layout>
        </section>
    </Div>
</template>

<script>
import { ref } from "vue";
import TopBar from "./TopBar.vue";
import LeftSidebarBar from "./LeftSidebar.vue";
import { Div, MainArea, MainContentArea } from "./style";
import AffixButton from "./AffixButton.vue";
import common from "../composable/common";

export default {
    components: {
        TopBar,
        LeftSidebarBar,
        Div,
        MainArea,
        MainContentArea,

        AffixButton,
    },
    setup() {
        const { appSetting, menuCollapsed, themeMode } = common();

        return {
            appSetting,
            menuCollapsed,
            themeMode,

            innerWidth: window.innerWidth,
        };
    },
};
</script>

<style>
#components-layout-demo-responsive .logo {
    height: 32px;
    margin: 16px 0px 16px 24px;
    text-align: center;
}

.site-layout-sub-header-background {
    background: #fff;
}

.site-layout-background {
    background: #fff;
}

[data-theme="dark"] .site-layout-sub-header-background {
    background: #141414;
}
</style>
