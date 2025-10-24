import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { find, forEach } from "lodash-es";
import common from "./common";

const hrmManagement = () => {
    const { dayjs, appSetting, user, formatAmountCurrency, formatDate } =
        common();
    const { t } = useI18n();

    const resetSelectformOption = ref([]);
    const letterHeadUser = ref({});
    const letterHeadAppreciation = ref({});
    const templateDescription = ref("");

    const monthArrays = ref([
        { name: t("common.january"), value: "01" },
        { name: t("common.february"), value: "02" },
        { name: t("common.march"), value: "03" },
        { name: t("common.april"), value: "04" },
        { name: t("common.may"), value: "05" },
        { name: t("common.june"), value: "06" },
        { name: t("common.july"), value: "07" },
        { name: t("common.august"), value: "08" },
        { name: t("common.september"), value: "09" },
        { name: t("common.october"), value: "10" },
        { name: t("common.november"), value: "11" },
        { name: t("common.december"), value: "12" },
    ]);

    const weekDays = ref([
        { name: t("common.monday"), value: "Monday" },
        { name: t("common.tuesday"), value: "Tuesday" },
        { name: t("common.wednesday"), value: "Wedensday" },
        { name: t("common.thursday"), value: "Thrusday" },
        { name: t("common.friday"), value: "Friday" },
        { name: t("common.saturday"), value: "Saturday" },
        { name: t("common.sunday"), value: "Sunday" },
    ]);

    const selectedLetterHeadFields = ref([]);

    const reSetListOfKeys = (lists) => {
        selectedLetterHeadFields.value = [];
        forEach(lists, (item) => {
            if (item == "employee_keys") {
                selectedLetterHeadFields.value.push({
                    title: t("template.available_employee_keys"),
                    values: [
                        { name: "EMPLOYEE_NAME" },
                        { name: "EMPLOYEE_DEPARTMENT" },
                        { name: "EMPLOYEE_ID" },
                        { name: "EMPLOYEE_JOINING_DATE" },
                        { name: "EMPLOYEE_PROBATION_END_DATE" },
                        { name: "EMPLOYEE_NOTICE_PERIOD_END_DATE" },
                        { name: "EMPLOYEE_LOCATION" },
                        { name: "EMPLOYEE_ADDRESS" },
                        { name: "EMPLOYEE_EXIT_DATE" },
                        { name: "EMPLOYEE_NOTICE_PERIOD_START_DATE" },
                        { name: "EMPLOYEE_DOB" },
                        { name: "EMPLOYEE_DESIGNATION" },
                    ],
                });
            }
            if (item == "other_keys") {
                selectedLetterHeadFields.value.push({
                    title: t("template.available_other_keys"),
                    values: [
                        { name: "SIGNATORY_DESIGNATION" },
                        { name: "CONTACT_ADDRESS" },
                        { name: "OFFICE_START_TIME" },
                        { name: "CURRENT_DATE" },
                        { name: "SIGNATORY" },
                        { name: "SIGNATORY_DEPARTMENT" },
                        { name: "COMPANY_NAME" },
                        { name: "OFFICE_END_TIME" },
                        { name: "CURRENT_MONTH" },
                        { name: "CURRENT_YEAR" },
                    ],
                });
            }
            if (item == "appreciation") {
                selectedLetterHeadFields.value.push({
                    title: t("template.available_appreciation_keys"),
                    values: [
                        { name: "APPRECIATION_AWARD_NAME" },
                        { name: "APPRECIATION_PRICE_AMOUNT" },
                        { name: "APPRECIATION_PRICE_GIVEN" },
                        { name: "APPRECIATION_GIVEN_DATE" },
                    ],
                });
            }
        });
    };

    const getMonthNameByNumber = (monthNumber) => {
        var newMonthNumber = parseInt(monthNumber);
        newMonthNumber =
            newMonthNumber <= 9 ? "0" + newMonthNumber : "" + newMonthNumber;
        const month = find(monthArrays.value, { value: newMonthNumber });

        return month ? month.name : "-";
    };

    const formatMinutes = (totalMinutes) => {
        if (totalMinutes > 0) {
            var hours = Math.floor(totalMinutes / 60);
            // var minutes = totalMinutes % 60;
               var hourMinutes = hours * 60;
            var minutes = (totalMinutes - hourMinutes)

            var output = "";
            if (hours > 0) {
                output += `${hours} ${t("attendance.hours")} `;
            }

            if (minutes > 0) {
                output += `${minutes.toFixed(0)} ${t("attendance.minutes")}`;
            }

            return output;
        } else {
            return "-";
        }
    };

    const formatShiftTime24Hours = (shiftTime) => {
        return shiftTime ? dayjs(shiftTime, "hh:mm A").format("HH:mm:ss"): undefined;
    };

    const formatShiftTime12Hours = (shiftTime) => {
        return shiftTime? dayjs(shiftTime, "HH:mm:ss").format("hh:mm A"): undefined;
    };

    const setLetterHeadTemplateValues = () => {
        resetSelectformOption.value = [];

        resetSelectformOption.value.push({
            name: "EMPLOYEE_NAME",
            value: letterHeadUser.value ? letterHeadUser.value.name : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_DEPARTMENT",
            value:
                letterHeadUser.value && letterHeadUser.value.department
                    ? letterHeadUser.value.department.name
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_ID",
            value: letterHeadUser.value
                ? letterHeadUser.value.employee_number
                : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_JOINING_DATE",
            value: letterHeadUser.value
                ? letterHeadUser.value.joining_date
                : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_LOCATION",
            value:
                letterHeadUser.value && letterHeadUser.value.location
                    ? letterHeadUser.value.location.name
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_ADDRESS",
            value:
                letterHeadUser.value && letterHeadUser.value.address
                    ? letterHeadUser.value.address
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_EXIT_DATE",
            value:
                letterHeadUser.value && letterHeadUser.value.end_date
                    ? letterHeadUser.value.end_date
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_DOB",
            value:
                letterHeadUser.value && letterHeadUser.value.dob
                    ? letterHeadUser.value.dob
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_NOTICE_PERIOD_START_DATE",
            value:
                letterHeadUser.value && letterHeadUser.value.notice_start_date
                    ? letterHeadUser.value.notice_start_date
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_NOTICE_PERIOD_END_DATE",
            value:
                letterHeadUser.value && letterHeadUser.value.notice_end_date
                    ? letterHeadUser.value.notice_end_date
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_PROBATION_END_DATE",
            value:
                letterHeadUser.value && letterHeadUser.value.probation_end_date
                    ? letterHeadUser.value.probation_end_date
                    : null,
        });
        resetSelectformOption.value.push({
            name: "EMPLOYEE_DESIGNATION",
            value:
                letterHeadUser.value && letterHeadUser.value.designation
                    ? letterHeadUser.value.designation.name
                    : null,
        });

        resetSelectformOption.value.push({
            name: "CONTACT_ADDRESS",
            value: appSetting.value.address ? appSetting.value.address : null,
        });
        resetSelectformOption.value.push({
            name: "SIGNATORY_DESIGNATION",
            value: user.value.designation ? user.value.designation.name : null,
        });
        resetSelectformOption.value.push({
            name: "OFFICE_START_TIME",
            value:
                letterHeadUser.value && letterHeadUser.value.shift
                    ? formatShiftTime12Hours(
                          letterHeadUser.value.shift.clock_in_time
                      )
                    : formatShiftTime12Hours(appSetting.value.clock_in_time),
        });

        resetSelectformOption.value.push({
            name: "OFFICE_END_TIME",
            value:
                letterHeadUser.value && letterHeadUser.value.shift
                    ? formatShiftTime12Hours(
                          letterHeadUser.value.shift.clock_out_time
                      )
                    : formatShiftTime12Hours(appSetting.value.clock_out_time),
        });
        resetSelectformOption.value.push({
            name: "COMPANY_NAME",
            value: appSetting.value ? appSetting.value.name : null,
        });
        resetSelectformOption.value.push({
            name: "SIGNATORY_DEPARTMENT",
            value: user.value.department ? user.value.department.name : null,
        });
        resetSelectformOption.value.push({
            name: "SIGNATORY",
            value: user.value ? user.value.name : null,
        });
        resetSelectformOption.value.push({
            name: "CURRENT_DATE",
            value: dayjs().format("YYYY-MM-DD"),
        });
        resetSelectformOption.value.push({
            name: "CURRENT_MONTH",
            value: dayjs().format("MMMM"),
        });
        resetSelectformOption.value.push({
            name: "CURRENT_YEAR",
            value: dayjs().format("YYYY"),
        });

        // Appreciations
        resetSelectformOption.value.push({
            name: "APPRECIATION_AWARD_NAME",
            value:
                letterHeadAppreciation.value &&
                letterHeadAppreciation.value.award
                    ? letterHeadAppreciation.value.award.name
                    : null,
        });
        resetSelectformOption.value.push({
            name: "APPRECIATION_PRICE_AMOUNT",
            value:
                letterHeadAppreciation.value &&
                letterHeadAppreciation.value.price_amount
                    ? formatAmountCurrency(
                          letterHeadAppreciation.value.price_amount
                      )
                    : null,
        });

        const priceGivenString =
            letterHeadAppreciation.value &&
            letterHeadAppreciation.value.price_given &&
            letterHeadAppreciation.value.price_given.length > 0
                ? `${letterHeadAppreciation.value.price_given
                      .map((item) => `${item.price_given}`)
                      .join(", ")}`
                : null;

        resetSelectformOption.value.push({
            name: "APPRECIATION_PRICE_GIVEN",
            value: priceGivenString,
        });
        resetSelectformOption.value.push({
            name: "APPRECIATION_GIVEN_DATE",
            value:
                letterHeadAppreciation.value &&
                letterHeadAppreciation.value.date
                    ? formatDate(letterHeadAppreciation.value.date)
                    : null,
        });
    };

    return {
        monthArrays,
        getMonthNameByNumber,
        formatMinutes,
        formatShiftTime24Hours,
        formatShiftTime12Hours,
        weekDays,

        selectedLetterHeadFields,
        resetSelectformOption,
        letterHeadUser,
        letterHeadAppreciation,
        setLetterHeadTemplateValues,
        templateDescription,
        reSetListOfKeys,
    };
};

export default hrmManagement;
