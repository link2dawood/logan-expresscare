<template>
    <a-card class="task-card" title="Tasks">
        <div class="task-list">
            <a-card
                v-for="(item, index) in tasks"
                :key="index"
                class="task-item-card"
                :bodyStyle="{ padding: '14px' }"
            >
                <div class="task-content">
                    <div class="task-left">
                        <a-checkbox v-model:checked="item.checked" />
                        <a-tooltip title="Mark as important">
                            <StarFilled v-if="item.important" class="important-star" />
                            <StarOutlined
                                v-else
                                class="star-icon"
                                @click="toggleImportant(item)"
                            />
                        </a-tooltip>
                        <span
                            :class="{
                                'task-title': true,
                                'completed-task': item.checked,
                            }"
                        >
                            {{ item.title }}
                        </span>
                    </div>
                    <div class="task-right">
                        <a-tag :color="item.status.color">{{ item.status.label }}</a-tag>
                        <a-avatar-group>
                            <a-avatar
                                v-for="(avatar, index) in item.assignees"
                                :key="index"
                                :src="avatar"
                                size="small"
                            />
                        </a-avatar-group>
                    </div>
                </div>
            </a-card>
        </div>
    </a-card>
</template>

<script setup>
import { ref } from "vue";
import { DownOutlined, StarFilled, StarOutlined } from "@ant-design/icons-vue";

const projects = ref(["All Projects", "Project A", "Project B", "Project C"]);

const tasks = ref([
    {
        title: "Patient appointment booking",
        important: true,
        checked: false,
        status: { label: "Onhold", color: "pink" },
        assignees: ["https://i.pravatar.cc/30?img=1", "https://i.pravatar.cc/30?img=2"],
    },
    {
        title: "Appointment booking with payment",
        important: false,
        checked: false,
        status: { label: "Inprogress", color: "purple" },
        assignees: ["https://i.pravatar.cc/30?img=3"],
    },
    {
        title: "Patient and Doctor video conferencing",
        important: false,
        checked: false,
        status: { label: "Completed", color: "green" },
        assignees: ["https://i.pravatar.cc/30?img=4", "https://i.pravatar.cc/30?img=5"],
    },
    {
        title: "Private chat module",
        important: false,
        checked: true,
        status: { label: "Pending", color: "blue" },
        assignees: ["https://i.pravatar.cc/30?img=6", "https://i.pravatar.cc/30?img=7"],
    },
    {
        title: "Go-Live and Post-Implementation Support",
        important: false,
        checked: false,
        status: { label: "Inprogress", color: "purple" },
        assignees: ["https://i.pravatar.cc/30?img=8"],
    },
]);

const toggleImportant = (task) => {
    task.important = !task.important;
};
</script>

<style scoped>
.task-card {
    max-width: 100%;
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
}

.filter-btn {
    background: transparent;
    border: none;
    cursor: pointer;
}

.task-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.task-item-card {
    display: flex;
    flex-direction: column;
    border-radius: 8px;
}

.task-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.task-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.task-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.task-title {
    font-size: 14px;
}

.completed-task {
    text-decoration: line-through;
    color: gray;
}

.star-icon {
    cursor: pointer;
    color: #999;
}

.important-star {
    color: gold;
    cursor: pointer;
}
</style>
