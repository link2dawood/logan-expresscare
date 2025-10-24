import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "shifts?fields=id,xid,name,clock_in_time,capture_location,clock_out_time,late_mark_after,early_clock_in_till,allow_clock_out_till,self_clocking,allowed_ip_address,early_clock_in_time";
    const addEditUrl = "shifts";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {
        name: "",
        clock_in_time: "",
        clock_out_time: "",
        late_mark_after: "",
        self_clocking: 1,
        allowed_ip_address: "",
        early_clock_in_time: "",
        allow_clock_out_till: "",
        time: [],
        capture_location: 0,
    };

    const columns = ref([
        {
            title: t("shift.name"),
            dataIndex: "name",
        },
        {
            title: t("shift.clock_in_time"),
            dataIndex: "clock_in_time",
        },
        {
            title: t("shift.clock_out_time"),
            dataIndex: "clock_out_time",
        },
        {
            title: t("shift.late_mark_after"),
            dataIndex: "late_mark_after",
        },
        {
            title: t("shift.allow_clock_out_till"),
            dataIndex: "allow_clock_out_till",
        },
        {
            title: t("shift.early_clock_in_time"),
            dataIndex: "early_clock_in_time",
        },
        {
            title: t("shift.self_clocking"),
            dataIndex: "self_clocking",
        },
        {
            title: t("shift.allowed_ip_address"),
            dataIndex: "allowed_ip_address",
        },
        {
            title: t("shift.total_employee"),
            dataIndex: "employee_count",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
