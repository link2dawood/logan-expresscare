<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :width="drawerWidth"
        @ok="onClose"
    >
        <a-card
            :title="pageTitle"
            style="width: 100%; height: 65vh"
            headStyle="background-color:gray; color:white"
        >
            <template #extra>
                <a-space>
                    <a-button type="primary" key="back" @click="onClose">
                        {{ $t("common.print")
                        }}<template #icon><PrinterOutlined /></template>
                    </a-button>
                    <a-button type="primary" key="back" @click="onClose">
                        {{ $t("common.download")
                        }}<template #icon><DownloadOutlined /></template>
                    </a-button>
                </a-space>
            </template>
            <div>
                <div v-html="htmlContent"></div>
            </div>
        </a-card>

        <template #footer>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import { PrinterOutlined, DownloadOutlined } from "@ant-design/icons-vue";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: {
        PrinterOutlined,
        DownloadOutlined,
    },
    setup(props, { emit }) {
        const htmlContent = ref();

        const onClose = () => {
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                htmlContent.value = props.data.description;
            }
        );

        return {
            onClose,

            htmlContent,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "50%",
        };
    },
});
</script>
