<template>
    <span>
        <template v-if="visibleAll || visible">
            {{ formattedSalary }}
        </template>
        <template v-else> **** </template>
        <a-button
            v-if="!visibleAll"
            type="link"
            @click="toggleVisible"
            style="padding: 0; margin-left: 4px"
        >
            <template #icon>
                <EyeOutlined v-if="visible" />
                <EyeInvisibleOutlined v-else />
            </template>
        </a-button>
    </span>
</template>

<script>
import { ref, computed, watch } from "vue";
import { EyeInvisibleOutlined, EyeOutlined } from "@ant-design/icons-vue";
import common from "../../common/composable/common";

export default {
    name: "SalaryVisibility",
    components: {
        EyeOutlined,
        EyeInvisibleOutlined,
    },
    props: {
        salary: {
            type: [Number, String],
            default: null,
        },
        formatAmountCurrency: {
            type: Function,
            default: (v) => v,
        },
        visibleAll: {
            type: Boolean,
            default: false,
        },
    },
    setup(props) {
        const visible = ref(false);
        const { formatAmountCurrency } = common();
        const toggleVisible = () => {
            visible.value = !visible.value;
        };
        const formattedSalary = computed(() => {
            if (props.salary == null) return "-";
            return formatAmountCurrency(props.salary);
        });
        watch(
            () => props.visibleAll,
            (newVal) => {
                if (newVal) visible.value = false;
            }
        );
        return { visible, toggleVisible, formattedSalary };
    },
};
</script>
