<template>
    <a-card
        class="notifications-container"
        title="Notifications"
        :bodyStyle="{ padding: '10px' }"
    >
        <template #extra>
            <a-button type="text"> View All </a-button>
        </template>
        <a-list
            :data-source="notifications"
            item-layout="horizontal"
            class="notifications-list"
            size="small"
        >
            <template #renderItem="{ item, index }">
                <a-list-item :class="{ 'last-item': index === notifications.length - 1 }">
                    <a-list-item-meta>
                        <template #avatar>
                            <a-avatar :src="item.avatar" />
                        </template>
                        <template #title>
                            <div class="notification-header">
                                <div class="notification-text">
                                    <strong>{{ item.name }}</strong> requested access to
                                    <strong>{{ item.system }}</strong>
                                </div>
                                <div class="notification-time">
                                    {{ item.time }}
                                </div>
                                <div v-if="item.file" class="file-attachment">
                                    <a-button type="link" icon="file-text-outlined">
                                        {{ item.file }}
                                    </a-button>
                                </div>
                                <div v-if="item.actions" class="action-buttons">
                                    <a-space>
                                        <a-button
                                            type="primary"
                                            size="small"
                                            @click="approveRequest(item.id)"
                                            >Approve</a-button
                                        >
                                        <a-button
                                            size="small"
                                            danger
                                            @click="declineRequest(item.id)"
                                            >Decline</a-button
                                        >
                                    </a-space>
                                </div>
                            </div>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
            </template>
        </a-list>
    </a-card>
</template>

<script setup>
import { ref } from "vue";

const notifications = ref([
    {
        id: 1,
        name: "Lex Murphy",
        time: "Today at 9:42 AM",
        system: "UNIX",
        avatar: "https://i.pravatar.cc/50?img=7",
        file: "EY_review.pdf",
    },
    {
        id: 2,
        name: "Lex Murphy",
        time: "Today at 10:00 AM",
        system: "UNIX",
        avatar: "https://i.pravatar.cc/50?img=8",
    },
    {
        id: 3,
        name: "Lex Murphy",
        time: "Today at 10:50 AM",
        system: "UNIX",
        avatar: "https://i.pravatar.cc/50?img=9",
        actions: true,
    },
    {
        id: 4,
        name: "Lex Murphy",
        time: "Today at 12:00 PM",
        system: "UNIX",
        avatar: "https://i.pravatar.cc/50?img=10",
    },
    {
        id: 5,
        name: "Lex Murphy",
        time: "Today at 05:00 PM",
        system: "UNIX",
        avatar: "https://i.pravatar.cc/50?img=11",
    },
]);
</script>

<style scoped>
.notifications-container {
    width: 100%;
}
.notifications-list {
    margin: 0;
    padding: 0;
    min-height: 255px;
}
.notification-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
}
.notification-text {
    font-size: 14px;
}
.notification-time {
    font-size: 12px;
    color: gray;
    white-space: nowrap;
}
.notification-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 4px;
    align-items: flex-start;
}
.file-attachment {
    display: flex;
    align-items: flex-start;
    margin-top: 4px;
}
.action-buttons {
    display: flex;
    align-items: flex-start;
    margin-top: 7px;
}
.last-item {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>
