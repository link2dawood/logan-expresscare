import { ref, computed } from "vue";
import { defineStore } from "pinia";

const IS_SELF_CLOCKING = "hrm_is_self_clocking";
const SHOW_CLOCK_IN_BUTTON = "hrm_show_clock_in_button";
const SHOW_CLOCK_OUT_BUTTON = "hrm_show_clock_out_button";
const CLOCK_IN_DATE_TIME = "hrm_clock_in_date_time";
const CLOCK_OUT_DATE_TIME = "hrm_clock_in_date_time";

import { getValueFromStorage, setValueInStorage } from "./storage";

export const useHrmStore = defineStore("hrm", () => {
    const isSelfClocking = ref(getValueFromStorage(IS_SELF_CLOCKING, false));
    const showClockInButton = ref(
        getValueFromStorage(SHOW_CLOCK_IN_BUTTON, false)
    );
    const showClockOutButton = ref(
        getValueFromStorage(SHOW_CLOCK_OUT_BUTTON, false)
    );
    const clockInDateTime = ref(
        getValueFromStorage(CLOCK_IN_DATE_TIME, undefined)
    );
    const clockOutDateTime = ref(
        getValueFromStorage(CLOCK_OUT_DATE_TIME, undefined)
    );

    const updateSelfClocking = (newVal) => {
        isSelfClocking.value = newVal;
        setValueInStorage(IS_SELF_CLOCKING, newVal);
    };

    const updateShowClockInButton = (newVal) => {
        showClockInButton.value = newVal ? 1 : 0;
        setValueInStorage(IS_SELF_CLOCKING, showClockInButton.value);
    };

    const updateShowClockOutButton = (newVal) => {
        showClockOutButton.value = newVal ? 1 : 0;
        setValueInStorage(SHOW_CLOCK_OUT_BUTTON, showClockOutButton.value);
    };

    const updateClockInDateTime = (newVal) => {
        clockInDateTime.value = newVal;
        setValueInStorage(CLOCK_IN_DATE_TIME, newVal);
    };

    const updateClockOutDateTime = (newVal) => {
        clockOutDateTime.value = newVal;
        setValueInStorage(CLOCK_OUT_DATE_TIME, newVal);
    };

    // actions
    const updateSelfClockingAction = () => {
        axiosAdmin.get("/hrm/today-attendance-details").then((response) => {
            updateSelfClocking(response.data.self_clocking);
            updateShowClockInButton(response.data.show_clock_in_button);
            updateShowClockOutButton(response.data.show_clock_out_button);
            updateClockInDateTime(response.data.clock_in_date_time);
            updateClockOutDateTime(response.data.clock_out_date_time);
        });
    };

    return {
        isSelfClocking,
        showClockInButton,
        showClockOutButton,
        clockInDateTime,
        clockOutDateTime,

        updateSelfClocking,
        updateShowClockInButton,
        updateShowClockOutButton,
        updateClockInDateTime,
        updateClockOutDateTime,

        updateSelfClockingAction,
    };
});
