<template>
    <header class="bg-gray-800 text-white p-4">
        <div class="max-w-4xl mx-auto flex items-center">
            <img src="https://demo.hrmifly.in/images/dark.png" alt="Logo" class="h-8" />
        </div>
    </header>

    <div
        class="relative bg-cover bg-center h-64"
        :style="{
            backgroundImage:
                formFields && formFields.company && formFields.company.profile_image_url
                    ? `url('${formFields.company.profile_image_url}')`
                    : '',
        }"
    >
        <div class="absolute inset-0 bg-black opacity-70"></div>
        <div class="relative flex items-center justify-center h-full">
            <h1 class="text-4xl text-white font-bold">
                {{ formFields.opening?.job_title }}
            </h1>
        </div>
    </div>
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto p-6 bg-white shadow-md my-8">
        <h1 class="text-3xl font-bold mb-4">
            {{ formFields.opening?.job_title }}
        </h1>

        <!-- Job Details Boxes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="border rounded-md shadow-sm flex items-center">
                <StateWidget style="border 1px solid black">
                    <template #image>
                        <CalendarOutlined style="color: #fff; font-size: 24px" />
                    </template>
                    <template #description>
                        <h2 v-if="formFields.opening">
                            {{ formatDate(formFields.opening?.publish_date) }}
                        </h2>
                        <p>{{ $t("opening.publish_date") }}</p>
                    </template>
                </StateWidget>
            </div>
            <div class="border rounded-md shadow-sm flex items-center">
                <StateWidget>
                    <template #image>
                        <UsergroupAddOutlined style="color: #fff; font-size: 24px" />
                    </template>
                    <template #description>
                        <h2 v-if="formFields.opening">
                            {{ formFields.opening?.no_of_vacancies }}
                        </h2>
                        <p>{{ $t("opening.no_of_vacancies") }}</p>
                    </template>
                </StateWidget>
            </div>
            <div class="border rounded-md shadow-sm flex items-center">
                <StateWidget>
                    <template #image>
                        <DollarOutlined style="color: #fff; font-size: 24px" />
                    </template>
                    <template #description>
                        <h2 v-if="formFields">
                            {{ formatAmountCurrency(formFields.opening?.ctc) }}
                        </h2>
                        <p>{{ $t("opening.ctc") }}</p>
                    </template>
                </StateWidget>
            </div>
            <div class="border rounded-md shadow-sm flex items-center">
                <StateWidget>
                    <template #image>
                        <LineChartOutlined style="color: #fff; font-size: 24px" />
                    </template>
                    <template #description>
                        <h2 v-if="formFields.opening">
                            {{ formFields.opening?.experience_required }}
                        </h2>
                        <p>{{ $t("opening.experience_required") }}</p>
                    </template>
                </StateWidget>
            </div>
            <div class="border rounded-md shadow-sm flex items-center">
                <StateWidget>
                    <template #image>
                        <EnvironmentOutlined style="color: #fff; font-size: 24px" />
                    </template>
                    <template #description>
                        <h2 v-if="formFields.location">
                            {{ formFields.location?.name }}
                        </h2>
                        <p>{{ $t("opening.location_id") }}</p>
                    </template>
                </StateWidget>
            </div>
        </div>

        <!-- Job Details Paragraph -->
        <section class="mb-8" v-if="formFields.opening?.introduction != null">
            <h2 class="text-2xl font-semibold mb-4">
                {{ $t("opening.introduction") }}
            </h2>
            <p class="text-gray-700">
                {{ formFields.opening?.introduction }}
            </p>
        </section>

        <!-- Job Responsbilities Paragraph -->
        <section class="mb-8" v-if="formFields.opening?.responsbilities != null">
            <h2 class="text-2xl font-semibold mb-4">
                {{ $t("opening.responsbilities") }}
            </h2>
            <p class="text-gray-700">
                {{ formFields.opening?.responsbilities }}
            </p>
        </section>

        <!-- Job Skills Paragraph -->
        <section class="mb-8" v-if="formFields.opening?.skill_set != null">
            <h2 class="text-2xl font-semibold mb-4">
                {{ $t("opening.skill_set") }}
            </h2>
            <p class="text-gray-700">
                {{ formFields.opening?.skill_set }}
            </p>
        </section>
    </main>
    <div class="button" v-if="remainDays(formFields.opening?.end_date)">
        <a-button
            type="primary"
            @click="
                () => {
                    $router.push({
                        name: 'front.career.application',
                        params: {
                            id: xid,
                        },
                    });
                }
            "
        >
            {{ $t("opening.apply_now") }}
        </a-button>
    </div>

    <footer class="bg-gray-800 text-white py-6">
        <div class="max-w-4xl mx-auto text-center">
            <p>{{ $t("opening.contact_us_at") }}</p>
            <p>
                {{ $t("opening.email") }}{{ formFields.company?.email }} |
                {{ $t("opening.phone") }} {{ formFields.company?.phone }}
            </p>
        </div>
    </footer>
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { forEach, find } from "lodash-es";
import {
    LineChartOutlined,
    EnvironmentOutlined,
    DollarOutlined,
    UsergroupAddOutlined,
    CalendarOutlined,
} from "@ant-design/icons-vue";
import StateWidget from "@/common/components/common/card/StateWidget.vue";
import common from "@/common/composable/common";
import dayjs from "dayjs";

export default defineComponent({
    components: {
        LineChartOutlined,
        EnvironmentOutlined,
        StateWidget,
        DollarOutlined,
        UsergroupAddOutlined,
        CalendarOutlined,
    },
    setup(props, { emit }) {
        const route = useRoute();
        const xid = ref("");
        const formFields = ref({});
        const { formatDate, formatAmountCurrency, appSetting } = common();

        onMounted(() => {
            xid.value = route.params.id;

            axiosAdmin.get(`job-details/${xid.value}`).then((successResponse) => {
                formFields.value = successResponse.data;
                remainDays(formFields.value.opening?.end_date);
            });
        });

        const remainDays = (complete_date) => {
            const today = dayjs();
            const targetDate = dayjs(complete_date);
            if (today.isAfter(targetDate)) {
                var differenceInDays = today.diff(targetDate, "day");
            }
            if (differenceInDays >= 1) {
                return false;
            } else {
                return true;
            }
        };

        return {
            xid,
            formatDate,
            formatAmountCurrency,
            appSetting,
            remainDays,
            formFields,
        };
    },
});
</script>
<style scoped>
.heading {
    text-align: left;
    margin-top: 40px;
    font-weight: bold;
    font-size: 20px;
    color: #4ca5f0;
}
.description {
    margin-top: 20px;
}
.nav {
    background-color: #315684;
    color: white;
    padding: 20px;
    height: 80px;
    font-size: 18px;
    vertical-align: top;
}
.nav-item-1 {
    float: left;
    font-size: 35px;
}

.nav-item-2 {
    float: right;
    margin-top: -12px;
}
.hero_heading {
    text-align: center;
    margin-top: 30px;
    font-size: 22px;
    color: red;
}
.button {
    margin-top: 25px;
    text-align: center;
    margin-bottom: 15px;
    font-size: 18px;
}
</style>
