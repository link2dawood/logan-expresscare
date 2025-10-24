<template>
    <a-card class="team-members-container" :bodyStyle="{ padding: '4px' }">
        <template #title>
            {{ $t("menu.assigned_survey") }}
        </template>
        <div class="pending-leave-hrm">
            <perfect-scrollbar
                :options="{
                    wheelSpeed: 1,
                    swipeEasing: true,
                    suppressScrollX: true,
                }"
            >
                <a-list
                    :data-source="feedBackUsers"
                    item-layout="horizontal"
                    class="team-list"
                    v-if="feedBackUsers && feedBackUsers.length > 0"
                >
                    <template #renderItem="{ item, index }">
                        <a-list-item
                            class="list-item"
                            :class="{
                                'first-item': index === 0,
                                'last-item': index === feedBackUsers.length - 1,
                            }"
                            v-if="item.feedback_given === 0"
                        >
                            <a-list-item-meta
                                :description="formatDate(item?.submit_date)"
                            >
                                <template #title>
                                    {{ item?.feedback?.title }}
                                </template>
                            </a-list-item-meta>
                            <template #actions>
                                <a-button
                                    type="primary"
                                    @click="feedbackOpened(item)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><PlayCircleOutlined /></template>
                                </a-button>
                            </template>
                        </a-list-item>
                    </template>
                </a-list>
            </perfect-scrollbar>
        </div>
        <a-result
            v-if="feedBackUsers && feedBackUsers.length <= 0"
            style="margin-top: -316px"
        >
            <template #icon>
                <ExclamationCircleFilled
                    :style="{
                        fontSize: '52px',
                        padding: '0px',
                    }"
                />
            </template>
            <template #extra>
                <span type="primary" :style="{ fontSize: '28px' }">
                    {{ $t("assigned_surve.there_is_no_assigned_survey") }}</span
                >
            </template>
        </a-result>
    </a-card>
    <Feedback
        :data="questionData"
        :visible="feedbackVisible"
        @close="feedbackClosed()"
        @addSuccess="feedbackClosed()"
        :pageTitle="$t('assigned_surve.assigned_survey_details')"
    />
</template>

<script>
import { ref, defineComponent, watch } from "vue";
import {
    MessageOutlined,
    ExclamationCircleFilled,
    PlayCircleOutlined,
} from "@ant-design/icons-vue";
import common from "@/common/composable/common";
import Feedback from "../assigned-survey/Feedback.vue";

export default defineComponent({
    props: ["data"],
    components: {
        MessageOutlined,
        ExclamationCircleFilled,
        PlayCircleOutlined,
        Feedback,
    },
    setup(props) {
        const { formatDate } = common();
        const feedBackUsers = ref([]);
        const questionData = ref({});
        const feedbackVisible = ref(false);

        const feedbackClosed = () => {
            feedbackVisible.value = false;
        };

        const feedbackOpened = (item) => {
            feedbackVisible.value = true;
            questionData.value = item;
        };

        watch(
            () => props.data,
            (newVal, oldVal) => {
                feedBackUsers.value = newVal.getFeedbackUsers;
            },
            { deep: true, immediate: true }
        );

        return {
            feedBackUsers,
            formatDate,
            feedbackVisible,
            questionData,
            feedbackClosed,
            feedbackOpened,
        };
    },
});
</script>
<style scoped>
.team-members-container {
    width: 100%;
    height: 500px;
    display: flex;
    flex-direction: column;
    padding: 0px;
}

.team-list-wrapper {
    flex-grow: 1;
    max-height: 400px;
    overflow-y: auto;
}

.team-list {
    padding: 0;
    margin: 0;
}

.pending-leave-hrm .ps {
    height: 378px;
}
</style>
