<template>
    <a-card :bordered="true" :bodyStyle="bodyStyle">
        <CardWidget>
            <a-space>
                <CardWidgetIcon :bgColor="bgColor">
                    <slot name="image"></slot>
                </CardWidgetIcon>
                <figcaption>
                    <slot name="description"></slot>
                </figcaption>
            </a-space>
        </CardWidget>
    </a-card>
</template>

<script>
import { CardWidget, CardWidgetIcon } from "./style";
import common from "../../../composable/common";

export default {
    props: {
        bgColor: {
            default: "#5F63F2",
        },
        bodyStyle: {
            default: {},
        },
    },
    components: {
        CardWidget,
        CardWidgetIcon,
    },
    setup(props, { emit }) {
        const { themeMode } = common();

        return {
            themeMode,
        };
    },
};
</script>

<style></style>
